<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LaunchServer extends Model
{
    // 设置模型关联表
    protected $table = 'launcher_server';

    // 自动维护时间戳
    public $timestamps = false;

    // 批量赋值字段
    protected $fillable = ['server', 'in_service'];

    // 设置时间戳
    protected function getDateFormat(){
        return time();
	}
}
