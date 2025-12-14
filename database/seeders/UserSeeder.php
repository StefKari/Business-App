<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $sysAdminRole = Role::where('slug', Role::SYS_ADMIN)->first();
        $adminRole = Role::where('slug', Role::ADMIN)->first();
        $moderatorRole = Role::where('slug', Role::MODERATOR)->first();

        // Create SysAdmin user
        $sysAdmin = User::create([
            'name' => 'System Administrator',
            'email' => 'sysadmin@business.test',
            'password' => Hash::make('password'),
            'role_id' => $sysAdminRole->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Admin user
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@business.test',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'is_active' => true,
            'created_by' => $sysAdmin->id,
            'email_verified_at' => now(),
        ]);

        // Create Moderator user
        User::create([
            'name' => 'Moderator',
            'email' => 'moderator@business.test',
            'password' => Hash::make('password'),
            'role_id' => $moderatorRole->id,
            'is_active' => true,
            'created_by' => $sysAdmin->id,
            'email_verified_at' => now(),
        ]);

        // Create additional test users
        User::create([
            'name' => 'Test Admin',
            'email' => 'test.admin@business.test',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'is_active' => true,
            'created_by' => $sysAdmin->id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Test Moderator',
            'email' => 'test.moderator@business.test',
            'password' => Hash::make('password'),
            'role_id' => $moderatorRole->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'email_verified_at' => now(),
        ]);
    }
}
