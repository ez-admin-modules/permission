<?php

namespace EzAdmin\Modules\Permission\Models;

use EzAdmin\Base\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $guarded = ['id'];

    protected static function newFactory()
    {
        return \EzAdmin\Modules\Permission\Database\factories\MenuFactory::new ();
    }
}
