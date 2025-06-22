@extends('admin.layout.master', [
    'title' => 'Medical Centers Management',
])

@vuedata([
    'filters' => (object) [
        'keyword' => '',
        'status' => '',
        'state' => '',
        'service' => '',
    ],
    'current_status' => $status ?? 'all',
    'current_state' => $state ?? 'all',
    'current_service' => $service ?? 'all',
    'centerModal' => false,
])

@section('content')
    <div class="v-cloak--hidden py-6 flex flex-col gap-6">
        <!-- Page Header -->
        <div class="px-6">
            <div class="flex items-center justify-between">
                <h1>Medical Centers Management</h1>
                <a href="{{ route('admin.centers.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fad fa-plus mr-2"></i>
                    Add Medical Center
                </a>
            </div>
        </div>

        <!-- Status Board -->
        <div class="px-6">
            @include('admin.partials.status-board', [
                'summary' => $summary ?? [],
                'url' => 'admin.centers.index',
                'statusMapKey' => 'CENTER_STATUS_MAP',
                'statusListKey' => 'CENTER_STATUS_LIST',
                'labels' => [
                    'total' => 'Total Centers',
                ],
            ])
        </div>

        <!-- Search and Filters -->
        <div class="px-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Search & Filters</h2>
                    <p class="mt-1 text-sm text-gray-600">Use the options below to filter the medical centers list.</p>
                </div>
                
                <div class="px-6 py-4 space-y-6">
                    <!-- Search Bar -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fad fa-search text-gray-400"></i>
                        </div>
                        <input type="text" v-model="filters.keyword" @input="filterListing" 
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                               placeholder="Search medical center name, code, etc...">
                    </div>

                    <!-- Filter Options -->
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

                        <!-- State Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">State/Region Filter</label>
                            <select v-model="current_state" @change="filterListing"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="all">All States/Regions</option>
                                <option value="W.P. Kuala Lumpur">Kuala Lumpur</option>
                                <option value="Selangor">Selangor</option>
                                <option value="Pulau Pinang">Penang</option>
                                <option value="Johor">Johor</option>
                                <option value="Perak">Perak</option>
                                <option value="Sarawak">Sarawak</option>
                                <option value="Sabah">Sabah</option>
                                <option value="Kelantan">Kelantan</option>
                                <option value="Pahang">Pahang</option>
                                <option value="Melaka">Melaka</option>
                                <option value="W.P. Putrajaya">Putrajaya</option>
                            </select>
                        </div>

                        <!-- Service Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service Filter</label>
                            <select v-model="current_service" @change="filterListing"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="all">All Services</option>
                                <option value="SPECT">SPECT Scan</option>
                                <option value="PET">PET Scan</option>
                                <option value="RAI">Iodine-131 Treatment</option>
                                <option value="PRRT">PRRT Treatment</option>
                                <option value="PSMA">PSMA Treatment</option>
                                <option value="SIRT">SIRT Treatment</option>
                                <option value="MIBG">MIBG Treatment</option>
                                <option value="BONE-P">Bone Pain Treatment</option>
                                <option value="RSO">RSO Treatment</option>
                                <option value="AC">AC Treatment</option>
                            </select>
                        </div>
                    </div>

                    <!-- Clear Filters -->
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
                    <h2 class="text-lg font-medium text-gray-900">Medical Centers List</h2>
                    <p class="mt-1 text-sm text-gray-600">Detailed information of all medical centers.</p>
                </div>
                
                <div class="overflow-hidden">
                    @component('components.listing-div', [
                        'rowNumber' => true,
                        'columns' => Columns::load([
                            'is_active' => ['label' => 'Status', 'sortable' => false, 'align' => 'center'],
                            'name' => ['label' => 'Center Name', 'sortable' => true],
                            'code' => ['label' => 'Code', 'sortable' => false, 'align' => 'center'],
                            'state' => ['label' => 'State', 'sortable' => false],
                            'services' => ['label' => 'Services', 'sortable' => false],
                            'created_at' => ['label' => 'Created At', 'sortable' => true],
                        ]),
                        'url' => passport_url('admin/private/centers?page=1'),
                        ':params' => '{ filters:filterParams, status:current_status, state:current_state, service:current_service }',
                        'initialSorts' => ['updated_at:desc'],
                        'ref' => 'listing',
                    ])
                    @slot('column:is_active')
                        <div class="flex justify-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                :class="row.is_active ? 'bg-accent-100 text-accent-800' : 'bg-warning-100 text-warning-800'"
                                v-text="row.is_active ? 'Active' : 'Inactive'">
                            </span>
                        </div>
                    @endslot

                    @slot('column:name')
                        <div class="flex flex-col">
                            <span class="font-semibold text-primary-600" v-text="row.name"></span>
                            <span class="text-xs text-gray-500" v-if="row.address" v-text="row.address"></span>
                        </div>
                    @endslot

                    @slot('column:code')
                        <div class="text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-mono bg-gray-100 text-gray-800"
                                v-text="row.code">
                            </span>
                        </div>
                    @endslot

                    @slot('column:services')
                        <div class="flex flex-wrap gap-1">
                            <template v-for="service in row.services" :key="service">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-secondary-100 text-secondary-800"
                                    v-text="service">
                                </span>
                            </template>
                        </div>
                    @endslot

                    @slot('action')
                        <div class="flex items-center gap-2">
                            <a :href="$ziggyRoute('admin.centers.show', [row.id])"
                                class="text-secondary-500 hover:text-secondary-600 transition-colors" aria-label="View Center"
                                data-balloon-pos="up-right" target="{{ $onTarget ?? false ? '_blank' : '_self' }}">
                                <i class="fad fa-eye text-base"></i>
                            </a>

                            <a :href="$ziggyRoute('admin.centers.edit', [row.id])"
                                class="text-primary-500 hover:text-primary-600 transition-colors" aria-label="Edit Center"
                                data-balloon-pos="up-right" v-if="row.deleted_at == null"
                                target="{{ $onTarget ?? false ? '_blank' : '_self' }}">
                                <i class="fad fa-pen text-base"></i>
                            </a>

                            <button @click="deleteCenter(row.id, row.name)"
                                class="text-danger-500 hover:text-danger-600 transition-colors cursor-pointer"
                                aria-label="Delete Center" data-balloon-pos="up-right">
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

{{-- Removed global function override to let mixin methods work properly --}}
