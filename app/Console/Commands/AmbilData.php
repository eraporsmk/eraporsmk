<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ixudra\Curl\Facades\Curl;
use App\Providers\HelperServiceProvider;
use Symfony\Component\Console\Output\BufferedOutput;
use App\Kompetensi_dasar;
use Illuminate\Support\Facades\Storage;
class AmbilData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sinkronisasi:ambildata {username_dapo} {password_dapo} {tahun_ajaran_id} {semester_id} {sekolah_id} {npsn} {server} {aksi} {last_sync} {updated_at} {satuan}';
	//protected $signature = 'sinkronisasi:ambildata {dataName}';

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
		$host_server = $arguments['server'];//.$arguments['aksi'];
		$satuan = $arguments['satuan'];
		$curl = Curl::to($host_server)
		->withHeader('x-api-key:'.$arguments['sekolah_id'])
		->withOption('USERPWD', "admin:1234")
		->returnResponseObject()
        ->withData($arguments)
        ->post();
		$response = json_decode($curl->content);
		if($curl->status == 200){
			$function_name = str_replace('-', '_', $arguments['aksi']);
			self::{$function_name}($response->dapodik, $satuan);
		} elseif($curl->status == 403 || $curl->status == 404  || $curl->status == 401) {
			$result['status'] = 0;
			$result['message'] = $response->error;
			$result['icon'] = 'error';
			echo json_encode($result);
		} else {
			$result['status'] = 0;
			$result['message'] = $host_server;//'Server tidak merespon';
			$result['icon'] = 'error';
			echo json_encode($result);
		}
    }
	private function sekolah($response, $satuan){
		$array = json_decode(json_encode($response), true);
		$this->call('sinkronisasi:prosesdata',['response' => array('query' => 'sekolah', 'data' => $array)]);
		$result['status'] = 1;
		$result['data'] = 'sekolah';
		$result['aksi'] = 'ptk';
		$result['progress'] = 10;
		$result['server']	= 'erapor_server';
		$result['satuan']	= $satuan;
		$result['message'] = 'Sinkron sekolah berhasil diproses';
		if($satuan){
			$result['status'] = 0;
		}
		echo json_encode($result);
	}
	private function ptk($response, $satuan){
		$array = json_decode(json_encode($response), true);
		$this->call('sinkronisasi:prosesdata',['response' => array('query' => 'guru', 'data' => $array)]);
		$result['status'] = 1;
		$result['data'] = 'rombongan_belajar';
		$result['aksi'] = 'rombongan-belajar';
		$result['progress'] = 20;
		$result['server']	= 'erapor_server';
		$result['satuan']	= $satuan;
		$result['message'] = 'Sinkron PTK berhasil diproses';
		if($satuan){
			$result['status'] = 0;
		}
		echo json_encode($result);
	}
	private function rombongan_belajar($response, $satuan){
		/*$this->output = new BufferedOutput;
		if(isset($response->post_login)){
			$post_login = $response->post_login;
			$array = json_decode(json_encode($response), true);
			if($post_login){
				$this->call('sinkronisasi:prosesdata',['response' => array('query' => 'rombongan_belajar', 'data' => $array)], $this->output);
			}
		} else {
			$post_login = 0;
		}
		$result['status'] = $post_login;*/
		$array = json_decode(json_encode($response), true);
		$this->call('sinkronisasi:prosesdata',['response' => array('query' => 'rombongan_belajar', 'data' => $array)]);
		$result['status'] = 1;
		$result['progress'] = 30;
		$result['data'] = 'siswa_aktif';
		$result['aksi'] = 'peserta-didik-aktif';
		$result['server']	= 'erapor_server';
		$result['satuan']	= $satuan;
		$result['message'] = 'Sinkron Rombongan Belajar berhasil diproses';
		if($satuan){
			$result['status'] = 0;
		}
		echo json_encode($result);
	}
	private function peserta_didik_aktif($response, $satuan){
		$array = json_decode(json_encode($response), true);
		$this->call('sinkronisasi:prosesdata',['response' => array('query' => 'siswa_aktif', 'data' => $array)]);
		$result['status'] = 1;
		$result['progress'] = 40;
		$result['data'] = 'siswa_keluar';
		$result['aksi'] = 'peserta-didik-keluar';
		$result['server']	= 'erapor_server';
		$result['satuan']	= $satuan;
		$result['message'] = 'Sinkron Peserta Didik Aktif berhasil diproses';
		if($satuan){
			$result['status'] = 0;
		}
		echo json_encode($result);
	}
	private function peserta_didik_keluar($response, $satuan){
		$array = json_decode(json_encode($response), true);
		$this->call('sinkronisasi:prosesdata',['response' => array('query' => 'siswa_keluar', 'data' => $array)]);
		$result['status'] = 1;
		$result['progress'] = 50;
		$result['data'] = 'pembelajaran';
		$result['aksi'] = 'pembelajaran';
		$result['server']	= 'erapor_server';
		$result['satuan']	= $satuan;
		$result['message'] = 'Sinkron Peserta Didik Keluar berhasil diproses';
		if($satuan){
			$result['status'] = 0;
		}
		echo json_encode($result);
	}
	private function pembelajaran($response, $satuan){
		$array = json_decode(json_encode($response), true);
		$this->call('sinkronisasi:prosesdata',['response' => array('query' => 'pembelajaran', 'data' => $array)]);
		$result['status'] = 1;
		$result['progress'] = 55;
		$result['data'] = 'ekskul';
		$result['aksi'] = 'ekstrakurikuler';
		$result['server']	= 'erapor_server';
		$result['satuan']	= $satuan;
		$result['message'] = 'Sinkron Pembelajaran berhasil diproses';
		if($satuan){
			$result['status'] = 0;
		}
		echo json_encode($result);
	}
	private function ekstrakurikuler($response, $satuan){
		$array = json_decode(json_encode($response), true);
		$this->call('sinkronisasi:prosesdata',['response' => array('query' => 'ekskul', 'data' => $array)]);
		$result['status'] = 1;
		$result['progress'] = 60;
		$result['data'] = 'anggota_ekskul';
		$result['aksi'] = 'anggota-ekskul';
		$result['server']	= 'erapor_server';
		$result['satuan']	= $satuan;
		$result['message'] = 'Sinkron Ekstrakurikuler berhasil diproses';
		if($satuan){
			$result['status'] = 0;
		}
		echo json_encode($result);
	}
	private function anggota_ekskul($response, $satuan){
		$array = json_decode(json_encode($response), true);
		$this->call('sinkronisasi:prosesdata',['response' => array('query' => 'anggota_ekskul', 'data' => $array)]);
		$result['status'] = 1;
		$result['progress'] = 75;
		$result['data'] = 'dudi';
		$result['aksi'] = 'dudi';
		$result['server']	= 'erapor_server';
		$result['satuan']	= $satuan;
		$result['message'] = 'Sinkron Anggota Ekstrakurikuler berhasil diproses';
		if($satuan){
			$result['status'] = 0;
		}
		echo json_encode($result);
	}
	private function dudi($response, $satuan){
		$array = json_decode(json_encode($response), true);
		$this->call('sinkronisasi:prosesdata',['response' => array('query' => 'dudi', 'data' => $array)]);
		$result['status'] = 1;
		$result['progress'] = 80;
		$result['data'] = 'jurusan';
		$result['aksi'] = 'jurusan';
		$result['server']	= 'erapor_server';
		$result['satuan']	= $satuan;
		$result['message'] = 'Sinkron Relasi Dunia Usaha &nbsp; Industri berhasil diproses';
		if($satuan){
			$result['status'] = 0;
		}
		echo json_encode($result);
	}
	private function jurusan($response, $satuan){
		$array = json_decode(json_encode($response), true);
		$this->call('sinkronisasi:prosesdata',['response' => array('query' => 'jurusan', 'data' => $array)]);
		$result['status'] = 1;
		$result['progress'] = 85;
		$result['data'] = 'kurikulum';
		$result['aksi'] = 'kurikulum';
		$result['server']	= 'erapor_server';
		$result['satuan']	= $satuan;
		$result['message'] = 'Sinkron Jurusan berhasil diproses';
		if($satuan){
			$result['status'] = 0;
		}
		echo json_encode($result);
	}
	private function kurikulum($response, $satuan){
		$array = json_decode(json_encode($response), true);
		$this->call('sinkronisasi:prosesdata',['response' => array('query' => 'kurikulum', 'data' => $array)]);
		$result['status'] = 1;
		$result['progress'] = 90;
		$result['data'] = 'mata_pelajaran';
		$result['aksi'] = 'mata-pelajaran';
		$result['server']	= 'erapor_server';
		$result['satuan']	= $satuan;
		$result['message'] = 'Sinkron Kurikulum berhasil diproses';
		if($satuan){
			$result['status'] = 0;
		}
		echo json_encode($result);
	}
	private function mata_pelajaran($response, $satuan){
		$array = json_decode(json_encode($response), true);
		$this->call('sinkronisasi:prosesdata',['response' => array('query' => 'mata_pelajaran', 'data' => $array)]);
		$result['status'] = 1;
		$result['progress'] = 95;
		$result['data'] = 'mapel_kur';
		$result['aksi'] = 'mata-pelajaran-kurikulum';
		$result['server']	= 'erapor_server';
		$result['satuan']	= $satuan;
		$result['message'] = 'Sinkron Mata Pelajaran berhasil diproses';
		if($satuan){
			$result['status'] = 0;
		}
		echo json_encode($result);
	}
	private function mata_pelajaran_kurikulum($response, $satuan){
		$array = json_decode(json_encode($response), true);
		$this->call('sinkronisasi:prosesdata',['response' => array('query' => 'mapel_kur', 'data' => $array)]);
		$result['status'] = 0;//$post_login;
		$result['progress'] = 97;
		$result['data'] = 'kompetensi_dasar';
		$result['aksi'] = 'count_kd';
		$result['server']	= 'erapor_dashboard';
		$result['satuan']	= $satuan;
		if($satuan){
			$result['message'] = 'Sinkron Mata Pelajaran Kurikulum berhasil diproses';
			$result['status'] = 0;
		} else {
			$result['message'] = 'Sinkronisasi dapodik berhasil diproses';
		}
		echo json_encode($result);
	}
	private function count_kd($response, $satuan){
		$jumlah_lokal = Kompetensi_dasar::count();
		$record['table'] = 'referensi kompetensi dasar';
		$record['jumlah'] = $response;
		$record['inserted'] = $jumlah_lokal;
		Storage::disk('public')->put('proses_kompetensi_dasar.json', json_encode($record));
		if($response > $jumlah_lokal){
			$this->call('kd:start');
		}
		$result['status'] = 0;
		$result['message'] = 'Sinkron Kompetensi Dasar berhasil diproses';
		echo json_encode($result);
	}
}
