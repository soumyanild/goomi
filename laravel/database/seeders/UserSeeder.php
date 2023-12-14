<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $isAdminExist = DB::table('users')->where(['role' => User::ROLE_ADMIN])->count();
        if (!$isAdminExist) {
            //$adminRole = DB::table('roles')->where(['is_deleteable' => 0])->first();
            DB::table('users')->insert([
                'full_name' => 'Admin',
                'phone_number' => '7878877877',
                'email' => 'admin@yopmail.com',
                'role' => User::ROLE_ADMIN,
                'password' => Hash::make('admin@123')
            ]);
        }
    }
}
