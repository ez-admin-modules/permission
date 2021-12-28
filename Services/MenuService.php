<?php

namespace EzAdmin\Modules\Permission\Services;

use EzAdmin\Base\Service;
use EzAdmin\Support\Tree;
use EzAdmin\Modules\Permission\Models\Menu;

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
}
