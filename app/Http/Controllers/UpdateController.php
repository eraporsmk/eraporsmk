<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use Artisan;
use App\Setting;
use App\Kompetensi_dasar;
use App\Kelompok;
use Illuminate\Support\Facades\Storage;
use App\Rombongan_belajar;
use App\Anggota_rombel;
use App\Ekstrakurikuler;
use App\Siswa;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
class UpdateController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
		$this->path = base_path('updates');
    }
    public function index(){
		if (!File::isDirectory($this->path)) {
            //MAKA FOLDER TERSEBUT AKAN DIBUAT
            File::makeDirectory($this->path);
        }
		$files =   File::allFiles($this->path);
		$folders = File::directories($this->path);
		foreach($folders as $folder){
			File::deleteDirectory($folder);
		}
		File::delete($files);
		return view('update');
    }
	public function update_versi(){
		//$user = auth()->user();
		/*
		$a = Rombongan_belajar::where(function($query){
			$query->where('jenis_rombel', 51);
			$query->where('sekolah_id', session('sekolah_id'));
			$query->where('semester_id', session('semester_id'));
		})->onlyTrashed()->get();
		$rombongan_belajar_id = [];
		foreach($a as $b){
			$rombongan_belajar_id[] = $b->rombongan_belajar_id;
			$c = Ekstrakurikuler::where(function($query) use ($b){
				$query->where('nama_ekskul', $b->nama);
				$query->where('sekolah_id', session('sekolah_id'));
				$query->where('semester_id', session('semester_id'));
			})->first();
			if($c){
				$anggota_not_deleted = Anggota_rombel::where('rombongan_belajar_id', $c->rombongan_belajar_id)->get();
				foreach($anggota_not_deleted as $not_deleted){
					$not_deleted->delete();
				}
				$c->rombongan_belajar_id = $b->rombongan_belajar_id;
				$c->save();
			}
			$anggota_deleted = Anggota_rombel::where('rombongan_belajar_id', $b->rombongan_belajar_id)->onlyTrashed()->get();
			foreach($anggota_deleted as $deleted){
				$siswa = Siswa::onlyTrashed()->find($deleted->peserta_didik_id);
				if($siswa){
					$siswa->restore();
				}
				$deleted->restore();
			}
			$b->restore();
		}
		if($rombongan_belajar_id){
			Rombongan_belajar::whereHas('anggota_rombel', function($query){
				$query->onlyTrashed();
			})->where(function($query) use ($rombongan_belajar_id){
				$query->whereNotIn('rombongan_belajar_id', $rombongan_belajar_id);
				$query->where('jenis_rombel', 51);
				$query->where('sekolah_id', session('sekolah_id'));
				$query->where('semester_id', session('semester_id'));
			})->delete();
		}
		*/
		Setting::where('key', 'app_version')->update(['value' => '5.0.8']);
		Setting::where('key', 'db_version')->update(['value' => '4.0.1']);
		//Artisan::call('migrate');
		Artisan::call('config:clear');
		Artisan::call('cache:clear');
		Artisan::call('view:clear');
		File::put(base_path().'/version.txt', '5.0.8');
		echo 'sukses';
	}
	public function periksa_pembaharuan(Request $request){
		try {
			$version = config('global.app_version');
			$client = new Client([
				'base_uri' => 'https://api.github.com',
				'timeout'  => 5.0,
				'verify' => false
			]);
			$action = '/repos/eraporsmk/eraporsmk/releases/latest';
			$curl = $client->get($action);
			$version = '5.0.7';
			if($curl->getStatusCode() == 200){
				$response = json_decode($curl->getBody());
				$versionAvailable = str_replace('v.', '', $response->tag_name);
				if (version_compare($version, $versionAvailable, '<')) {
					$output = [
						'server' => TRUE,
						'new_version' => $versionAvailable,
						'current_version' => $version,
						'zipball_url' => $response->zipball_url,
					];
				} else {
					$output = [
						'server' => TRUE,
						'new_version' => FALSE,
						'current_version' => $version,
						'zipball_url' => NULL,
					];
				}
			} else {
				$output = [
					'server' => FALSE,
					'new_version' => FALSE,
					'current_version' => $version,
					'zipball_url' => NULL,
				];
			}
		} catch (\Exception $e) {
			$output = [
				'server' => FALSE,
				'new_version' => FALSE,
				'current_version' => $version,
				'zipball_url' => NULL,
			];
		}
		return response()->json($output);
	}
	public function download_updateTest(Request $request){
		$output = [
			'next' => 'unzip',
		];
		return response()->json($output);
	}
	public function download_update(Request $request){
		try {
			$versionAvailable = $request->get('versionAvailable');
			$zipball_url = $request->get('zipball_url');
			$storageFolder = $this->path.'/'.$versionAvailable.'-'.now()->timestamp;
			$storageFilename = $storageFolder.'.zip';
			$downloadReleaseClient = new Client([
				'base_uri' => 'https://api.github.com',
				'verify' => false,
				'sink' => $storageFilename,
			]);
			$downloadRelease = $downloadReleaseClient->get($zipball_url, [
				'progress' => function(
					$downloadTotal,
					$downloadedBytes,
					$uploadTotal,
					$uploadedBytes
				) {
					//do something
					$record['downloadTotal'] = 17387450;
					$record['downloadedBytes'] = $downloadedBytes;
					$record['uploadTotal'] = $uploadTotal;
					$record['uploadedBytes'] = $uploadedBytes;
					Storage::disk('public')->put('download_upload.json', json_encode($record));
				},
			]);
			$output = [
				'next' => 'unzip',
				'storageFilename' => $storageFilename,
				'storageFolder' => $storageFolder,
				'versionAvailable' => $versionAvailable,
				'status' => NULL,
			];
		} catch (\Exception $e) {
			$output = [
				'next' => NULL,
				'storageFilename' => NULL,
				'storageFolder' => NULL,
				'versionAvailable' => NULL,
				'status' => 'Proses mengunduh berkas pembaharuan terhenti'
			];
		}
		return response()->json($output);
		//$this->unzipArchive($storageFilename, $storageFolder, false);
		//$this->createReleaseFolder($storageFolder, $versionAvailable);
	}
	public function persentase(){
		$json = Storage::disk('public')->get('download_upload.json');
		$json_output = json_decode($json);
		$downloadTotal = 0;
		$percent = 0;
		if($json_output){
			$percent = ($json_output->downloadTotal) ? round($json_output->downloadedBytes / $json_output->downloadTotal * 100,2) : 0;
		}
		$output = [
			'percent' => $percent,
			'downloadTotal' => $downloadTotal,
		];
		return response()->json($output);
	}
	public function unzipArchiveTest(){
		$output = [
			'status' => NULL,
			'next' => 'proses',
		];
		return response()->json($output);
	}
	public function createReleaseFolderTest(){
		$output = [
			'status' => 'Berhasil memperbarui aplikasi',
			'next' => NULL,
		];
		return response()->json($output);
	}
	public function unzipArchive(Request $request){
		$file = $request->get('storageFilename');
		$targetDir = $request->get('storageFolder');
		$versionAvailable = $request->get('versionAvailable');
		$output = [
			'releaseFolder' => $targetDir,
			'releaseName' => $versionAvailable,
			'status' => NULL,
			'next' => 'proses',
		];
        if (empty($file) || ! File::exists($file)) {
			//throw new \InvalidArgumentException("Archive [{$file}] cannot be found or is empty.");
			$output = [
				'releaseFolder' => NULL,
				'releaseName' => NULL,
				'status' => 'Berkas pembaharuan tidak tersedia.',
				'next' => FALSE,
			];
        }

        $zip = new \ZipArchive();
        $res = $zip->open($file);

        if (! $res) {
			//throw new Exception("Cannot open zip archive [{$file}].");
			$output = [
				'releaseFolder' => NULL,
				'releaseName' => NULL,
				'status' => 'Berkas pembaharuan tidak dapat di akses.',
				'next' => FALSE,
			];
        }

        if (empty($targetDir)) {
            $extracted = $zip->extractTo(File::dirname($file));
        } else {
            $extracted = $zip->extractTo($targetDir);
        }

        $zip->close();

        if ($extracted) {
            File::delete($file);
        }
		return response()->json($output);
	}
	public function createReleaseFolder(Request $request){
		$releaseFolder = $request->get('releaseFolder');
		$releaseName = $request->get('releaseName');
		$folders = File::directories($releaseFolder);
		Storage::disk('public')->delete('download_upload.json');
		if (count($folders) === 1) {
            // Only one sub-folder inside extracted directory
            File::moveDirectory($folders[0], $this->path.'/'.$releaseName);
            File::deleteDirectory($folders[0]);
            File::deleteDirectory($releaseFolder);
        } else {
            // Release (with all files and folders) is already inside, so we need to only rename the folder
            File::moveDirectory($releaseFolder, $this->path.'/'.$releaseName);
		}
		$this->update_versi();
    }
}
