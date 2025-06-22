@extends('admin.layout.master', [
    'title' => 'Roles & Permissions Management',
])

@vuedata([
    'filters' => (object) [
        'status' => [],
    ],
    'current_status' => $status ?? 'all',
    'current_type' => 'all',
    'roleModal' => false,
])

@section('content')
    <div class="v-cloak--hidden py-6 flex flex-col gap-6">
        <div class="px-6">
            <div class="flex items-center justify-between">
                <h1>Roles & Permissions Management</h1>
            </div>
        </div>


        <div class="px-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Role List</h2>
                    <p class="mt-1 text-sm text-gray-600">All roles and their permission information in the system.</p>
                </div>

                <div class="overflow-hidden">
                    @component('components.listing-div', [
                        'rowNumber' => true,
                        'columns' => Columns::load([
                            'name' => ['label' => 'Role Name', 'sortable' => true],
                            'display_name' => ['label' => 'Display Name', 'sortable' => false],
                            'permissions_count' => ['label' => 'Permissions', 'sortable' => false, 'align' => 'center'],
                            'users_count' => ['label' => 'Users', 'sortable' => false, 'align' => 'center'],
                            'created_at' => ['label' => 'Created At', 'sortable' => true],
                        ]),
                        'url' => passport_url('admin/private/roles?page=1'),
                        ':params' => '{ filters:filterParams, status:current_status, type:current_type }',
                        'initialSorts' => ['created_at:desc'],
                        'ref' => 'listing',
                    ])
                        @slot('column:name')
                            <div class="flex flex-col">
                                <span class="font-semibold text-primary-600" v-text="row.name"></span>
                                <span class="text-xs text-gray-500" v-if="row.description" v-text="row.description"></span>
                            </div>
                        @endslot

                        @slot('column:display_name')
                            <span v-text="row.display_name || row.name"></span>
                        @endslot

                        @slot('column:permissions_count')
                            <div class="text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="row.permissions_count > 0 ? 'bg-accent-100 text-accent-800' :
                                        'bg-gray-100 text-gray-800'"
                                    v-text="row.permissions_count">
                                </span>
                            </div>
                        @endslot

                        @slot('column:users_count')
                            <div class="text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="row.users_count > 0 ? 'bg-secondary-100 text-secondary-800' :
                                        'bg-gray-100 text-gray-800'"
                                    v-text="row.users_count">
                                </span>
                            </div>
                        @endslot
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
@endsection
