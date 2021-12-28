<?php

namespace EzAdmin\Modules\Permission\Services;

use EzAdmin\Base\Service;
use EzAdmin\Modules\Permission\Models\Adminer;
use EzAdmin\Modules\Permission\Models\Menu;

class AdminerService extends Service
{
    /**
     * @param $userId
     */
    public function getRoleIds($userId)
    {
        $user = Adminer::with(['roles'])->find($userId);

        $roleIds = [];
        foreach ($user->roles as $role) {
            $roleIds[] = $role->id;
        }
        return $roleIds;
    }

    /**
     * @param $userId
     */
    public function getMenus($userId, array $type = [])
    {
        $menus = [];

        $fields = ['id', 'pid', 'type', 'name', 'route', 'icon', 'component', 'mark', 'is_frame', 'is_cache', 'is_visible'];
        if ($this->isSuperAdmin($userId)) {
            $where = [
                ['status', '=', 1],
            ];
            if ($type) {
                $where[] = [
                    function ($query) use ($type) {
                        $query->whereIn('type', $type);
                    },
                ];
            }
            $menus = Menu::where($where)->select($fields)->orderBy('sort', 'desc')->get();
        } else {
            $user = Adminer::find($userId);
            $user->roles()
                 ->with([
                     'menus' => function ($query) use ($fields, $type) {
                         if ($type) {
                             $query->whereIn('type', $type);
                         }
                         $query->select($fields)->orderBy('sort', 'desc');
                     },
                 ])
                 ->get()
                 ->each(function ($role) use (&$menus) {
                     $menus = $role->menus->concat($menus);
                 });
        }
        return collect($menus)->unique('id');
    }

    /**
     * 是否为超级管理员
     *
     * @return bool
     */
    public function isSuperAdmin($userId)
    {
        return Adminer::find($userId)->roles()->where('mark', config('permission.super_role_mark'))->exists();
    }

    /**
     * 判断是否含有权限
     *
     * @param $userId
     * @param $permission
     */
    public function hasPermission($userId, $permission)
    {
        if ($this->isSuperAdmin($userId)) {
            return true;
        }
        $permissionId = Menu::where('status', 1)->where('mark', $permission)->value('id');
        // 没有设置默认有权限
        if (!$permissionId) {
            return true;
        }

        $permissionIds = $this->getMenus($userId)->pluck('id')->toArray();
        if (in_array($permissionId, $permissionIds)) {
            return true;
        }
        return false;
    }
}
