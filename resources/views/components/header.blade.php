<div class="sticky top-0 z-[50] flex items-center justify-between h-12 bg-bg px-4 shadow">
    <div class="flex items-center justify-between gap-1">
        <!-- Sidebar Toggle Button -->
        @if(Auth::check())
            <button @click="sidebarOpen = !sidebarOpen; clickSidebar = !clickSidebar" class="w-10">
                <i :class="sidebarOpen ? 'fad fa-bars-open' : 'fad fa-bars'"></i>
            </button>
            <a href="{{ route(app('domain') . '.dashboard.index') }}" class="flex items-center gap-2">
                <img src="{{ '/images/logo.png' }}" class="w-10" />
                <h1>SIMIRA</h1>
            </a>
        @else
            <a href="{{ route('auth.login') }}" class="flex items-center gap-2">
                <img src="{{ '/images/logo.png' }}" class="w-10" />
                <h1>SIMIRA</h1>
            </a>
        @endif
    </div>
    <!-- User Menu -->
    <div class="relative flex items-center sm:space-x-7 space-x-3">
        @if(Auth::check())
            <!-- Back to Dashboard Button -->
            <a href="{{ route(app('domain') . '.dashboard.index') }}" 
               class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-accent-600 to-accent-700 hover:from-accent-700 hover:to-accent-800 text-white text-xs font-medium rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent-500/50">
                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                                    Back to Dashboard
            </a>
        @endif
        
        <div class="relative flex items-center">
           
            @if (Auth::user())
                <!-- Notification bell -->
                <notification-bell class="mr-4"></notification-bell>
                
                <profile-dropdown name="{{ Auth::user()->name }}" email="{{ Auth::user()->email }}"
                    :is-admin="{{ Auth::user()->isAdmin }}" :is-tutor="{{ Auth::user()->isTutor }}"
                    :is-client="{{ Auth::user()->isClient }}" profile-image="{{ Auth::user()->avatar_url }}"
                    profile-url="{{ route(app('domain') . '.profile.edit') }}"
                    :menu-items="{{ json_encode([
                        // ['name' => 'Dashboard', 'url' => '#'],
                        // ['name' => 'Settings', 'url' => '#'],
                        // ['name' => 'Notifications', 'url' => '#'],
                        ['name' => 'Log out', 'url' => route('auth.logout')],
                    ]) }}"
                    csrf-token="{{ csrf_token() }}"></profile-dropdown>
            @endif
        </div>
    </div>
</div>
