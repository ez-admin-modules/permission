<?php

namespace EzAdmin\Modules\Permission\Http\Controllers\Api\Admin\V1;

use App\Http\Controllers\Controller;
use EzAdmin\Support\Tree;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use EzAdmin\Support\Facades\Response;
use EzAdmin\Modules\Permission\Models\Menu;
use EzAdmin\Modules\Permission\Services\MenuService;

class MenuController extends Controller
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
            'status' => ['in:0,1'],
        ], [], [
            'status' => '状态',
        ]);

        $name = $request->input('name');
        $status = $request->input('status');

        $list = Menu::when($name, function ($query) use ($name) {
            $query->where('name', 'like', "%{$name}%");
        })->when($status !== null, function ($query) use ($status) {
              $query->where('status', $status);
          })
          ->select(['id', 'pid', 'type', 'name', 'route', 'icon', 'component', 'mark', 'is_frame', 'status', 'is_cache', 'is_visible', 'sort', 'created_at'])
          ->orderBy('sort', 'desc')
          ->get()
          ->toArray();

        return Response::success(Tree::instance()->init($list)->getTreeList());
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request    $request
     * @return Response
     */
    public function store(Request $request, Menu $menu)
    {
        $request->validate([
            'type'  => ['required', 'in:0,1,2'],
            'name'  => ['required', 'min:2', 'max:10'],
            'route' => [Rule::requiredIf(in_array($request->type, [0, 1]))],
        ], [], [
            'type'  => '类型',
            'name'  => '名称',
            'route' => '路由地址',
        ]);

        $type = $request->input('type');
        if ($type == 1 || $type == 0) {
            $menu->route = $request->input('route');
            $menu->icon = $request->input('icon', '');
            $menu->is_visible = $request->input('is_visible');
            $menu->status = $request->input('status');
            $menu->is_frame = $request->input('is_frame');
        }
        if ($type == 1) {
            $menu->component = $request->input('component');
            $menu->is_cache = $request->input('is_cache');
        }
        if ($type == 1 || $type == 2) {
            $menu->mark = $request->input('mark');
        }

        $menu->pid = $request->input('pid', 0);
        $menu->creator_id = $request->user()->id;
        $menu->type = $type;
        $menu->name = $request->input('name');
        $menu->sort = $request->input('sort', 1);

        $menu->save();

        return Response::success(null, '添加成功！');
    }

    /**
     * Show the specified resource.
     * @param  int        $id
     * @return Response
     */
    public function show($id)
    {
        return Response::success(Menu::findOrFail($id));
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
            'type'  => ['required', 'in:0,1,2'],
            'name'  => ['required', 'min:2', 'max:10'],
            'route' => [Rule::requiredIf(in_array($request->type, [0, 1]))],
        ], [], [
            'type'  => '类型',
            'name'  => '名称',
            'route' => '路由地址',
        ]);

        $menu = Menu::findOrFail($id);

        $pid = $request->input('pid', 0);
        // 不能修改为子集
        $childrenIds = MenuService::instance()->getChildrenIds($id, true);
        if (in_array($pid, $childrenIds)) {
            Response::fail('父级菜单不能是自己的子级菜单或本身！');
        }

        $type = $request->input('type');
        if ($type == 1 || $type == 0) {
            $menu->route = $request->input('route');
            $menu->icon = $request->input('icon');
            $menu->is_frame = $request->input('is_frame');
            $menu->is_visible = $request->input('is_visible');
            $menu->status = $request->input('status');
        }
        if ($type == 1) {
            $menu->component = $request->input('component');
            $menu->is_cache = $request->input('is_cache');
        }
        if ($type == 1 || $type == 2) {
            $menu->mark = $request->input('mark');
        }

        $menu->pid = $pid;
        $menu->creator_id = $request->user()->id;
        $menu->type = $type;
        $menu->name = $request->input('name');
        $menu->sort = $request->input('sort', 1);

        $menu->save();

        return Response::success(null, '修改成功！');
    }

    /**
     * Remove the specified resource from storage.
     * @param  int        $id
     * @return Response
     */
    public function destroy($id)
    {
        // 判断是否存在子级
        $menu = Menu::findOrFail($id);

        $childrenIds = MenuService::instance()->getChildrenIds($id);
        if ($childrenIds) {
            Response::fail('您不能删除含有子菜单的菜单！');
        }
        $menu->delete();

        return Response::success(null, '删除成功！');
    }

    /**
     * @param Request $request
     */
    public function changeVisible(Request $request)
    {
        $request->validate([
            'id'         => 'required',
            'is_visible' => 'required',
        ], [], [
            'id'         => 'ID',
            'is_visible' => '状态',
        ]);

        $id = $request->input('id');
        $is_visible = $request->input('is_visible');

        $menu = Menu::findOrFail($id);

        $menu->is_visible = $is_visible;
        $menu->save();

        return Response::success(null, '修改成功！');
    }
}
