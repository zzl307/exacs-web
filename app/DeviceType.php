<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceType extends Model
{
    // 设备类型

    // 设置模型关联表
    protected $table = 'device_type';

    // 指定主键
    protected $primaryKey = 'id';

    // 自动维护时间戳
    public $timestamps = false;

    // 批量赋值字段
    protected $fillable = ['name'];
}
