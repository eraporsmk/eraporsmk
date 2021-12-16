<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Kompetensi_dasar;
use App\Kelompok;
use App\Budaya_kerja;
use App\Elemen_budaya_kerja;
use App\Opsi_budaya_kerja;
use App\Teknik_penilaian;
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
        $data = [
            [
                'id' => 1,
                    'nama' => 'Beriman, bertakwa kepada Tuhan Yang Maha Esa, dan Berakhlak Mulia',
                    'elemen' => [
                        [
                            'id' => 1,
                            'nama' => 'Akhlak beragama',
                            'deskripsi' => 'Melakukan perbuatan baik sesuai tuntunan ajaran agama secara sadar dan berulang'
                        ],
                        [
                            'id' => 2,
                            'nama' => 'Akhlak pribadi',
                            'deskripsi' => 'Berkata yang baik dan jujur, menjaga amanah dengan baik, konsisten, serta menjauhkan diri dari perbuatan yang kurang baik'
                        ],
                        [
                            'id' => 3,
                            'nama' => 'Akhlak kepada manusia',
                            'deskripsi' => 'Melakukan perbuatan baik kepada orang lain'
                        ],
                        [
                            'id' => 4,
                            'nama' => 'Akhlak kepada alam',
                            'deskripsi' => 'Memelihara alam'
                        ],
                        [
                            'id' => 5,
                            'nama' => 'Akhlak bernegara',
                            'deskripsi' => 'Mematuhi peraturan perundangan yang berlaku'
                        ]
                    ],
            ],
            [
                'id' => 2,
                    'nama' => 'Bernalar Kritis',
                    'elemen' => [
                        [
                            'id' => 6,
                            'nama' => 'Mengidentifikasi, mengklarifikasi, dan mengolah informasi dan gagasan',
                            'deskripsi' => 'Secara kritis mengklarifikasi serta menganalisis gagasan dan informasi yang kompleks dan abstrak dari berbagai sumber. Memprioritaskan suatu gagasan yang paling relevan dari hasil klarifikasi dan analisis'
                        ],
                        [
                            'id' => 7,
                            'nama' => 'Menganalisis dan mengevaluasi penalaran',
                            'deskripsi' => 'Menganalisis dan mengevaluasi penalaran yang digunakannya dalam menemukan dan mencari solusi serta mengambil keputusan'
                        ],
                        [
                            'id' => 8,
                            'nama' => 'Merefleksi dan mengevaluasi pemilirannya sendiri',
                            'deskripsi' => 'Menjelaskan alasan untuk mendukung pemikirannya dan memikirkan pandangan yang mungkin berlawanan dengan pemikirannya dan mengubah pemikirannya jika diperlukan'
                        ],
                    ],
            ],
            [
                'id' => 3,
                    'nama' => 'Mandiri',
                    'elemen' => [
                        [
                            'id' => 9,
                            'nama' => 'Pemahaman diri dan situasi',
                            'deskripsi' => 'Mempunyai kemampuan dalam membaca keadaan diri dalam menghadapi tantangan yang ada serta mencari pemecahan tantangan  berdasarkan situasi yang ada.'
                        ],
                        [
                            'id' => 10,
                            'nama' => 'Regulasi diri',
                            'deskripsi' => 'Mempunyai standar dalam mengatur diri sendiri dan menjalankan kewajiban diri dengan tetap menghormati hak-hak orang lain.'
                        ],
                    ],
            ],
            [
                'id' => 4,
                    'nama' => 'Berkebinekaan Global',
                    'elemen' => [
                        [
                            'id' => 11,
                            'nama' => 'Mengenal dan benghargai budaya',
                            'deskripsi' => 'Keinginan untuk mengetahui budaya lain dan membangun rasa menghargai terhadap kebudayaan yang berbeda tersebut'
                        ],
                        [
                            'id' => 12,
                            'nama' => 'Komunikasi dan interaksi antar budaya',
                            'deskripsi' => 'Kemampuan dalam menjalin hubungan melalui berbagai macam bentuk komunikasi dan interaksi dengan orang lain yang mempunyai budaya dan latar belakang yang berbeda'
                        ],
                        [
                            'id' => 13,
                            'nama' => 'Refleksi dan tanggungjawab terhadap kebinekaan',
                            'deskripsi' => 'Keinginan untuk menjadikan pengalaman bertemu orang yang berbeda sebagai satu cara untuk membangun persahabatan dan pertemanan yang berdasarkan pada nilai-nilai kemanusiaan'
                        ],
                        [
                            'id' => 14,
                            'nama' => 'Berkeadilan sosial',
                            'deskripsi' => 'Kemampuan dalam bersiap adil terhadap orang-orang yang berbeda latar belakang'
                        ],
                    ],
            ],
            [
                'id' => 5,
                    'nama' => 'Kreatif',
                    'elemen' => [
                        [
                            'id' => 15,
                            'nama' => 'Menghasilkan gagasan yang orisinal',
                            'deskripsi' => 'Melahirkan gagasan berdasarkan pemikiran sendiri atau tim dengan mempertimbangkan berbagai macam informasi yang sesuai dengan gagasan tersebut.'
                        ],
                        [
                            'id' => 16,
                            'nama' => 'Melahirkan karya dan tindakan yang orisinal',
                            'deskripsi' => 'Melahirkan sesuatu yang asli serta bisa meningkatkan kualitas hidup diri sendiri serta orang banyak'
                        ],
                        [
                            'id' => 17,
                            'nama' => 'Memiliki keluwesan berpikir dalam mencari alternatif solusi permasalahan',
                            'deskripsi' => 'Mampu mencari alternatif-alternatif penyelesaian suatu masalah dengan mempertimbangkan baik atau buruknya solusi tersebut termasuk keluar dalam tekanan'
                        ],
                    ],
            ],
            [
                'id' => 6,
                    'nama' => 'Bergotong royong',
                    'elemen' => [
                        [
                            'id' => 18,
                            'nama' => 'Kolaborasi',
                            'deskripsi' => 'Menjalin kerjasama dan bersinergi untuk mencapai tujuan dan kebaikan bersama dengan mengesampingkan kepentingan pribadi.'
                        ],
                        [
                            'id' => 19,
                            'nama' => 'Kepedulian',
                            'deskripsi' => 'Mengekspresikan kepedulian pada sesama dan makhluk hidup lainnya'
                        ],
                        [
                            'id' => 20,
                            'nama' => 'Berbagi',
                            'deskripsi' => 'Berbagi setiap sumber daya yang dimiliki, termasuk ilmu dan pengetahuan  dengan tetap berpegang teguh pada nilai-nilai kebenaran dan kemajuan bersama'
                        ],
                    ],
            ],
        ];
        foreach($data as $d){
            Budaya_kerja::updateOrCreate(
                [
                    'budaya_kerja_id' => $d['id'],
                ],
                [
                    'aspek' => $d['nama'],
                    'last_sync' => now(),
                ]
            );
            foreach($d['elemen'] as $elemen){
                Elemen_budaya_kerja::updateOrCreate(
                    [
                        'elemen_id' => $elemen['id'],
                    ],
                    [
                        'budaya_kerja_id' => $d['id'],
                        'elemen' => $elemen['nama'],
                        'deskripsi' => $elemen['deskripsi'],
                        'last_sync' => now(),
                    ]
                );
            }
        }
        $data = [
            [
                'id' => 1,
                'kode' => 'BB',
                'nama' => 'Belum Berkembang',
                'deskripsi' => 'Peserta Didik masih membutuhkan bimbingan dalam mengembangkan kemampuan',
                'warna' => 'yellow',
            ],
            [
                'id' => 2,
                'kode' => 'MB',
                'nama' => 'Mulai Berkembang',
                'deskripsi' => 'Peserta Didik mulai mengembangkan kemampuan namun masih belum ajek',
                'warna' => 'blue',
            ],
            [
                'id' => 3,
                'kode' => 'BSH',
                'nama' => 'Berkembang Sesuai Harapan',
                'deskripsi' => 'Peserta Didik telah mengembangkan kemampuan hingga berada dalam tahap ajek',
                'warna' => 'red',
            ],
            [
                'id' => 4,
                'kode' => 'SB',
                'nama' => 'Sangat Berkembang',
                'deskripsi' => 'Peserta Didik mengembangkan kemampuannya melampaui harapan',
                'warna' => 'green',
            ],
        ];
        foreach($data as $d){
            Opsi_budaya_kerja::updateOrCreate(
                [
                    'opsi_id' => $d['id'],
                ],
                [
                    'kode' => $d['kode'],
                    'nama' => $d['nama'],
                    'deskripsi' => $d['deskripsi'],
                    'warna' => $d['warna'],
                    'last_sync' => now(),
                ]
            );
        }
        $data = [
            [
                'kompetensi_id' => 3,
                'nama' => 'Sumatif (SMK PK)',
                'bobot' => 1,
            ],
            [
                'kompetensi_id' => 3,
                'nama' => 'Formatif (SMK PK)',
                'bobot' => 0,
            ]
        ];
        foreach($data as $d){
            Teknik_penilaian::updateOrCreate(
                [
                    'kompetensi_id' => $d['kompetensi_id'],
                    'nama' => $d['nama'],
                    'bobot' => $d['bobot'],
                ],
                [
                    'last_sync' => now(),
                ]
            );
        }
        $data = (new FastExcel)->import('public/kkm.xlsx', function ($line) {
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
        $data = (new FastExcel)->import('public/rerf_cp.xlsx', function ($line) {
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
