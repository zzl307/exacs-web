<?php

namespace App\Http\Controllers;

use App\Device;
use App\DevicePackage;
use App\DeviceTag;
use App\DeviceLog;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	// API
	public function getDeviceInfo()
	{
		$device_id = request()->input('device_id');
		$device = Device::device($device_id);
		return json_encode($device);
	}

	public function getDevicePackageInfo()
	{
		$devpkg_id = request()->input('devpkg_id');
		$devpkg = DevicePackage::find($devpkg_id);
		return json_encode($devpkg);
	}

	public function home(Device $device)
	{
		$devs = array();
		$devs[''] = DB::table('device')->where('launcher_id', 0)->count();
		foreach (Device::select('devtype', DB::raw('COUNT(device_id) as count'))->groupBy('devtype')->get() as $vo) {
			if (!empty($vo->devtype)) {
				$devs[$vo->devtype] = $vo->count;
			}
		}
		$pkgs = array();
		foreach (DevicePackage::select('package_name', DB::raw('COUNT(device_id) as count'))->groupBy('package_name')->get() as $vo) {
			$pkgs[$vo->package_name] = $vo->count;
		}
		$tags = array();
		foreach (DeviceTag::select('tag', DB::raw('COUNT(device_id) as count'))->groupBy('tag')->get() as $vo) {
			$tags[$vo->tag] = $vo->count;
		}

		// 设备类型
		$deviceType = \App\DeviceType::all();

		// cpu报警
		$deviceCpuAlarm = $device->getCpuAlarm();

		return View('device.devices', compact('devs', 'pkgs', 'tags', 'deviceType', 'deviceCpuAlarm'));
	}

	public function search(Device $device)
	{
		$data = request()->all();

		if (empty($data['key'])) {
			$data['key'] = '';
		}

		if (!isset($data['list'])) {
			$data['list'] = 15;
		}

		if ($data['list'] == '') {
			$data['list'] = 600;
		}

		$query = "";
		foreach ($data as $key => $val) {
			if (!empty($query)) {
				$query .= '&';
			}
			$query .= $key.'='.$val;
		}

		$key = $data['key'];
		$list = $data['list'];

		if (isset($data['key'])) {
			if (collect(\App\DeviceType::all()->toArray())->contains('name', $data['key'])) {
				$data['devtype'] = $data['key'];
			}
		}

		if (isset($data['devtype'])) {
			$devtype = $data['devtype'];
		} else {
			$devtype = '';
		}

		// 设备类型
		$deviceType = \App\DeviceType::all();

		$devIds = array();
		$total = 0;

		if (empty($key) && empty($devtype)) {	
			foreach (Device::select('device_id')->paginate($list) as $vo) {
				$devIds[] = $vo->device_id;
			}
			$total = Device::count();
		}

		if ($total == 0) {
			if (isset($data['key'])) {
				if (collect(\App\DeviceType::all()->toArray())->contains('name', $data['key'])) {
					$total = Device::select('device_id')
                                ->where('devtype', '=', $data['key'])
                                ->count();

					if ($total > 0) {	
						foreach (Device::select('device_id')->where('devtype', $data['key'])->paginate($list) as $vo) {
							$devIds[] = $vo->device_id;
						}
					}
				}
			}
		}

		if ($total == 0) {	
			$a = explode(':', $key);
			if (count($a) > 1) {
				if ($a[0] == 'launcher') {
					$lchid = $a[1];
					if (!empty($devtype)) {
						$total = Device::select('device_id')
											->where('launcher_id', $lchid)
											->where('devtype', '=', $devtype)
											->count();
						if ($total > 0) {
							foreach (Device::select('device_id')->where('launcher_id', $lchid)->where('devtype', '=', $devtype)->paginate($list) as $vo) {
								$devIds[] = $vo->device_id;
							}		
						}
					} else {
						$total = Device::select('device_id')
											->where('launcher_id', $lchid)
											->count();
						if ($total > 0) {
							foreach (Device::select('device_id')->where('launcher_id', $lchid)->paginate($list) as $vo) {
								$devIds[] = $vo->device_id;
							}		
						}
					}
				} elseif ($a[0] == 'package') {	
					$pkgid = $a[1];
					if (!empty($devtype)) {
						$total = DevicePackage::join('device', 'device_package.device_id', '=', 'device.device_id')
												->select('device_package.device_id')
												->where('package_id', $pkgid)
												->where('device.devtype', '=', $devtype)
												->count();
						if ($total > 0) {
							foreach (DevicePackage::join('device', 'device_package.device_id', '=', 'device.device_id')->select('device_package.device_id')->where('package_id', $pkgid)->where('device.devtype', '=', $devtype)->paginate($list) as $vo) {
								$devIds[] = $vo->device_id;
							}
						}
					} else {
						$total = DevicePackage::select('device_id')
												->where('package_id', $pkgid)
												->count();
						if ($total > 0) {
							foreach (DevicePackage::select('device_id')->where('package_id', $pkgid)->paginate($list) as $vo) {
								$devIds[] = $vo->device_id;
							}
						}
					}
					
				}
			}
		}

		if ($total == 0) {	
			if (!empty($devtype)) {
				$total = Device::select('device_id')
                                ->where('device_id', $key)
                                ->where('devtype', '=', $devtype)
                                ->count();

				if ($total > 0) {	
					foreach (Device::select('device_id')->where('device_id', $key)->where('devtype', '=', $devtype)->paginate($list) as $vo) {
						$devIds[] = $vo->device_id;
					}
				}
			} else {
				$total = Device::select('device_id')
                                ->where('device_id', $key)
                                ->count();

				if ($total > 0) {	
					foreach (Device::select('device_id')->where('device_id', $key)->paginate($list) as $vo) {
						$devIds[] = $vo->device_id;
					}
				}
			}
		}

		if ($total == 0) {	
			if (!empty($devtype)) {
				$total = DevicePackage::join('device', 'device_package.device_id', '=', 'device.device_id')
											->where('package_name', $key)
											->where('devtype', '=', $devtype)
											->count();
				if ($total > 0) {	
					foreach (DevicePackage::join('device', 'device_package.device_id', '=', 'device.device_id')->select('device_package.device_id')->where('package_name', $key)->where('devtype', '=', $devtype)->paginate($list) as $vo) {
						$devIds[] = $vo->device_id;
					}
				}
			} else {
				$total = DevicePackage::where('package_name', $key)
				                            ->count();

				if ($total > 0) {	
					foreach (DevicePackage::select('device_id')->where('package_name', $key)->paginate($list) as $vo) {
						$devIds[] = $vo->device_id;
					}
				}
			}
			
		}

		if ($total == 0) {	
			if (!empty($devtype)) {
				$total = Device::where('ip_address', 'like', '%'.$key.'%')
									->where('devtype', '=', $devtype)
									->count();

				if ($total > 0) {
					foreach (Device::select('device_id')->where('ip_address', 'like', '%'.$key.'%')->where('devtype', '=', $devtype)->paginate($list) as $vo) {
						$devIds[] = $vo->device_id;
					}
				}
			} else {
				$total = Device::where('ip_address', 'like', '%'.$key.'%')
				                    ->count();

				if ($total > 0) {
					foreach (Device::select('device_id')->where('ip_address', 'like', '%'.$key.'%')->paginate($list) as $vo) {
						$devIds[] = $vo->device_id;
					}
				}
			}
			
		}

		if ($total == 0) {
			if (!empty($devtype)) {
				$total = Device::where('devinfo', 'like', '%'.$key.'%')
				                    ->where('devtype', '=', $devtype)
				                    ->count();
				if ($total > 0) {
					foreach (Device::select('device_id')->where('devinfo', 'like', '%'.$key.'%')->where('devtype', '=', $devtype)->paginate($list) as $vo) {
						$devIds[] = $vo->device_id;
					}
				}
			} else {
				$total = Device::where('devinfo', 'like', '%'.$key.'%')
				                    ->count();
				if ($total > 0) {
					foreach (Device::select('device_id')->where('devinfo', 'like', '%'.$key.'%')->paginate($list) as $vo) {
						$devIds[] = $vo->device_id;
					}
				}
			}
			
		}

		if ($total == 0) {
			if (!empty($devtype)) {
				$total = DeviceTag::join('device', 'device_tag.device_id', '=', 'device.device_id')
										->select('device_tag.device_id')
										->where('tag', 'like', '%'.$key.'%')
										->where('devtype', '=', $devtype)
										->count();
				if ($total > 0) {
					foreach (DeviceTag::join('device', 'device_tag.device_id', '=', 'device.device_id')->select('device_tag.device_id')->where('tag', 'like', '%'.$key.'%')->where('devtype', '=', $devtype)->paginate($list) as $vo) {
						$devIds[] = $vo->device_id;
					}
				}
			} else {
				$total = DeviceTag::select('device_id')
										->where('tag', 'like', '%'.$key.'%')
						                ->count();
				if ($total > 0) {
					foreach (DeviceTag::select('device_id')->where('tag', 'like', '%'.$key.'%')->paginate($list) as $vo) {
						$devIds[] = $vo->device_id;
					}
				}
			}
			
		}

		// cpu报警
		$deviceCpuAlarm = $device->getCpuAlarm();

		$devices = Device::devices($devIds);

		$paginator = new LengthAwarePaginator($devices, $total, $list, null, [
			'path' => url('devices/search?'.$query),
			'pageName' => 'page',
		]);

		return View('device.devices', compact('data', 'devices', 'total', 'paginator', 'deviceType', 'deviceCpuAlarm'));
	}

	// 设备配置导出
	public function DerviceExport()
	{
		$data = request()->all();

		if (array_key_exists('key', $data)) {
			$key = $data['key'];
			if (empty($key)) {
				$devices = Device::devices();
				$key = '';
				$this->export($devices);
			}

			$mac = $key;

			// 检查是否是mac
			$mac = strtolower($mac);
			$mac = str_replace(":", "", $mac);
			$mac = str_replace("-", "", $mac);

			if (preg_match('/^[0-9a-f]{12}$/', $mac)) {
				$key = $mac;
				$devices = array();
				$device = Device::device($mac);
				if ($device)
					$devices[$mac] = $device;
				$this->export($devices);
			}

			$count = DB::table('device_type')->where('name', $key)->count();
			if ($count > 0) {
				$devIds = array();
				foreach (DB::table('device')->where('devtype', $key)->get() as $vo)
				{
					$devIds[] = $vo->device_id;
				}
				$devices = Device::devices($devIds);
				$this->export($devices);
			}

			$count = DB::table('package_name')->where('name', $key)->count();
			if ($count > 0) {
				$devIds = array();
				foreach (DB::table('device_package')->where('package_name', $key)->get() as $vo)
				{
					$devIds[] = $vo->device_id;
				}
				$devices = Device::devices($devIds);
				$this->export($devices);
			}

			$rows = Device::select('device_id')->where('ip_address', $key)->get();
			if (count($rows) > 0) {
				$devIds = array();
				foreach ($rows as $vo)
				{
					$devIds[] = $vo->device_id;
				}
				$devices = Device::devices($devIds);
				$this->export($devices);
			}

			$devices = Device::devicesByTag($key);
			$this->export($devices);
		} elseif (array_key_exists('launcher', $data)) {
			$lchid = $data['launcher'];
			$devices = Device::devicesByLauncher($lchid);
			$key = '';
			$this->export($devices);
		} elseif (array_key_exists('package', $data)) {
			$pkgid = $data['package'];
			$devices = Device::devicesByPackage($pkgid);
			$key = '';
			$this->export($devices);
		}
	}

	public function export($data)
	{	
		$content = [ 0 =>
			['设备号', '设备类型', '设备信息', '设备IP', '设备标签', '状态']
		];

		foreach ($data as $device)
		{
			$a = array();
			$a[] = $device['device_id'];
			$a[] = $device['device_type'];
			$a[] = $device['device_info'];
			$a[] = $device['ip_address'];
			$a[] = implode(',', $device['tags']);
			if (\App\Device::isDisabled($device)) {
				$a[] = '禁用';
			} elseif (\App\Device::isExpired($device)) {
				$a[] = '过期';
			} elseif (!\App\Device::isOnline($device)) {
				$a[] = '离线';
			} elseif ($device['status_code'] == 0) {
				$a[] = '未运行';
			} elseif ($device['status_code'] == 200) {
				$a[] = '配置中';
			} elseif ($device['status_code'] == 426) {
				$a[] = '升级中';
			} elseif ($device['status_code'] == 428) {
				$a[] = '执行脚本';
			} else {
				$a[] = '异常';
			}
			
			$content[] = $a;
		}

        Excel::create('设备配置-'.date('YmdHis', time()), function ($excel) use ($content) {
	            $excel->sheet('score', function ($sheet) use ($content) { $sheet->rows($content);
            });
        })->export('xls');
	}

	public function addDevice()
	{
		if (request()->isMethod('POST'))
		{
			$device_id = request()->input('device_id');
			$device_id = strtolower($device_id);
			$device_id = str_replace(":", "", $device_id);
			$device_id = str_replace("-", "", $device_id);

			if (!preg_match('/^[0-9a-f]{12}$/', $device_id))
				return redirect()->back()->with('error', '设备号格式错误');

			if (Device::find($device_id))
				return redirect('devices/search?key='.$device_id)->with('error', '设备'.$device_id.'已经存在');

			$devtype = request()->input('devtype');
			$dlserver_id = request()->input('dlserver_id');
			$expire_time = request()->input('expire_time');
			$tags = request()->input('tags');

			$row = new Device;
			$row->device_id = $device_id;
			if (!empty($devtype))
				$row->devtype = $devtype;
			$row->dlserver_id = $dlserver_id;
			$row->create_time = date('Y-m-d H:i:s', time());
			$row->config_time = date('Y-m-d H:i:s', time());
			if (!empty($expire_time))
				$row->expire_time = $expire_time;
			$row->save();

			DB::table('device_tag')->where('device_id', $device_id)->delete();
			if (!empty($tags))
			{
				$tags = explode(',', $tags);
				foreach ($tags as $tag)
				{
					$row = new DeviceTag;
	   				$row->device_id = $device_id;
	   				$row->tag = trim($tag);
					$row->save();
				}
			}

			DeviceLog::logit($device_id, '添加'.$device_id.'设备配置');

			return redirect('devices/search?key='.$device_id)->with('success', $device_id.'添加成功');
		}

		return redirect()->back();
	}

	public function updateDevice()
	{
		if (request()->isMethod('POST'))
		{
			$device_id = request()->input('device_id');
			$disabled = request()->input('disabled');
			$launcher_id = request()->input('launcher_id');
			$launcher_immune = request()->input('launcher_immune');
			$dlserver_id = request()->input('dlserver_id');
			$expire_time = request()->input('expire_time');
			$tags = request()->input('tags');

			$row = Device::find($device_id);
			if ($row == null) {
				return redirect()->back()->with('error', '设备已经不存在');
			}
			$devtype = request()->input('devtype');
			$vtund = request()->input('device_disabled');

			$row->config_time = date('Y-m-d H:i:s', time());
			$row->disabled = $disabled;
			$row->launcher_id = $launcher_id;
			$row->launcher_immune = $launcher_immune;
			$row->dlserver_id = $dlserver_id;
			$row->vtund = $vtund;
			if (!empty($expire_time))
				$row->expire_time = $expire_time;

			$row->save();

			if (empty($tags))
				$tags = '';

			$tags = explode(',', $tags);
			foreach ($tags as $tag)
				$newtags[] = trim($tag);

			$oldtags = DeviceTag::tags($device_id);

			foreach (array_diff($oldtags, $newtags) as $tag)
			{
				DB::table('device_tag')->where(['device_id' => $device_id, 'tag' => $tag])->delete();
			}

			foreach (array_diff($newtags, $oldtags) as $tag)
			{
				$row = new DeviceTag;
				$row->device_id = $device_id;
				$row->tag = trim($tag);
				$row->save();
			}

			DeviceLog::logit($device_id, '修改'.$device_id.'设备配置');

			return redirect()->back()->with('success', '设备配置修改成功');
		}

		return redirect()->back();
	}

	public function deleteDevice()
	{
		$device_id = request()->input('device_id');

		\DB::table('device')
		        ->where('device_id', '=', $device_id)
		        ->delete();

		\DB::table('device_package')
		        ->where('device_id', '=', $device_id)
		        ->delete();

		\DB::table('device_tag')
		        ->where('device_id', '=', $device_id)
		        ->delete();

		\DB::table('device_job_exec')
				->where('device_id', '=', $device_id)
		        ->delete();

		DeviceLog::logit($device_id, '删除'.$device_id.'设备配置');

		return redirect()->back()->with('success', '成功删除设备' . $device_id);
	}

	// 设备批量删除
	public function batchDeleteDevice()
	{	
		$id = request()->input('device_id');
		$device_id = explode(',' , $id);
        DB::table('device')
	        ->whereIn('device_id', $device_id)
	        ->delete();

		DB::table('device_package')
		        ->whereIn('device_id', $device_id)
		        ->delete();

		DB::table('device_tag')
		        ->whereIn('device_id', $device_id)
		        ->delete();    

	    DeviceLog::logit($device_id, '批量删除设备配置'.$id);

	    return redirect()->back()->with('success', '成功删除设备' . $id);
	}

	public function addPackage()
	{
		if (request()->isMethod('POST'))
		{
			$device_id = request()->input('device_id');
			$pkgname = request()->input('pkgname');
			$pkgtype = request()->input('pkgtype');
			$pkgid = request()->input('pkgid');
			$config = request()->input('config');

			$count = DB::table('device_package')->where(['device_id' => $device_id, 'package_name' => $pkgname])->count();
			if ($count > 0)
				return redirect()->back()->with('error', '设备'.$device_id.'已经配置运行'.$pkgname);

			if (empty($config))
				$config = '<conf/>';

			$row = new DevicePackage;
			$row->device_id = $device_id;
			$row->package_name = $pkgname;
			$row->package_type = $pkgtype;
			$row->package_id = $pkgid;
			$row->config = $config;
			$row->save();

			DeviceLog::logit($device_id, '配置运行'.$pkgname);

			return redirect()->back()->with('success', '设备'.$device_id.'成功配置运行'.$pkgname);
		}

		return redirect()->back();
	}

	public function editPackage()
	{
		if (request()->isMethod('POST'))
		{
			$devpkg_id = request()->input('devpkg_id');
			$pkgtype = request()->input('pkgtype');
			$pkgid = request()->input('pkgid');
			$config = request()->input('config');

			$row = DevicePackage::find($devpkg_id);
			if (!$row)
				return redirect()->back()->with('error', '设备的应用配置已经不存在');

			$row->package_type = $pkgtype;
			$row->package_id = $pkgid;
			$row->config = $config;
			$row->save();

			DeviceLog::logit($row->device_id, $row->package_name.'配置修改');

			return redirect()->back()->with('success', '设备的应用配置修改成功');
		}

		return redirect()->back();
	}

	public function deletePackage()
	{
		$id = request()->input('id');
		$devpkg = DevicePackage::find($id);
		if (!$devpkg)
			return redirect()->back()->with('error', '设备的应用配置已经不存在');
		$devpkg->delete();

		DeviceLog::logit($devpkg->device_id, $devpkg->pkname.'配置删除');

		return redirect()->back()->with('success', '设备的应用配置删除成功');
	}

	public function logs()
	{
		$data = request()->all();
		if (empty($data)) {
			$tables = DB::select("SELECT DATA_LENGTH+INDEX_LENGTH as data_bytes, table_name FROM information_schema.TABLES where TABLE_SCHEMA='exacs' AND table_name LIKE 'device_log_%' ORDER BY table_name DESC");
			$stats = array();
			$stats['total'] = 0;
			foreach ($tables as $vo) {
				$row = DB::select("SELECT COUNT(*) as count FROM ".$vo->table_name);
				$a = array();
				$a['tbname'] = $vo->table_name;
				$a['bytes'] = $vo->data_bytes;
				$a['records'] = $row[0]->count;

				$stats['total'] += $vo->data_bytes;
				$stats['tables'][] = $a;
			}
			
			return View('device.logs', compact('stats'));
		}

		if (empty($data['date'])) {
			$data['date'] = date('Y-m-d', time());
		}

		$date = $data['date'];
		$device_id = $data['device_id'];

		$cond = 'true';

		if (!empty($device_id)) {
			$cond .= ' and device_id = "'.$device_id.'"';
		}

		if (!empty($data['level'])) {
			$cond .= ' and level = "'.$data['level'].'"';
		}

		$tbname = 'device_log'.'_'.str_replace('-', '', $data['date']);
		$tables = DB::select("SHOW TABLES LIKE '".$tbname."'");

		if (count($tables) == 0) {
			return View('device.logs', compact('data'));
		}

		$logs = DB::table($tbname)
		                ->whereRaw($cond)
		                ->orderBy('id', 'desc')
		                ->paginate(20);

		return View('device.logs', compact('data', 'logs'));
	}

	public function deleteLogs()
	{
		$tbname = request()->input('tbname');
		if (!empty($tbname))
			DB::select('drop table '.$tbname);

		return redirect()->back();
	}

	public function batchSetLauncher()
	{
		if (request()->isMethod('POST'))
		{
			$devices = explode(',', request()->input('devices'));
			$launcher_id = request()->input('launcher_id');

			foreach ($devices as $device_id)
			{
				$row = Device::find($device_id);
				if ($row)
				{
					$row->launcher_id = $launcher_id;
					$row->save();

					DeviceLog::logit($device_id, '批量更改启动程序'.request()->input('devices'));
				}
			}
		}

		return redirect()->back();
	}

	public function batchSetPackage()
	{
		if (request()->isMethod('POST'))
		{
			$devices = explode(',', request()->input('devices'));
			$pkgname = request()->input('pkgname');
			$pkgtype = request()->input('pkgtype');
			$pkgid = request()->input('pkgid');
			$config_set = request()->input('config_set');
			$config = request()->input('config');

			foreach ($devices as $device_id)
			{
				$row = DevicePackage::where(['device_id' => $device_id, 'package_name' => $pkgname])->first();
				if ($row)
				{
					$row->package_id = $pkgid;
					$row->package_type = $pkgtype;
					if ($config_set == "on")
					{
						$row->config = '<conf/>';
						if (!empty($config))
							$row->config = $config;
					}
				}
				else
				{
					$row = new DevicePackage;
					$row->device_id = $device_id;
					$row->package_name = $pkgname;
					$row->package_type = $pkgtype;
					$row->package_id = $pkgid;
					$row->config = '<conf/>';
					if (!empty($config))
						$row->config = $config;
				}
				$row->save();

				DeviceLog::logit($device_id, '批量修改应用软件'.$pkgname);
			}
		}

		return redirect()->back();
	}

	// 设备脚本配置
	public function deviceExec()
	{	
		if (request()->isMethod('POST')) {
			$data = request()->all();

			$device = \DB::table('device_job')
								->insert([
									'name' => $data['name'],
									'content' => str_replace("\r\n", "\n", $data['content']),
									'time' => date('Y-m-d H:i:s', time())
								]);

			if ($device) {
				return redirect()->back()->with('success', '设备脚本配置添加成功');
			} else {
				return redirect()->back()->with('error', '设备脚本配置添加失败');
			}					

		}

		$deviceExec = \DB::table('device_job')
						->get();

		return View('device/deviceExec', compact('deviceExec'));
	}

	// 修改设备脚本配置
	public function deviceExecEdit()
	{
		if (request()->isMethod('POST')) {
			$data = request()->all();

			$device = \DB::table('device_job')
								->where('id', '=', $data['id'])
								->update([
									'name' => $data['name'],
									'content' => str_replace("\r\n", "\n", $data['content']),
									'time' => date('Y-m-d H:i:s', time())
								]);

			if ($device) {
				return redirect()->back()->with('success', '修改设备脚本配置成功');
			} else {
				return redirect()->back()->with('error', '修改设备脚本配置失败');
			}
		}

		$data = request()->all();
		$deviceExec = \DB::table('device_job')
								->where('id', '=', $data['id'])
								->first();
		
		return json_encode($deviceExec);
	}

	// 设备配置详情
	public function deviceExecShow($id)
	{
		$device = \DB::table('device_job')
								->join('device_job_exec', 'device_job_exec.job_id', '=', 'device_job.id')
								->where('device_job.id', '=', $id)
								->get();

		$device_exec_content = \DB::table('device_job')
										->where('id', '=', $id)
										->get();

		return view('device/deviceExecShow', compact('device', 'device_exec_content'));
	}

	// 删除脚本配置
	public function execDelete()
	{
		$data = request()->all();

		$exec = \DB::table('device_job_exec')
								->where('job_id', '=', $data['id'])
								->get();

		if ($exec !== '') {
			return redirect()->back()->with('warning', '脚本有设备运行');
		}

		$deviceExec = \DB::table('device_job')
								->where('id', '=', $data['id'])
								->delete();

		if ($deviceExec) {
			return redirect()->back()->with('success', '删除设备脚本配置成功');
		} else {
			return redirect()->back()->with('error', '删除设备脚本配置失败');
		}
	}

	// 添加设备脚本
	public function addDeviceExec()
	{
		if (request()->isMethod('POST')) {
			$data = request()->all();

			$device_id = explode(',', $data['device_id']);
			foreach ($device_id as $vo) {
				$device_exec = \DB::table('device_job_exec')
										->where('device_id', '=', $vo)
										->get();

				if ($device_exec->isEmpty()) {
					$device = \DB::table('device_job_exec')
										->insert([
											'device_id' => $vo,
											'job_id' => $data['exec'],
											'status' => $data['disabled'],
											'time' => date('Y-m-d H:i:s', time())
										]);
					
				} else {
					$device = \DB::table('device_job_exec')
										->where('device_id', '=', $vo)
										->update([
											'job_id' => $data['exec'],
											'status' => $data['disabled'],
											'time' => date('Y-m-d H:i:s', time())
										]);
				}
			}

			if ($device) {
				return redirect()->back()->with('success', '设备运行脚本添加成功');
			} else {
				return redirect()->back()->with('error', '设备运行脚本添加失败');
			}
			
		}
		$data = request()->all();

		$device_id = explode(',', $data['device_id']);
		$device_exec['deviceExec'] = \DB::table('device_job_exec')
											->whereIn('device_id', $device_id)
											->select('job_id', 'status')
											->distinct()
											->get();

		$device_exec['exec'] = '';
		foreach (\App\Device::getDeviceExec() as $vo) {
			if (isset($device_exec['deviceExec'][0])) {
				if ($vo->id == $device_exec['deviceExec'][0]->job_id) {
					$device_exec['exec'] .= '<option value='.$vo->id.' selected="selected">'.$vo->name.'</option>';
				} else {
					$device_exec['exec'] .= '<option value='.$vo->id.'>'.$vo->name.'</option>';
				}
			} else {
				$device_exec['exec'] .= '<option value='.$vo->id.'>'.$vo->name.'</option>';
			}
			
		}

		return json_encode($device_exec);
	}

	// 删除设备脚本
	public function deviceExecDelete()
	{
		$data = request()->all();

		$deviceExec = \DB::table('device_job_exec')
								->where('id', '=', $data['id'])
								->delete();

		if ($deviceExec) {
			return redirect()->back()->with('success', '删除设备运行脚本成功');
		} else {
			return redirect()->back()->with('error', '删除设备运行脚本失败');
		}
	}
}
