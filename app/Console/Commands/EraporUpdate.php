<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;
use App\Setting;
class EraporUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erapor:update {json?}';

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
        $json = $this->argument('json');
        if($json){
            $a = ['foo'=>
                ['bar', 'i', $json]
            ];
            $a = collect($a);
            $this->info($a->toJson());
        } else {
            $version = File::get(base_path().'/version.txt');
            \Artisan::call('migrate');
            Setting::where('key', 'app_version')->update(['value' => $version]);
            $this->info('Berhasil memperbaharui aplikasi e-Rapor SMK ke versi 5.0.9');
        }
    }
}
