<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    // 设置模型关联表
    protected $table = 'admin_role';

    // 批量赋值字段
    protected $fillable = ['name', 'description'];

    // 当前角色的所有权限
    public function permissions()
    {
    	return $this->belongsToMany(\App\AdminPermission::class, 'admin_role_permission', 'role_id', 'permission_id')
    				->withPivot([
    					'role_id',
    					'permission_id'
    				]);
    }

    // 给角色赋予权限
    public function grantPermission($permission)
    {
    	return $this->permissions()->save($permission);
    }

    // 取消角色赋予的权限
    public function deletePermission($permission)
    {
    	return $this->permissions()->detach($permission);
    }

    // 判断角色有没有权限
    public function hasPermission($permission)
    {
    	return $this->permissions()->contains($permission);
    }
}
