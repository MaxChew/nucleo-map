@extends('admin.layout.master', [
    'title' => 'Medical Center Details',
])

@section('content')
    <div class="v-cloak--hidden py-6 flex flex-col gap-6">
        <div class="px-6">
            <div class="flex items-center justify-between">
                <h1>Medical Center Details</h1>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.centers.edit', $center) }}"
                        class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fad fa-pen mr-2"></i>
                        Edit
                    </a>
                    <a href="{{ route('admin.centers.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-secondary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-secondary-700 focus:bg-secondary-700 active:bg-secondary-900 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fad fa-arrow-left mr-2"></i>
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Center Summary -->
        <div class="px-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="w-16 h-16 bg-primary-600 rounded-full flex items-center justify-center text-white text-xl font-bold mr-4">
                            {{ strtoupper(substr($center->name, 0, 2)) }}
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">{{ $center->name }}</h2>
                            <p class="text-sm text-gray-600">{{ $center->state }} - {{ $center->code_name }} ({{ $center->code_no }})</p>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    @class([
                                        'bg-accent-100 text-accent-800' => $center->is_active,
                                        'bg-warning-100 text-warning-800' => !$center->is_active,
                                    ])>
                                    {{ $center->is_active ? 'Active Status' : 'Inactive Status' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Center Details -->
        <div class="px-6">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Basic Information</h2>
                        <p class="mt-1 text-sm text-gray-600">Basic information about the medical center.</p>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="space-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">Medical Center Name</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $center->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">State/Region</dt>
                                <dd class="text-lg text-gray-900">{{ $center->state }}</dd>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Code Name</dt>
                                    <dd class="text-lg font-mono bg-gray-100 px-3 py-2 rounded-lg text-gray-900">{{ $center->code_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Code Number</dt>
                                    <dd class="text-lg font-mono bg-gray-100 px-3 py-2 rounded-lg text-gray-900">{{ $center->code_no }}</dd>
                                </div>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-2">Status</dt>
                                <dd>
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium"
                                        @class([
                                            'bg-accent-100 text-accent-800 border border-accent-200' => $center->is_active,
                                            'bg-warning-100 text-warning-800 border border-warning-200' => !$center->is_active,
                                        ])>
                                        <div class="w-2 h-2 rounded-full mr-2"
                                            @class([
                                                'bg-accent-400' => $center->is_active,
                                                'bg-warning-400' => !$center->is_active,
                                            ])></div>
                                        {{ $center->is_active ? 'Active Status' : 'Inactive Status' }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Contact Information</h2>
                        <p class="mt-1 text-sm text-gray-600">Contact details and website of the medical center.</p>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="space-y-6">
                            @if($center->contact)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-2">Contact Details</dt>
                                <dd class="bg-gray-50 rounded-xl p-4 text-gray-900 whitespace-pre-line leading-relaxed">{{ $center->contact }}</dd>
                            </div>
                            @else
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-2">Contact Details</dt>
                                <dd class="text-gray-400 italic">No contact information provided</dd>
                            </div>
                            @endif
                            
                            @if($center->webpage)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-2">Official Website</dt>
                                <dd>
                                    <a href="{{ $center->webpage }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-secondary-50 hover:bg-secondary-100 text-secondary-600 hover:text-secondary-700 rounded-lg border border-secondary-200 transition-all duration-200">
                                        <i class="fad fa-external-link mr-2"></i>
                                        <span class="truncate max-w-xs">{{ $center->webpage }}</span>
                                    </a>
                                </dd>
                            </div>
                            @else
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-2">Official Website</dt>
                                <dd class="text-gray-400 italic">No website information provided</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services -->
        <div class="px-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Services Provided</h2>
                    <p class="mt-1 text-sm text-gray-600">Nuclear medicine services provided by this medical center.</p>
                </div>
                <div class="px-6 py-4">
                    @if($center->services && count($center->services) > 0)
                        @php
                            $services = [
                                'SPECT' => 'SPECT Scan',
                                'PET' => 'PET Scan',
                                'RAI' => 'Iodine-131 Treatment',
                                'PRRT' => 'PRRT Treatment',
                                'PSMA' => 'PSMA Treatment',
                                'SIRT' => 'SIRT Treatment',
                                'MIBG' => 'MIBG Treatment',
                                'BONE-P' => 'Bone Pain Treatment',
                                'RSO' => 'RSO Treatment',
                                'AC' => 'AC Treatment',
                            ];
                        @endphp
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($center->services as $serviceCode)
                                @php
                                    $service = $services[$serviceCode] ?? $serviceCode;
                                @endphp
                                <div class="flex items-center p-4 bg-primary-50 hover:bg-primary-100 border border-primary-200 rounded-lg transition-all duration-200">
                                    <div class="w-3 h-3 bg-primary-500 rounded-full mr-3"></div>
                                    <span class="text-primary-800 font-medium text-sm">{{ $service }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fad fa-clipboard-list text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500 text-sm">No service information available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Metadata -->
        <div class="px-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">System Information</h2>
                    <p class="mt-1 text-sm text-gray-600">Record creation and update time information.</p>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $center->created_at?->format('Y-m-d H:i:s') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Updated At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $center->updated_at?->format('Y-m-d H:i:s') }}</dd>
                        </div>
                        @if($center->created_by)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created By</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $center->creator?->name ?? 'N/A' }}</dd>
                        </div>
                        @endif
                        @if($center->updated_by)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Updated By</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $center->updater?->name ?? 'N/A' }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection 