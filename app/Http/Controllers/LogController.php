<?php

namespace App\Http\Controllers;

use App\Devlog;
use Illuminate\Http\Request;

class LogController extends Controller
{	
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    // 日志管理
    public function devlog()
    {
    	// 分页展示
    	$devlog = DeviceLog::orderBy('id', 'desc')->paginate(16);

    	return View('log.devlog', [
    		'devlog' => $devlog
    	]);
    }
}
