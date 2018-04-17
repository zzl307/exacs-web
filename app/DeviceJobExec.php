<?php

namespace App;

use App\Job;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Joblist extends Model
{
    // 脚本执行配置
    const STATUS_ON = 0;

    const SATUS_OFF = 1;

    // 设置模型关联表
    protected $table = 'joblist';

    // 设置主键
    protected $primaryKey = 'id';

    // 自动维护时间戳
    public $timestamps = false;

    // 批量复制字段
    protected $fillable = ['device_id', 'job_id', 'status', 'time'];

    // 设置时间戳
    protected function getDateFormat()
    {
    	return time();
    }

    public function getStatus($id = null)
    {
        $arr = [
            self::STATUS_ON => '执行',
            self::SATUS_OFF => '未执行'
        ];
        if ($id !== null) {
            return array_key_exists($id, $arr) ? $arr[$id] : self::SATUS_OFF;
        }

        return $arr;
    }

    public function getDeviceId($id = null)
    {
        $device = DB::table('device')
                    ->where('id', '=', $id)
                    ->first();

        if ($device) {
            $device_id = $device->device_id;
        } else {
            $device_id = '';
        }

        return $device_id;
    }

    public function JobId($id = null)
    {
        $job = Job::find($id);

        $job_name = $job->name;

        return $job_name;
    }
}
