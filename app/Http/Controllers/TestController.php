<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
class TestController extends Controller
{
	public function __construct()
    {
        $this->path = storage_path('backup');
    }
	public function index(){
		dd(config());
		//echo 'test';
		//$a = 'pg_dump -U postgres -h 127.0.0.1 -p 5432 erapor_git > erapor_smk.sql';
		//$last_line = system('composer update', $retval);
		dump(getcwd());
		chdir('..');
		// current directory
		dump(getcwd());
		//$last_line = system('composer update', $retval);
		//dump($last_line);
		//dd($retval);
	}
}
