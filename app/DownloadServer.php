<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DownloadServer extends Model
{
	// 设置模型关联表
	protected $table = 'download_server';

	// 自动维护时间戳
	public $timestamps = false;

	// 批量赋值字段
	protected $fillable = ['name', 'server', 'in_service', 'exclude', 'sync_status', 'sync_time'];

	// 设置时间戳
	protected function getDateFormat(){
		return time();
	}
}
