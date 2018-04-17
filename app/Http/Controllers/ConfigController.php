<?php

namespace App\Http\Controllers;

use App\DownloadServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{   
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function dlserver()
	{
		$dlservers = DownloadServer::all();
		return View('config.dlserver', compact('dlservers'));
	}
}
