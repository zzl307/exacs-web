<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'IndexController@index');

// 登录
Auth::routes();

// 后台首页
Route::get('/home', 'IndexController@index');

// 系统设置
Route::group(['middleware' => 'can:system_view'], function () {
	Route::group(['prefix' => 'config'], function () {
		Route::any('dlserver', 'ConfigController@dlserver');
	});
});

Route::group(['prefix' => 'launcher'], function () {
	Route::any('/', 'LauncherController@home');
	Route::group(['middleware' => 'can:launcher_release'], function () {
		Route::any('release/info', 'LauncherController@getReleaseInfo');
		Route::any('release/add', 'LauncherController@addRelease');
		Route::any('release/update', 'LauncherController@updateRelease');
		Route::any('release/delete', 'LauncherController@deleteRelease');
	});
	Route::group(['middleware' => 'can:launcher_delete'], function () {
		Route::any('delete', 'LauncherController@deleteReleaseFile');
	});
	Route::group(['middleware' => 'can:launcher_validate'], function () {
		Route::any('check', 'LauncherController@checkReleaseFile');
		Route::any('uncheck', 'LauncherController@uncheckReleaseFile');
	});
	Route::group(['middleware' => 'can:launcher_test'], function () {
		Route::any('uploadTest', 'LauncherController@uploadTest');
	});
	Route::group(['middleware' => 'can:launcher_upload'], function () {
		Route::any('upload', 'LauncherController@upload');
	});
	Route::group(['middleware' => 'can:launcher_upgrade'], function () {
		Route::any('upgrade', 'LauncherController@upgrade');
	});
	Route::any('files', 'LauncherController@displayFiles');
	Route::any('getLauncherList', 'LauncherController@getLauncherList');
});
	
Route::group(['prefix' => 'package'], function () {
	Route::any('/', 'PackageController@package');
	Route::group(['middleware' => 'can:package_release'], function () {
		Route::any('add', 'PackageController@addPackage');
		Route::any('info', 'PackageController@getPackageInfo');
		Route::any('update', 'PackageController@updatePackage');
	});
	Route::group(['middleware' => 'can:package_delete'], function () {
		Route::any('delete', 'PackageController@deletePackage');
	});
	Route::group(['middleware' => 'can:package_release'], function () {
		Route::any('release/info', 'PackageController@getReleaseInfo');
		Route::any('release/add', 'PackageController@addRelease');
		Route::any('release/update', 'PackageController@updateRelease');
		Route::any('release/delete', 'PackageController@deleteRelease');
	});
	Route::any('file/list', 'PackageController@displayFiles');
	Route::group(['middleware' => 'can:package_delete'], function () {
		Route::any('file/delete', 'PackageController@deletePackageFile');
	});
	Route::group(['middleware' => 'can:package_validate'], function () {
		Route::any('file/check', 'PackageController@checkPackageFile');
		Route::any('file/uncheck', 'PackageController@uncheckPackageFile');
	});
	Route::group(['middleware' => 'can:package_test'], function () {
		Route::any('uploadTest', 'PackageController@uploadTest');
	});
	Route::group(['middleware' => 'can:package_upload'], function () {
		Route::any('upload', 'PackageController@upload');
	});
	Route::group(['middleware' => 'can:package_upgrade'], function () {
		Route::any('upgrade', 'PackageController@upgrade');
	});
	Route::any('getPackageList', 'PackageController@getPackageList');
});

// 文件上传
Route::group(['prefix' => 'upload'], function () {
	// 文件上传首页
	Route::get('/index', 'UploadController@index')->name('upload.index');
	// 文件上传
	Route::post('/store', 'UploadController@store')->name('upload.store');
	// 文件下载
	Route::get('/download/{filename}', 'UploadController@download')->name('upload.download');
	// 文件删除
	Route::get('/delete/{filename}', 'UploadController@delete')->name('upload.delete');
});

