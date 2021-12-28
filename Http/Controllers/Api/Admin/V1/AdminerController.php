<?php

namespace EzAdmin\Modules\Permission\Http\Controllers\Api\Admin\V1;

use App\Http\Controllers\Controller;
use EzAdmin\Modules\Permission\Models\Adminer;
use EzAdmin\Modules\Permission\Services\AdminerService;
use EzAdmin\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminerController extends Controller
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
            'start'  => ['date_format:Y-m-d'],
            'end'    => ['date_format:Y-m-d'],
            'status' => ['in:0,1'],
        ], [], [
            'start'  => '开始时间',
            'end'    => '结束时间',
            'status' => '状态',
        ]);

        $username = $request->input('username');
        $start = $request->input('start');
        $end = $request->input('end');
        $status = $request->input('status');
        $size = $request->input('size');

        $data = Adminer::with(['roles' => function ($query) {
            $query->select(['id', 'name']);
        }])
            ->when($status !== null, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($username, function ($query) use ($username) {
                $query->where('username', 'like', "%{$username}%");
            })
            ->when($start, function ($query) use ($start) {
                $query->where('created_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                $query->where('created_at', '>=', $end);
            })
            ->select(['id', 'username', 'nickname', 'avatar', 'status', 'created_at']);
        if ($size) {
            $list = $data->paginate($size);
        } else {
            $list = $data->get();
        }

        foreach ($list as $v) {
            $v->is_admin = AdminerService::instance()->isSuperAdmin($v->id);
            $v->roles->makeHidden('pivot');
        }
        return Response::success($list);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     */
    public function store(Request $request, Adminer $user)
    {
        $request->validate([
            'username' => ['required', 'max:20', 'min:2', Rule::unique('adminers')->where(function ($query) {
                return $query->whereNull('deleted_at');
            })],
            'nickname' => ['required', 'max:20', 'min:2'],
            'password' => ['min:6', 'max:20'],
            'avatar'   => ['url'],
            'role_ids' => ['array'],
            'status'   => ['required', 'in:0,1'],
        ], [], [
            'username' => '名称',
            'nickname' => '昵称',
            'password' => '密码',
            'avatar'   => '头像',
            'status'   => '状态',
            'role_ids' => '角色',
        ]);

        $user->creator_id = $request->user()->id;
        $user->username = $request->input('username');
        $user->nickname = $request->input('nickname');
        $user->password = $request->input('password');
        $user->avatar = $request->input('avatar');
        $user->status = $request->input('status');
        $user->save();

        $role_ids = $request->input('role_ids', []);
        $user->roles()->attach($role_ids);

        return Response::success(null, '添加成功！');
    }

    /**
     * Show the specified resource.
     * @param int $id
     */
    public function show($id)
    {
        $user = Adminer::with(['roles' => function ($query) {
            $query->select(['id', 'name']);
        }])
            ->where('id', $id)
            ->select(['id', 'username', 'nickname', 'avatar', 'status'])
            ->first();
        $user = collect($user)->toArray();
        if ($user) {
            $user['role_ids'] = collect($user['roles'])->map(function ($role) {
                return $role['id'];
            });
            unset($user['roles']);
        }
        return Response::success($user);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int     $id
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'username' => ['required', 'max:20', 'min:2', Rule::unique('adminers')->where(function ($query) use ($id) {
                return $query->whereNull('deleted_at')->where('id', '<>', $id);
            })],
            'nickname' => ['required', 'max:20', 'min:2'],
            'password' => ['min:6', 'max:20'],
            'avatar'   => ['url'],
            'status'   => ['in:0,1'],
        ], [], [
            'username' => '用户名',
            'nickname' => '昵称',
            'password' => '密码',
            'avatar'   => '头像',
            'status'   => '状态',
        ]);

        $user = Adminer::findOrFail($id);
        if (AdminerService::instance()->isSuperAdmin($id)) {
            Response::fail('您不能修改超级管理员！');
        }
        $user->username = $request->input('username');
        $user->nickname = $request->input('nickname');

        $password = $request->input('password');
        if ($password) {
            $user->password = $password;
        }
        $user->avatar = $request->input('avatar');
        $user->status = $request->input('status');
        $user->save();

        $role_ids = $request->input('role_ids', []);
        $user->roles()->sync($role_ids);

        return Response::success(null, '更新成功！');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     */
    public function destroy(Request $request, $ids)
    {
        $selfId = $request->user()->id;

        $ids = explode(',', $ids);

        DB::beginTransaction();
        try {
            foreach ($ids as $id) {
                if ($id == $selfId) {
                    throw new \Exception('您不能删除自己！');
                }
                if (AdminerService::instance()->isSuperAdmin($id)) {
                    throw new \Exception('您不能删除超级管理员！');
                }

                $user = Adminer::findOrFail($id);
                $user->delete();
                $user->roles()->detach();
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Response::fail($th->getMessage());
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
            'status' => 'required',
        ], [], [
            'id'     => 'ID',
            'status' => '状态',
        ]);

        $id = $request->input('id');
        $status = $request->input('status');

        $user = Adminer::findOrFail($id);
        if (AdminerService::instance()->isSuperAdmin($id)) {
            Response::fail('您不能修改超级管理员！');
        }
        $user->status = $status;
        $user->save();

        return Response::success(null, '修改成功！');
    }
}
