<template>
    <div class="google-map-container h-full w-full relative">
        <!-- Loading state -->
        <div v-if="isLoading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
            <div class="text-center">
                <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-primary-600 mx-auto mb-4"></div>
                <p class="text-gray-600">Loading map...</p>
            </div>
        </div>

        <!-- Error state -->
        <div v-if="hasError" class="absolute inset-0 bg-white flex items-center justify-center z-10">
            <div class="text-center">
                <div class="bg-danger-100 rounded-full p-4 mb-4 mx-auto w-16 h-16 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-2xl text-danger-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Map loading failed</h3>
                <p class="text-gray-600 mb-4">{{ errorMessage }}</p>
                <button @click="initializeMap" class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">
                    Reload
                </button>
            </div>
        </div>

        <!-- Google Map container - Always render but may be hidden -->
        <div ref="mapContainer" class="h-full w-full" :class="{ 'opacity-0': isLoading || hasError }"></div>

        <!-- Results statistics -->
        <div v-if="!isLoading && !hasError"
            class="absolute top-14 left-2 bg-white rounded-lg px-4 py-2 shadow-lg border border-gray-200 z-10">
            <p class="text-sm text-gray-700">
                <i class="fas fa-map-marker-alt text-accent-600 mr-1"></i>
                Displaying <span class="font-semibold text-primary-600">{{ displayedCentersCount }}</span> medical centers
            </p>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, watch, nextTick } from 'vue'

