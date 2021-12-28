<?php

namespace EzAdmin\Modules\Permission\Http\Controllers\Api\Admin\V1;

use App\Http\Controllers\Controller;
use EzAdmin\Modules\Permission\Models\Role;
use EzAdmin\Modules\Permission\Services\AdminerService;
use EzAdmin\Modules\Permission\Services\RoleService;
use EzAdmin\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'name'   => ['max:20'],
            'status' => ['in:0,1'],
        ], [
            'name.max'  => '名称不能大于20个字符',
            'status.in' => '状态格式不符',
        ]);

        $name = $request->input('name', '');
        $status = $request->input('status');
        $size = $request->input('size');

        $data = Role::when($name, function ($query) use ($name) {
            $query->where('name', 'like', "%{$name}%");
        })
            ->when($status !== null, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->select(['id', 'name', 'mark', 'status', 'created_at']);
        if ($size) {
            $list = $data->paginate($size);
        } else {
            $list = $data->get();
        }

        foreach ($list as $v) {
            $v->is_admin = RoleService::instance()->isSuperRole($v->id);
        }

        return Response::success($list);
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request    $request
     * @return Response
     */
    public function store(Request $request, Role $role)
    {
        $request->validate([
            'name'        => ['required', 'min:2', 'max:20', Rule::unique('roles')->where(function ($query) {
                return $query->whereNull('deleted_at');
            })],
            'mark'        => ['required', 'min:2', 'max:20', 'alpha', Rule::unique('roles')->where(function ($query) {
                return $query->whereNull('deleted_at');
            })],
            'description' => ['max:250'],
            'status'      => ['required', 'in:0,1'],
            'menu_ids'    => ['array'],
        ], [], [
            'name'        => '名称',
            'mark'        => '标识',
            'description' => '描述',
            'status'      => '状态',
            'menu_ids'    => '菜单',
        ]);
        // 新增
        $role->creator_id = $request->user()->id;
        $role->name = $request->input('name');
        $role->mark = $request->input('mark');
        $role->description = $request->input('description', '');
        $role->status = $request->input('status');
        $role->save();

        $menu_ids = $request->input('menu_ids');
        if ($menu_ids) {
            $role->menus()->attach($menu_ids);
        }

        return Response::success(null, '添加成功！');
    }

    /**
     * Show the specified resource.
     * @param  int        $id
     * @return Response
     */
    public function show($id)
    {
        $role = Role::with([
            'menus' => function ($query) {
                $query->select('id');
            },
        ])->select(['id', 'name', 'mark', 'status', 'description'])
          ->find($id);

        $role = collect($role)->toArray();
        if ($role) {
            $role['menu_ids'] = collect($role['menus'])->map(function ($menu) {
                return $menu['id'];
            });
            unset($role['menus']);
        }

        return Response::success($role);
    }

    /**
     * Update the specified resource in storage.
     * @param  Request    $request
     * @param  int        $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => ['required', 'min:2', 'max:20', Rule::unique('roles')->where(function ($query) use ($id) {
                return $query->whereNull('deleted_at')->where('id', '<>', $id);
            })],
            'mark'        => ['required', 'min:2', 'max:20', 'alpha', Rule::unique('roles')->where(function ($query) use ($id) {
                return $query->whereNull('deleted_at')->where('id', '<>', $id);
            })],
            'description' => ['max:250'],
            'status'      => ['required', 'in:0,1'],
            'menu_ids'    => ['array'],
        ], [], [
            'name'        => '名称',
            'mark'        => '标识',
            'description' => '描述',
            'status'      => '状态',
            'menu_ids'    => '菜单',
        ]);

        $role = Role::findOrFail($id);
        if (RoleService::instance()->isSuperRole($id)) {
            Response::fail('您不能修改超级管理员角色！');
        }
        $role->name = $request->input('name');
        $role->mark = $request->input('mark');
        $role->description = $request->input('description', '');
        $role->status = $request->input('status');
        $role->save();

        $menu_ids = $request->input('menu_ids', []);
        $role->menus()->sync($menu_ids);

        return Response::success(null, '更新成功！');
    }

    /**
     * Remove the specified resource from storage.
     * @param  int        $id
     * @return Response
     */
    public function destroy(Request $request, $ids)
    {
        $userId = $request->user()->id;

        $ids = explode(',', $ids);
        // 禁止删除本身所在组
        $roleIds = AdminerService::instance()->getRoleIds($userId);

        if (array_diff($roleIds, $ids) != $roleIds) {
            Response::fail('您不能删除自己所在角色！');
        }

        DB::beginTransaction();
        try {
            foreach ($ids as $id) {
                $role = Role::findOrFail($id);
                if (RoleService::instance()->isSuperRole($id)) {
                    throw new \Exception('您不能删除超级管理员角色！');
                }
                // 判断是否包含用户
                $userIds = RoleService::instance()->getAdminerIds($id);
                if ($userIds) {
                    throw new \Exception('您不能删除包含用户的角色！');
                }
                $role->menus()->detach();
                $role->delete();
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Response::error($th->getMessage());
        }

        return Response::success(null, '删除成功！');
    }

    /**
     * @param Request $request
     */
    public function changeStatus(Request $request)
    {
        $request->validate([
            'id'     => 'required',
            'status' => ['required', 'in:0,1'],
        ], [], [
            'id'     => 'ID',
            'status' => '状态',
        ]);

        $id = $request->input('id');
        $status = $request->input('status');

        $role = Role::findOrFail($id);
        if (RoleService::instance()->isSuperRole($id)) {
            Response::fail('您不能修改超级管理员角色！');
        }
        $role->status = $status;
        $role->save();

        return Response::success(null, '修改成功！');
    }
}
