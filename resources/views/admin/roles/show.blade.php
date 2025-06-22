@extends('admin.layout.master', [
    'title' => 'Role Details - ' . $role->name,
])

@section('content')
    <div class="v-cloak--hidden flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <h1>Role Details: {{ $role->name }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.roles.edit', $role) }}" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                    <i class="fad fa-edit mr-2"></i>Edit Role
                </a>
                <a href="{{ route('admin.roles.index') }}" class="bg-secondary-500 hover:bg-secondary-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                    <i class="fad fa-arrow-left mr-2"></i>Back to Roles
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Role Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Role Information</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $role->name }}</p>
                    </div>
                    
                    @if($role->display_name)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Display Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $role->display_name }}</p>
                    </div>
                    @endif
                    
                    @if($role->description)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $role->description }}</p>
                    </div>
                    @endif
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Created At</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $role->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $role->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Role Statistics -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Statistics</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-primary-50 rounded-lg">
                        <span class="text-sm font-medium text-primary-900">Total Permissions</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                            {{ $role->permissions->count() }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-accent-50 rounded-lg">
                        <span class="text-sm font-medium text-accent-900">Assigned Users</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent-100 text-accent-800">
                            {{ $role->users->count() }}
                        </span>
                    </div>
                    
                    @if(in_array($role->name, ['superadmin', 'admin']))
                    <div class="flex justify-between items-center p-3 bg-warning-50 rounded-lg">
                        <span class="text-sm font-medium text-warning-900">System Role</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning-100 text-warning-800">
                            <i class="fad fa-lock mr-1"></i>Protected
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Permissions Section -->
        @if($role->permissions->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Assigned Permissions ({{ $role->permissions->count() }})</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($role->permissions as $permission)
                <div class="flex items-center p-3 bg-success-50 rounded-lg">
                    <i class="fad fa-check-circle text-success-500 mr-3"></i>
                    <span class="text-sm font-medium text-success-900">{{ $permission->name }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Assigned Permissions</h3>
            <div class="text-center py-8">
                <i class="fad fa-shield-alt text-gray-300 text-4xl mb-4"></i>
                <p class="text-gray-500">No permissions assigned to this role yet.</p>
                <a href="{{ route('admin.roles.edit', $role) }}" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors mt-4 inline-flex items-center">
                    <i class="fad fa-plus mr-2"></i>Assign Permissions
                </a>
            </div>
        </div>
        @endif

        <!-- Users Section -->
        @if($role->users->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Users with this Role ({{ $role->users->count() }})</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($role->users as $user)
                <div class="flex items-center p-3 bg-secondary-50 rounded-lg">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-secondary-500 rounded-full flex items-center justify-center">
                            <span class="text-xs font-medium text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-secondary-900">{{ $user->name }}</p>
                        <p class="text-xs text-secondary-700">{{ $user->email }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Users with this Role</h3>
            <div class="text-center py-8">
                <i class="fad fa-users text-gray-300 text-4xl mb-4"></i>
                <p class="text-gray-500">No users assigned to this role yet.</p>
            </div>
        </div>
        @endif
    </div>
@endsection 