<?php

namespace EzAdmin\Modules\Permission\Services;

use EzAdmin\Base\Service;
use EzAdmin\Modules\Permission\Models\Role;

class RoleService extends Service
{
    /**
     * 获取角色组用户
     *
     * @param  $roleId
     * @return mixed
     */
    public function getAdminerIds($roleId)
    {
        $role = Role::with(['adminers'])->find($roleId);

        $adminerIds = [];
        foreach ($role->adminers as $user) {
            $adminerIds[] = $user->id;
        }

        return $adminerIds;
    }

    /**
     * 是否为超级管理员角色
     *
     * @param $roleId
     */
    public function isSuperRole($roleId)
    {
        return Role::where('id', $roleId)->where('mark', config('permission.super_role_mark'))->exists();
    }
}
