<?php

namespace EzAdmin\Modules\Permission\Services;

use EzAdmin\Base\Service;
use EzAdmin\Modules\Permission\Exceptions\PermissionException;
use EzAdmin\Modules\Permission\Models\Adminer;
use Illuminate\Support\Facades\Hash;

class AuthService extends Service
{
    /**
     * 账号密码登录
     *
     * @param $username
     * @param $password
     */
    public function loginByName($username, $password)
    {
        $admin = Adminer::where('username', $username)->first();
        if (!$admin) {
            throw new PermissionException('账号或密码错误');
        }
        if (!Hash::check($password, $admin->password)) {
            throw new PermissionException('账号或密码错误');
        }
        return $this->loginById($admin['id']);
    }

    /**
     * 通过ID登录
     *
     * @param $userId
     */
    public function loginById($userId)
    {
        $admin = Adminer::where('id', $userId)->first();
        if (!$admin) {
            throw new PermissionException('账号错误');
        }
        $admin->login_failure = 0;
        $admin->login_at = now();
        $admin->login_ip = request()->ip();
        $admin->save();

        return $admin->createToken($admin->username)->plainTextToken;
    }
}
