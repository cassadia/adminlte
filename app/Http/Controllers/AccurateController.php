<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Promise;
use GuzzleHttp\Pool;
use App\Models\AccurateToken;
use App\Models\AccurateSession;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Accurate;
use App\Models\AccuratePage;
use App\Models\AccurateLogNew;
use Illuminate\Support\Facades\DB;

class AccurateController extends Controller
{
    //
    public function refreshToken()
    {
        $cek = DB::table('accurate')->whereNull('deleted_at')->get();
        $message = [];

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

                $message[] = [
                    'message' => 'Data Token berhasil di refresh, Pada database: ' . $data->kd_database,
                    'data' => [
                        'access_token' => $accessToken,
                        'token_type' => $tokenType,
                        'refresh_token' => $refreshToken,
                        'kd_database' => $data->kd_database,
                        'expires_in' => $expiresIn
                    ]
                ];

                // return response()->json([
                //     'access_token' => $accessToken,
                //     'token_type' => $tokenType,
                //     'refresh_token' => $refreshToken,
                //     'kd_database' => $data->kd_database,
                //     'expires_in' => $expiresIn
                // ]);
            } catch (\Exception $e) {
                // Tangani kesalahan jika terjadi
                // return response()->json(['error' => $e->getMessage()], 500);
                return [
                    'message' => 'Error ' . $e->getMessage(),
                    'code' => 500
                ];
            }
        }
        // return response()->json($message);
        return $message;
    }

    public function getSession()
    {
        $client = new Client();
        $cek = DB::table('accurate')->whereNull('deleted_at')->get();
        $message = [];

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

                $message[] = [
                    "message" => $res->getBody()
                ];

            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
        return $message;
    }

    public function getListItem()
    {
        $client = new Client();

        $getDB = $this->getActiveDatabase();

        $test = [];
        $message = [];

        if (count($getDB)>0) {
            foreach ($getDB as $database) {
                $getAccess = $this->getDatabaseAccess($database->kd_database);
                $firstAccess = $getAccess->first();
                if ($firstAccess) {
                    $headers = $this->buildHeaders($firstAccess);
                    $host = $getAccess->first()->host;
                    $request = new Request('GET', $host . '/accurate/api/item/list.do', $headers);
                    $kdDb = $database->kd_database;

                    try {
                        $res = $client->sendAsync($request)->wait();
                        $result = json_decode((string)$res->getBody(), true);
                        $totalPages = $result['sp']['pageCount'];
                        $batchSize = 10;
                        $totalBatches = ceil($totalPages / $batchSize);
                        $cekPages = $this->cekPages($kdDb);

                        if ($cekPages) {
                            $insert = $this->insertProduct(
                                $getAccess->first(),
                                $cekPages->id,
                                $cekPages->startPage,
                                $cekPages->endPage,
                                $kdDb
                            );
                            $message[] = [
                                'message' => $insert
                            ];
                        } else {
                            $this->createAccuratePages($totalBatches, $totalPages, $kdDb);
                            $cekPages = $this->cekPages($kdDb);

                            if ($cekPages) {
                                $insert = $this->insertProduct(
                                    $getAccess->first(),
                                    $cekPages->id,
                                    $cekPages->startPage,
                                    $cekPages->endPage,
                                    $kdDb
                                );
                                $message[] = [
                                    'message' => $insert
                                ];
                            }
                        }
                    } catch (\Exception $e) {
                        return response()->json(['error' => $e->getMessage()], 500);
                    }
                } else {
                    $messages[] = ['message' => 'Tidak ada akses untuk database: '];
                }
            }
        }
        return $message;
    }

    public function getListItemNew()
    {
        $client = new Client();
        $getDB = $this->getActiveDatabase();
        $messages = [];
        $insert = [];

        if (count($getDB)>0) {
            foreach ($getDB as $database) {
                $getAccess = $this->getDatabaseAccess($database->kd_database);
                $firstAccess = $getAccess->first();
                if ($firstAccess) {
                    $headers = $this->buildHeaders($firstAccess);
                    $host = $firstAccess->host;
                    $kdDb = $database->kd_database;

                    try {
                        $request = $client->getAsync($host . '/accurate/api/item/list.do', ['headers' => $headers]);
                        $response = $request->wait();
                        $result = json_decode((string)$response->getBody(), true);

                        $totalPages = $result['sp']['pageCount'];
                        $pageSize = $result['sp']['pageSize'];
                        $promises = [];

                        $startTime = microtime(true);
                        for ($i = 1; $i <= $totalPages; $i++) {
                            $promises[] = $client->getAsync(
                                $host . '/accurate/api/item/list.do?fields=id,name,itemType,itemTypeName,unitPrice,no,charField1,availableToSell,charField4,charField5,upcNo&sp.page=' . $i . '&sp.pageSize=' . $pageSize,
                                ['headers' => $headers]
                            );
                        }

                        $responses = Promise\Utils::settle($promises)->wait();

                        foreach ($responses as $response) {
                            if ($response['state'] === 'fulfilled') {
                                $result = json_decode((string)$response['value']->getBody(), true);
                                $insert = $this->insertProductNew(
                                    $getAccess->first(),
                                    $result['d'],
                                    $kdDb,
                                    $startTime
                                );
                            }
                        }

                        $messages[] = ['message' => 'Proses berhasil untuk database: ' . $kdDb];
                        // $messages[] = ['message' => $insert];
                    } catch (\Throwable $e) {
                        $messages[] = ['error' => $e->getMessage()];
                    }
                } else {
                    $messages[] = ['message' => 'Tidak ada akses untuk database: '];
                }
            }
        }

        return response()->json(['messages' => $messages]);
    }

    public function postTransaction()
    {
        $cekTrans = DB::table('transaction')
                        ->select('kd_database')
                        ->where('is_send_to_accu', 0)
                        ->distinct()
                        ->get();

        $message = [];

        if (count($cekTrans)>0) {
            foreach ($cekTrans as $dbTrans) {
                $getAccess = $this->getDatabaseAccess($dbTrans->kd_database);

                try {
                    $client = new Client();

                    foreach ($getAccess as $access) {
                        $headers = $this->buildHeaders($access);

                        $dataTrans = DB::table('transaction')
                                        ->where('is_send_to_accu', 0)
                                        ->where('kd_database', $dbTrans->kd_database)
                                        ->get();
                        foreach ($dataTrans as $item => $value) {
                            $data["detailItem[".$item."].unitPrice"] = $value->harga_jual;
                            $data["detailItem[".$item."].quantity"] = $value->qty;
                            $data["detailItem[".$item."].itemNo"] = $value->kd_produk;
                            $data["data[".$item."].transDate"] = date("d/m/Y", strtotime($value->created_at));
                        }

                        $getListCust = $this->getListCustomer($access->host, $headers);
                        foreach ($getListCust['d'] as $cust) {
                            $data["customerNo"] = $cust['customerNo'];
                        }

                        $getAutoNumber = $this->getAutoNumber($access->host, $headers);
                        foreach ($getAutoNumber['d'] as $number) {
                            $data["typeAutoNumber"] = urlencode($number['id']);
                            $data["branchId"] = urlencode("50");
                        }

                        $data_query = http_build_query($data);
                        $url = $access->host . '/accurate/api/sales-invoice/save.do?' . $data_query;
                        $request = new Request('POST', urldecode($url), $headers);
                        $res = $client->sendAsync($request)->wait();
                        $res_body = json_decode($res->getBody(), true);

                        if ($res_body['d'][0]) {
                            $pesan = $res_body['d'][0];
                            preg_match('/"([^"]+)"/', $pesan, $matches);
                            $nomorFaktur = $matches[1];
                            DB::table('transaction')
                                ->where('is_send_to_accu', 0)
                                ->where('kd_database', $dbTrans->kd_database)
                                ->update([
                                    'is_send_to_accu' => 1,
                                    'no_accu_trans' => $nomorFaktur
                                ]);
                        }

                        $message[] = [
                            'message' => $res_body['d'][0]
                        ];

                        // if ($res_body['s']) {
                        //     $pesan = $res_body['d'][0];
                        //     preg_match('/"([^"]+)"/', $pesan, $matches);
                        //     $nomorFaktur = $matches[1];

                        //     foreach ($dataTrans as $item) {
                        //         $data = Transaction::find($item->id)->where('kd_database', $dbTrans->kd_database);
                        //         $data->update([
                        //             'is_send_to_accu' => 1,
                        //             'no_accu_trans' => $nomorFaktur
                        //         ]);
                        //     }
                        //     // return response()->json(['message' => $res_body['d']], 200);
                        //     $message[] = [
                        //         'message' => $res_body['d'][0]
                        //     ];
                        // } else {
                        //     echo "Permintaan gagal: Data tidak disimpan.";
                        // }
                    }
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
            return $message;
        } else {
            // return response()->json(['message' => 'Belum ada transaksi yang perlu diproses!'], 200);
            return ['message' => 'Belum ada transaksi yang perlu diproses!'];
        }
    }

    public function updatePriceAndStock()
    {
        $client = new Client();

        $getDB = $this->getActiveDatabase();
        $message = [];

        if (count($getDB)>0) {
            foreach ($getDB as $database) {
                $getAccess = $this->getDatabaseAccess($database->kd_database);
                if ($getAccess) {
                    $headers = $this->buildHeaders($getAccess->first());
                    $host = $getAccess->first()->host;
                    $request = new Request('GET', $host . '/accurate/api/item/list.do', $headers);
                    $kdDb = $database->kd_database;

                    try {
                        $jmlDataUpdate = 1;
                        $res = $client->sendAsync($request)->wait();
                        $result = json_decode((string)$res->getBody(), true);

                        for ($i=1; $i<=$result['sp']['pageCount']; $i++) {
                        // for ($i=1; $i<=3; $i++) {
                            $request_new = new Request('GET'
                                , $host . '/accurate/api/item/list.do?fields=id,name,itemType,itemTypeName,unitPrice,no,charField1,availableToSell,charField4,charField5&sp.page=' . $i
                                , $headers
                            );
                            $res_new = $client->sendAsync($request_new)->wait();
                            $result_new = json_decode((string)$res_new->getBody(), true);

                            foreach ($result_new['d'] as $data) {
                                $kdProduct = $data['no'];
                                $kdProductAccu = $data['id'];
                                $nmProduct = $data['name'];
                                $hargaJual = $data['unitPrice'];
                                $stockAvail = intval($data['availableToSell']);
                                $status = $data['charField5']=='N' ? "Tidak Aktif" : "Aktif";
                                $database = $kdDb;

                                $productExist = Product::where('kd_produk', $kdProduct)->where('database', $database)->count();

                                if ($productExist > 0) {
                                    Product::where('kd_produk', $kdProduct)
                                        ->where('database', $database)
                                        ->update([
                                            'harga_jual' => $hargaJual,
                                            'qty_available' => $stockAvail,
                                            'status' => $status
                                        ]);
                                    $jmlDataUpdate++;
                                }
                            }
                        }
                        $message[] = [
                            'message' => response()->json([
                                'message' => 'Data berhasil diperbaharui sebanyak: ' . $jmlDataUpdate . ', Pada database: ' . $kdDb
                            ], 200)
                        ];
                        // return response()->json([
                        //     'message' => 'Data berhasil diperbaharui sebanyak: ' . $jmlDataUpdate . ', Pada database: ' . $kdDb
                        // ], 200);
                    } catch (\Exception $e) {
                        return response()->json(['error' => $e->getMessage()], 500);
                    }
                } else {
                    echo 'else out';
                }
            }
        }
        return $message;
    }

    public function updatePriceAndStockNew()
    {
        $client = new Client();
        $getDB = $this->getActiveDatabase();
        $message = [];

        if (count($getDB) > 0) {
            foreach ($getDB as $database) {
                $getAccess = $this->getDatabaseAccess($database->kd_database);
                $firstAccess = $getAccess->first();
                if ($firstAccess) {
                    $headers = $this->buildHeaders($firstAccess);
                    $host = $firstAccess->host;
                    $kdDb = $database->kd_database;

                    try {
                        $startTime = microtime(true);
                        $initialRequest = new Request('GET', $host . '/accurate/api/item/list.do', $headers);
                        $res = $client->send($initialRequest);
                        $result = json_decode((string)$res->getBody(), true);

                        // Buat batch request untuk semua halaman
                        $promises = [];
                        for ($i = 1; $i <= 1; $i++) {
                        // for ($i = 1; $i <= $result['sp']['pageCount']; $i++) {
                            $promises[] = $client->getAsync(
                                $host . '/accurate/api/item/list.do?fields=id,name,itemType,itemTypeName,unitPrice,no,charField1,availableToSell,charField4,charField5&sp.page=' . $i,
                                ['headers' => $headers]
                            );
                        }

                        // Kirim semua request secara bersamaan
                        $responses = Promise\Utils::settle($promises)->wait();

                        $productsToUpdate = [];
                        foreach ($responses as $response) {
                            if ($response['state'] === 'fulfilled') {
                                $result_new = json_decode((string)$response['value']->getBody(), true);
                                foreach ($result_new['d'] as $data) {
                                    $productsToUpdate[] = [
                                        'kd_produk' => $data['no'],
                                        'nm_produk' => $data['name'],
                                        'database' => $kdDb,
                                        'harga_jual' => $data['unitPrice'],
                                        'qty_available' => intval($data['availableToSell']),
                                        'status' => $data['charField5'] == 'N' ? "Tidak Aktif" : "Aktif",
                                        'updated_at' => now()
                                    ];
                                }
                            }
                        }

                        // Bulk Update Database
                        foreach (array_chunk($productsToUpdate, 500) as $chunk) {
                            Product::upsert($chunk, ['kd_produk', 'database'], ['harga_jual', 'qty_available', 'status', 'updated_at']);
                        }

                        $endTime = microtime(true);

                        $duration = $endTime - $startTime;

                        AccurateLogNew::create([
                            'kd_database' => $kdDb,
                            'scheduler' => 'updatePriceAndStockNew',
                            'rowCount' => 0,
                            'updateRowCount' => count($productsToUpdate),
                            'startTime' => date('Y-m-d H:i:s', $startTime),
                            'endTime' => date('Y-m-d H:i:s', $endTime),
                            'duration' => number_format($duration, 2) . ' seconds'
                        ]);

                        $message[] = [
                            'message' => response()->json([
                                'message' => 'Data berhasil diperbaharui sebanyak: ' . count($productsToUpdate) . ', Pada database: ' . $kdDb
                            ], 200)
                        ];
                    } catch (\Exception $e) {
                        return response()->json(['error' => $e->getMessage()], 500);
                    }
                } else {
                    $messages[] = ['message' => 'Tidak ada akses untuk database: '];
                }
            }
        }
        // dd($message);
        return $message;
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
            ->get();
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

                $productExist = Product::where('kd_produk', $kdProduct)
                    ->where('kd_produk_accu', $kdProductAccu)
                    ->where('database', $kdDb)
                    ->count();

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
                        ->where('database', $kdDb)
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
            'message' => 'Data berhasil diimpor sebanyak: ' . $jmlDataInsert . ', Data berhasil diupdate sebanyak: ' . $jmlDataUpdate . ', Pada database: ' . $kdDb
        ], 200);
    }

    private function insertProductNew($Access, $result, $kdDb, $startTime)
    {
        $client = new Client();
        $newProducts = [];
        $updateProducts = [];

        foreach($result as $data) {
            $kdProduct = $data['no'];
            $kdProductAccu = $data['id'];
            $nmProduct = $data['name'];
            $hargaJual = $data['unitPrice'];
            $stockAvail = $data['availableToSell'];
            $status = $data['charField5'] === 'N' ? "Tidak Aktif" : "Aktif";
            $barcode = $data['upcNo'];

            $productExist = Product::where('kd_produk', $kdProduct)
                ->where('kd_produk_accu', $kdProductAccu)
                ->where('database', $kdDb)
                ->first();

            if (!$productExist) {
                $newProducts[] = [
                    'kd_produk' => $kdProduct,
                    'kd_produk_accu' => $kdProductAccu,
                    'nm_produk' => $nmProduct,
                    'harga_jual' => $hargaJual,
                    'qty_available' => $stockAvail,
                    'database' => $kdDb,
                    'status' => $status,
                    'barcode' => $barcode,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            } else {
                $updateProducts[] = [
                    'kd_produk' => $kdProduct,
                    'kd_produk_accu' => $kdProductAccu,
                    'nm_produk' => $nmProduct,
                    'harga_jual' => $hargaJual,
                    'qty_available' => $stockAvail,
                    'barcode' => $barcode,
                    'status' => $status,
                    'updated_at' => now()
                ];
            }
        }

        // Insert batch data baru
        if (!empty($newProducts)) {
            Product::insert($newProducts);
        }

        // Update batch data
        foreach ($updateProducts as $product) {
            Product::where('kd_produk', $product['kd_produk'])
                ->where('kd_produk_accu', $product['kd_produk_accu'])
                ->where('database', $kdDb)
                ->update([
                    'nm_produk' => $product['nm_produk'],
                    'barcode' => $product['barcode'],
                    'status' => $product['status'],
                    'qty_available' => $product['qty_available'],
                    'harga_jual' => $product['harga_jual'],
                    'updated_at' => $product['updated_at']
                ]);
        }

        $endTime = microtime(true);

        $duration = $endTime - $startTime;

        AccurateLogNew::create([
            'kd_database' => $kdDb,
            'scheduler' => 'insertProductNew',
            'rowCount' => count($newProducts),
            'updateRowCount' => count($updateProducts),
            'startTime' => date('Y-m-d H:i:s', $startTime),
            'endTime' => date('Y-m-d H:i:s', $endTime),
            'duration' => number_format($duration, 2) . ' seconds'
        ]);

        return response()->json([
            'message' => 'Data berhasil diimpor sebanyak: ' . count($newProducts) .
                ', Data berhasil diupdate sebanyak: ' . count($updateProducts) .
                ', Pada database: ' . $kdDb
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

    private function getListCustomer($host, $headers)
    {
        try {
            $client = new Client();
            $request = new Request('GET', $host . '/accurate/api/customer/list.do?fields=id,name,no,category,email,customerNo&filter.keywords.val=WEB.&filter.keywords.op=CONTAIN', $headers);
            $res = $client->sendAsync($request)->wait();
            return json_decode($res->getBody(), true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    private function getAutoNumber($host, $headers)
    {
        try {
            $client = new Client();
            $request = new Request('GET', $host . '/accurate/api/auto-number/list.do?filter.keywords.val=penjualan website&filter.keywords.op=CONTAIN', $headers);
            $res = $client->sendAsync($request)->wait();
            return json_decode($res->getBody(), true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
