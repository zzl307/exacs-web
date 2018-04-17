<?php

use App\AdminUser;
use Illuminate\Database\Seeder;

class AdminUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(AdminUser $adminUser)
    {	
		$adminUser->name = '管理员';		        
		$adminUser->email = 'admin@exands.com';		        
		$adminUser->password = bcrypt('admin');

		$adminUser->save();		        
    }
}
