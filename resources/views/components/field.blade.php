@php
    $errorbag = isset($errorbag) ? $errorbag : 'verrors';
    if (isset($name)) $name = empty($raw) ? "'{$name}'" : $name;
@endphp

<div class="mb-5 relative{{ isset($class) ? ' ' . $class : '' }}"
    {!! isset($name) ? ":class=\"{ 'has-error': {$errorbag}.has({$name}) }\"" : ''  !!}
>
    @isset($label)
        <h3 class="dark:text-gray-300" @isset($id) for="{{ $id }}" @else :for="{{ $name ?? 'false' }}" @endisset>
            {{ $label }}
            @if($required ?? false) <span class="text-danger-500">*</span> @endif
        </h3>
    @endisset

    @isset($helper)
        <span class="block mt-px text-gray-600">{{ $helper }}</span>
    @endisset

    {{ $slot }}

    @isset($name)
        @if(!($hideError ?? false))
            <span v-if="{{ "{$errorbag}.has({$name})" }}"
                class="block mt-1 text-xs text-danger-600"
                v-text="{{ "{$errorbag}.first({$name})" }}"
            ></span>
            @isset($display_error_place)
            <span v-else
                class="block mt-0 md:mt-4 lg:mt-4 text-danger-600"

            >&nbsp;</span>
            @endisset
        @endif
    @endisset

    @isset($message)
        <p class="mt-2 text-xs text-gray-400 italic !font-medium">{{ $message }}</p>
    @endisset

</div>
