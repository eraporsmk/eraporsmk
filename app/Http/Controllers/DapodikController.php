<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sekolah;
use CustomHelper;
use Artisan;
class DapodikController extends Controller
{
    public function cek_koneksi(Request $request){
        $all_data = $request->all();
        $sekolah = Sekolah::find($request->sekolah_id);
        return response()->json(['status' => 'success', 'sekolah' => $sekolah]);
    }
    public function kirim_data(Request $request){
        $json = CustomHelper::prepare_receive($request->data);
        $data = json_decode($json);
        Artisan::call('sinkronisasi:prosesdata',['response' => array('query' => $request->permintaan, 'data' => $data, 'count' => 0, 'page' => 0)]);
        return response()->json(['status' => 'success', 'data' => $request->all(), 'all_data' => $data]);
    }
}
