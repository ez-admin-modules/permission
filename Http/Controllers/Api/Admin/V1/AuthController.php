<?php

namespace EzAdmin\Modules\Permission\Http\Controllers\Api\Admin\V1;

use App\Http\Controllers\Controller;
use EzAdmin\Modules\Permission\Services\AdminerService;
use EzAdmin\Modules\Permission\Services\AuthService;
use EzAdmin\Support\Facades\Response;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum'], ['except' => ['login']]);
    }

    /**
     * @param Request $request
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ], [
            'username.required' => '用户名不能为空',
            'password.required' => '密码不能为空',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        $token = AuthService::instance()->loginByName($username, $password);

        return Response::success(['token' => $token], '登录成功！');
    }

    /**
     * @param Request $request
     */
    public function show(Request $request)
    {
        $user = $request->user();
        // 角色
        $user->roles = $user->roles()->pluck('mark');
        $user = $user->only(['id', 'username', 'nickname', 'avatar', 'roles']);
        $user['menus'] = AdminerService::instance()->getMenus($user['id'], [0, 1]);
        $user['permission'] = AdminerService::instance()->getMenus($user['id'], [2])->pluck('mark');
        return Response::success($user);
    }

    /**
     * @param Request $request
     */
    public function update(Request $request)
    {
        $request->validate([
            'avatar'   => ['url'],
            'nickname' => ['required', 'max:20', 'min:2'],
            'password' => ['min:6', 'max:20'],
        ], [], [
            'avatar'   => '头像',
            'nickname' => '昵称',
            'password' => '密码',
        ]);

        $user = $request->user();
        $user->avatar = $request->input('avatar');
        $user->nickname = $request->input('nickname');
        $user->password = $request->input('password');
        $user->save();

        return Response::success(null, '修改成功！');
    }

    /**
     * @param Request $request
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return Response::success(null, '登出成功！');
    }
}
