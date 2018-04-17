<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Launcher extends Model
{
	// 启动程序包管理
	// 设置模型关联表
	protected $table = 'launcher';

	// 自动维护时间戳
	public $timestamps = false;

	// 指定主键
	protected $primaryKey = 'id';

	// 批量赋值字段
	protected $fillable = ['time', 'filename', 'devtype', 'cksum', 'fuzzyname', 'test'];

	// 设置时间戳
	protected function getDateFormat(){
		return time();
	}

	public static function launchers($devtype)
	{
		$launchers = DB::table('launcher')->where('devtype', '=', $devtype)->get();
		return $launchers;
	}
}
