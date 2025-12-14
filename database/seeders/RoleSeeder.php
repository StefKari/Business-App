<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'System Administrator',
                'slug' => Role::SYS_ADMIN,
                'description' => 'Full system access with all privileges',
                'level' => Role::LEVEL_SYS_ADMIN,
            ],
            [
                'name' => 'Administrator',
                'slug' => Role::ADMIN,
                'description' => 'Administrative access with limited privileges',
                'level' => Role::LEVEL_ADMIN,
            ],
            [
                'name' => 'Moderator',
                'slug' => Role::MODERATOR,
                'description' => 'Read-only access to designated content',
                'level' => Role::LEVEL_MODERATOR,
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
