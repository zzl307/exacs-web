<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceTagAttrs extends Model
{
    // 脚本配置
    // 设置模型关联表
    protected $table = 'device_tag_attrs';

    // 指定主键
    protected $primaryKey = 'id';

    // 自动维护时间戳
    public $timestamps = false;
    
    // 批量赋值字段
    protected $fillable = ['name', 'dist'];

    // 设置时间戳
    protected function getDateFormat(){
        return time();
	}
}
