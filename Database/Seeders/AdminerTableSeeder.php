<?php

namespace EzAdmin\Modules\Permission\Database\Seeders;

use EzAdmin\Modules\Permission\Models\Adminer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class AdminerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Adminer::factory()
            ->hasRoles(1)
            ->create();
    }
}
