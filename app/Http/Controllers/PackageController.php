<?php

namespace App\Http\Controllers;

use App\DeviceType;
use App\DevicePackage;
use App\PackageName;
use App\PackageType;
use App\Package;
use App\PackageRelease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mail;

class PackageController extends Controller
{   
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function package()
	{
		$name = request()->input('name');
		if (empty($name))
			return View('package.home', [ 'packages' => PackageName::all() ]);

		$releases = DB::table('package_release')
							->where('pkgname', $name)
							->orderBy('id', 'desc')
							->get();
		
		return View('package.releases', compact('name', 'releases'));
	}

	public function addPackage()
	{
		if (request()->isMethod('POST'))
		{
			$data = request()->input('Data');
			if (!isset($data))
				return redirect()->back()->with('error', '参数错误');

			$a = array();
			foreach (explode(',', $data['dist']) as $u)
			{
				$u = trim($u);
				if (!empty($u))
					$a[] = $u;
			}
			$data['dist'] = implode(', ', $a);

			$row = new PackageName;
			$row->name = $data['name'];
			$row->note = $data['note'];
			$row->dist = $data['dist'];

			if (!$row->save())
				return redirect()->back()->with('error', '数据库写入失败');

			return redirect()->back()->with('success', '软件'.$data['name'].'创建成功');;
		}

		return redirect()->back();
	}

	public function getPackageInfo()
	{
		$name = request()->input('name');
		$info = PackageName::find($name);
		return json_encode($info);
	}

	public function updatePackage()
	{
		$name = request()->input('name');
		if (empty($name))
			return redirect()->back()->with('error', '参数错误');

		$row = PackageName::find($name);
		if (!$row)
			return redirect()->back()->with('error', $name.'已不存在');

		$note = request()->input('note');
		if (empty($note))
			$note = '';

		$dist = request()->input('dist');
		if (empty($note))
			$dist = '';

		$a = array();
		foreach (explode(',', $dist) as $u)
		{
			$u = trim($u);
			if (!empty($u))
				$a[] = $u;
		}
		$dist = implode(', ', $a);

		$row->note = $note;
		$row->dist = $dist;
		$row->save();

		return redirect()->back()->with('success', $name.'修改成功');;
	}

	public function deletePackage()
	{
		$name = request()->input('name');
		if (empty($name))
			return redirect()->back()->with('error', '参数错误');

		$count = DB::table('package_release')->where('pkgname', $name)->count();
		if ($count > 0)
			return redirect()->back()->with('error', $name.'有版本发布');

		PackageName::destroy($name);
		return redirect()->back()->with('success', '软件'.$name.'删除成功');
	}

	public function getReleaseInfo()
	{
		$id = request()->input('id');
		$release = PackageRelease::find($id);
		return json_encode($release);
	}

	public function addRelease()
	{
		$name = request()->input('name');
		$version = request()->input('version');
		$note = request()->input('note');
		$test = request()->input('test');

		if (empty($name) || empty($version))
			return redirect()->back()->with('error', '参数错误');

		$count = DB::table('package_release')->where(['pkgname' => $name, 'version' => $version])->count();
		if ($count > 0)
			return redirect()->back()->with('error', $name.'-'.$version.'已经存在');

		if (empty($note))
			$note = '';

		$release = new PackageRelease;
		$release->pkgname = $name;
		$release->version = $version;
		$release->note = $note;
		$release->test = $test;
		$release->time = date('Y-m-d H:i:s', time());
		$release->save();

		$pkg = PackageName::find($name);
		if ($pkg && !empty($pkg->dist))
		{
			// 发送内容
			$content = $note;
			// 纯文本发送
			Mail::raw($content, function ($message) use ($release, $pkg)
			{
				$message->from('noreply@exands.cn', '');
				$message->subject('软件发布:'.$release->pkgname.'-'.$release->version);
				foreach (explode(',', $pkg->dist) as $u)
					$message->to(trim($u));
			});
		}

		return redirect()->back()->with('success', $name.'-'.$version.'添加成功');
	}

