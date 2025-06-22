<div>
    <P class="!text-sm text-gray-500 dark:text-gray-400">{{ $title }}</P>
    @if (isset($vText))
        <span v-text="{{ $vText }}"
            class="text-gray-700 dark:text-gray-300 font-medium {{ $contentCss ?? '' }}"></span>
    @elseif (isset($content))
        <span class="text-gray-700 dark:text-gray-300 font-medium {{ $contentCss ?? '' }}">{!! $content !!}</span>
    @else
        {{ $slot }}
    @endif
</div>
