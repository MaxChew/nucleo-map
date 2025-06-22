@if(isset($breadcrumb) && is_array($breadcrumb))
    <nav class="breadcrumb">
        <ul class="flex space-x-2 text-sm">
            @foreach($breadcrumb as $url => $title)
                @if ($loop->last)
                    <li class="font-semibold text-gray-600 dark:text-gray-100">{{ $title }}</li>
                @else
                    <li>
                        <a href="{{ $url }}" class="text-primary-600 dark:text-primary-400 hover:underline">{!! $title !!}</a>
                    </li>
                    <span>/</span>
                @endif
            @endforeach
        </ul>
    </nav>
@endif
