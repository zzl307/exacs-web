<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

    // 文件上传首页
    public function index()
    {	
    	$files = Storage::disk('upload')->files();

    	return view('upload.index', compact('files'));
    }

    // 文件上传
    public function store()
    {
    	if (!request()->isMethod('POST')) {
			return redirect()->back();
    	}

		$file = request()->file('source');

		if (!$file->isValid()) {
			return redirect()->back()->with('error', '文件上传失败');
		}

		$originalName = $file->getClientOriginalName();
		$realPath = $file->getRealPath();

		if (Storage::disk('upload')->exists($originalName)) {
			return redirect()->back()->with('error', '文件已存在');
		} else {
			Storage::disk('upload')->put($originalName, file_get_contents($realPath));

			return redirect()->back()->with('success', '文件上传成功');
		}
    }

    // 文件下载
    public function download($filename)
    {
    	// 判断文件有没有存在
        $exists = Storage::disk('upload')->exists($filename);

        if ($exists) {
            $file = storage_path('app/uploads/files').'/'.$filename;
            $header = [
                'Content-Type' => 'application/octet-stream'
            ];

            return response()->download($file, $filename, $header);
        } else {
            return redirect()->back()->with('error', '文件不存在');
        }
    }

    // 文件删除
    public function delete($filename)
    {
    	// 判断文件有没有存在
        $exists = Storage::disk('upload')->exists($filename);

        if ($exists) {
        	Storage::disk('upload')->delete($filename);
        	
        	return redirect()->back()->with('success', '删除 '.$filename.'成功');
        }
    }
}
