@extends('admin.layout.master', [
    'title' => 'FontAwesome Test Page',
])

@section('content')
    <div class="v-cloak--hidden py-6 flex flex-col gap-6">
        <div class="px-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">FontAwesome Icon Test</h1>
            </div>
        </div>

        <div class="px-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <h2 class="text-lg font-medium text-gray-900">FontAwesome Pro 5.15.3 Icon Test</h2>
            <p class="mt-1 text-sm text-gray-600">Test whether different types of FontAwesome icons display correctly</p>
                </div>

                <div class="px-6 py-4 space-y-6">
                    <!-- Solid Icons -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Solid Icons</h3>
                        <div class="flex flex-wrap gap-4">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                                <span>fas fa-check-circle</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fa fa-home text-primary-600 text-2xl"></i>
                                <span>fa fa-home</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-edit text-secondary-600 text-2xl"></i>
                                <span>fas fa-edit</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-eye text-accent-600 text-2xl"></i>
                                <span>fas fa-eye</span>
                            </div>
                        </div>
                    </div>

                    <!-- Regular Icons -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Regular Icons</h3>
                        <div class="flex flex-wrap gap-4">
                            <div class="flex items-center space-x-2">
                                <i class="far fa-sort-down text-gray-600 text-2xl"></i>
                                <span>far fa-sort-down</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="far fa-trash-alt text-danger-600 text-2xl"></i>
                                <span>far fa-trash-alt</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="far fa-blinds-open text-warning-600 text-2xl"></i>
                                <span>far fa-blinds-open</span>
                            </div>
                        </div>
                    </div>

                    <!-- Duotone Icons -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Duotone Icons</h3>
                        <div class="flex flex-wrap gap-4">
                            <div class="flex items-center space-x-2">
                                <i class="fad fa-user-graduate text-primary-600 text-2xl"></i>
                                <span>fad fa-user-graduate</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fad fa-chalkboard-teacher text-secondary-600 text-2xl"></i>
                                <span>fad fa-chalkboard-teacher</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fad fa-pen text-accent-600 text-2xl"></i>
                                <span>fad fa-pen</span>
                            </div>
                        </div>
                    </div>

                    <!-- Brand Icons -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Brand Icons</h3>
                        <div class="flex flex-wrap gap-4">
                            <div class="flex items-center space-x-2">
                                <i class="fab fa-facebook text-blue-600 text-2xl"></i>
                                <span>fab fa-facebook</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fab fa-google text-red-500 text-2xl"></i>
                                <span>fab fa-google</span>
                            </div>
                        </div>
                    </div>

                    <!-- Animation Test -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Animation Test</h3>
                        <div class="flex flex-wrap gap-4">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-spinner fa-spin text-primary-600 text-2xl"></i>
                                <span>fa-spin rotation</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-heart fa-pulse text-red-500 text-2xl"></i>
                                <span>fa-pulse pulsing</span>
                            </div>
                        </div>
                    </div>

                    <!-- Size Test -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Size Test</h3>
                        <div class="flex items-center gap-4">
                            <i class="fas fa-star text-yellow-500 fa-xs"></i>
                            <i class="fas fa-star text-yellow-500 fa-sm"></i>
                            <i class="fas fa-star text-yellow-500 fa-1x"></i>
                            <i class="fas fa-star text-yellow-500 fa-lg"></i>
                            <i class="fas fa-star text-yellow-500 fa-2x"></i>
                            <i class="fas fa-star text-yellow-500 fa-3x"></i>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">fa-xs, fa-sm, fa-1x, fa-lg, fa-2x, fa-3x</p>
                    </div>

                    <!-- Status Indicator Test -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Status Indicator Test</h3>
                        <div class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-check-circle text-accent-600"></i>
                                <span class="bg-accent-100 text-accent-800 px-2 py-1 rounded text-sm">Success Status</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-exclamation-triangle text-warning-600"></i>
                                <span class="bg-warning-100 text-warning-800 px-2 py-1 rounded text-sm">Warning Status</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-times-circle text-danger-600"></i>
                                <span class="bg-danger-100 text-danger-800 px-2 py-1 rounded text-sm">Error Status</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 