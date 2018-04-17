<?php

use Illuminate\Database\Seeder;

class AdminRolePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 18 ; $i++) { 
        	\DB::table()
        			->insert([
        				'role_id' => 1,
        				'permission_id' => $i
        			]);
        }
    }
}
