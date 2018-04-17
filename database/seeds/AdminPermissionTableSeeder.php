<?php

use Illuminate\Database\Seeder;

class AdminPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(AdminPermission $adminPermission)
    {
        $data = [
        	0 => [
        		'keyword' => 'system_view',
        		'name' => '系统配置查看',
        		'description' => ''
        	], 1 => [
        		'keyword' => 'system_config',
        		'name' => '系统参数配置',
        		'description' => ''
        	], 2 => [
        		'keyword' => 'launcher_release',
        		'name' => '启动程序版本发布',
        		'description' => ''
        	], 3 => [
        		'keyword' => 'launcher_delete',
        		'name' => '启动程序文件删除',
        		'description' => ''
        	], 4 => [
        		'keyword' => 'launcher_upload',
        		'name' => '启动程序文件上传',
        		'description' => ''
        	], 5 => [
        		'keyword' => 'launcher_test',
        		'name' => '启动程序测试文件上传',
        		'description' => ''
        	], 6 => [
        		'keyword' => 'launcher_validate',
        		'name' => '启动程序测试验证',
        		'description' => ''
        	], 7 => [
        		'keyword' => 'package_release',
        		'name' => '应用程序版本发布',
        		'description' => ''
        	], 8 => [
        		'keyword' => 'package_delete',
        		'name' => '应用程序文件删除',
        		'description' => ''
        	], 9 => [
        		'keyword' => 'package_upload',
        		'name' => '应用程序文件上传',
        		'description' => ''
        	], 10 => [
        		'keyword' => 'package_test',
        		'name' => '应用程序测试文件上传',
        		'description' => ''
        	], 11 => [
        		'keyword' => 'package_validate',
        		'name' => '应用程序测试验证',
        		'description' => ''
        	], 12 => [
        		'keyword' => 'package_upgrade',
        		'name' => '应用程序设备升级',
        		'description' => ''
        	], 13 => [
        		'keyword' => 'launcher_upgrade',
        		'name' => '启动程序设备升级',
        		'description' => ''
        	], 14 => [
        		'keyword' => 'device_config',
        		'name' => '设备配置',
        		'description' => ''
        	], 15 => [
        		'keyword' => 'device_tag_config',
        		'name' => '设备标签配置',
        		'description' => ''
        	], 16 => [
        		'keyword' => 'user_config',
        		'name' => '用户管理',
        		'description' => ''
        	], 0 => [
        		'keyword' => 'device_log_delete',
        		'name' => '设备日志文件删除',
        		'description' => ''
        	]
        ];

        foreach ($data as $vo) {
        	$adminPermission->kwyword = $vo['kwyword'];
        	$adminPermission->name = $vo['name'];
        	$adminPermission->description = $vo['description'];

        	$adminPermission->save();
        }
    }
}
