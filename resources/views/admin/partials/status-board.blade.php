@php
    $statusMapKey = $statusMapKey ?? 'COURSE_STATUS_MAP';
@endphp

<div class="py-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total statistics card -->
        <div class="group">
            <a href="{{ $url ?? '' }}?status=all" class="block">
                <div class="relative bg-gradient-to-br from-slate-50 to-white p-6 rounded-2xl border border-gray-200/50 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 hover:border-primary-300">
                    <!-- Hover effect -->
                    <div class="absolute inset-0 bg-gradient-to-br from-gray-400/80 to-gray-500/90 rounded-2xl opacity-0  transition-opacity duration-300"></div>
                    
                    <!-- Icon and title -->
                    <div class="relative flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-gray-400 to-gray-500 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 011-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-600 transition-colors duration-300">Total</h3>
                                <p class="text-xs text-gray-500 transition-colors duration-300">All Records</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Value display -->
                    <div class="relative mb-4">
                        <div class="text-3xl font-bold text-gray-700 transition-colors duration-300">{{ number_format($summary->all ?? 0) }}</div>
                        <div class="text-sm text-gray-500 transition-colors duration-300 mt-1">
                            @if(($summary->all ?? 0) > 0)
                                <span class="inline-flex items-center text-green-600 transition-colors duration-300">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                    Active Data
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Progress bar -->
                    <div class="relative w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-gray-400 to-gray-500 h-2 rounded-full" style="width: 100%"></div>
                    </div>
                </div>
            </a>
        </div>

        @foreach ($summary as $key => $data)
            @if ($key === 'all' || is_array($data) || $key === 'total' || $key === 'by_state' || $key === 'by_service')
                @continue
            @endif
            
            @php
                // Ensure $data is numeric type
                if (!is_numeric($data)) {
                    continue;
                }
                
                $colorClass = App\Enums\Status::CLASSNAME[strtoupper($key)] ?? 'gray-400';
                
                // Get status name with error handling
                try {
                    $statusName = ucfirst(\App\Enums\Status::getCustomValueByKey($statusMapKey, $key)) ?: ucfirst($key);
                } catch (\Exception $e) {
                    $statusName = ucfirst($key);
                }
                $percentage = ($summary->all ?? 0) > 0 ? round(($data / ($summary->all ?? 1)) * 100) : 0;
                
                // Define gradient color mapping
                $gradientColors = [
                    'green-500' => ['from-green-400', 'to-green-600'],
                    'blue-500' => ['from-blue-400', 'to-blue-600'],
                    'yellow-500' => ['from-yellow-400', 'to-yellow-600'],
                    'red-500' => ['from-red-400', 'to-red-600'],
                    'purple-500' => ['from-purple-400', 'to-purple-600'],
                    'indigo-500' => ['from-indigo-400', 'to-indigo-600'],
                    'pink-500' => ['from-pink-400', 'to-pink-600'],
                    'primary-500' => ['from-primary-400', 'to-primary-600'],
                    'accent-500' => ['from-accent-400', 'to-accent-600'],
                    'secondary-500' => ['from-secondary-400', 'to-secondary-600'],
                ];
                
                $gradientClass = $gradientColors[$colorClass] ?? ['from-gray-400', 'to-gray-600'];
            @endphp

            <!-- Status card -->
            <div class="group">
                <a href="{{ $url ?? '' }}?status={{ $key }}" class="block">
                    <div class="relative bg-gradient-to-br from-white to-gray-50 p-6 rounded-2xl border {{ $key == ($status??'') ? 'border-' . $colorClass . ' ring-4 ring-' . $colorClass . '/20 shadow-lg' : 'border-gray-200/50' }} shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 hover:border-{{ $colorClass }}/50">
                        <!-- Icon and title -->
                        <div class="relative flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br {{ $gradientClass[0] }} {{ $gradientClass[1] }} rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    @if($key === 'active')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @elseif($key === 'inactive')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @elseif($key === 'pending')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-{{ $colorClass }} transition-colors duration-300">{{ $statusName }}</h3>
                                    <p class="text-xs text-gray-500 transition-colors duration-300">{{ $percentage }}% of total</p>
                                </div>
                            </div>
                            
                            <!-- Growth indicator -->
                            @if($data > 0)
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $colorClass }}/10 text-{{ $colorClass }} group-hover:bg-white/20 transition-colors duration-300">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                        </svg>
                                        Active
                                    </span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Value display -->
                        <div class="relative mb-4">
                            <div class="text-3xl font-bold text-{{ $colorClass }} transition-colors duration-300">{{ number_format($data) }}</div>
                            <div class="text-sm text-gray-500 transition-colors duration-300 mt-1">
                                @if($percentage > 0)
                                    <span class="text-{{ $colorClass }} transition-colors duration-300">{{ $percentage }}% of total</span>
                                @endif
                            </div>
                        </div>

                        <!-- Progress bar -->
                        <div class="relative w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r {{ $gradientClass[0] }} {{ $gradientClass[1] }} h-2 rounded-full transition-all duration-500" style="width: {{ min($percentage, 100) }}%"></div>
                        </div>

                        <!-- Hover effect -->
                        <div class="absolute inset-0 bg-gradient-to-br {{ $gradientClass[0] }}/80 {{ $gradientClass[1] }}/90 rounded-2xl opacity-0 transition-opacity duration-300"></div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div> 