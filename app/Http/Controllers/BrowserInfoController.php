<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BrowserInfo;
use GuzzleHttp\Client;

class BrowserInfoController extends Controller
{
    public function store(Request $request)
    {
        // Ambil IP pengguna
        $ipAddress = $request->ip();

        // Dapatkan latitude dan longitude menggunakan API
        $client = new Client();
        $response = $client->request('GET', "http://ip-api.com/json/{$ipAddress}");
        $locationData = json_decode($response->getBody(), true);

        $latitude = $locationData['lat'] ?? null;
        $longitude = $locationData['lon'] ?? null;

        // Simpan informasi browser dan lokasi ke database
        $browserInfo = new BrowserInfo();
        $browserInfo->app_name = $request->appName;
        $browserInfo->app_version = $request->appVersion;
        $browserInfo->platform = $request->platform;
        $browserInfo->user_agent = $request->userAgent;
        $browserInfo->language = $request->language;
        $browserInfo->ip_address = $ipAddress;
        $browserInfo->latitude = $latitude;   // Tambahkan latitude
        $browserInfo->longitude = $longitude;  // Tambahkan longitude
        $browserInfo->save();

        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
    }
}
