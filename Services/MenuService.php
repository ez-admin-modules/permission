<?php

namespace EzAdmin\Modules\Permission\Services;

use EzAdmin\Base\Service;
use EzAdmin\Modules\Permission\Models\Menu;
use EzAdmin\Support\Tree;

class MenuService extends Service
{
    /**
     * 获取子角色ID
     *
     * @param  $roleId
     * @param  $withSelf 是否包含本身
     * @return mixed
     */
    public function getChildrenIds($menuId, $withSelf = false)
    {
        $menus = Menu::select(['id', 'pid', 'name'])->get()->toArray();

        return Tree::instance()->init($menus)->getChildrenIds($menuId, $withSelf);
    }

    /**
     * 创建菜单
     *
     * @param array $menus
     * @param mixed $pid     父类的pid
     */
    public function create($menus = [], $pid = 0)
    {
        $allow = array_flip(['type', 'name', 'route', 'icon', 'component', 'mark', 'is_frame', 'is_cache', 'is_visible']);
        foreach ($menus as $v) {
            $data = array_intersect_key($v, $allow);
            $data['pid'] = $pid;
            $data['status'] = 1;

            $menu = Menu::create($data);
            $hasChild = isset($v['children']) && $v['children'] ? true : false;
            if ($hasChild) {
                $this->create($v['children'], $menu['id']);
            }
        }
    }
}
