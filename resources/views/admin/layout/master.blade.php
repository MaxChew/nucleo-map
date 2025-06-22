<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    @include('layout.head')
    @vite(['resources/js/admin.js'])
</head>

<body class="font-sans font-normal leading-normal text-dark bg-gray-100">
    <div id="app" v-cloak>
        <div class="flex relative">
            @include('admin.partials.side-menu')
            <div class="flex-auto bg-primary-100">
                @include('admin.partials.header')
                
                @yield('content')

                @include('admin.partials.footer')
            </div>
        </div>
    </div>

    @include('layout.js-variable')
    @stack('pre-scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @stack('scripts')
</body>

</html> 