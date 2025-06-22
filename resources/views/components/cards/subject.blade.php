<div class="flex items-center space-x-4 p-3 w-full bg-gray-200 dark:bg-gray-900 rounded-lg">
    <div class="w-full flex flex-col">
        <h3 class="text-gray-700 dark:text-gray-300">
            @if (isset($vTextName))
                <span v-text="{{ $vTextName }}"></span>
            @else
                <span>{{ $name }}</span>
            @endif
        </h3>
        <p class="text-xs text-gray-600 dark:text-gray-400">
            @if (isset($vTextCode))
                <span v-text="{{ $vTextCode }}"></span>
            @else
                <span>{{ $code }}</span>
            @endif
        </p>

    </div>
</div>
