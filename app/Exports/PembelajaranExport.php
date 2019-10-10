<?php

namespace App\Exports;
use App\Pembelajaran;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class PembelajaranExport implements FromQuery, WithHeadings, ShouldAutoSize
{
	use Exportable;
	public function __construct(string $rombongan_belajar_id)
    {
        $this->rombongan_belajar_id = $rombongan_belajar_id;
    }
	
	public function query()
    {
        return Pembelajaran::query()->select(['pembelajaran_id', 'mata_pelajaran_id', 'nama_mata_pelajaran', 'guru_id', 'kelompok_id', 'no_urut'])->where('rombongan_belajar_id', $this->rombongan_belajar_id);
    }
	public function headings(): array
    {
        return [
            'pembelajaran_id',
            'ID Mata Pelajaran',
            'Nama Mata Pelajaran',
            'Guru Mata Pelajaran',
			'Kelompok',
			'Nomor Urut',
        ];
    }
}
