<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use App\Exports\PembelajaranExport;
//use App\Guru;
//use App\Pembelajaran;
class ExcelController extends Controller
{
    public function pembelajaran(){
		//return (new PembelajaranExport)->download('pembelajaran.xlsx');
		//$data['guru'] = Guru::get()->toArray();
		//$data['pembelajaran'] = Pembelajaran::with('guru')->where('rombongan_belajar_id', 'c79de663-d25e-41a4-9577-a66ff0dcbc35')->get()->toArray();
		return Excel::download(new PembelajaranExport('c79de663-d25e-41a4-9577-a66ff0dcbc35'), 'pembelajaran.xlsx');
	}
}
