<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    
    <!-- Basic Meta -->
    <title>Nucleo Map - Medical Centers Map</title>
    <meta name="description" content="Malaysia Medical Centers Map">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Prevent clickjacking in iframe - these will be set via HTTP headers -->
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
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
        /* Embedded-specific styles */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
        
        #nucleo-map-app {
            height: 100vh;
            width: 100vw;
            position: relative;
        }
        
        /* Prevent FOUC */
        .v-cloak--hidden {
            display: none;
        }
        
        /* Simplified loading screen */
        .embed-loading {
            position: fixed;
            inset: 0;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .embed-loading-content {
            text-align: center;
            color: #374151;
        }
        
        .embed-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #e5e7eb;
            border-top: 3px solid #a05aff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 16px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Simplified application title in embedded mode */
        .embed-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 12px 16px;
            flex-shrink: 0;
        }
        
        .embed-header h1 {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
            color: #111827;
        }
        
        /* Hide certain elements not needed in embedded mode */
        .embed-mode .hide-in-embed {
            display: none !important;
        }
        
        /* Adjust filter panel in embedded mode */
        .embed-mode .filter-panel {
            width: 280px !important;
        }
        
        /* Ensure map displays correctly in iframe */
        .gm-style {
            font-family: inherit !important;
        }
        
        /* Optimize loading state in embedded mode */
        .embed-mode .loading-text {
            font-size: 14px;
        }
    </style>
</head>
<body class="embed-mode">
    <!-- Simplified loading screen -->
    <div id="embed-loading" class="embed-loading">
        <div class="embed-loading-content">
            <div class="embed-spinner"></div>
            <p class="text-sm text-gray-600">Loading...</p>
        </div>
    </div>

    <!-- Vue application mount point -->
    <div id="nucleo-map-app" class="v-cloak--hidden">
        <!-- Vue components will be rendered here -->
    </div>

    <!-- JavaScript global variables -->
    <script>
        // Embedded mode configuration
        window.mapConfig = @json(array_merge($mapConfig, ['embed_mode' => true]));
        window.initialFilters = @json($initialFilters);
        window.pageConfig = {
            title: 'Nucleo Map - Medical Centers Map',
            embed_mode: true
        };
        
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
        
        // Function to hide embedded loading screen
        window.hideInitialLoading = function() {
            const loadingEl = document.getElementById('embed-loading');
            if (loadingEl) {
                loadingEl.style.opacity = '0';
                loadingEl.style.transition = 'opacity 0.3s ease-out';
                setTimeout(() => {
                    loadingEl.style.display = 'none';
                }, 300);
            }
        };
        
        // Error handling for embedded mode (more concise)
        window.addEventListener('error', function(e) {
            console.error('Embed error:', e.error);
        });
        
        // Send events to parent window (for iframe communication)
        window.sendToParent = function(eventType, data) {
            if (window.parent && window.parent !== window) {
                try {
                    window.parent.postMessage({
                        type: 'nucleo-map-' + eventType,
                        data: data
                    }, '*');
                } catch (e) {
                    console.warn('Unable to send message to parent:', e);
                }
            }
        };
        
        // Notify parent window that map has loaded
        window.addEventListener('load', function() {
            window.sendToParent('loaded', {
                timestamp: new Date().toISOString(),
                url: window.location.href
            });
        });
    </script>

    <!-- Google Maps API -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY', 'AIzaSyBOti4mM-6x9WDnZIjIeyEU21OpBXqWBgw') }}&libraries=places&callback=initGoogleMaps&loading=async">
    </script>
    
    <script>
        // Google Maps API callback function
        window.initGoogleMaps = function() {
            console.log('Google Maps API loaded in embed mode');
            window.googleMapsLoaded = true;
            
            // Notify parent window that Google Maps has loaded
            window.sendToParent('maps-loaded', {
                timestamp: new Date().toISOString()
            });
            
            // Trigger custom event to notify Vue application
            window.dispatchEvent(new CustomEvent('google-maps-loaded'));
        };
        
        // Set Google Maps loading timeout (shorter time in embedded mode)
        setTimeout(() => {
            if (!window.googleMapsLoaded) {
                console.warn('Google Maps API loading timeout in embed mode');
                window.sendToParent('maps-error', {
                    message: 'Google Maps API loading timeout'
                });
                window.dispatchEvent(new CustomEvent('google-maps-error', {
                    detail: { message: 'Google Maps API loading timeout' }
                }));
            }
        }, 8000);
    </script>

    <!-- Vue.js Application -->
    @vite(['resources/js/map.js'])

    <!-- Embedded-specific initialization -->
    <script>
        // Ensure DOM is fully loaded before hiding loading screen
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loading screen faster in embedded mode
            setTimeout(() => {
                window.hideInitialLoading();
            }, 600);
        });
        
        // Listen for messages from parent window
        window.addEventListener('message', function(event) {
            // Check message origin (security consideration)
            if (event.origin !== window.location.origin && 
                !event.origin.includes('wordpress.com') && 
                !event.origin.includes('wp.com')) {
                return;
            }
            
            try {
                const message = event.data;
                if (message && message.type && message.type.startsWith('nucleo-map-')) {
                    const actionType = message.type.replace('nucleo-map-', '');
                    
                    switch (actionType) {
                        case 'resize':
                            // Handle iframe size adjustment
                            if (window.vueMapApp && window.vueMapApp.$refs.mapView) {
                                window.vueMapApp.$refs.mapView.handleResize();
                            }
                            break;
                            
                        case 'filter':
                            // Handle filter requests from parent window
                            if (window.vueMapApp && message.data) {
                                window.vueMapApp.updateFiltersFromParent(message.data);
                            }
                            break;
                    }
                }
            } catch (e) {
                console.warn('Error processing parent message:', e);
            }
        });
        
                    // Auto-adjust iframe height (if supported)
        function adjustIframeHeight() {
            const height = document.body.scrollHeight;
            window.sendToParent('resize', {
                height: height,
                width: document.body.scrollWidth
            });
        }
        
        // Listen for content changes and adjust height
        if (window.ResizeObserver) {
            const resizeObserver = new ResizeObserver(adjustIframeHeight);
            resizeObserver.observe(document.body);
        }
    </script>
</body>
</html> 