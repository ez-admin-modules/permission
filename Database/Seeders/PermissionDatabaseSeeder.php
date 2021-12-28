<?php

namespace EzAdmin\Modules\Permission\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class PermissionDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call([
            AdminerTableSeeder::class,
            MenuTableSeeder::class,
        ]);
    }
}
