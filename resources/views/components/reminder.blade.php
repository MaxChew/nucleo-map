<div
    class="bg-gradient-to-r from-yellow-100 via-yellow-50 to-yellow-100 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 border border-yellow-200 dark:border-gray-700 rounded-2xl shadow-md p-4 sm:p-6 flex items-center gap-6">
    @if($date !== '1970-01-01' && $date !== null)
        <div class="flex items-center justify-center text-gray-400" :aria-label="'{{ $date }}'" data-balloon-pos="up-right">
            <i class="fad fa-history text-base"></i>
        </div>
    @endif
    <div class="flex flex-col flex-grow">
        <div class="flex justify-between items-center gap-2">
            @if (isset($href) && $href)
                <a href="{{ $href }}"
                    class="bg-orange-500 dark:bg-orange-700 text-sm md:text-base text-white text-center font-semibold px-4 py-2 rounded-full hover:bg-orange-600 dark:hover:bg-orange-600 transition-all">
                    {{ $hrefTitle }}
                </a>
            @endif

            @if (isset($html) && $html)
                {{ $slot }}
            @endif

            @isset($tag)
                <span
                    class="inline-block text-center text-xs md:text-sm font-semibold {{ $tagBgCss ?? 'bg-gray-200' }} px-3 py-1 rounded-full">
                    {{ $tag }}
                </span>
            @endisset
        </div>
        @isset($desc)
            <p class="text-gray-700 dark:text-gray-300 text-xs sm:text-sm mt-3">
                {!! $desc !!}
            </p>
        @endisset

    </div>
</div>