	public function updateRelease()
	{
		$id = request()->input('id');
		$note = request()->input('note');
		$test = request()->input('test');

		$release = PackageRelease::find($id);
		if (!$release)
			return redirect()->back()->with('error', '版本已经不存在');

		if (empty($note))
			$note = '';

		$release->note = $note;
		$release->test = $test;
		$release->save();
		return redirect()->back()->with('success', $release->pkgname.'-'.$release->version.'修改成功');
	}

	public function deleteRelease()
	{
		$id = request()->input('id');
		if (empty($id))
			return redirect()->back()->with('error', '参数错误');

		$release = PackageRelease::find($id);
		if (!$release)
			return redirect()->back()->with('error', '版本已经不存在');

		$pattern = $release->pkgname.'-'.$release->version.'.%';
		$count = DB::table('package')->where('filename', 'like', $pattern)->count();
		if ($count > 0)
			return redirect()->back()->with('error', $release->pkgname.'-'.$release->version.'有程序文件存在');

		$release->delete();
		return redirect()->back()->with('success', $release->pkgname.'-'.$release->version.'成功删除');
	}

	public function uploadTest()
	{
		if (!request()->isMethod('POST'))
			return redirect()->back();

		$id = request()->input('id');
		if (empty($id))
			return redirect()->back()->with('error', '参数错误');

		$release = PackageRelease::find($id);
		if (!$release)
			return redirect()->back()->with('error', '版本已经不存在');

		$file = request()->file('source');
		if (!$file->isValid())
			return redirect()->back()->with('error', '文件上传失败');

		$originalName = $file->getClientOriginalName();

		$devtypes = DeviceType::all();
		$pkgtypes = PackageType::all();

		$pkgtype = '';
		$devtype = '';
		$match = false;
		foreach ($devtypes as $vo)
		{   
			foreach ($pkgtypes as $vk)
			{
				$filename = $release->pkgname.'-'.$release->version.'.'.$vo->name.'.'.$vk->name;

				if ($originalName == $filename)
				{
					$match = true;
					$pkgtype = $vk->name;
					$devtype = $vo->name;
					break;
				}
			}
			if ($match)
				break;
		}

		if ($match == false)
			return redirect()->back()->with('error', '文件格式错误');

		$realPath = $file->getRealPath();
		$cksum = md5(file_get_contents($realPath));
		$fuzzyname = 'pkg'.substr($cksum, 24, 32);

		$package = DB::Table('package')->where('filename', $originalName)->first();
		if ($package)
	  		Storage::disk('uploads')->delete($package->fuzzyname);

		// 存入磁盘
		$result = Storage::disk('uploads')->put($fuzzyname, file_get_contents($realPath));
		if ($result == false || $result == 0)
			return redirect()->back()->with('error', '文件写入失败');

		$result = Package::updateOrCreate(array('filename' => $originalName), array('time' => date('Y-m-d H:i:s', time()), 'pkgname' => $release->pkgname, 'pkgtype' => $pkgtype, 'devtype' => $devtype, 'cksum' => $cksum, 'fuzzyname' => $fuzzyname));
		if (!$result)
			return redirect()->back()->with('error', '文件更新数据库失败');

		return redirect()->back()->with('success', '文件上传成功');
	}

	public function upload()
	{
		if (!request()->isMethod('POST'))
			return redirect()->back();

		$id = request()->input('id');
		if (empty($id))
			return redirect()->back()->with('error', '参数错误');

		$release = PackageRelease::find($id);
		if (!$release)
			return redirect()->back()->with('error', '版本已经不存在');

		$file = request()->file('source');
		if (!$file->isValid())
			return redirect()->back()->with('error', '文件上传失败');

		$originalName = $file->getClientOriginalName();

		$devtypes = DeviceType::all();
		$pkgtypes = PackageType::all();

		$pkgtype = '';
		$devtype = '';
		$match = false;
		foreach ($devtypes as $vo)
		{   
			foreach ($pkgtypes as $vk)
			{
				$filename = $release->pkgname.'-'.$release->version.'.'.$vo->name.'.'.$vk->name;

				if ($originalName == $filename)
				{
					$match = true;
					$pkgtype = $vk->name;
					$devtype = $vo->name;
					break;
				}
			}
			if ($match)
				break;
		}

		if ($match == false)
			return redirect()->back()->with('error', '文件格式错误');

		$realPath = $file->getRealPath();
		$cksum = md5(file_get_contents($realPath));
		$fuzzyname = 'pkg'.substr($cksum, 24, 32);

		$package = DB::Table('package')->where('filename', $originalName)->first();
		if ($package)
	  		Storage::disk('uploads')->delete($package->fuzzyname);

		// 存入磁盘
		$result = Storage::disk('uploads')->put($fuzzyname, file_get_contents($realPath));
		if ($result == false || $result == 0)
			return redirect()->back()->with('error', '文件写入失败');

		$result = Package::updateOrCreate(array('filename' => $originalName), array('time' => date('Y-m-d H:i:s', time()), 'pkgname' => $release->pkgname, 'pkgtype' => $pkgtype, 'devtype' => $devtype, 'cksum' => $cksum, 'fuzzyname' => $fuzzyname));
		if (!$result)
			return redirect()->back()->with('error', '文件更新数据库失败');

		return redirect()->back()->with('success', '文件上传成功');
	}

