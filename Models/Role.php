<?php

namespace EzAdmin\Modules\Permission\Models;

use EzAdmin\Base\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var array
     */
    protected $guarded = ['id'];

    protected static function newFactory()
    {
        return \EzAdmin\Modules\Permission\Database\factories\RoleFactory::new ();
    }

    /**
     * @return mixed
     */
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'role_menus');
    }

    /**
     * @return mixed
     */
    public function adminers()
    {
        return $this->belongsToMany(Adminer::class, 'role_menus');
    }
}
