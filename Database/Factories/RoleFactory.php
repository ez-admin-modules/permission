<?php
namespace EzAdmin\Modules\Permission\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \EzAdmin\Modules\Permission\Models\Role::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => '超级管理员',
            'mark' => config('permission.super_role_mark'),
            'description' => '',
            'status' => 1,
        ];
    }
}

