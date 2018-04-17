<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Package extends Model
{
    // 应用软件包管理
    // 设置模型关联表
    protected $table = 'package';

    // 自动维护时间戳
    public $timestamps = false;

    // 指定主键
    protected $primaryKey = 'id';

    // 批量赋值字段
    protected $fillable = ['time', 'filename', 'pkgname', 'pkgtype', 'devtype', 'cksum', 'fuzzyname', 'test'];

    // 设置时间戳
    protected function getDateFormat(){
        return time();
	}

	public static function packages($pkgname, $pkgtype, $devtype)
	{
		$packages = DB::table('package')->where(['pkgname' => $pkgname, 'pkgtype' => $pkgtype, 'devtype' => $devtype])->get();
		return $packages;
	}
}
