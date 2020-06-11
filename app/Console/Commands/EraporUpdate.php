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
            $db_version = File::get(base_path().'/db_version.txt');
            \Artisan::call('migrate');
            \Artisan::call('cache:clear');
            \Artisan::call('view:clear');
            \Artisan::call('config:cache');
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
            Setting::where('key', 'app_version')->update(['value' => $version]);
            Setting::where('key', 'db_version')->update(['value' => $db_version]);
            $this->info('Berhasil memperbaharui aplikasi e-Rapor SMK ke versi 5.1.0');
        }
    }
}
