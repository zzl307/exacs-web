<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageName extends Model
{
	// 应用软件包管理
	// 设置模型关联表
	protected $table = 'package_name';

	// 自动维护时间戳
	public $timestamps = false;

	// 指定主键
	protected $primaryKey = 'name';
	protected $keyType = 'string';

	// 批量赋值字段
	protected $fillable = ['name', 'note', 'dist'];

	// 设置时间戳
	protected function getDateFormat(){
		return time();
	}
}
