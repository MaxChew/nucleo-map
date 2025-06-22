<!-- Meta Tags -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="application-name" content="{{ config('app.name') }}">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">

<!-- Favicon -->
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
<link rel="mask-icon" href="{{ asset('safari-pinned-tab.svg') }}" color="#ff4a00">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">

<!-- Title -->
@if($title = $title ?? null)
    <title>{{ $title }} | {{ config('app.name') }}</title>
@else
    <title>{{ config('defaults.meta_title') }}</title>
@endif

<!-- Styles -->
@vite(['resources/css/app.css'])

    <!-- FontAwesome Pro 5.15.3 - Line 54 -->
<link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">

@stack('styles') 