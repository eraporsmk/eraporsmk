<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Kompetensi_dasar;
use App\Kelompok;

class RefCP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ref:cp';

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
        $users = (new FastExcel)->import('public/kkm.xlsx', function ($line) {
            Kelompok::updateOrCreate(
                [
                    'kelompok_id' => $line['kelompok_id'],
                ],
                [
                    'nama_kelompok' => $line['nama_kelompok'],
                    'kurikulum' => $line['kurikulum'],
                    'kkm' => ($line['kkm']) ? $line['kkm'] : NULL,
                    'last_sync' => now(),
                ]
            );
        });
        $users = (new FastExcel)->import('public/rerf_cp.xlsx', function ($line) {
            $find = Kompetensi_dasar::where(function($query) use ($line){
                $query->where('id_kompetensi', $line['elemen']);
                $query->where('kompetensi_id', 3);
                $query->where('mata_pelajaran_id', $line['mata_pelajaran_id']);
                $query->where('kelas_10', 1);
                $query->where('kelas_11', 0);
                $query->where('kelas_12', 0);
                $query->where('kelas_13', 0);
            })->first();
            if($find){
                $find->id_kompetensi = $line['elemen'];
                $find->kompetensi_id = 3;
                $find->mata_pelajaran_id = $line['mata_pelajaran_id'];
                $find->kelas_10 = 1;
                $find->kelas_11 = 0;
                $find->kelas_12 = 0;
                $find->kelas_13 = 0;
                $find->kurikulum = 2021;
                $find->kompetensi_dasar = $line['deskripsi'];
                $find->last_sync = now();
                $find->save();
            } else {
                Kompetensi_dasar::create(
                    [
                        'id_kompetensi' => $line['elemen'],
                        'kompetensi_id' => 3,
                        'mata_pelajaran_id' => $line['mata_pelajaran_id'],
                        'kelas_10' => 1,
                        'kelas_11' => 0,
                        'kelas_12' => 0,
                        'kelas_13' => 0,
                        'kurikulum' => 2021,
                        'kompetensi_dasar_id'	=> Str::uuid(),
                        'kompetensi_dasar' => $line['deskripsi'],
                        'last_sync' => now(),
                    ]
                );
            }
        });
    }
}
