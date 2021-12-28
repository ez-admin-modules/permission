<?php

namespace EzAdmin\Modules\Permission\Database\Seeders;

use EzAdmin\Modules\Permission\Services\MenuService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class MenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $menus = [
            [
                'type'       => 0,
                'name'       => '控制台',
                'route'      => 'dashboard',
                'icon'       => 'dashboard',
                'component'  => 'dashboard',
                'mark'       => null,
                'is_frame'   => 0,
                'is_cache'   => 0,
                'is_visible' => 1,
            ], [
                'type'       => 0,
                'name'       => '权限管理',
                'route'      => 'permission',
                'icon'       => 'peoples',
                'component'  => null,
                'mark'       => null,
                'is_frame'   => 0,
                'is_cache'   => 0,
                'is_visible' => 1,
                'children'   => [
                    [
                        'type'       => 1,
                        'name'       => '用户管理',
                        'route'      => 'adminer',
                        'icon'       => 'user',
                        'component'  => 'permission/adminer/index',
                        'mark'       => 'permission:adminer:index',
                        'is_frame'   => 0,
                        'is_cache'   => 1,
                        'is_visible' => 1,
                    ], [
                        'type'       => 1,
                        'name'       => '角色管理',
                        'route'      => 'role',
                        'icon'       => 'tree',
                        'component'  => 'permission/role/index',
                        'mark'       => 'permission:role:index',
                        'is_frame'   => 0,
                        'is_cache'   => 0,
                        'is_visible' => 1,
                        'children'   => [
                            [
                                'type'       => 2,
                                'name'       => '添加用户',
                                'route'      => '',
                                'icon'       => '',
                                'component'  => '',
                                'mark'       => 'permission:adminer:store',
                                'is_frame'   => 0,
                                'is_cache'   => 0,
                                'is_visible' => 1,
                                'sort'       => 1,
                                'status'     => 1,
                            ], [
                                'type'       => 2,
                                'name'       => '编辑用户',
                                'route'      => '',
                                'icon'       => '',
                                'component'  => '',
                                'mark'       => 'permission:adminer:update',
                                'is_frame'   => 0,
                                'is_cache'   => 0,
                                'is_visible' => 1,
                                'sort'       => 1,
                                'status'     => 1,
                            ], [
                                'type'       => 2,
                                'name'       => '删除用户',
                                'route'      => '',
                                'icon'       => '',
                                'component'  => null,
                                'mark'       => 'permission:adminer:destroy',
                                'is_frame'   => 0,
                                'is_cache'   => 0,
                                'is_visible' => 1,
                                'sort'       => 1,
                                'status'     => 1,
                            ],
                        ],
                    ], [
                        'type'       => 0,
                        'name'       => '菜单管理',
                        'route'      => 'menu',
                        'icon'       => 'tree-table',
                        'component'  => 'permission/menu/index',
                        'mark'       => null,
                        'is_frame'   => 0,
                        'is_cache'   => 0,
                        'is_visible' => 1,
                    ],
                ],
            ],
        ];

        MenuService::instance()->create($menus);
    }
}
