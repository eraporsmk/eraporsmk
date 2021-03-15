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
use App\Semester;
use App\Tahun_ajaran;
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
		Artisan::call('erapor:update');
		echo 'sukses';
		exit;
		Setting::where('key', 'app_version')->update(['value' => '5.1.2']);
		Setting::where('key', 'db_version')->update(['value' => '4.0.4']);
		$new_tahun = [
			[
				'tahun_ajaran_id' 	=> 2020,
				'nama'				=> "2020/2021",
				'periode_aktif'		=> 1,
				'tanggal_mulai'		=> "2020-07-25",
				'tanggal_selesai'	=> "2021-06-01"
			]
		];
		$new_semester = [
			[
				'semester_id' 		=> "20201",
				'tahun_ajaran_id'	=> 2020,
				'nama' 				=> "2020/2021 Ganjil",
				'semester' 			=> 1,
				'periode_aktif'		=> 0,
				'tanggal_mulai'		=> "2020-07-01",
				'tanggal_selesai'	=> "2020-12-31"
			],
			[
				'semester_id' 		=> "20202",
				'tahun_ajaran_id'	=> 2020,
				'nama' 				=> "2020/2021 Genap",
				'semester' 			=> 2,
				'periode_aktif'		=> 1,
				'tanggal_mulai'		=> "2021-01-01",
				'tanggal_selesai'	=> "2021-07-15"
			]
		];
		foreach($new_tahun as $tahun){
			Tahun_ajaran::updateOrCreate(
				$tahun,
				[
					'created_at' 		=> date('Y-m-d H:i:s'),
					'updated_at' 		=> date('Y-m-d H:i:s'),
					'last_sync'			=> date('Y-m-d H:i:s'),
				]
			);
		}
		foreach($new_semester as $semester){
			Semester::updateOrCreate(
				$semester,
				[
					'created_at' 		=> date('Y-m-d H:i:s'),
					'updated_at' 		=> date('Y-m-d H:i:s'),
					'last_sync'			=> date('Y-m-d H:i:s'),
				]
			);
		}
		Semester::where('semester_id', '!=', '20202')->update(['periode_aktif' => 0]);
		Semester::where('semester_id', '20202')->update(['periode_aktif' => 1]);
		Artisan::call('cache:clear');
		Artisan::call('view:clear');
		Artisan::call('config:cache');
		$path = base_path('bootstrap/cache');
		$files = File::files($path);
		$config = FALSE;
		$config_ = FALSE;
		foreach($files as $file){
			if($file->getRelativePathname() == 'config-.php'){
				$config_ = $file->getPathname();
			}
			if($file->getRelativePathname() == 'config.php'){
				$config = $file->getPathname();
			}
		}
		if($config_ && $config){
			File::move($config_,$config);
		} elseif($config_ && !$config){
			File::move($config_,$files.'/config.php');
		}
		Artisan::call('migrate');
		system('composer update');
		File::put(base_path().'/version.txt', '5.0.9');
		echo 'sukses';
	}
	public function periksa_pembaharuan(Request $request){
		//edit lagi
		try {
			$version = config('global.app_version');
			$client = new Client([
				'base_uri' => 'https://api.github.com',
				'timeout'  => 5.0,
				'verify' => false
			]);
			$action = '/repos/eraporsmk/eraporsmk/releases/latest';
			$curl = $client->get($action);
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
		try{
			$file = $request->get('storageFilename');
			$targetDir = $request->get('storageFolder');
			$versionAvailable = $request->get('versionAvailable');
			$zipHandle = zip_open($file);
			$archive = basename($file);
			$update = 0;
			while ($zip_item = zip_read($zipHandle) ){
				$filename = zip_entry_name($zip_item);
				$newFileName = explode('/', $filename);
				if(count($newFileName) > 1){
					$collection = collect($newFileName);
					$collection->forget(0);
					$filename = implode('/',$collection->all());
				}
				$dirname = dirname($filename);
				// Exclude these cases (1/2)
				if(	substr($filename,-1,1) == '/' || dirname($filename) === $archive || substr($dirname,0,2) === '__') continue;

				//Exclude root folder (if exist)
				if( substr($dirname,0, strlen($archive)) === $archive )
					$dirname = substr($dirname, (strlen($dirname)-strlen($archive)-1)*(-1));

				// Exclude these cases (2/2)
				// todo:check linux and windows test
				//if($dirname === '.' ) continue;
				$filename = $dirname.'/'.basename($filename); //set new purify path for current file
				//$filename = str_replace($dirname.'/','',$filename);
				//echo $dirname;
				//dd($filename);
				if ( !is_dir(base_path().'/'.$dirname) ){ //Make NEW directory (if exist also in current version continue...)
					File::makeDirectory(base_path().'/'.$dirname, $mode = 0755, true, true);
				}

				if ( !is_dir(base_path().'/'.$filename) ){ //Overwrite a file with its last version
					$contents = zip_entry_read($zip_item, zip_entry_filesize($zip_item));
					$contents = str_replace("\r\n", "\n", $contents);
					if ( strpos($filename, 'database.php') === false ) {
						File::put(base_path().'/'.$filename, $contents);
						unset($contents);
					}
				}
				$update++;
			}
			zip_close($zipHandle);
			if($update){
				$output = [
					'status' => NULL,
					'next' => 'proses',
				];
			} else {
				$output = [
					'status' => 'Berkas pembaharuan tidak tersedia',
					'next' => FALSE,
				];
			}
		} catch (\Exception $e) {
			$output = [
				'next' => FALSE,
				'status' => 'Berkas pembaharuan tidak dapat di akses'
			];
		}
		return response()->json($output);
	}
	public function unzipArchiveOld(Request $request){
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
			//File::moveDirectory($folders[0], $this->path.'/'.$releaseName);
			File::moveDirectory($folders[0], base_path());
            File::deleteDirectory($folders[0]);
            File::deleteDirectory($releaseFolder);
        } else {
            // Release (with all files and folders) is already inside, so we need to only rename the folder
			//File::moveDirectory($releaseFolder, $this->path.'/'.$releaseName);
			File::moveDirectory($releaseFolder, base_path());
		}
		$this->update_versi();
    }
}
