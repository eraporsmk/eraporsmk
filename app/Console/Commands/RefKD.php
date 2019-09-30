<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Kompetensi_dasar;
use App\Kurikulum;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
class RefKD extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ref_kd:start';

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
		$this->info("Memulai proses sinkronisasi referensi kompetensi dasar");
    }
}
