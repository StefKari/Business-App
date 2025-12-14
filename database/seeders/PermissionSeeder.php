<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'users' => 'User Management',
            'roles' => 'Role Management',
            'permissions' => 'Permission Management',
            'activity_logs' => 'Activity Logs',
            'visibility' => 'Visibility Settings',
            'dashboard' => 'Dashboard Access',
        ];

        $actions = ['view', 'create', 'update', 'delete', 'restore', 'force-delete'];

        foreach ($modules as $module => $description) {
            foreach ($actions as $action) {
                Permission::create([
                    'name' => ucfirst($action) . ' ' . $description,
                    'slug' => "{$module}.{$action}",
                    'module' => $module,
                    'description' => ucfirst($action) . ' permission for ' . $description,
                ]);
            }
        }

        // Special permissions
        Permission::create([
            'name' => 'Manage Visibility',
            'slug' => 'visibility.manage',
            'module' => 'visibility',
            'description' => 'Manage visibility settings for content',
        ]);

        Permission::create([
            'name' => 'View All Data',
            'slug' => 'data.view-all',
            'module' => 'system',
            'description' => 'View all data regardless of visibility settings',
        ]);

        $this->assignPermissionsToRoles();
    }

    private function assignPermissionsToRoles(): void
    {
        $sysAdmin = Role::where('slug', Role::SYS_ADMIN)->first();
        $admin = Role::where('slug', Role::ADMIN)->first();
        $moderator = Role::where('slug', Role::MODERATOR)->first();

        // SysAdmin gets all permissions
        $allPermissions = Permission::all();
        $sysAdmin->permissions()->attach($allPermissions->pluck('id'));

        // Admin gets limited permissions
        $adminPermissions = Permission::whereIn('module', ['users', 'dashboard'])
            ->whereIn('slug', [
                'users.view',
                'users.create',
                'users.update',
                'dashboard.view',
            ])->get();
        $admin->permissions()->attach($adminPermissions->pluck('id'));

        // Moderator gets only view permissions
        $moderatorPermissions = Permission::where('slug', 'like', '%.view')->get();
        $moderator->permissions()->attach($moderatorPermissions->pluck('id'));
    }
}
