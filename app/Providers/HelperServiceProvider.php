<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Nilai;
use App\Pembelajaran;
use App\Agama;
use App\Anggota_rombel;
use App\Semester;
use App\Setting;
use App\Status_penilaian;
use UserHelp;
class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
		//
	}
	public static function status_penilaian($sekolah_id, $semester_id){
		$status = Status_penilaian::firstOrCreate(
			[
				'sekolah_id' => $sekolah_id,
				'semester_id' => $semester_id,
			],
			['status' => 1]
		);
		return $status;		
	}
	public static function prepare_send($str){
		return rawurlencode(base64_encode(gzcompress(self::encryptor(serialize($str)))));
	}
	public static function prepare_receive($str){
		return unserialize(self::decryptor(gzuncompress(base64_decode(rawurldecode($str)))));
	}
	public static function encryptor($str){
		return $str;
	}
	public static function decryptor($str){
		return $str;
	}
	public static function TanggalIndo($date){
		$BulanIndo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
		$tahun = substr($date, 0, 4);
		$bulan = substr($date, 5, 2);
		$tgl   = substr($date, 8, 2);
		$result = $tgl . " " . $BulanIndo[(int)$bulan-1] . " ". $tahun; 
		return($result);
	}
	public static function jenis_gtk($query){
		$data['tendik'] = array(11,30,40,41,42,43,44,57,58,59);
		$data['guru'] = array(3,4,5,6,7,8,9,10,12,13,14,20,25,26,51,52,53,54,56);
		$data['instruktur'] = array(97);
		$data['asesor'] = array(98);
		return $data[$query];
	}
	public static function get_ta(){
		$check = DB::select('select column_name from information_schema.columns where table_schema = ? and table_name = ?',['ref', 'semester']);
		$user = auth()->user();
		if($check){
			if(isset($user->periode_aktif)){
				return Semester::find($user->periode_aktif);
			} else {
				return Semester::where('periode_aktif', 1)->first();
			}
		}
	}
	public static function table_sync(){
		$table_sync = array(
			'sekolah', //no semester
			'jurusan_sp', //no semester
			'guru', //no semester
			'rombongan_belajar',
			'peserta_didik', //no semester
			'pembelajaran',
			'ekstrakurikuler',
			'anggota_rombel',
			'dudi',
			'mou',
			'absen',
			'catatan_ppk',
			'catatan_wali',
			'deskripsi_mata_pelajaran',
			'deskripsi_sikap',
			'kd_nilai', //no semester
			'nilai',
			'nilai_akhir',
			'nilai_ekstrakurikuler',
			'nilai_sikap',
			'nilai_ukk',
			'prakerin',
			'prestasi',
			'remedial',
			'rencana_penilaian',
			'teknik_penilaian', //no semester
			'bobot_keterampilan',
			'nilai_rapor',
			'kenaikan_kelas',
			'indikator_karakter',
			//'ref.sikap', //no semester
			//'ref.kompetensi_dasar', //no semester
			//'ref.semester',
			'users',
			'role_user',
		);
		return $table_sync;
	}
	public static function test($var){
		echo '<pre>';
		print_r($var);
		echo '</pre>';
	}
	public static function status_label($status){
		if($status == '1') : 
			$label = '<span class="btn btn-xs btn-success"> Aktif </span>';
		elseif ($status == '0') : 
			$label = '<span class="btn btn-xs btn-danger"> Non Aktif </span>';
		endif;
		return $label;
	}
	public static function check_2018(){
		$semester = config('site.semester');//self::get_ta();
		$tahun = substr($semester->semester_id,0,4);
		if($tahun >= 2018){
			return true;
		} else {
			return false;
		}
	}
	public static function get_kkm($kelompok_id, $kkm){
		if($kkm){
			return $kkm;
		}
		$check_2018 = self::check_2018();
		if($check_2018){
			$produktif = array(4,5,9,10,13);
			$non_produktif = array(1,2,3,6,7,8,11,12,99);
			if(in_array($kelompok_id,$produktif)){
				$new_kkm = 65;
			} elseif(in_array($kelompok_id,$non_produktif)) {
				$new_kkm = 60;
			} else {
				$new_kkm = $kkm;
			}
		}
		return $new_kkm;
	}
	public static function get_nilai($anggota_rombel_id, $kd_nilai_id){
		$get_nilai = Nilai::where('anggota_rombel_id', '=', $anggota_rombel_id)->where('kd_nilai_id', '=', $kd_nilai_id)->first();
		$nilai = ($get_nilai) ? $get_nilai->nilai : '';
		return $nilai;
	}
	public static function array_to_object($d) {
        return is_array($d) ? (object) array_map(__METHOD__, $d) : $d;
    }
	public static function predikat($kkm, $nilai, $produktif = NULL){
		if($produktif){
			$result = array(
				'A+'	=> 100, // 95 - 100
				'A'		=> 94, // 90 - 94
				'A-'	=> 89, // 85 - 89
				'B+'	=> 84, // 80 - 84
				'B'		=> 79, // 75 - 79
				'B-'	=> 74, // 70 - 74
				'C'		=> 69, // 65 - 69
				'D'		=> 64, // 0 - 59
			);
		} else {
			$result = array(
				'A+'	=> 100, // 95 - 100
				'A'		=> 94, // 90 - 94
				'A-'	=> 89, // 85 - 89
				'B+'	=> 84, // 80 - 84
				'B'		=> 79, // 75 - 79
				'B-'	=> 74, // 70 - 74
				'C'		=> 69, // 60 - 69
				'D'		=> 59, // 0 - 59
			);
		}
		if($result[$nilai] > 100)
			$result[$nilai] = 100;
		return $result[$nilai];
	}
	public static function konversi_huruf($kkm, $nilai, $produktif = NULL){
		$check_2018 = self::check_2018();
		if($check_2018){
			$show = 'predikat';
			$a = self::predikat($kkm,'A') + 1;
			$a_min = self::predikat($kkm,'A-') + 1;
			$b_plus = self::predikat($kkm,'B+') + 1;
			$b = self::predikat($kkm,'B') + 1;
			$b_min = self::predikat($kkm,'B-') + 1;
			$c = self::predikat($kkm,'C') + 1;
			$d = self::predikat($kkm,'D', $produktif) + 1;
			if($nilai == 0){
				$predikat 	= '-';
			} elseif($nilai >= $a){//$settings->a_min){ //86
				$predikat 	= 'A+';
			} elseif($nilai >= $a_min){//$settings->a_min){ //86
				$predikat 	= 'A';
			} elseif($nilai >= $b_plus){//$settings->a_min){ //86
				$predikat 	= 'A-';
			} elseif($nilai >= $b){//$settings->a_min){ //86
				$predikat 	= 'B+';
			} elseif($nilai >= $b_min){//$settings->a_min){ //86
				$predikat 	= 'B';
			} elseif($nilai >= $c){//$settings->a_min){ //86
				$predikat 	= 'B-';
			} elseif($nilai >= $d){//$settings->a_min){ //86
				$predikat 	= 'C';
			} elseif($nilai < $d){//$settings->a_min){ //86
				$predikat 	= 'D';
			}
		} else {
			$b = self::predikat($kkm,'b') + 1;
			$c = self::predikat($kkm,'c') + 1;
			$d = self::predikat($kkm,'d') + 1;
			if($n == 0){
				$predikat 	= '-';
				$sikap		= '-';
				$sikap_full	= '-';
			} elseif($n >= $b){//$settings->a_min){ //86
				$predikat 	= 'A';
				$sikap		= 'SB';
				$sikap_full	= 'Sangat Baik';
			} elseif($n >= $c){ //71
				$predikat 	= 'B';
				$sikap		= 'B';
				$sikap_full	= 'Baik';
			} elseif($n >= $d){ //56
				$predikat 	= 'C';
				$sikap		= 'C';
				$sikap_full	= 'Cukup';
			} elseif($n < $d){ //56
				$predikat 	= 'D';
				$sikap		= 'K';
				$sikap_full	= 'Kurang';
			}
		}
		return $predikat;
	}
	public static function sebaran($input, $a,$b){
		$range_data = range($a,$b);	
		$output = array_intersect($input , $range_data);
		return $output;
	}
	public static function sebaran_tooltip($input, $a,$b,$c){
		$range_data = range($a,$b);
		$output = array_intersect($input , $range_data);
		$data = array();
		$nama_siswa = '';
		foreach($output as $k=>$v){
			$data[] = $k;
		}
		if(count($output) == 0){
			$result = count($output);
		} else {
			$result = '<a class="tooltip-'.$c.'" href="javascript:void(0)" title="'.implode('<br />',$data).'" data-html="true">'.count($output).'</a>';
		}
		return $result;
	}
	public static function filter_agama_siswa($pembelajaran_id, $rombongan_belajar_id){
		$ref_agama = Agama::all();
		foreach($ref_agama as $agama){
			$nama_agama = str_replace('Budha','Buddha',$agama->nama);
			$agama_id[$agama->id] = $nama_agama;
		}
		$get_mapel = Pembelajaran::with('mata_pelajaran')->find($pembelajaran_id);
		$nama_mapel = str_replace('Pendidikan Agama','',$get_mapel->mata_pelajaran->nama);
		$nama_mapel = str_replace('KongHuChu','Konghuchu',$nama_mapel);
		$nama_mapel = str_replace('Kong Hu Chu','Konghuchu',$nama_mapel);
		$nama_mapel = str_replace('dan Budi Pekerti','',$nama_mapel);
		$nama_mapel = str_replace('Pendidikan Kepercayaan terhadap','',$nama_mapel);
		$nama_mapel = str_replace('Tuhan YME','Kepercayaan kpd Tuhan YME',$nama_mapel);
		$nama_mapel = trim($nama_mapel);
		$agama_id = array_search($nama_mapel, $agama_id);
		return $agama_id;
	}
	public static function filter_pembelajaran_agama($agama_siswa, $nama_agama){
		$nama_agama = str_replace('Budha','Buddha',$nama_agama);
		$nama_agama = str_replace('Pendidikan Agama','',$nama_agama);
		$nama_agama = str_replace('dan Budi Pekerti','',$nama_agama);
		$nama_agama = str_replace('Pendidikan Kepercayaan','',$nama_agama);
		$nama_agama = str_replace('terhadap','kpd',$nama_agama);
		$nama_agama = str_replace('KongHuChu','Konghuchu',$nama_agama);
		$nama_agama = str_replace('Kong Hu Chu','Konghuchu',$nama_agama);
		$nama_agama = trim($nama_agama);
		$agama_siswa = str_replace('KongHuChu','Konghuchu',$agama_siswa);
		$agama_siswa = str_replace('Kong Hu Chu','Konghuchu',$agama_siswa);
		if($agama_siswa == $nama_agama){
			return true;
		} else {
			return false;
		}
	}
	public static function mapel_agama(){
		return ['100014000', '100014140', '100015000', '100015010', '100016000', '100016010', '109011000', '109011010', '100011000', '100011070', '100013000', '100013010', '100012000', '100012050'];
	}
	public static function filter_agama_mapel($get_id_mapel, $all_mapel,$agama_siswa){
		$get_mapel_agama = Pembelajaran::with('mata_pelajaran')->whereIn('mata_pelajaran_id', $get_id_mapel);
		foreach($get_mapel_agama as $agama){
			if (strpos($agama->mata_pelajaran->nama, $agama_siswa) === false) {
				$mapel_agama_jadi[] = $agama->pembelajaran_id;
			}
		}
		if(isset($mapel_agama_jadi) && $all_mapel){
			$all_mapel = array_diff($all_mapel, $mapel_agama_jadi);
		}
		return $all_mapel;
	}
	public static function atest(){
		$a = Auth::user();
		return $a;
	}
	public static function keterangan_ukk($n, $lang = 'ID'){
		if($lang == 'ID'){
			if(!$n){
				$predikat 	= '';
			} elseif($n >= 91){
				$predikat 	= 'Sangat Kompeten';
			} elseif($n >= 80 && $n <= 90){
				$predikat 	= 'Kompeten';
			} elseif($n >= 70 && $n <= 79){
				$predikat 	= 'Cukup Kompeten';
			} elseif($n < 70){
				$predikat 	= 'Belum Kompeten';
			}
		} else {
			if(!$n){
				$predikat 	= '';
			} elseif($n >= 91){
				$predikat 	= 'Highly Competent';
			} elseif($n >= 80 && $n <= 90){
				$predikat 	= 'Competent';
			} elseif($n >= 70 && $n <= 79){
				$predikat 	= 'Partly Competent';
			} elseif($n < 70){
				$predikat 	= 'Not Yet Competent';
			}
		}
		return $predikat;
	}
	public static function penyebut($nilai) {
		$nilai = abs($nilai);
		$huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
		$temp = "";
		if ($nilai < 12) {
			$temp = " ". $huruf[$nilai];
		} else if ($nilai <20) {
			$temp = self::penyebut($nilai - 10). " Belas";
		} else if ($nilai < 100) {
			$temp = self::penyebut($nilai/10)." Puluh". self::penyebut($nilai % 10);
		} else if ($nilai < 200) {
			$temp = " seratus" . self::penyebut($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = self::penyebut($nilai/100) . " Ratus" . self::penyebut($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " seribu" . self::penyebut($nilai - 1000);
		} else if ($nilai < 1000000) {
			$temp = self::penyebut($nilai/1000) . " Ribu" . self::penyebut($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$temp = self::penyebut($nilai/1000000) . " Tuta" . self::penyebut($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$temp = self::penyebut($nilai/1000000000) . " Milyar" . self::penyebut(fmod($nilai,1000000000));
		} else if ($nilai < 1000000000000000) {
			$temp = self::penyebut($nilai/1000000000000) . " Trilyun" . self::penyebut(fmod($nilai,1000000000000));
		}     
		return $temp;
	}
	public static function terbilang($nilai) {
		if($nilai<0) {
			$hasil = "minus ". trim(self::penyebut($nilai));
		} else {
			$hasil = trim(self::penyebut($nilai));
		}     		
		return ucwords(strtolower($hasil));
	}
	public static function get_setting($key){
		$setting = Setting::where('key', $key)->first();
		return $setting->value;
	}
	public static function nama_guru($gelar_depan, $nama, $gelar_belakang){
		$nama = strtoupper($nama);
		if($gelar_depan && $gelar_depan->count()){
			$gelar_depan = $gelar_depan->implode('kode', '. ').'. ';
		} else {
			$gelar_depan = '';
		}
		if($gelar_belakang && $gelar_belakang->count()){
			$gelar_belakang = ', '.$gelar_belakang->implode('kode', '. ').'.';
		} else {
			$gelar_belakang = '';
		}
		$return  = $gelar_depan.$nama.$gelar_belakang;
		return $return;
	}
	public static function get_previous_letter($string){
		$last = substr($string, -1);
		$part=substr($string, 0, -1);
		if(strtoupper($last)=='A'){
			$l = substr($part, -1);
			if($l=='A'){
				return substr($part, 0, -1)."Z";
			}
			return $part.chr(ord($l)-1);
		}else{
			return $part.chr(ord($last)-1);
		}
	}
	public static function status_kenaikan($status){
		if($status == 1){
			$status_teks = 'Naik ke kelas';
		} elseif($status == 2){
			$status_teks = 'Tetap dikelas';
		} elseif($status == 3){
			$status_teks = 'Lulus';
		} else {
			$status_teks = 'Tidak Lulus';
		}
		return $status_teks;
	}
	public static function clean($string) {
		$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
		$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
		return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
	}
	public static function password_dapo($password_dapo){
		$passCode = UserHelp::doEncrypt($password_dapo);
		return $passCode;
	}
	public static function __oc(){
		$__oc=strtolower(substr(php_uname(),0,3));
		if($__oc=='win'){
			return true;
		}
		return false;
	}
	public static function escapeJsonString($value) { # list from www.json.org: (\b backspace, \f formfeed)
		$escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
		$replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
		$result = str_replace($escapers, $replacements, $value);
		return $result;
	}
	public static function opsi_budaya($n){
		if(!$n){
			$predikat 	= '-';
		} elseif($n >= 4){
			$predikat 	= '<span class="badge bg-green">&nbsp;&nbsp;&nbsp;&nbsp;</span>';
		} elseif($n >= 3){
			$predikat 	= '<span class="badge bg-red">&nbsp;&nbsp;&nbsp;&nbsp;</span>';
		} elseif($n >= 2){
			$predikat 	= '<span class="badge bg-blue">&nbsp;&nbsp;&nbsp;&nbsp;</span>';
		} elseif($n >= 1){
			$predikat 	= '<span class="badge bg-yellow">&nbsp;&nbsp;&nbsp;&nbsp;</span>';
		}
		return $predikat;
	}
}
