<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getUsersSummary()
    {
        return [
            'all' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];
    }

    public function getAdminsSummary()
    {
        return [
            'all' => User::role('admin')->count(),
            'active' => User::role('admin')->where('is_active', true)->count(),
            'inactive' => User::role('admin')->where('is_active', false)->count(),
        ];
    }
} 