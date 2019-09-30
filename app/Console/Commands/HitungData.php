<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
class HitungData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sinkronisasi:hitungdata {file}';

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
		if(!Storage::disk('public')->exists($arguments['file'])){
			$record['table'] = '';
			$record['jumlah'] = 0;
			$record['inserted'] = 0;
			Storage::disk('public')->put($arguments['file'], json_encode($record));
		}
		$json = Storage::disk('public')->get($arguments['file']);
		echo $json;
		/*$response = json_decode($json);
		if($response){
			$this->info('Proses sinkronisasi data '.$response->table);
			$this->info('('.$response->inserted.' /');
			$this->info($response->jumlah.')');
		} else {
			$this->info('Proses sinkronisasi data ');
		}*/
    }
}