	public function displayFiles()
	{
		$name = request()->input('name');
		$id = request()->input('release');
		$devices = DevicePackage::deviceNumByPackage();

		if (empty($id))
		{
			if (empty($name))
			{
				$packages = Package::all();
				$release = '';
				return View('package.packages', compact('packages', 'devices', 'release'));
			}
			else
			{
				$packages = DB::table('package')->where('pkgname', $name)->get();
				return View('package.packages', compact('name', 'packages', 'devices'));
			}
		}

		$release = PackageRelease::find($id);
		if (!$release)
			return redirect()->back()->with('error', '该版本已经不存在');

		$devtypes = DeviceType::all();
		$pkgtypes = PackageType::all();

		$packages = array();
		foreach (Package::all() as $package)
		{
			$match = false;
			foreach ($devtypes as $devtype)
			{   
				foreach ($pkgtypes as $pkgtype)
				{
					$filename = $release->pkgname.'-'.$release->version.'.'.$devtype->name.'.'.$pkgtype->name;

					if ($package->filename == $filename)
					{
						$match = true;
						break;
					}
				}
				if ($match)
					break;
			}

			if ($match)
				$packages[] = $package;
		}

		return View('package.packages', compact('name', 'release', 'packages', 'devices'));
	}

	// 应用软件版本删除
	public function deletePackageFile()
	{
		$id = request()->input('id');
		if (empty($id))
			return redirect()->back()->with('error', '参数错误');

		$row = Package::find($id);

		$device = \DB::table('device_package')
						->where('package_id', '=', $id)
						->get();

		if (!$device->isEmpty()) {
			return redirect()->back()->with('error', $row->filename.'有设备在使用');
		}

		if (!isset($row))
			return redirect()->back()->with('error', '文件已经不存在');;

		if ($row->delete())
		{
			Storage::disk('uploads')->delete($row->fuzzyname);
			return redirect()->back()->with('success', '删除成功');
		}

		return redirect()->back()->with('error', '删除失败');
	}

	public function checkPackageFile()
	{
		$id = request()->input('id');
		if (empty($id))
			return redirect()->back()->with('error', '参数错误');

		$package = Package::find($id);
		if (!$package)
			return redirect()->back()->with('error', '文件已经不存在');;

		$package->test = 0;
		$package->save();

		return redirect()->back();
	}

	public function uncheckPackageFile()
	{
		$id = request()->input('id');
		if (empty($id))
			return redirect()->back()->with('error', '参数错误');

		$package = Package::find($id);
		if (!$package)
			return redirect()->back()->with('error', '文件已经不存在');;

		$package->test = 1;
		$package->save();

		return redirect()->back();
	}

	public function upgrade()
	{
		if (request()->isMethod('POST'))
		{
			$id = request()->input('id');
			$to = request()->input('to');

			if ($id != $to)
			{
				DevicePackage::upgradePackages($id, $to);
			}
		}

		return redirect()->back();
	}

	public function getPackageList()
	{
		$pkgname = request()->input('pkgname');
		$pkgtype = request()->input('pkgtype');
		$devtype = request()->input('devtype');
		$packages = Package::packages($pkgname, $pkgtype, $devtype);
		
		return json_encode($packages);
	}
}
