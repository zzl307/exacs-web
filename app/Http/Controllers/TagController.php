<?php

namespace App\Http\Controllers;

use App\DeviceTag;
use App\DeviceTagAttrs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{   
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function home()
	{
		return View('tags.home', [ 'tags' => DeviceTagAttrs::all() ]);
	}

	public function detail()
	{
		$tagid = request()->input('tagid');
		$row = DeviceTagAttrs::find($tagid);
		return json_encode($row);
	}

	public function add()
	{
		if (!request()->isMethod('POST'))
			return redirect()->back();

		$tagname = request()->input('tagname');
		$dist = request()->input('dist');
		$status = request()->input('status');

		if ($dist)
		{
			foreach (explode(',', $dist) as $u)
				$a[] = trim($u);
			$dist = implode(', ', $a);
		}
		else
		{
			$dist = '';
		}

		try
		{
			$row = new DeviceTagAttrs();
			$row->name = $tagname;
			$row->dist = $dist;
			$row->status = $status;

			$row->save();
		}
		catch (\Exception $e)
		{
			if ($e->getCode() == "23000")
				return redirect()->back()->with('error', '标签<'.$tagname.'>已经存在');

			return redirect()->back()->with('error', $e->getMessage());
		}

		return redirect()->back()->with('success', '标签<'.$tagname.'>添加成功');
	}

	public function edit()
	{
		if (!request()->isMethod('POST'))
			return redirect()->back();

		$tagid = request()->input('tagid');
		$tagname = request()->input('tagname');
		$dist = request()->input('dist');
		$status = request()->input('status');

		$row = DeviceTagAttrs::find($tagid);
		if ($row == null)
			return redirect()->back()->with('error', '数据已经不存在');

		if ($dist)
		{
			foreach (explode(',', $dist) as $u)
				$a[] = trim($u);
			$dist = implode(', ', $a);
		}
		else
		{
			$dist = '';
		}

		try
		{
			$row->name = $tagname;
			$row->dist = $dist;
			$row->status = $status;

			$row->save();
		}
		catch (\Exception $e)
		{
			if ($e->getCode() == "23000")
				return redirect()->back()->with('error', '标签<'.$tagname.'>已经存在');

			return redirect()->back()->with('error', $e->getMessage());
		}

		return redirect()->back()->with('success', '标签<'.$tagname.'>修改成功');
	}

	public function delete()
	{
		$tagid = request()->input('tagid');

		$row = DeviceTagAttrs::find($tagid);
		if ($row == null)
			return redirect()->back()->with('error', '标签已经不存在');

		$tagname = $row->name;
		$row->delete();

		return redirect()->back()->with('success', '标签<'.$tagname.'删除成功');
	}
}
