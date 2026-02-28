<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminAccountsSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'master@ccs.local'],
            [
                'full_name' => 'Master Administrator',
                'username' => 'masteradmin',
                'password' => Hash::make('ChangeMe123A'),
                'role' => 'master_admin',
                'is_active' => true,
                'force_password_change' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@ccs.local'],
            [
                'full_name' => 'School Administrator',
                'username' => 'schooladmin',
                'password' => Hash::make('ChangeMe123A'),
                'role' => 'admin',
                'is_active' => true,
                'force_password_change' => true,
            ]
        );
    }
}
