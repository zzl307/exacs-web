<?php

use Illuminate\Database\Seeder;

class AdminUserRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /DB::table('admin_user_role')
        		->insert([
        			'rold_id' => 1,
        			'user_id' => 1
        		]);
    }
}
