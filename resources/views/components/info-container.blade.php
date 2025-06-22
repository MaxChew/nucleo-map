<div class="content-wrapper w-full">
    <div
        class="flex gap-2 justify-between items-center font-bold text-gray-700 dark:text-white px-4 py-3 sm:px-5 sm:py-4 border-b">
        @isset($title)
            <h2 class="text-xs md:text-base">
                {{ $title }}
                @isset($desc)
                    <p class="text-xxs md:text-xs text-gray-400 italic !font-medium">{{ $desc }}</p>
                @endisset
            </h2>
        @endisset

        @isset($tag)
            <span
                class="text-gray-600 dark:text-gray-200 bg-gray-100 dark:bg-gray-600 px-3 py-1 rounded-full border border-gray-300 text-xs sm:text-sm font-normal text-center">
                {{ $tag }}
            </span>
        @endisset
    </div>

    <div class="p-4 sm:p-5">
        @if (isset($otherStyle))
            {{ $slot }}
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
