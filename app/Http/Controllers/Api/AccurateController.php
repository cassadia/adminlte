<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Illuminate\Http\Request;
use GuzzleHttp\Promise;
use GuzzleHttp\Pool;
use App\Http\Controllers\Controller;
use App\Services\UserRoleService;
use Illuminate\Support\Facades\DB;

class AccurateController extends Controller
{
    protected $userRoleService;

    public function __construct(UserRoleService $userRoleService)
    {
        $this->userRoleService = $userRoleService;
    }

    public function postTransaction(Request $request)
    {
        DB::beginTransaction();

        try {
            $cekTrans = DB::table('transaction')
                ->select('kd_database')
                ->where('is_send_to_accu', 0)
                ->where('kd_database', $request->kdDB)
                ->groupBy('kd_database')
                ->get();

            $message = [];

            if (count($cekTrans)>0) {
                $getAccess = $this->getDatabaseAccess($cekTrans[0]->kd_database);

                $client = new Client();
                foreach ($getAccess as $access) {
                    $headers = $this->buildHeaders($access);
                    $dataTrans = DB::table('transaction')
                                    ->where('is_send_to_accu', 0)
                                    ->where('kd_database', $access->kd_database)
                                    ->whereNull('deleted_at')
                                    ->get();

                    $data = [];
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
                    $request = new GuzzleRequest(
                        'POST',
                        urldecode($url),
                        $headers,
                    );
                    $res = $client->sendAsync($request)->wait();
                    $res_body = json_decode($res->getBody(), true);

                    if ($res_body['d'][0]) {
                        $pesan = $res_body['d'][0];
                        preg_match('/"([^"]+)"/', $pesan, $matches);
                        $nomorFaktur = $matches[1];
                        DB::table('transaction')
                            ->where('is_send_to_accu', 0)
                            ->where('kd_database', $access->kd_database)
                            ->whereNull('deleted_at')
                            ->update([
                                'is_send_to_accu' => 1,
                                'no_accu_trans' => $nomorFaktur
                            ]);
                    }

                    $message[] = [
                        'message' => $res_body['d'][0]
                    ];
                }

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'data' => $message[0],
                ], 200)->header('Cache-Control', 'no-store');

            } else {
                return response()->json([
                    'status' => 'success',
                    'data' => 'Data kosong',
                ], 200)->header('Cache-Control', 'no-store');
            }

        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'status' => 'failed',
                'data' => $th->getMessage()
            ], 500)->header('Cache-Control', 'no-store');
        }
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

    private function getListCustomer($host, $headers)
    {
        try {
            $client = new Client();
            $request = new GuzzleRequest( // Use GuzzleRequest here
                'GET',
                $host . '/accurate/api/customer/list.do?fields=id,name,no,category,email,customerNo&filter.keywords.val=WEB.&filter.keywords.op=CONTAIN',
                $headers
            );

            $res = $client->sendAsync($request)->wait();
            $data = json_decode($res->getBody(), true);

            return $data; // No need to decode twice
        } catch (\Throwable $th) {
            // Check if a response object exists before trying to access it
            if ($th instanceof \GuzzleHttp\Exception\RequestException && $th->hasResponse()) {
                $response = $th->getResponse();
                $statusCode = $response ? $response->getStatusCode() : null;
                $body = $response ? (string)$response->getBody() : null;

                if ($statusCode === 401) {
                    throw new \Exception("Unauthorized: Token expired, perlu refresh token!");
                }
            }

            // Re-throw the original exception after handling potential 401 errors
            throw $th;
        }
    }

    private function getAutoNumber($host, $headers)
    {
        try {
            $client = new Client();
            // $request = new Request('GET', $host . '/accurate/api/auto-number/list.do?filter.keywords.val=penjualan website&filter.keywords.op=CONTAIN', $headers);
            $request = new GuzzleRequest(
                'GET',
                $host . '/accurate/api/auto-number/list.do?filter.keywords.val=penjualan website&filter.keywords.op=CONTAIN',
                $headers
            );
            $res = $client->sendAsync($request)->wait();
            return json_decode($res->getBody(), true);
        } catch (\Throwable $th) {
            if ($th instanceof \GuzzleHttp\Exception\RequestException && $th->hasResponse()) {
                $response = $th->getResponse();
                $statusCode = $response ? $response->getStatusCode() : null;
                $body = $response ? (string)$response->getBody() : null;

                if ($statusCode === 401) {
                    throw new \Exception("Unauthorized: Token expired, perlu refresh token!");
                }
            }

            throw $th;
        }
    }
}
