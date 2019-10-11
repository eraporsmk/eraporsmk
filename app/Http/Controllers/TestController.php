<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CustomHelper;
use ServerProvider;
use Ixudra\Curl\Facades\Curl;
class TestController extends Controller
{
    public function index(){
		$semester = CustomHelper::get_ta();
		$data_sync = array(
			'username_dapo'		=> 'smkn1cermegresik@yahoo.co.id',
			'password_dapo'		=> '56b1b39ee632499f225821d476ece77b',
			'npsn'				=> '20500423',
			'tahun_ajaran_id'	=> $semester->tahun_ajaran_id,
			'semester_id'		=> $semester->semester_id,
		);
		$curl = Curl::to(ServerProvider::url_register())
		->returnResponseObject()
		->withData($data_sync)
		->post();
		//dd($curl);
		$response = json_decode($curl->content);
		dd($response);
	}
}
