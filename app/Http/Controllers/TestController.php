<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
class TestController extends Controller
{
	public function __construct()
    {
        $this->path = storage_path('backup');
    }
	public function index(){
		echo 'test';
	}
}
