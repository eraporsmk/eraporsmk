<?php

namespace App\Imports;

use App\Guru;
use App\Agama;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
class InstrukturImport implements ToModel, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
	public function startRow(): int
    {
        return 2;
    }
    public function model(array $row)
    {
		if($row[1]){
			$excel_date = $row[7]; //here is that value 41621 or 41631
			$pos = strpos($excel_date, '/');
			if ($pos === false) {
				$unix_date = ($excel_date - 25569) * 86400;
				$excel_date = 25569 + ($unix_date / 86400);
				$unix_date = ($excel_date - 25569) * 86400;
				$tanggal_lahir = gmdate("Y-m-d", $unix_date);
			} else {
				$excel_date = str_replace('/','-',$excel_date);
				$tanggal_lahir = date('Y-m-d', strtotime($excel_date));
			}
			$agama = Agama::where('nama', $row[8])->first();
			if($agama){
				$agama_id = ($agama->id) ? $agama->id : $agama->agama_id;
			} else {
				$agama_id = 1;
			}
			$user = auth()->user();
			$random = Str::random(6);
			$email = (strtolower($row[16])) ? strtolower($row[16]) : strtolower($random).'@erapor-smk.net';
			return Guru::firstOrCreate(
				[
					'email' 				=> $email,
					'sekolah_id'			=> $user->sekolah_id,
				],
				[
					'nama'					=> $row[1],
					'nuptk'					=> ($row[2]) ? $row[2] : mt_rand(),
					'nip'					=> $row[3],
					'nik'					=> $row[4],
					'jenis_kelamin'			=> $row[5],
					'tempat_lahir'			=> $row[6],
					'tanggal_lahir'			=> $tanggal_lahir,
					'jenis_ptk_id'			=> 97,
					'agama_id'				=> $agama_id,
					'status_kepegawaian_id'	=> 99,
					'alamat'				=> $row[9],
					'rt'					=> $row[10],
					'rw'					=> $row[11],
					'desa_kelurahan'		=> $row[12],
					'kecamatan'				=> $row[13],
					'kode_wilayah'			=> '000000  ',
					'kode_pos'				=> $row[14],
					'no_hp'					=> $row[15],
					'email' 				=> $email,
					'last_sync'				=> date('Y-m-d H:i:s')
				]
			);
		}
	}
}
