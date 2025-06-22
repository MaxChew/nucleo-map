@php
    $placeholder = $placeholder ?? 'Search...';
    $addButton = $addButton ?? true;
    $addButtonText = $addButtonText ?? 'Add New';
    $addButtonRoute = $addButtonRoute ?? '#';
@endphp

<div class="bg-white p-4 rounded-lg shadow-sm border">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <!-- Search Box -->
        <div class="flex-1 w-full sm:max-w-md">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input
                    type="text"
                    v-model="filters.keyword"
                    @input="filterListing"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                    placeholder="{{ $placeholder }}"
                />
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3">
                    @if($addButton)
            <a href="{{ $addButtonRoute === '#' ? '#' : route($addButtonRoute) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ $addButtonText }}
            </a>
        @endif
        </div>
    </div>
</div> 