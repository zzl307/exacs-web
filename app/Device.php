<?php

namespace App;

use App\Launcher;
use App\Package;
use App\DownloadServer;
use App\DeviceTag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Device extends Model
{
	// 设置模型关联表
	protected $table = 'device';

	// 指定主键
	protected $primaryKey = 'device_id';
	protected $keyType = 'string';

	// 自动维护时间戳
	public $timestamps = false;

	// 设置时间戳
	protected function getDateFormat()
	{
		return time();
	}

	public static function devices($devIds = null)
	{
		$launchers = array();
		foreach (Launcher::all() as $vo)
		{
			$launchers[$vo->id] = $vo;
		}

		$packages = array();
		foreach (Package::all() as $vo)
		{
			$packages[$vo->id] = $vo;
		}

		$dlservers = array();
		foreach (DownloadServer::all() as $vo)
		{
			$dlservers[$vo->id] = $vo;
		}

		$devpkgs = array();
		if (is_array($devIds)) {
			$devicePackage = DevicePackage::whereIn('device_id', $devIds)->get();
		} elseif (empty($devIds)) {
			$devicePackage = DevicePackage::all();
		} else {
			$devicePackage = DevicePackage::where('device_id', '=', $devIds)->get();
		}
		foreach ($devicePackage as $id => $vo)
		{
			if (!isset($devpkgs[$vo->device_id]))
				$devpkgs[$vo->device_id] = array();

			$devpkgs[$vo->device_id][$id] = $vo;
		}

		$devtags = array();
		if (is_array($devIds)) {
			$deviceTag = DB::table('device_tag')->whereIn('device_id', $devIds)->get();
		} elseif (empty($devIds)) {
			$deviceTag = DB::table('device_tag')->get();
		} else {
			$deviceTag = DB::table('device_tag')->where('device_id', '=', $devIds)->get();
		}
		foreach ($deviceTag as $vo)
		{
			if (!isset($devtags[$vo->device_id]))
				$devtags[$vo->device_id] = array();

			$devtags[$vo->device_id][] = $vo->tag;
		}

		$deviceExec = array();
		if (is_array($devIds)) {
			$device_exec = DB::table('device_job_exec')->whereIn('device_id', $devIds)->get();
		} elseif (empty($devIds)) {
			$device_exec = DB::table('device_job_exec')->get();
		} else {
			$device_exec = DB::table('device_job_exec')->where('device_id', '=', $devIds)->get();
		}
		foreach ($device_exec as $vo) {
			if (!isset($deviceExec[$vo->device_id]))
				$deviceExec[$vo->device_id] = array();

			$deviceExec[$vo->device_id][] = $vo;
		}

		$deviceUptimeInfo = array();
		if (is_array($devIds)) {
			$device_uptime_info = DB::table('device_uptime_info')->whereIn('device_id', $devIds)->get();
		} elseif (empty($devIds)) {
			$device_uptime_info = DB::table('device_uptime_info')->get();
		} else {
			$device_uptime_info = DB::table('device_uptime_info')->where('device_id', '=', $devIds)->get();
		}
		foreach ($device_uptime_info as $vo) {
			if (!isset($deviceUptimeInfo[$vo->device_id]))
				$deviceUptimeInfo[$vo->device_id] = array();

			$deviceUptimeInfo[$vo->device_id][] = $vo;
		}

		$sql = "select * from device";
		if (isset($devIds))
		{	
			if (is_array($devIds)) {
				$a = implode("','", $devIds);
			} else {
				$a = $devIds;
			}
			$sql = $sql . " where device_id in ('" . $a . "')";
		}
		$sql = $sql . " order by id";

		$devices = array();
		foreach (DB::select($sql) as $vo)
		{
			$device = array();

			$device['device_id'] = $vo->device_id;
			$device['disabled'] = $vo->disabled;
			$device['device_type'] = $vo->devtype;
			$device['device_info'] = $vo->devinfo;
			$device['ip_address'] = $vo->ip_address;
			$device['launcher_id'] = $vo->launcher_id;
			$device['launcher_immune'] = $vo->launcher_immune;
			$device['dlserver_id'] = $vo->dlserver_id;
			$device['create_time'] = $vo->create_time;
			$device['config_time'] = $vo->config_time;
			$device['expire_time'] = $vo->expire_time;
			$device['update_time'] = $vo->update_time;
			$device['status_code'] = $vo->status_code;
			$device['retry_after'] = $vo->retry_after;
			$device['vtun_disabled'] = $vo->vtund;
			$vtund_id = substr($vo->device_id, 6, 12);
			$vtundIds = [];
			for ($i=0; $i <=2 ; $i++) { 
				$vtundIds[] = substr($vtund_id, $i*2, 2);
			}
			$vtund_ids = [];
			foreach ($vtundIds as $vtundId) {
				$vtund_ids[] = hexdec($vtundId);
			}
			$device['vtund_id'] = '10.'.implode('.', $vtund_ids);

			if ($vo->launcher_id > 0 && !empty($launchers[$vo->launcher_id]))
			{
				$device['launcher'] = $launchers[$vo->launcher_id]->filename;
			} else {
				$device['launcher'] = '';
			}

			if ($vo->dlserver_id > 0 && !empty($dlservers[$vo->dlserver_id]))
			{
				$device['dlserver'] = $dlservers[$vo->dlserver_id]->name;
			}

			$device['packages'] = array();
			if (isset($devpkgs[$vo->device_id]))
			{
				foreach ($devpkgs[$vo->device_id] as $v)
				{
					$pkgid = $v->package_id;
					if ($pkgid == 0) {
						$pkgname = $v->package_name.'-[version].[devtype].'.$v->package_type;
					} elseif (isset($packages[$pkgid]->filename)) {
						$pkgname = $packages[$pkgid]->filename;
					}
					$a = array();
					$a['id'] = $v->id;
					$a['package_id'] = $v->package_id;
					$a['package_name'] = $v->package_name;
					$a['package_type'] = $v->package_type;
					$a['package'] = isset($pkgname) ? $pkgname : '';
					$a['config'] = htmlspecialchars($v->config);

					$device['packages'][] = $a;
				}
			}
			ksort($device['packages']);

			$device['tags'] = array();
			if (isset($devtags[$vo->device_id]))
				$device['tags'] = $devtags[$vo->device_id];

			$device['exec'] = array();
			if (isset($deviceExec[$vo->device_id]))
				$device['exec'] = $deviceExec[$vo->device_id];

			$device['uptimeInfo'] = array();
			if (isset($deviceUptimeInfo[$vo->device_id])) 
				$device['uptimeInfo'] = $deviceUptimeInfo[$vo->device_id][0];

			$devices[$vo->device_id] = $device;
		}

		return $devices;
	}

	public static function device($device_id)
	{
		$devIds = array();
		$devIds[] = $device_id;
		$devices = Device::devices($devIds);
		if (!array_key_exists($device_id, $devices))
			return null;

		return $devices[$device_id];
	}

	public static function devicesByTag($tag)
	{	
		$devIds = DeviceTag::devices($tag);

		if (empty($devIds))
			return null;

		return Device::devices($devIds);
	}

	public static function isDisabled($device)
	{
		return ($device['disabled'] > 0);
	}

	public static function isExpired($device)
	{
		$exptime = strtotime($device['expire_time']);
		return ($exptime > 0 && $exptime < time());
	}

	public static function isOnline($device)
	{
		return (strtotime($device['update_time']) + $device['retry_after']) >= time();
	}

	public static function deviceNumByLauncher()
	{
		$devices = array();
		foreach (DB::table('device')->select('launcher_id', DB::raw('COUNT(device_id) as devices'))->groupBy('launcher_id')->get() as $vo)
		{
			$devices[$vo->launcher_id] = $vo->devices;
		}
		return $devices;
	}

	public static function devicesByLauncher($lchid)
	{
		$devIds = array();
		foreach (DB::table('device')->select('device_id')->where('launcher_id', $lchid)->get() as $vo)
			$devIds[] = $vo->device_id;
		return Device::devices($devIds);
	}

	public static function upgradeLaunchers($id, $to)
	{
		DB::table('device')->where('launcher_id', $id)->update(['launcher_id' => $to]);
	}

	public static function devicesByPackage($pkgid)
	{
		$devIds = array();
		foreach (DB::table('device_package')->select('device_id')->where('package_id', $pkgid)->get() as $vo)
			$devIds[] = $vo->device_id;
		return Device::devices($devIds);
	}

	public static function getDeviceExecName($id)
	{
		$deviceExec = \DB::table('device_job')
							->where('id', '=', $id)
							->first();

		return $deviceExec->name;
	}

	public static function getDeviceExec()
	{
		$deviceExec = \DB::table('device_job')
								->get();

		return $deviceExec;
	}

	// cpu报警
	public function scopeGetCpuAlarm()
	{
		$devices = \DB::table('device_uptime_info')
							->select('device_id', 'uptime', 'cpu', 'mem_total', 'mem_free')
							->get();

		$devices_data = [];
		foreach ($this->scopeToArray($devices) as $vo) {
			$vo['mem'] = @intval(($vo['mem_total']-$vo['mem_free'])/$vo['mem_total']*100);
			$devices_data[] = $vo;
		}

		list($under, $above) = collect($devices_data)->partition(function ($item) {
			return $item['cpu'] > 70 or $item['mem'] > 70;
		});

		if ($under) {
			return $under;
		} else {
			return '';
		}
	}

	public static function secToTime($seconds){  
        $result = '00:00:00';  
        if ($seconds > 0) {  
            $hour = floor($seconds/3600);
            $minute = floor(($seconds-3600 * $hour)/60);  
            $second = floor((($seconds-3600 * $hour) - 60 * $minute) % 60);  
            $result = $hour.'小时'.$minute.'分钟'.$second.'秒';  
        }

        return $result;
	}

	public function scopeToArray($obj)
	{
		return json_decode(json_encode($obj), true);
	}
}
