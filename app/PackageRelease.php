<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageRelease extends Model
{
    // 应用软件包版本管理
    // 设置模型关联表
    protected $table = 'package_release';

    // 自动维护时间戳
    public $timestamps = false;

    // 指定主键
    protected $primaryKey = 'id';

    // 批量赋值字段
    protected $fillable = ['time', 'pkgname', 'version', 'note'];

    // 设置时间戳
    protected function getDateFormat(){
        return time();
	}
}
