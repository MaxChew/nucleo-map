<?php

namespace App\Services;

use App\Models\Role;

class RoleService
{
    public function getRolesSummary()
    {
        return [
            'total' => Role::count(),
            'with_permissions' => Role::whereHas('permissions')->count(),
            'without_permissions' => Role::whereDoesntHave('permissions')->count(),
        ];
    }

    public function canDeleteRole(Role $role)
    {
        // Cannot delete system roles
        if (in_array($role->name, ['super-admin', 'admin'])) {
            return false;
        }

        // Cannot delete roles that have users
        if ($role->users()->count() > 0) {
            return false;
        }

        return true;
    }

    public function getSystemRoles()
    {
        return ['super-admin', 'admin'];
    }
} 