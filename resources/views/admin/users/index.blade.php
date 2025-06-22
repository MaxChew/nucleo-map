@extends('admin.layout.master', [
    'title' => 'User Management',
])

@vuedata([
    'filters' => (object) [
        'keyword' => '',
        'status' => '',
        'months' => [],
        'year' => null,
        'countrys' => [],
    ],
    'current_status' => $status ?? 'all',
    'current_type' => 'all',
    'userModal' => false,
])

@section('content')
    <div class="v-cloak--hidden py-6 flex flex-col gap-6">
        <!-- Page Header -->
        <div class="px-6">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
                <a href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fad fa-plus mr-2"></i>
                    Add User
                </a>
            </div>
        </div>

        <!-- Status Board -->
        <div class="px-6">
            @include('admin.partials.status-board', [
                'summary' => $summary ?? [],
                'url' => 'admin.users.index',
                'statusMapKey' => 'USER_STATUS_MAP',
                'statusListKey' => 'USER_STATUS_LIST',
                'labels' => [
                    'total' => 'Total Users',
                ],
            ])
        </div>

        <!-- Search and Filters -->
        <div class="px-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Search & Filter</h2>
                    <p class="mt-1 text-sm text-gray-600">Use the following options to filter the user list.</p>
                </div>
                
                <div class="px-6 py-4 space-y-6">
                    <!-- Search Bar -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fad fa-search text-gray-400"></i>
                        </div>
                        <input type="text" v-model="filters.keyword" @input="filterListing" 
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                               placeholder="Search name, email or phone number...">
                    </div>

                    <!-- Filter Options Grid -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Filter</label>
                            <select v-model="current_status" @change="filterListing"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="all">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <!-- User Type Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">User Type</label>
                            <select v-model="current_type" @change="filterListing"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="all">All Types</option>
                                <option value="admin">Administrator</option>
                                <option value="user">Regular User</option>
                            </select>
                        </div>

                        <!-- Gender Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gender Filter</label>
                            <select v-model="filters.gender" @change="filterListing"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="">All Genders</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>

                    <!-- Clear Filters Button -->
                    <div class="flex justify-end">
                        <button @click="clearFilters" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition ease-in-out duration-150">
                            <i class="fad fa-refresh mr-2"></i>
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="px-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">User List</h2>
                    <p class="mt-1 text-sm text-gray-600">Detailed information of all users.</p>
                </div>
                
                <div class="overflow-hidden">
                    @component('components.listing-div', [
                        'rowNumber' => true,
                        'columns' => Columns::load([
                            'is_active' => ['label' => 'Status', 'sortable' => false, 'align' => 'center'],
                            'name' => ['label' => 'Name', 'sortable' => true],
                            'email' => ['label' => 'Email', 'sortable' => true],
                            'mobile' => ['label' => 'Mobile', 'sortable' => false],
                            'gender' => ['label' => 'Gender', 'sortable' => false],
                            'created_at' => ['label' => 'Created At', 'sortable' => true],
                        ]),
                        'url' => passport_url('admin/private/users?page=1'),
                        ':params' => '{ filters:filterParams, status:current_status, type:current_type }',
                        'initialSorts' => ['updated_at:desc'],
                        'ref' => 'listing',
                    ])
                    
                    <!-- Status Column Slot -->
                    @slot('column:is_active')
                        <div class="flex justify-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                :class="row.is_active ? 'bg-accent-100 text-accent-800' : 'bg-warning-100 text-warning-800'"
                                v-text="row.is_active ? 'Active' : 'Inactive'">
                            </span>
                        </div>
                    @endslot

                    <!-- Name Column Slot -->
                    @slot('column:name')
                        <div class="flex flex-col">
                            <span class="font-semibold text-primary-600" v-text="row.name"></span>
                            <span class="text-xs text-gray-500" v-if="row.title" v-text="row.title"></span>
                        </div>
                    @endslot

                    <!-- Email Column Slot -->
                    @slot('column:email')
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-900" v-text="row.email"></span>
                            <span class="text-xs text-gray-500" v-if="row.email_verified_at">Verified</span>
                        </div>
                    @endslot

                    <!-- Mobile Column Slot -->
                    @slot('column:mobile')
                        <span class="text-sm text-gray-900" v-text="row.mobile || '-'"></span>
                    @endslot

                    <!-- Gender Column Slot -->
                    @slot('column:gender')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-secondary-100 text-secondary-800"
                            v-text="row.gender ? (row.gender === 'male' ? 'Male' : 'Female') : '-'">
                        </span>
                    @endslot

                    <!-- Action Buttons -->
                    @slot('action')
                        <div class="flex items-center gap-2">
                            <a :href="$ziggyRoute('admin.users.show', [row.id])"
                                class="text-secondary-500 hover:text-secondary-600 transition-colors" 
                                aria-label="View" data-balloon-pos="up-right">
                                <i class="fad fa-eye text-base"></i>
                            </a>

                            <a :href="$ziggyRoute('admin.users.edit', [row.id])"
                                class="text-primary-500 hover:text-primary-600 transition-colors" 
                                aria-label="Edit" data-balloon-pos="up-right">
                                <i class="fad fa-pen text-base"></i>
                            </a>

                            <button @click="deleteUser(row.id, row.name)"
                                class="text-danger-500 hover:text-danger-600 transition-colors cursor-pointer"
                                aria-label="Delete" data-balloon-pos="up-right">
                                <i class="fad fa-trash text-base"></i>
                            </button>
                        </div>
                    @endslot
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Removed global function override to allow mixin methods to work normally --}}
