<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sekolah;
class DapodikController extends Controller
{
    public function cek_koneksi(Request $request){
        $all_data = $request->all();
        $sekolah = Sekolah::find($request->sekolah_id);
        return response()->json(['status' => 'success', 'sekolah' => $sekolah]);
    }
}
