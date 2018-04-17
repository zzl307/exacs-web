<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DevicePackage extends Model
{
    // 标签管理
    // 设置模型关联表
    protected $table = 'device_package';

    // 指定主键
    protected $primaryKey = 'id';

    // 自动维护时间戳
    public $timestamps = false;

    // 批量赋值字段
    protected $fillable = ['device_id', 'package_id', 'package_name', 'package_type', 'config'];

    // 设置时间戳
    protected function getDateFormat(){
        return time();
	}

	public static function get($devIds = null)
	{
		if (!isset($devIds))
			return DevicePackage::all();

        if (is_array($devIds)) {
            $a = implode(',', $devIds);
        } else {
            $a = $devIds;
        }
		
		$result = DB::select("select device_package.id, device_package.device_id, device_package.package_id, device_package.package_name, device_package.package_type, device_package.config, package.filename from device_package left join package on device_package.package_id=package.id where device_package.device_id in ('".$a."')");
		return $result;
	}

	public static function deviceNumByPackage()
	{
		$devices = array();
		foreach (DB::table('device_package')->select('package_id', DB::raw('COUNT(device_id) as devices'))->groupBy('package_id')->get() as $vo)
		{
			$devices[$vo->package_id] = $vo->devices;
		}
		return $devices;
	}

	public static function upgradePackages($id, $to)
	{
		DB::table('device_package')->where('package_id', $id)->update(['package_id' => $to]);
	}
}
