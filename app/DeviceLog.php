<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceLog extends Model
{
    // 设备日志管理
    // 设置模型关联表
    protected $table = 'device_log';

    // 设置主键
    protected $primaryKey = 'id';

    // 批量赋值字段
    protected $fillable = ['time', 'device_id', 'level', 'log'];

    // 设置时间戳
    protected function getDateFormat()
    {
    	return time();
    }

	public static function logit($device_id, $content)
	{
		$log['time'] = date('Y-m-d H:i:s', time());
		$log['device_id'] = $device_id;
		$log['level'] = 5;
		$log['log'] = \Auth::user()->name.': '.$content;

		$tbname = 'device_log_'.date('Ymd', time());
        try {
            \DB::table($tbname)->insert($log);
        } catch (\Exception $e) {
            if ($e->getCode() == '42S02') {
                abort(404, $tbname.' 不存在');
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
	}
}
    