export default {
    name: 'GoogleMapView',
    props: {
        centers: {
            type: Array,
            default: () => []
        },
        filters: {
            type: Object,
            default: () => ({})
        }
    },
    emits: ['markerClick', 'mapReady', 'error'],
    setup(props, { emit }) {
        const mapContainer = ref(null)
        const isLoading = ref(true)
        const hasError = ref(false)
        const errorMessage = ref('')

        let map = null
        let markers = []
        let infoWindow = null

        // Calculate displayed medical centers count
        const displayedCentersCount = ref(0)

        // Malaysia center coordinates
        const MALAYSIA_CENTER = { lat: 4.2105, lng: 101.9758 }
        const DEFAULT_ZOOM = 7

        // Wait for Google Maps API to load
        const waitForGoogleMapsAPI = () => {
            return new Promise((resolve, reject) => {
                // If already loaded, return directly
                if (window.google && window.google.maps) {
                    resolve()
                    return
                }

                // Listen for Google Maps API load events
                const handleMapsLoaded = () => {
                    window.removeEventListener('google-maps-loaded', handleMapsLoaded)
                    window.removeEventListener('google-maps-error', handleMapsError)
                    resolve()
                }

                const handleMapsError = (event) => {
                    window.removeEventListener('google-maps-loaded', handleMapsLoaded)
                    window.removeEventListener('google-maps-error', handleMapsError)
                    reject(new Error(event.detail?.message || 'Google Maps API loading failed'))
                }

                window.addEventListener('google-maps-loaded', handleMapsLoaded)
                window.addEventListener('google-maps-error', handleMapsError)

                // Set timeout
                setTimeout(() => {
                    window.removeEventListener('google-maps-loaded', handleMapsLoaded)
                    window.removeEventListener('google-maps-error', handleMapsError)
                    reject(new Error('Google Maps API loading timeout'))
                }, 15000)
            })
        }

        // Initialize map
        const initializeMap = async () => {
            try {
                hasError.value = false

                // Check if Google Maps API is loaded
                if (typeof google === 'undefined') {
                    // Wait for Google Maps API to load
                    await waitForGoogleMapsAPI()
                }

                await nextTick()

                // Check if map container exists
                if (!mapContainer.value) {
                    throw new Error('Map container element not found')
                }

                console.log('Map container:', mapContainer.value)
                console.log('Map container size:', mapContainer.value.offsetWidth, 'x', mapContainer.value.offsetHeight)

                // Create map instance
                map = new google.maps.Map(mapContainer.value, {
                    center: MALAYSIA_CENTER,
                    zoom: DEFAULT_ZOOM,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    styles: [
                        {
                            featureType: 'poi',
                            elementType: 'labels',
                            stylers: [{ visibility: 'off' }]
                        }
                    ],
                    mapTypeControl: true,
                    zoomControl: true,
                    streetViewControl: false,
                    fullscreenControl: true
                })

                // Create InfoWindow
                infoWindow = new google.maps.InfoWindow()

                // Load medical center markers
                await loadCenterMarkers()

                isLoading.value = false
                emit('mapReady', map)

            } catch (error) {
                console.error('Map initialization failed:', error)
                hasError.value = true
                errorMessage.value = error.message || 'Map loading failed, please try again later'
                isLoading.value = false
                emit('error', error)
            }
        }

        // Load medical center markers
        const loadCenterMarkers = async () => {
            console.log('loadCenterMarkers is called')
            console.log('map:', !!map)
            console.log('props.centers:', props.centers)
            console.log('props.centers.length:', props.centers?.length)
            
            if (!map || !props.centers) {
                console.log('Map or medical center data does not exist, exiting marker loading')
                return
            }

            // Clear existing markers
            clearMarkers()
            markers = []

            displayedCentersCount.value = props.centers.length
            console.log('displayedCentersCount:', displayedCentersCount.value)

            // Create markers for each medical center
            props.centers.forEach((center, index) => {
                console.log(`Processing medical center ${index + 1}:`, center)
                console.log('center.coordinates:', center.coordinates)
                
                if (center.coordinates) {
                    console.log(`Creating marker for ${center.name} at coordinates:`, center.coordinates)
                    
                    const marker = new google.maps.Marker({
                        position: center.coordinates,
                        map: map,
                        title: center.name,
                        icon: {
                            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="16" cy="16" r="12" fill="#1BCFB4" stroke="#ffffff" stroke-width="2"/>
                  <text x="16" y="20" text-anchor="middle" fill="white" font-size="12" font-weight="bold">H</text>
                </svg>
              `),
                            scaledSize: new google.maps.Size(32, 32),
                            anchor: new google.maps.Point(16, 16)
                        }
                    })

                                console.log(`Marker created successfully:`, marker)
            
            // Map marker click event
                    marker.addListener('click', () => {
                        showInfoWindow(marker, center)
                        emit('markerClick', center)
                    })

                    markers.push({ marker, center })
                } else {
                    console.log(`Medical center ${center.name} has no coordinate data`)
                }
            })

            // Adjust map viewport to include all markers
            if (markers.length > 0) {
                const bounds = new google.maps.LatLngBounds()
                markers.forEach(({ center }) => {
                    if (center.coordinates) {
                        bounds.extend(center.coordinates)
                    }
                })
                map.fitBounds(bounds)

                // If there's only one marker, set appropriate zoom level
                if (markers.length === 1) {
                    google.maps.event.addListenerOnce(map, 'bounds_changed', () => {
                        if (map.getZoom() > 10) {
                            map.setZoom(10)
                        }
                    })
                }
            }
        }

        // Display info window
        const showInfoWindow = (marker, center) => {
            const servicesHtml = center.services && center.services.length > 0
                ? center.services.map(service => `<span class="service-tag">${service}</span>`).join(' ')
                : '<span class="text-gray-500">No service information</span>'

            const content = `
        <div class="info-window p-4 max-w-sm">
          <div class="flex items-start">
            <div class="bg-accent-600 rounded-full p-2 mr-3 flex-shrink-0">
              <i class="fas fa-hospital text-white text-sm"></i>
            </div>
            <div class="flex-1">
              <h3 class="font-semibold text-gray-900 mb-1">${center.name}</h3>
              <p class="text-sm text-gray-600 mb-2">
                <i class="fas fa-map-marker-alt text-primary-600 mr-1"></i>
                ${center.state}
              </p>
              <div class="text-xs text-gray-500 mb-2">
                <span class="font-medium">Code:</span> ${center.code_name} (${center.code_no})
              </div>
              ${center.contact ? `
                <div class="text-xs text-gray-600 mb-2">
                  <i class="fas fa-phone text-secondary-600 mr-1"></i>
                  ${center.contact.replace(/\n/g, '<br>')}
                </div>
              ` : ''}
              <div class="mb-3">
                <div class="text-xs font-medium text-gray-700 mb-1">Services Provided:</div>
                <div class="flex flex-wrap gap-1">
                  ${servicesHtml}
                </div>
              </div>
              ${center.webpage && center.webpage !== 'Not specified' ? `
                <a href="${center.webpage}" target="_blank" 
                   class="inline-flex items-center text-xs text-primary-600 hover:text-primary-700 font-medium">
                  <i class="fas fa-external-link-alt mr-1"></i>
                                          View Website
                </a>
              ` : ''}
            </div>
          </div>
        </div>
        
        <style>
          .info-window .service-tag {
            display: inline-block;
            background: #e5f7f4;
            color: #0d9488;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 500;
            margin-right: 4px;
            margin-bottom: 2px;
          }
          .info-window a {
            text-decoration: none;
          }
          .info-window a:hover {
            text-decoration: underline;
          }
        </style>
      `

            infoWindow.setContent(content)
            infoWindow.open(map, marker)
        }

        // Clear all markers
        const clearMarkers = () => {
            markers.forEach(({ marker }) => {
                marker.setMap(null)
            })
            markers = []
        }

        // Watch for medical centers data changes
        watch(() => props.centers, async () => {
            if (map) {
                await loadCenterMarkers()
            }
        }, { deep: true })

        // Initialize map when component is mounted, with delay to ensure DOM is ready
        onMounted(async () => {
            // Wait for DOM to be fully ready
            await nextTick()
            
            // Wait longer to ensure map container is rendered
            setTimeout(() => {
                console.log('Starting map initialization...')
                console.log('Map container exists:', !!mapContainer.value)
                if (mapContainer.value) {
                    console.log('Map container size:', mapContainer.value.offsetWidth, 'x', mapContainer.value.offsetHeight)
                }
                initializeMap()
            }, 500)
        })

        return {
            mapContainer,
            isLoading,
            hasError,
            errorMessage,
            displayedCentersCount,
            initializeMap
        }
    }
}
</script>

<style scoped>
.google-map {
    position: relative;
}

/* Ensure map container has correct dimensions */
.google-map>div {
    min-height: 400px;
}

/* Loading animation */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>