<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 创建权限
        $permissions = [
            'view-dashboard',
            'manage-users',
            'manage-roles',
            'manage-permissions',
            'manage-system',
            'view-reports',
            'manage-content',
            'access-logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Super Admin role - has all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin']);
        if (!$superAdminRole->permissions->count()) {
            $superAdminRole->givePermissionTo(Permission::all());
        }

        // Admin role - has limited permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        if (!$adminRole->permissions->count()) {
            $adminRole->givePermissionTo([
                'view-dashboard',
                'view-reports',
                'manage-content',
            ]);
        }

        // Create super admin user
        $superAdmin = User::firstOrCreate([
            'email' => 'superadmin@example.com',
        ], [
            'name' => 'Super Administrator',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        if (!$superAdmin->hasRole('superadmin')) {
            $superAdmin->assignRole('superadmin');
        }

        // Create admin user
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Administrator',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        $this->command->info('Roles and permissions have been created successfully!');
        $this->command->info('Super Admin: superadmin@example.com / password123');
        $this->command->info('Admin: admin@example.com / password123');
    }
} 