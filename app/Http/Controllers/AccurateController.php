<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Models\AccurateToken;
use App\Models\AccurateSession;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Accurate;
use App\Models\AccuratePage;
use Illuminate\Support\Facades\DB;

class AccurateController extends Controller
{
    //
    public function refreshToken()
    {
        $cek = DB::table('accurate')->whereNull('deleted_at')->get();

        foreach ($cek as $data) {
            $clientID = $data->client_id;
            $clientSecret = $data->client_secret;

            $text = rtrim($clientID) . ":" . rtrim($clientSecret);
            $enkrip = base64_encode($text);

            $client = new Client();
            $headers = [
                'Authorization' => 'Basic ' . $enkrip
            ];
    
            $getRefreshToken = AccurateToken::whereNull('deleted_at')
                ->where('kd_database', '=', $data->kd_database)->first();
    
            $url = 'https://account.accurate.id/oauth/token?grant_type=refresh_token&refresh_token=' . rtrim($getRefreshToken->refresh_token);
            
            try {
                $response = $client->post($url, [
                    'headers' => $headers
                ]);
    
                // Ambil isi dari respons
                $body = $response->getBody();
                $contents = $body->getContents();
    
                $responseArray = json_decode($contents, true);
    
                $accessToken = $responseArray['access_token'];
                $tokenType = $responseArray['token_type'];
                $refreshToken = $responseArray['refresh_token'];
                $expiresIn = $responseArray['expires_in'];
    
                $getRefreshToken->update(['deleted_at' => now()]);
    
                AccurateToken::create([
                    'access_token' => $accessToken,
                    'token_type' => $tokenType,
                    'refresh_token' => $refreshToken,
                    'kd_database' => $data->kd_database,
                    'expires_in' => $expiresIn
                ]);
    
                return response()->json([
                    'access_token' => $accessToken,
                    'token_type' => $tokenType,
                    'refresh_token' => $refreshToken,
                    'kd_database' => $data->kd_database,
                    'expires_in' => $expiresIn
                ]);
            } catch (\Exception $e) {
                // Tangani kesalahan jika terjadi
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    }

    public function getSession()
    {
        $client = new Client();
        $cek = DB::table('accurate')->whereNull('deleted_at')->get();

        foreach ($cek as $data) {
            $getRefreshToken = AccurateToken::whereNull('deleted_at')
                ->where('kd_database', $data->kd_database)
                ->first();
    
            $headers = [
                'Authorization' => 'Bearer ' . $getRefreshToken->access_token
            ];
    
            $request = new Request('GET'
                , 'https://account.accurate.id/api/open-db.do?id=' . $data->kd_database, $headers
            );
    
            try {
                $res = $client->sendAsync($request)->wait();
                $result = json_decode((string)$res->getBody(), true);
    
                $host = $result['host'];
                $session = $result['session'];
                $admin = $result['admin'];
                $dataVersion = $result['dataVersion'];
                $accessibleUntil = $result['accessibleUntil'];
                $licenseEnd = $result['licenseEnd'];
    
                $getSession = AccurateSession::whereNull('deleted_at')
                    ->where('kd_database', $data->kd_database)
                    ->first();

                if ($getSession) {
                    $getSession->delete();
                }
    
                AccurateSession::create([
                    'host' => $host,
                    'session' => $session,
                    'admin' => $admin,
                    'data_version' => $dataVersion,
                    'accessible_until' => $accessibleUntil,
                    'license_end' => $licenseEnd,
                    'kd_database' => $data->kd_database
                ]);
    
                return $res->getBody();
    
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    }

    public function getListItem()
    {
        $client = new Client();

        $getDB = $this->getActiveDatabase();

        $test = [];

        if (count($getDB)>0) {
            foreach ($getDB as $database) {
                $getAccess = $this->getDatabaseAccess($database->kd_database);
                if ($getAccess) {
                    $headers = $this->buildHeaders($getAccess);
                    $request = new Request('GET', $getAccess->host . '/accurate/api/item/list.do', $headers);
                    $kdDb = $database->kd_database;

                    try {
                        // $jmlDataInsert = 1;
                        // $jmlDataUpdate = 1;
                        $res = $client->sendAsync($request)->wait();
                        $result = json_decode((string)$res->getBody(), true);
                        $totalPages = $result['sp']['pageCount'];
                        $batchSize = 10;
                        $totalBatches = ceil($totalPages / $batchSize);
                        $cekPages = $this->cekPages($kdDb);

                        if ($cekPages) {
                            return $this->insertProduct(
                                $getAccess,
                                $cekPages->id,
                                $cekPages->startPage,
                                $cekPages->endPage,
                                $kdDb
                            );
                        } else {
                            $this->createAccuratePages($totalBatches, $totalPages, $kdDb);
                            $cekPages = $this->cekPages($kdDb);

                            if ($cekPages) {
                                return $this->insertProduct(
                                    $getAccess,
                                    $cekPages->id,
                                    $cekPages->startPage,
                                    $cekPages->endPage,
                                    $kdDb
                                );
                            }
                        }
                    } catch (\Exception $e) {
                        return response()->json(['error' => $e->getMessage()], 500);
                    }
                }
            }
        }
    }

    public function postTransaction()
    {
        $cekTrans = DB::table('transaction')
                        ->select('kd_database')
                        ->where('is_send_to_accu', 0)
                        ->distinct()
                        ->get();

        if (count($cekTrans)>0) {
            foreach ($cekTrans as $dbTrans) {
                
                $getAccess = $this->getDatabaseAccess($dbTrans->kd_database);
                    
                try {
                    $client = new Client();
                    $headers = $this->buildHeaders($getAccess);
    
                    $dataTrans = DB::table('transaction')
                                    ->where('is_send_to_accu', 0)
                                    ->get();
    
                    foreach ($dataTrans as $item => $value) {
                        $data["detailItem[".$item."].unitPrice"] = $value->harga_jual;
                        $data["detailItem[".$item."].quantity"] = $value->qty;
                        $data["detailItem[".$item."].itemNo"] = $value->kd_produk;
                        $data["data[".$item."].transDate"] = date("d/m/Y", strtotime($value->created_at));
                    }
    
                    $qs = http_build_query($data);
                    $test = $getAccess->host . '/accurate/api/sales-invoice/save.do?customerNo=WEB.00015&typeAutoNumber=' . urlencode("100") . '&branchId=' . urlencode("50") . '&' . $qs;
    
    
                    $request = new Request('POST', urldecode($test), $headers);
                    $res = $client->sendAsync($request)->wait();
                    $res_body = json_decode($res->getBody(), true);
    
                    if ($res_body['s']) {
                        $pesan = $res_body['d'][0];
                        preg_match('/"([^"]+)"/', $pesan, $matches);
                        $nomorFaktur = $matches[1];
    
                        foreach ($dataTrans as $item) {
                            $data = Transaction::find($item->id);
                            $data->update([
                                'is_send_to_accu' => 1,
                                'no_accu_trans' => $nomorFaktur
                            ]);
                        }
                        return response()->json(['message' => $res_body['d']], 200);
    
                    } else {
                        echo "Permintaan gagal: Data tidak disimpan.";
                    }
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
        } else {
            return response()->json(['message' => 'Belum ada transaksi yang perlu diproses!'], 200);
        }
    }

    public function updatePriceAndStock()
    {
        $client = new Client();

        $getDB = $this->getActiveDatabase();

        if (count($getDB)>0) {
            foreach ($getDB as $database) {
                $getAccess = $this->getDatabaseAccess($database->kd_database);
                if ($getAccess) {
                    $headers = $this->buildHeaders($getAccess);
                    $request = new Request('GET', $getAccess->host . '/accurate/api/item/list.do', $headers);
                    $kdDb = $database->kd_database;

                    try {
                        $jmlDataUpdate = 1;
                        $res = $client->sendAsync($request)->wait();
                        // $result = json_decode((string)$res->getBody(), true);
            
                        // for ($i=1; $i<=$result['sp']['pageCount']; $i++) {
                        for ($i=1; $i<=3; $i++) {
                            $request_new = new Request('GET'
                                , $getAccess->host . '/accurate/api/item/list.do?fields=id,name,itemType,itemTypeName,unitPrice,no,charField1,availableToSell,charField4,charField5&sp.page=' . $i
                                , $headers
                            );
                            $res_new = $client->sendAsync($request_new)->wait();
                            $result_new = json_decode((string)$res_new->getBody(), true);
            
                            foreach ($result_new['d'] as $data) {
                                $kdProduct = $data['no'];
                                $kdProductAccu = $data['id'];
                                $nmProduct = $data['name'];
                                $hargaJual = $data['unitPrice'];
                                $stockAvail = $data['availableToSell'];
                                $status = $data['charField5']=='N' ? "Tidak Aktif" : "Aktif";
                                $database = $kdDb;
            
                                $productExist = Product::where('kd_produk', $kdProduct)->count();
            
                                if ($productExist > 0) {
                                    Product::where('kd_produk', $kdProduct)
                                    ->update([
                                        'harga_jual' => $hargaJual,
                                        'qty_available' => $stockAvail,
                                        'status' => $status
                                    ]);
                                    $jmlDataUpdate++;
                                }
                            }
                        }
                        return response()->json([
                            'message' => 'Data berhasil diperbaharui sebanyak: ' . $jmlDataUpdate
                        ], 200);
                    } catch (\Exception $e) {
                        return response()->json(['error' => $e->getMessage()], 500);
                    }
                } else {
                    echo 'else out';
                }
            }
        }
    }

    private function getActiveDatabase()
    {
        return DB::table('accurate_db')
        ->whereNull('deleted_at')
        ->get();
    }

    private function getDatabaseAccess($kdDatabase)
    {
        return DB::table('accurate_tokens as a')
            ->select('a.kd_database', 'a.access_token', 'a.refresh_token', 'b.host', 'b.session')
            ->join('accurate_sessions as b', 'b.kd_database', '=', 'a.kd_database')
            ->where('a.kd_database', $kdDatabase)
            ->whereNull('a.deleted_at')
            ->whereNull('b.deleted_at')
            ->first();
    }
    
    private function buildHeaders($getAccess)
    {
        return [
            'X-Session-ID' => $getAccess->session,
            'Authorization' => 'Bearer ' . $getAccess->access_token
        ];
    }

    private function cekPages($kdDb)
    {
        return AccuratePage::whereNull('deleted_at')
            ->where('kd_database', $kdDb)
            ->first();
    }

    private function insertProduct($Access, $id, $startPage, $endPage, $kdDb)
    {
        $client = new Client();

        $jmlDataInsert = 0;
        $jmlDataUpdate = 0;

        for ($page=$startPage; $page<=$endPage; $page++) {
            $request_new = new Request('GET'
                , $Access->host . '/accurate/api/item/list.do?fields=id,name,itemType,itemTypeName,unitPrice,no,charField1,availableToSell,charField4,charField5,upcNo&sp.page=' . $page
                , $this->buildHeaders($Access)
            );
            $res_new = $client->sendAsync($request_new)->wait();
            $result_new = json_decode((string)$res_new->getBody(), true);

            foreach ($result_new['d'] as $data) {
                $kdProduct = $data['no'];
                $kdProductAccu = $data['id'];
                $nmProduct = $data['name'];
                $hargaJual = $data['unitPrice'];
                $stockAvail = $data['availableToSell'];
                $status = $data['charField5']=='N' ? "Tidak Aktif" : "Aktif";
                $barcode = $data['upcNo'];
                $database = $kdDb;

                $productExist = Product::where('kd_produk', $kdProduct)->count();

                if ($productExist <= 0) {
                    Product::create([
                        'kd_produk' => $kdProduct,
                        'kd_produk_accu' => $kdProductAccu,
                        'nm_produk' => $nmProduct,
                        'harga_jual' => $hargaJual,
                        'qty_available' => $stockAvail,
                        'database' => $database,
                        'status' => $status,
                        'barcode' => $barcode
                    ]);
                    $jmlDataInsert = $jmlDataInsert + 1;
                } else {
                    Product::where('kd_produk', $kdProduct)
                        ->where('kd_produk_accu', $kdProductAccu)
                        ->whereNull('deleted_at')
                        ->update([
                            'nm_produk' => $nmProduct,
                            'barcode' => $barcode,
                            'status' => $status
                        ]);
                    $jmlDataUpdate = $jmlDataUpdate + 1;
                }
            }
        }
        AccuratePage::where('id', '=', $id)
        ->update([
            'deleted_at' => now(),
            'rowCount' => $jmlDataInsert,
            'updateRowCount' => $jmlDataUpdate
        ]);
        return response()->json([
            'message' => 'Data berhasil diimpor sebanyak: ' . $jmlDataInsert . ', Data berhasil diupdate sebanyak: ' . $jmlDataUpdate
        ], 200);
    }

    private function createAccuratePages($totalBatches, $totalPages, $kdDb)
    {
        for ($batch = 1; $batch <= $totalBatches; $batch++) {
            $startPage = ($batch - 1) * 10 + 1;
            $endPage = min($startPage + 10 - 1, $totalPages);
    
            AccuratePage::create([
                'batch' => $batch,
                'startPage' => $startPage,
                'endPage' => $endPage,
                'totalBatches' => $totalBatches,
                'kd_database' => $kdDb
            ]);
        }
    }
}
