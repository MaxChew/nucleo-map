<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 显示仪表板
     */
    public function index()
    {
        $user = Auth::user();
        
        // 检查用户角色
        $isSuperAdmin = $user->hasRole('superadmin');
        $isAdmin = $user->hasRole('admin');
        
        return view('dashboard.index', compact('user', 'isSuperAdmin', 'isAdmin'));
    }
} 