<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminUserTableSeeder::class);
        $this->call(AdminRoleTableSeeder::class);
        $this->call(AdminPermissionTableSeeder::class);
        $this->call(AdminUserRoleTableSeeder::class);
        $this->call(AdminPermissionTableSeeder::class);
    }
}
