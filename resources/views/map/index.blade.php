<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    
    <!-- SEO Meta -->
    <title>{{ $pageConfig['title'] }}</title>
    <meta name="description" content="{{ $pageConfig['description'] }}">
    <meta name="keywords" content="{{ $pageConfig['keywords'] }}">
    <meta name="author" content="Nucleo Map">
    
    <!-- Open Graph Meta -->
    <meta property="og:title" content="{{ $pageConfig['og_title'] }}">
    <meta property="og:description" content="{{ $pageConfig['og_description'] }}">
    <meta property="og:image" content="{{ $pageConfig['og_image'] }}">
    <meta property="og:url" content="{{ $pageConfig['og_url'] }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Nucleo Map">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageConfig['og_title'] }}">
    <meta name="twitter:description" content="{{ $pageConfig['og_description'] }}">
    <meta name="twitter:image" content="{{ $pageConfig['og_image'] }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon-64x64.png') }}">
    
    <!-- CSS -->
    @vite(['resources/css/app.css'])
    
    <!-- FontAwesome -->
    <link href="{{ asset('fontawesome/css/all.min.css') }}" rel="stylesheet">
    
    <!-- Preconnect to Google APIs -->
    <link rel="preconnect" href="https://maps.googleapis.com">
    <link rel="dns-prefetch" href="//maps.googleapis.com">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        /* Prevent FOUC (Flash of Unstyled Content) */
        .v-cloak--hidden {
            display: none;
        }
        
        /* Ensure full screen map */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }
        
        #nucleo-map-app {
            height: 100vh;
            width: 100vw;
        }
        
        /* Loading screen */
        .initial-loading {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, #a05aff 0%, #1bcfb4 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .loading-content {
            text-align: center;
            color: white;
        }
        
        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 24px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .loading-logo i {
            color: #a05aff;
            font-size: 32px;
        }
    </style>
</head>
<body>
    <!-- Initial loading screen -->
    <div id="initial-loading" class="initial-loading">
        <div class="loading-content">
            <div class="loading-logo">
                <i class="fas fa-map-marked-alt"></i>
            </div>
            <div class="loading-spinner"></div>
            <!-- HIDDEN title texts -->
            <!-- <h2 class="text-2xl font-bold mb-2">Nucleo Map</h2> -->
            <p class="text-lg opacity-90">Loading map data...</p>
            <!-- <p class="text-sm opacity-75 mt-2">Malaysia Medical Centers Map</p> -->
        </div>
    </div>

    <!-- Vue application mount point -->
    <div id="nucleo-map-app" class="v-cloak--hidden">
        <!-- Vue components will be rendered here -->
    </div>

    <!-- JavaScript global variables -->
    <script>
        // Application configuration
        window.mapConfig = @json($mapConfig);
        window.initialFilters = @json($initialFilters);
        window.pageConfig = @json($pageConfig);
        
        // API basic configuration
        window.axios = window.axios || {};
        window.axios.defaults = window.axios.defaults || {};
        window.axios.defaults.headers = window.axios.defaults.headers || {};
        window.axios.defaults.headers.common = window.axios.defaults.headers.common || {};
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        
        // CSRF Token configuration
        const token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        }
        
        // Function to hide initial loading screen
        window.hideInitialLoading = function() {
            const loadingEl = document.getElementById('initial-loading');
            if (loadingEl) {
                loadingEl.style.opacity = '0';
                loadingEl.style.transition = 'opacity 0.5s ease-out';
                setTimeout(() => {
                    loadingEl.style.display = 'none';
                }, 500);
            }
        };
        
        // Error handling
        window.addEventListener('error', function(e) {
            console.error('Global error:', e.error);
        });
        
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Unhandled promise rejection:', e.reason);
        });
    </script>

    <!-- Google Maps API -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY', 'AIzaSyBOti4mM-6x9WDnZIjIeyEU21OpBXqWBgw') }}&libraries=places&callback=initGoogleMaps&loading=async">
    </script>
    
    <script>
        // Google Maps API callback function
        window.initGoogleMaps = function() {
            console.log('Google Maps API loaded successfully');
            window.googleMapsLoaded = true;
            
            // Trigger custom event to notify Vue application
            window.dispatchEvent(new CustomEvent('google-maps-loaded'));
        };
        
        // Set Google Maps loading timeout
        setTimeout(() => {
            if (!window.googleMapsLoaded) {
                console.warn('Google Maps API loading timeout');
                window.dispatchEvent(new CustomEvent('google-maps-error', {
                    detail: { message: 'Google Maps API loading timeout' }
                }));
            }
        }, 10000);
    </script>

    <!-- Vue.js Application -->
    @vite(['resources/js/map.js'])

    <!-- Hide loading screen after application starts -->
    <script>
        // Ensure DOM is fully loaded before hiding loading screen
        document.addEventListener('DOMContentLoaded', function() {
            // Give Vue application some time to initialize
            setTimeout(() => {
                window.hideInitialLoading();
            }, 1000);
        });
    </script>
</body>
</html> 