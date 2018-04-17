<?php

use App\AdminRole;
use Illuminate\Database\Seeder;

class AdminRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(AdminRole $adminRole)
    {
        $data = [
        	0 => [
        		'name' => '超级用户',
        		'description' => '超级用户'
        	], 1 => [
        		'name' => '一般用户',
        		'description' => '可以单个设备进行配置'
        	], 2 => [
        		'name' => '用户管理员',
        		'description' => '管理系统的访问用户'
        	], 3 => [
        		'name' => '软件发布员',
        		'description' => '发布软件'
        	], 4 => [
        		'name' => '启动程序开发人员',
        		'description' => ''
        	], 5 => [
        		'name' => '应用软件开发人员',
        		'description' => ''
        	], 6 => [
        		'name' => '启动程序测试人员',
        		'description' => ''
        	], 7 => [
        		'name' => '应用软件测试人员',
        		'description' => ''
        	], 8 => [
        		'name' => '高级用户',
        		'description' => '可以批量进行设备配置升级'
        	], 9 => [
        		'name' => '系统管理员',
        		'description' => ''
        	]
        ];

        foreach ($data as $vo) {
        	$adminRole->name = $vo['name'];
        	$adminRole->description = $vo['description'];

        	$adminRole->save();
        }
    }
}
