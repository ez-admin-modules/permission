<?php

namespace EzAdmin\Modules\Permission\Models;

use EzAdmin\Base\Model;
use EzAdmin\Permission\Support\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Adminer extends Model
{
    use HasFactory, HasApiTokens, SoftDeletes;

    /**
     * @var array
     */
    protected $guarded = ['id'];

    protected static function newFactory()
    {
        return \EzAdmin\Modules\Permission\Database\factories\AdminerFactory::new ();
    }

    /**
     * 设置头像
     *
     * @param  string $value
     * @return void
     */
    public function setAvatarAttribute($avatar)
    {
        $this->attributes['avatar'] = $avatar ?? Helper::DefaultAvatar();
    }

    /**
     * 设置密码
     *
     * @param  string $value
     * @return void
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    /**
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'adminer_roles');
    }
}
