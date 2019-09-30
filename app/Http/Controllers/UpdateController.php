<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use App\Setting;
use Codedge\Updater\UpdaterFacade as Updater;
use Alert;
class UpdateController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(){
		//Alert::message('Message', 'Optional Title');
		$current_version = Setting::where('key', '=', 'app_version')->first();
		$updater = Updater::isNewVersionAvailable('5.0.0');
		$params = array(
			'updater' 	=> $updater,
		);
		return view('update')->with($params);
    }
	private function new_version($current_version){
		$host_server = 'http://103.40.55.226/updater/index.php';
		$host_server = 'http://localhost/updater/index.php';
		$response = Curl::to($host_server)->withData(array('versi' => $current_version))->get();
		return json_decode($response);
	}
	public function proses_update(){
		$a = Updater::fetch();
		//$a = Updater::update();
		$response['data'] = $a;
		if($a){
			$response['text'] = '<p class="text-green"><strong>[BERHASIL]</strong></p>';
		} else {
			$response['text'] = '<p class="text-red"><strong>[GAGAL]</strong></p>';
		}
		echo json_encode($response);
	}
	public function extract_to(){
		$a = true;
		$response['data'] = $a;
		if($a){
			$response['text'] = '<p class="text-green"><strong>[BERHASIL]</strong></p>';
		} else {
			$response['text'] = '<p class="text-red"><strong>[GAGAL]</strong></p>';
		}
		echo json_encode($response);
	}
	public function update_versi(){
		$a = Setting::where('key', '=', 'app_version')->update(['value' => '5.0.1']);
		$response['data'] = $a;
		if($a){
			//Setting::where('key', '=', 'db_version')->update(['value' => '4.0.1']);
			$response['text'] = '<p class="text-green"><strong>[BERHASIL]</strong></p>';
		} else {
			$response['text'] = '<p class="text-red"><strong>[GAGAL]</strong></p>';
		}
		echo json_encode($response);
	}
}