// 设备管理
Route::group(['prefix' => 'devices'], function () {
	// 设备管理
	Route::any('home', 'DeviceController@home');
	Route::any('search', 'DeviceController@search');
	// 设备导出
	Route::any('DerviceExport', 'DeviceController@DerviceExport')->name('DerviceExport');
	// 设备脚本配置
	Route::any('deviceExec', 'DeviceController@deviceExec');
	// 设备脚本详情
	Route::any('deviceExecShow/{id}', 'DeviceController@deviceExecShow');
	// 修改设备脚本配置
	Route::any('deviceExecEdit', 'DeviceController@deviceExecEdit');
	// 删除脚本配置
	Route::any('execDelete', 'DeviceController@execDelete');
	// 添加设备脚本
	Route::any('addDeviceExec', 'DeviceController@addDeviceExec');
	// 删除设备脚本
	Route::any('deviceExecDelete', 'DeviceController@deviceExecDelete');
	// API
	Route::any('getDeviceInfo', 'DeviceController@getDeviceInfo');
	Route::any('getDevicePackageInfo', 'DeviceController@getDevicePackageInfo');
	// 设备日志
	Route::any('logs', 'DeviceController@logs');
	Route::group(['middleware' => 'can:device_log_delete'], function () {
		Route::any('logs/delete', 'DeviceController@deleteLogs');
	});
	// 设备添加修改
	Route::group(['middleware' => 'can:device_config'], function () {
		Route::any('addDevice', 'DeviceController@addDevice');
		Route::any('updateDevice', 'DeviceController@updateDevice');
		Route::any('deleteDevice', 'DeviceController@deleteDevice');
		// 设备批量删除
		Route::any('batchDeleteDevice', 'DeviceController@batchDeleteDevice');
		Route::any('addPackage', 'DeviceController@addPackage');
		Route::any('editPackage', 'DeviceController@editPackage');
		Route::any('deletePackage', 'DeviceController@deletePackage');
		// 批量升级设备的启动程序
		Route::group(['middleware' => 'can:launcher_upgrade'], function () {
			Route::any('batchSetLauncher', 'DeviceController@batchSetLauncher');
			Route::any('batchDelete/{id}', 'ConfigController@deviceBatchDelete');
			
		});
		// 批量升级设备的应用软件
		Route::group(['middleware' => 'can:package_upgrade'], function () {
			Route::any('batchSetPackage', 'DeviceController@batchSetPackage');
			Route::any('devicePackage', 'DeviceController@devicePackage');
		});
	});

	// 标签管理
	Route::group(['middleware' => 'can:device_tag_config'], function () {
		Route::any('tags', 'TagController@home');
		Route::any('tags/detail', 'TagController@detail');
		Route::any('tags/add', 'TagController@add');
		Route::any('tags/edit', 'TagController@edit');
		Route::any('tags/delete', 'TagController@delete');
	});
});

Route::group(['middleware' => 'can:user_config'], function () {
	// 用户管理权限
	Route::group(['prefix' => 'user'], function () {
		Route::any('list', 'UserController@userList');
		Route::any('add', 'UserController@addUser');
		Route::any('resetPassword', 'UserController@resetUserPassword');
		Route::any('getUserRoles', 'UserController@getUserRoles');
		Route::any('setUserRoles', 'UserController@setUserRoles');
		Route::any('delete', 'UserController@deleteUser');
		Route::any('roles', 'UserController@roles');
		Route::any('roles/add', 'UserController@addRole');
		Route::any('role', 'UserController@getRole');
		Route::any('role/update', 'UserController@updateRole');
		Route::any('role/delete', 'UserController@deleteRole');
		Route::any('role/getRolePermissions', 'UserController@getRolePermissions');
		Route::any('role/setRolePermissions', 'UserController@setRolePermissions');
		Route::any('permission', 'UserController@permissions');
		Route::any('permission/add', 'UserController@addPermission');
		Route::any('permission/update', 'UserController@updatePermission');
		Route::any('permission/delete', 'UserController@deletePermission');
	});
});

// 重置密码
Route::any('/resetPassword', 'UserController@resetPassword');

// 文件下载
Route::get('download/{package}', array(
    'as'    => 'download',
    'uses'  => 'SystemController@getPackageDownload'
));

// 实时报警
Route::any('police', 'IndexController@police');
