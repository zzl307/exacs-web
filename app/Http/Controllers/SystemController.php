<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemController extends Controller
{
    // 文件下载
    public function getPackageDownload($package)
	{   
	    // 判断文件有没有存在
        $exists = Storage::disk('uploads')->exists($package);

        // 查询文件名
        $launcherFilename = \DB::table('launcher')
                        ->where('fuzzyname', '=', $package)
                        ->first();

        $packageFilename = \DB::table('package')
                        ->where('fuzzyname', '=', $package)
                        ->first();

        if ($exists) {
            $file = storage_path('app/uploads').'/'.$package;
            if ($launcherFilename) {
                $name = $launcherFilename->filename;
            } else {
                $name = $packageFilename->filename;
            }
            $header = [
                'Content-Type' => 'application/octet-stream'
            ];

            return response()->download($file, $name, $header);
        } else {
            return redirect()->back()->with('error', '文件不存在');
        }
	}
}
