<?php

namespace App\Http\Controllers;

use App\Launcher;
use App\LauncherRelease;
use App\DeviceType;
use App\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LauncherController extends Controller
{   
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function home()
	{
		$releases = DB::table('launcher_release')->orderBy('id', 'desc')->get();
		return View('launcher.home', compact('releases'));
	}

	public function getReleaseInfo()
	{
		$version = request()->input('version');
		$release = LauncherRelease::find($version);
		return json_encode($release);
	}

	public function addRelease()
	{
		if (request()->isMethod('POST'))
		{
			$version = request()->input('version');
			$note = request()->input('note');
			$test = request()->input('test');

			if (LauncherRelease::find($version))
				return redirect()->back()->with('error', '版本'.$version.'已经存在');;

			if (empty($note))
				$note = '';

			$row = new LauncherRelease;
			$row->time = date('Y-m-d H:i:s', time());
			$row->version = $version;
			$row->note = $note;
			$row->test = $test;
			$row->save();
		}

		return redirect()->back();
	}

	public function updateRelease()
	{
		$version = request()->input('version');
		$note = request()->input('note');
		$test = request()->input('test');

		if (empty($version))
			return redirect()->back()->with('error', '参数错误');

		if (empty($note))
			$note = '';

		$row = LauncherRelease::find($version);
		if (!$row)
			return redirect()->back()->with('error', '版本已经不存在');;

		$row->note = $note;
		$row->test = $test;
		$row->save();

		return redirect()->back()->with('success', $version.'更新成功');
	}

	public function deleteRelease()
	{
		$version = request()->input('version');
		$pattern = 'exa-launcher-'.$version.'.%';
		$launchers = DB::table('launcher')->where('filename', 'like', $pattern)->get();
		if (!$launchers->isEmpty())
			return redirect()->back()->with('error', '版本'.$version.'有启动程序');

		if (!LauncherRelease::destroy($version))
			return redirect()->back()->with('error', '版本'.$version.'删除失败');

		return redirect()->back()->with('success', '版本'.$version.'删除成功');
	}

	public function displayFiles()
	{
		$version = request()->input('version');
		$devices = Device::deviceNumByLauncher();

		if (empty($version))
		{
			$launchers = Launcher::all();
			$release = '';
			return View('launcher.launchers', compact('launchers', 'devices', 'release'));
		}

		$release = LauncherRelease::find($version);
		if (!$release)
			return redirect()->back()->with('error', '版本已经不存在');

		$devtypes = DeviceType::all();

		$launchers = array();
		foreach (Launcher::all() as $launcher)
		{	
			$match = false;
			foreach ($devtypes as $devtype)
			{   
				$filename = 'exa-launcher-'.$version.'.'.$devtype->name;

				if ($launcher->filename == $filename)
				{
					$match = true;
					break;
				}
			}

			if ($match)
				$launchers[] = $launcher;
		}

		return View('launcher.launchers', compact('release', 'launchers', 'devices'));
	}

	public function uploadTest()
	{
		if (!request()->isMethod('POST'))
			return redirect()->back();

		$version = request()->input('version');
		$file = request()->file('source');

		if (!$file->isValid())
			return redirect()->back()->with('error', '文件上传失败');

		$originalName = $file->getClientOriginalName();

		$devtypes = DeviceType::all();
		$pkgtypes = \App\PackageType::all();

		$devtype = '';
		$match = false;
		foreach ($devtypes as $vo)
		{   
			$filename = 'exa-launcher-'.$version.'.'.$vo->name;

			if ($originalName == $filename)
			{
				$match = true;
				$devtype = $vo->name;
				break;
			}
		}

		if ($match == false || $devtype == '')
			return redirect()->back()->with('error', '文件格式错误');

		$realPath = $file->getRealPath();
		$cksum = md5(file_get_contents($realPath));
		$fuzzyname = 'lch'.substr($cksum, 24, 32);

		$launcher = DB::Table('launcher')->where('filename', $originalName)->first();
		if ($launcher)
			Storage::disk('uploads')->delete($launcher->fuzzyname);

		$result = Launcher::updateOrCreate(array('filename' => $originalName), array('time' => date('Y-m-d H:i:s', time()), 'devtype' => $devtype, 'cksum' => $cksum, 'fuzzyname' => $fuzzyname));
		if (!$result)
			 return redirect()->back()->with('error', '文件保存失败');

		Storage::disk('uploads')->put($fuzzyname, file_get_contents($realPath));
		return redirect()->back()->with('success', '文件上传成功');
	}

	public function upload()
	{
		if (!request()->isMethod('POST'))
			return redirect()->back();

		$version = request()->input('version');
		$file = request()->file('source');

		if (!$file->isValid())
			return redirect()->back()->with('error', '文件上传失败');

		$originalName = $file->getClientOriginalName();

		$devtypes = DeviceType::all();
		$pkgtypes = \App\PackageType::all();

		$devtype = '';
		$match = false;
		foreach ($devtypes as $vo)
		{   
			$filename = 'exa-launcher-'.$version.'.'.$vo->name;

			if ($originalName == $filename)
			{
				$match = true;
				$devtype = $vo->name;
				break;
			}
		}

		if ($match == false || $devtype == '')
			return redirect()->back()->with('error', '文件格式错误');

		$realPath = $file->getRealPath();
		$cksum = md5(file_get_contents($realPath));
		$fuzzyname = 'lch'.substr($cksum, 24, 32);

		$launcher = DB::Table('launcher')->where('filename', $originalName)->first();
		if ($launcher)
			Storage::disk('uploads')->delete($launcher->fuzzyname);

		$result = Launcher::updateOrCreate(array('filename' => $originalName), array('time' => date('Y-m-d H:i:s', time()), 'devtype' => $devtype, 'cksum' => $cksum, 'fuzzyname' => $fuzzyname));
		if (!$result)
			 return redirect()->back()->with('error', '文件保存失败');

		Storage::disk('uploads')->put($fuzzyname, file_get_contents($realPath));
		return redirect()->back()->with('success', '文件上传成功');
	}

	public function checkReleaseFile()
	{
		$id = request()->input('id');
		if (empty($id))
			return redirect()->back()->with('error', '参数错误');;

		$row = Launcher::find($id);
		if (!isset($row))
			return redirect()->back()->with('error', '文件已经不存在');;

		$row->test = 0;
		$row->save();

		return redirect()->back();
	}

	public function uncheckReleaseFile()
	{
		$id = request()->input('id');
		if (empty($id))
			return redirect()->back()->with('error', '参数错误');;

		$row = Launcher::find($id);
		if (!isset($row))
			return redirect()->back()->with('error', '文件已经不存在');;

		$row->test = 1;
		$row->save();

		return redirect()->back();
	}

	public function deleteReleaseFile()
	{
		$id = request()->input('id');
		if (empty($id))
			return redirect()->back()->with('error', '参数错误');;

		$row = Launcher::find($id);
		if (!isset($row))
			return redirect()->back()->with('error', '文件已经不存在');;

		$count = DB::table('device')->where('launcher_id', $id)->count();

		if ($count > 0)
			return redirect()->back()->with('error', $row->filename.'有设备在使用');

		$row->delete();
		Storage::disk('uploads')->delete($row->fuzzyname);
		return redirect()->back()->with('success', '成功删除'.$row->filename);
	}

	public function upgrade()
	{
		if (request()->isMethod('POST'))
		{
			$id = request()->input('id');
			$to = request()->input('to');

			if ($id != $to)
			{
				Device::upgradeLaunchers($id, $to);
			}
		}

		return redirect()->back();
	}

	public function getLauncherList()
	{
		$devtype = request()->input('devtype');
		$launchers = Launcher::launchers($devtype);
		return json_encode($launchers);
	}
}
