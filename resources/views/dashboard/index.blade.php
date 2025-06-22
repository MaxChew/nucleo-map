<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-gray-900">{{ config('app.name') }}</h1>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- User Info -->
                    <div class="flex items-center text-sm text-gray-700">
                        <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center mr-3">
                            <span class="text-white font-medium">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <div class="font-medium">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                        </div>
                    </div>
                    
                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button 
                            type="submit"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                        >
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="px-4 py-6 sm:px-0">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        Welcome back, {{ $user->name }}!
                    </h2>
                    <p class="text-gray-600 mb-6">
                        You have successfully logged into the admin backend. Current time: {{ format_datetime(now(), 'Y-m-d H:i') }}
                    </p>
                    
                    <!-- Role Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                        <!-- User Role -->
                        <div class="bg-gradient-to-r from-primary-500 to-primary-600 p-4 rounded-lg text-white">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <div>
                                    <div class="text-lg font-semibold">User Role</div>
                                    <div class="text-sm opacity-90">
                                        @if($isSuperAdmin)
                                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger-100 text-danger-800">
                                                                            Super Administrator
                            </span>
                        @elseif($isAdmin)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                                                            Administrator
                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Regular User
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Login Info -->
                        <div class="bg-gradient-to-r from-accent-500 to-accent-600 p-4 rounded-lg text-white">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <div class="text-lg font-semibold">Login Time</div>
                                    <div class="text-sm opacity-90">{{ format_datetime(now(), 'H:i') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- IP Address -->
                        <div class="bg-gradient-to-r from-secondary-400 to-secondary-500 p-4 rounded-lg text-white">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                                </svg>
                                <div>
                                    <div class="text-lg font-semibold">IP Address</div>
                                    <div class="text-sm opacity-90">{{ get_ip()['ip'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nucleo Map Introduction Section -->
                    @if($isSuperAdmin || $isAdmin)
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">🗺️ Nucleo Map Nuclear Medicine Center Management System</h3>
                        <div class="mb-4 p-4 bg-gradient-to-r from-primary-50 to-accent-50 border border-primary-200 rounded-lg">
                            <p class="text-sm text-gray-700 mb-2">
                                <strong>Welcome to Nucleo Map!</strong> This is a map application system designed specifically for managing nuclear medicine medical centers in Malaysia.
                            </p>
                            <p class="text-xs text-gray-600 mb-3">
                                The system integrates Laravel + Vue 3 technology to provide a complete medical center management solution.
                            </p>
                            
                            <!-- System Modules -->
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                <div class="bg-white/70 backdrop-blur-sm rounded-lg p-3 border border-primary-100">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-primary-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-medium text-gray-900">Map Module</div>
                                            <div class="text-xs text-gray-600">Interactive Maps</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-white/70 backdrop-blur-sm rounded-lg p-3 border border-accent-100">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-accent-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-medium text-gray-900">Center Management</div>
                                            <div class="text-xs text-gray-600">Medical Centers</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-white/70 backdrop-blur-sm rounded-lg p-3 border border-secondary-100">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-secondary-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-medium text-gray-900">User Management</div>
                                            <div class="text-xs text-gray-600">System Users</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-white/70 backdrop-blur-sm rounded-lg p-3 border border-warning-100">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-warning-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-medium text-gray-900">Role & Permissions</div>
                                            <div class="text-xs text-gray-600">Access Control</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-white/70 backdrop-blur-sm rounded-lg p-3 border border-danger-100">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-danger-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-medium text-gray-900">Activity Logs</div>
                                            <div class="text-xs text-gray-600">System Tracking</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif



                    <!-- Quick Actions -->
                    <div class="border-t pt-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                            <!-- 1. Example Page -->
                            <a href="{{ route('map.index') }}" class="bg-gray-100 hover:bg-gray-200 p-4 rounded-lg text-center transition-colors">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900">Example Page</div>
                            </a>
                            
                            <!-- 2. User Management -->
                            <a href="{{ route('admin.users.index') }}" class="bg-gray-100 hover:bg-gray-200 p-4 rounded-lg text-center transition-colors">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900">User Management</div>
                            </a>
                            
                            <!-- 3. Center Management -->
                            <a href="{{ route('admin.centers.index') }}" class="bg-gray-100 hover:bg-gray-200 p-4 rounded-lg text-center transition-colors">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900">Center Management</div>
                            </a>
                            
                            <!-- 4. Color Palette -->
                            <a href="{{ route('color-palette') }}" class="bg-gradient-to-br from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 p-4 rounded-lg text-center transition-all text-white">
                                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                                </svg>
                                <div class="text-sm font-medium">Color Palette</div>
                            </a>
                            
                            <!-- 5. iFrame Example -->
                            <a href="{{ url('/map/iframe-example') }}" class="bg-gradient-to-br from-accent-500 to-accent-600 hover:from-accent-600 hover:to-accent-700 p-4 rounded-lg text-center transition-all text-white">
                                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <div class="text-sm font-medium">iFrame Example</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 