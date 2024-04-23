<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        set_time_limit(999999);
        $this->call(FunctionsTableSeeder::class);
        $this->call(ControllersTableSeeder::class);
        $this->call(ModulesTableSeeder::class);
        $this->call(ActionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(RoleplayTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(MenugroupsTableSeeder::class);
        $this->call(MenusTableSeeder::class);
    }
}
