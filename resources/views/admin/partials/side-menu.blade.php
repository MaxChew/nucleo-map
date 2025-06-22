<nav class="bg-slate-900 w-64 min-h-screen flex flex-col shadow-2xl">
    <!-- Logo Section -->
    <div class="flex items-center justify-center h-16 bg-gradient-to-r from-primary-600 to-primary-700 shadow-lg">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2L3 7v11h4v-6h6v6h4V7l-7-5z"/>
                </svg>
            </div>
            <h1 class="text-white text-lg font-bold">Nucleo-Map Admin</h1>
        </div>
    </div>

    <!-- Navigation Items -->
    <div class="flex-1 px-3 py-6 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-lg transform scale-105' : 'text-slate-300 hover:bg-slate-800 hover:text-white hover:transform hover:scale-105' }}">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : 'bg-slate-800 group-hover:bg-primary-500/20' }} mr-3 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                </svg>
            </div>
            <span>Dashboard</span>
            @if(request()->routeIs('admin.dashboard'))
                <div class="ml-auto w-2 h-2 bg-white rounded-full"></div>
            @endif
        </a>

        <!-- User Management Section -->
        <div class="pt-4">
            <div class="flex items-center px-4 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                User Management
            </div>
            
            <div class="space-y-1 ml-2">
                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-r from-accent-500 to-accent-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center justify-center w-6 h-6 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-white/20' : 'bg-slate-800 group-hover:bg-accent-500/20' }} mr-3 transition-all duration-200">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <span>Users</span>
                    @if(request()->routeIs('admin.users.*'))
                        <div class="ml-auto">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-white/20 text-white">2</span>
                        </div>
                    @endif
                </a>
                
                <a href="{{ route('admin.roles.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.roles.*') ? 'bg-gradient-to-r from-accent-500 to-accent-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center justify-center w-6 h-6 rounded-lg {{ request()->routeIs('admin.roles.*') ? 'bg-white/20' : 'bg-slate-800 group-hover:bg-accent-500/20' }} mr-3 transition-all duration-200">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <span>Roles & Permissions</span>
                </a>
            </div>
        </div>

        <!-- Medical Centers Section -->
        <div class="pt-4">
            <div class="flex items-center px-4 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Medical Centers
            </div>
            
            <div class="space-y-1 ml-2">
                <a href="{{ route('admin.centers.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.centers.*') ? 'bg-gradient-to-r from-accent-500 to-accent-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center justify-center w-6 h-6 rounded-lg {{ request()->routeIs('admin.centers.*') ? 'bg-white/20' : 'bg-slate-800 group-hover:bg-accent-500/20' }} mr-3 transition-all duration-200">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <span>Centers</span>
                    @if(request()->routeIs('admin.centers.*'))
                        <div class="ml-auto">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-white/20 text-white">{{ \App\Models\Center::count() }}</span>
                        </div>
                    @endif
                </a>
            </div>
        </div>

        <!-- System Section -->
        <div class="pt-4">
            <div class="flex items-center px-4 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                System
            </div>
            
            <div class="space-y-1 ml-2">
                <a href="{{ route('admin.logs.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.logs.*') ? 'bg-gradient-to-r from-secondary-500 to-secondary-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center justify-center w-6 h-6 rounded-lg {{ request()->routeIs('admin.logs.*') ? 'bg-white/20' : 'bg-slate-800 group-hover:bg-secondary-500/20' }} mr-3 transition-all duration-200">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span>Activity Logs</span>
                </a>
            </div>
        </div>
    </div>

    <!-- User Profile Section -->
    <div class="border-t border-slate-700 p-4 bg-slate-800/50">
        <div class="flex items-center space-x-3">
            <div class="relative">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-lg">
                    <span class="text-sm font-bold text-white">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                </div>
                <div class="absolute -bottom-1 -right-1 h-4 w-4 bg-accent-500 rounded-full border-2 border-slate-900"></div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate">Super Administrator</p>
                <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email ?? 'superadmin@example.com' }}</p>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-700">
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                class="flex items-center w-full px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700 rounded-lg transition-all duration-200 group">
                <svg class="w-4 h-4 mr-3 group-hover:rotate-12 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </div>
</nav>
