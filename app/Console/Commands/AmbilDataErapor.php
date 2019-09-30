<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Storage;
use CustomHelper;
class AmbilDataErapor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sinkronisasi:ambildataerapor {sekolah_id} {npsn} {tahun_ajaran_id} {semester_id} {last_sync} {server} {table} {next}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->arguments();
		$host_server = $arguments['server'];
		$response = Curl::to($host_server)->withData($arguments)->post();
		$response = json_decode($response);
		if($response){
			$table = $response->table;
			$next = $response->next;
			$counter = $response->counter;
			$count_result = $response->count_result;
			$response_server = $response->response;
			$data_sync = array(
				'sekolah_id'		=> $arguments['sekolah_id'],
				'npsn'				=> $arguments['npsn'],
				'tahun_ajaran_id'	=> $arguments['tahun_ajaran_id'],
				'semester_id'		=> $arguments['semester_id'],
				'last_sync'			=> $arguments['last_sync'],
				'server'			=> $arguments['server'],
				'table'				=> $table,
				'next'				=> $next,
			);
			if($response->response){
				$data = CustomHelper::prepare_receive($response->response);
				$data = json_decode($data);
			} else {
				$data = '';
			}
			$set_data = array_merge(array('count_result' => $count_result), array('counter' => $counter), array('data_sync' => $data_sync), array('data' => $data));
			$arr_content['query'] = 'ambil';
			$arr_content['percent'] = 5;
			$arr_content['message'] = 'Mengambil data '.$table;
			$arr_content['total'] = $count_result;
			$arr_content['response'] = $response_server;
			Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
			self::{$table}($set_data);
			//self::ref_sekolah($data);
		}
    }
	private function ref_sekolah($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 8;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("sekolah_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'jurusan_sp';
		$query['data_sync']['next'] = 'ref_guru';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
		//self::jurusan_sp($set_data);
	}
	private function jurusan_sp($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 10;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("jurusan_sp_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'ref_guru';
		$query['data_sync']['next'] = 'rombongan_belajar';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
		//self::ref_guru($set_data);
	}
	private function ref_guru($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 12;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("guru_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'rombongan_belajar';
		$query['data_sync']['next'] = 'ekstrakurikuler';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function rombongan_belajar($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 15;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("rombongan_belajar_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'ekstrakurikuler';
		$query['data_sync']['next'] = 'pembelajaran';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function ekstrakurikuler($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 20;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("ekstrakurikuler_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'pembelajaran';
		$query['data_sync']['next'] = 'ref_siswa';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function pembelajaran($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 24;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("pembelajaran_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'ref_siswa';
		$query['data_sync']['next'] = 'anggota_rombel';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function ref_siswa($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 28;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("siswa_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'anggota_rombel';
		$query['data_sync']['next'] = 'nilai_ekstrakurikuler';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function anggota_rombel($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 30;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("anggota_rombel_$query[counter].json", json_encode($query));
			//data
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'nilai_ekstrakurikuler';
		$query['data_sync']['next'] = 'teknik_penilaian';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function nilai_ekstrakurikuler($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 33;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("nilai_ekskul_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'teknik_penilaian';
		$query['data_sync']['next'] = 'bobot_keterampilan';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function teknik_penilaian($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 36;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("teknik_penilaian_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'bobot_keterampilan';
		$query['data_sync']['next'] = 'rencana_penilaian';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function bobot_keterampilan($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 40;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("bobot_keterampilan_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'rencana_penilaian';
		$query['data_sync']['next'] = 'kd_nilai';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function rencana_penilaian($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 44;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("rencana_penilaian_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'kd_nilai';
		$query['data_sync']['next'] = 'nilai';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function kd_nilai($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 48;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("kd_nilai_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'nilai';
		$query['data_sync']['next'] = 'nilai_akhir';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function nilai($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 52;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("nilai_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'nilai_akhir';
		$query['data_sync']['next'] = 'remedial';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function nilai_akhir($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 58;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("nilai_akhir_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'remedial';
		$query['data_sync']['next'] = 'absen';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function remedial($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 61;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("remedial_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'absen';
		$query['data_sync']['next'] = 'catatan_ppk';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function absen($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 65;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("absen_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'catatan_ppk';
		$query['data_sync']['next'] = 'catatan_wali';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function catatan_ppk($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 68;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("catatan_ppk_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'catatan_wali';
		$query['data_sync']['next'] = 'deskripsi_mata_pelajaran';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function catatan_wali($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 71;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("catatan_wali_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'deskripsi_mata_pelajaran';
		$query['data_sync']['next'] = 'deskripsi_sikap';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function deskripsi_mata_pelajaran($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 75;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("deskripsi_mapel_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'deskripsi_sikap';
		$query['data_sync']['next'] = 'prakerin';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function deskripsi_sikap($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 77;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("deskripsi_sikap_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'prakerin';
		$query['data_sync']['next'] = 'prestasi';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function prakerin($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 80;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("prakerin_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'prestasi';
		$query['data_sync']['next'] = 'ref_sikap';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function prestasi($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 83;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("prestasi_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'ref_sikap';
		$query['data_sync']['next'] = 'kenaikan_kelas';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function ref_sikap($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 87;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("ref_sikap_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'kenaikan_kelas';
		$query['data_sync']['next'] = 'indikator_karakter';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function kenaikan_kelas($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 90;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("kenaikan_kelas_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		$query['data_sync']['table'] = 'indikator_karakter';
		//$query['data_sync']['next'] = 'indikator_karakter';
		$host_server = $query['data_sync']['server'];
		$response = Curl::to($host_server)->withData($query['data_sync'])->post();
		$response = json_decode($response);
		$count_result = 0;
		if($response){
			$count_result = $response->count_result;
			if($response->response){
				$response = CustomHelper::prepare_receive($response->response);
				$response = json_decode($response);
			}
		} else {
			$response = '';
		}
		$set_data = array_merge(array('count_result' => $count_result), array('counter' => $query['counter']), array('data_sync' => $query['data_sync']), array('data' => $response));
		self::{$query['data_sync']['table']}($set_data);
	}
	private function indikator_karakter($query){
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = 95;
		$arr_content['message'] = 'Mengambil data '.$query['data_sync']['table'];
		$arr_content['total'] = $query['count_result'];
		$arr_content['response'] = '';
		$arr_content['data_sync'] = $query['data_sync'];
		//echo "indikator_karakter_$query[counter].json";
		if($query['data']){
			$arr_content['response'] = $query['data'];
			Storage::disk('public')->put("indikator_karakter_$query[counter].json", json_encode($query));
			//proses
		}
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
	}
}
