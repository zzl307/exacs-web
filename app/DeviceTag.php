<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DeviceTag extends Model
{
    // 标签管理
    // 设置模型关联表
    protected $table = 'device_tag';

    // 指定主键
    protected $primaryKey = 'id';

    // 自动维护时间戳
    public $timestamps = false;

    // 批量赋值字段
    protected $fillable = ['device_id', 'tag'];

    // 设置时间戳
    protected function getDateFormat(){
        return time();
	}

	public static function tags($device_id = null)
	{
		$result = [];
 		if ($device_id) {
			$result = DB::table('device_tag')->where('device_id', '=', $device_id)->get();
 		} else {
			$result = DB::table('device_tag')->distinct()->get();
 		}

		$tags = [];
		foreach($result as $vo) {
			$tags[] = $vo->tag;
		}
			
		return $tags;
	}

	public static function get($devIds)
	{
		if (!isset($devIds))
			return DeviceTag::orderBy('tag', 'asc')->get();

		if (is_array($devIds)) {
            $a = implode(',', $devIds);
        } else {
            $a = $devIds;
        }

		$result = DB::select("select * from device_tag where device_id in ('".$a."') order by tag asc");
		return $result;
	}

	public static function devices($tag)
	{
		$devices = array();
		foreach (DB::select("select device_id from device_tag where tag like '%".$tag."%'") as $vo)
			$devices[] = $vo->device_id;
		return $devices;
	}
}
