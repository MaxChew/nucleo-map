@extends('admin.layout.master', [
    'title' => 'Activity Logs',
])

@vuedata([
    'filters' => (object) [
        'keyword' => '',
        'status' => [],
        'log_name' => '',
        'event' => '',
    ],
    'current_status' => $status ?? 'all',
    'logs_stats' => $logs_stats ?? [],
    'logTypes' => $logTypes ?? [],
    'eventTypes' => $eventTypes ?? [],
    'current_log_name' => $log_name ?? 'all',
    'current_event' => $event ?? 'all',
])

@section('content')
    <div class="v-cloak--hidden py-6 flex flex-col gap-6">
        <!-- Page Header -->
        <div class="px-6">
            <div class="flex items-center justify-between">
                <h1>Activity Logs</h1>
                <button @click="bulkClearLogs" 
                        v-if="hasLogs"
                        class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fad fa-trash mr-2"></i>
                    Clear All Logs
                </button>
            </div>
        </div>

        <!-- Status Board -->
        <div class="px-6">
            @include('admin.partials.status-board', [
                'summary' => $logs_stats ?? [],
                'labels' => [
                    'total' => 'Total Logs',
                    'today' => 'Today',
                    'this_week' => 'This Week',
                    'this_month' => 'This Month',
                ]
            ])
        </div>

        <!-- Search and Filters -->
        <div class="px-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Search & Filter</h2>
                    <p class="mt-1 text-sm text-gray-600">Use the following options to filter the activity logs list.</p>
                </div>
                
                <div class="px-6 py-4 space-y-6">
                    <!-- Search Bar -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fad fa-search text-gray-400"></i>
                        </div>
                        <input type="text" v-model="filters.keyword" @input="filterListing" 
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                               placeholder="Search description, user or log name...">
                    </div>

                    <!-- Filter Options Grid -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <!-- Log Type Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Log Type</label>
                            <select v-model="current_log_name" @change="filterListing"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="all">All Types</option>
                                <option value="default">Default</option>
                                <option value="user">User</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>

                        <!-- Event Type Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Type</label>
                            <select v-model="current_event" @change="filterListing"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="all">All Events</option>
                                <option value="created">Created</option>
                                <option value="updated">Updated</option>
                                <option value="deleted">Deleted</option>
                                <option value="login">Login</option>
                                <option value="logout">Logout</option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Filter</label>
                            <select v-model="current_status" @change="filterListing"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="all">All Status</option>
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
                    <h2 class="text-lg font-medium text-gray-900">Activity Logs List</h2>
                    <p class="mt-1 text-sm text-gray-600">Detailed information of all system activity logs.</p>
                </div>
                
                <div class="overflow-hidden">
                    @component('components.listing-div', [
                        'rowNumber' => true,
                        'columns' => Columns::load([
                            'log_name' => ['label' => 'Log Name', 'sortable' => true],
                            'event' => ['label' => 'Event', 'sortable' => true],
                            'description' => ['label' => 'Description', 'sortable' => true],
                            'causer_name' => ['label' => 'User', 'sortable' => false],
                            'subject_type' => ['label' => 'Subject', 'sortable' => false],
                            'created_at' => ['label' => 'Date', 'sortable' => true],
                        ]),
                        'url' => passport_url('admin/private/logs?page=1'),
                        ':params' => '{ filters:filterParams, status:current_status, log_name:current_log_name, event:current_event }',
                        'initialSorts' => ['created_at:desc'],
                        'ref' => 'listing',
                    ])
                        @slot('column:log_name')
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-secondary-100 text-secondary-800': row.log_name === 'default',
                                        'bg-accent-100 text-accent-800': row.log_name === 'user',
                                        'bg-primary-100 text-primary-800': row.log_name === 'admin',
                                        'bg-gray-100 text-gray-800': !['default', 'user', 'admin'].includes(row.log_name)
                                    }"
                                    v-text="row.log_name || 'default'">
                                </span>
                            </div>
                        @endslot

                        @slot('column:event')
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-accent-100 text-accent-800': row.event === 'created',
                                        'bg-warning-100 text-warning-800': row.event === 'updated',
                                        'bg-danger-100 text-danger-800': row.event === 'deleted',
                                        'bg-secondary-100 text-secondary-800': row.event === 'login',
                                        'bg-primary-100 text-primary-800': row.event === 'logout',
                                        'bg-gray-100 text-gray-800': !row.event || !['created', 'updated', 'deleted', 'login', 'logout'].includes(row.event)
                                    }"
                                    v-text="row.event || 'N/A'">
                                </span>
                            </div>
                        @endslot

                        @slot('column:description')
                            <div class="max-w-sm">
                                <p class="text-sm text-gray-900 truncate" v-text="row.description" :title="row.description"></p>
                            </div>
                        @endslot

                        @slot('column:causer_name')
                            <div class="flex items-center" v-if="row.causer_name">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-white" v-text="row.causer_name.charAt(0).toUpperCase()"></span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900" v-text="row.causer_name"></p>
                                    <p class="text-xs text-gray-500" v-text="row.causer_email" v-if="row.causer_email"></p>
                                </div>
                            </div>
                            <div v-else class="text-sm text-gray-500">
                                <i class="fad fa-robot mr-1 text-accent-500"></i>System
                            </div>
                        @endslot

                        @slot('column:subject_type')
                            <div v-if="row.subject_type">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent-100 text-accent-800"
                                    v-text="row.subject_type">
                                </span>
                                <p class="text-xs text-gray-500 mt-1" v-if="row.subject_name" v-text="row.subject_name"></p>
                            </div>
                            <span v-else class="text-sm text-gray-400">-</span>
                        @endslot

                        @slot('column:created_at')
                            <div class="text-sm text-gray-900">
                                <p v-text="$dayjs(row.created_at).format('MMM DD, YYYY')"></p>
                                <p class="text-xs text-gray-500" v-text="$dayjs(row.created_at).format('HH:mm:ss')"></p>
                                <p class="text-xs text-secondary-600" v-text="row.time_ago"></p>
                            </div>
                        @endslot

                        @slot('action')
                            <div class="flex items-center gap-2">
                                <a :href="$ziggyRoute ? $ziggyRoute('admin.logs.show', row.id) : '#'" 
                                   class="text-secondary-500 hover:text-secondary-600 transition-colors" 
                                   aria-label="View Log Details" 
                                   data-balloon-pos="up-right"
                                   target="{{ $onTarget ?? false ? '_blank' : '_self' }}">
                                    <i class="fad fa-eye text-base"></i>
                                </a>
                                
                                <button @click="deleteLog(row.id, row.description)"
                                    class="text-danger-500 hover:text-danger-600 transition-colors cursor-pointer" 
                                    aria-label="Delete Log" 
                                    data-balloon-pos="up-right">
                                    <i class="fad fa-trash text-base"></i>
                                </button>
                            </div>
                        @endslot

                        @slot('more')
                            <div class="p-4 bg-gray-50 rounded-lg" v-if="row.formatted_properties">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Properties</h4>
                                <div class="space-y-2">
                                    <div v-for="(value, key) in row.formatted_properties" :key="key" class="flex">
                                        <dt class="text-xs font-medium text-gray-500 w-1/3" v-text="key + ':'"></dt>
                                        <dd class="text-xs text-gray-900 w-2/3">
                                            <pre class="whitespace-pre-wrap text-xs" v-if="typeof value === 'string' && value.includes('{')" v-text="value"></pre>
                                            <span v-else v-text="value"></span>
                                        </dd>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="p-4 text-center text-gray-500">
                                No additional properties
                            </div>
                        @endslot
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Removed global function override to allow mixin methods to work normally --}}

 