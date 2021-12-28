<?php
namespace EzAdmin\Modules\Permission\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AdminerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \EzAdmin\Modules\Permission\Models\Adminer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'username' => 'admin',
            'nickname' => 'Admin',
            'password' => bcrypt('admin123'),
            'avatar'   => ez_avatar_url(),
            'status'   => 1,
        ];
    }
}
