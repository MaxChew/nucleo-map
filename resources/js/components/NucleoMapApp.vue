<template>
  <div class="nucleo-map-app h-screen flex flex-col bg-gray-50">
    <!-- Application title -->
    <div class="bg-white shadow-sm border-b border-gray-200 flex-shrink-0">
      <div class="px-6 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <div class="bg-primary-600 rounded-full p-2 mr-3">
              <i class="fas fa-map-marked-alt text-white text-lg"></i>
            </div>
            <div>
              <h1 class="text-xl font-bold text-gray-900"></h1>
              <p class="text-sm text-gray-600"></p>
            </div>
          </div>
          
          <!-- Mobile filter button -->
          <button
            @click="toggleFilterPanel"
            class="md:hidden bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg transition-colors"
          >
            <i class="fas fa-sliders-h mr-2"></i>
            Filter
          </button>
        </div>
      </div>
    </div>

    <!-- Main content area -->
    <div class="flex-1 flex overflow-hidden">
      
      <!-- Filter panel -->
      <div 
        class="filter-panel bg-white shadow-lg border-r border-gray-200 flex-shrink-0 transition-all duration-300 ease-in-out"
        :class="{
          'w-80 opacity-100': !isMobile && showFilterPanel,
          'w-0 opacity-0 overflow-hidden pointer-events-none': !isMobile && !showFilterPanel,
          'fixed inset-0 z-50': isMobile && showFilterPanel,
          'hidden': isMobile && !showFilterPanel
        }"
      >
        <div v-if="showFilterPanel || isMobile" class="w-full h-full">
          <MapFilterPanel
            :filters="currentFilters"
            :available-states="availableStates"
            :available-services="availableServices"
            :result-stats="resultStats"
            @update-filters="handleFilterUpdate"
            @toggle-panel="toggleFilterPanel"
          />
        </div>
      </div>

      <!-- Map area -->
      <div class="flex-1 relative">
        <!-- Loading overlay -->
        <div v-if="isLoading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
          <div class="text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-primary-600 mx-auto mb-4"></div>
            <p class="text-gray-600 text-lg">Loading medical center data...</p>
          </div>
        </div>

        <!-- Error state -->
        <div v-else-if="hasError" class="absolute inset-0 bg-white flex items-center justify-center z-10">
          <div class="text-center max-w-md">
            <div class="bg-danger-100 rounded-full p-6 mb-6 mx-auto w-24 h-24 flex items-center justify-center">
              <i class="fas fa-exclamation-triangle text-3xl text-danger-600"></i>
            </div>
            <h3 class="text-xl font-medium text-gray-900 mb-4">Unable to load map data</h3>
            <p class="text-gray-600 mb-6">{{ errorMessage }}</p>
            <div class="space-x-4">
              <button 
                @click="loadMapData"
                class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors"
              >
                <i class="fas fa-redo mr-2"></i>
                Reload
              </button>
              <button 
                @click="resetFilters"
                class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
              >
                <i class="fas fa-times mr-2"></i>
                Clear Filters
              </button>
            </div>
          </div>
        </div>

        <!-- Google Map component -->
        <GoogleMapView
          :centers="filteredCenters"
          :filters="currentFilters"
          @marker-click="handleMarkerClick"
          @map-ready="handleMapReady"
          @error="handleMapError"
        />

        <!-- Desktop filter panel toggle button -->
        <button
          v-if="!isMobile"
          @click="toggleFilterPanel"
          class="absolute top-1 bg-white hover:bg-gray-50 border border-gray-200 rounded-lg p-3 shadow-lg transition-all duration-300 ease-in-out z-30 cursor-pointer"
          :class="showFilterPanel ? 'left-[200px]' : 'left-4'"
          :title="showFilterPanel ? 'Hide filter panel' : 'Show filter panel'"
          style="min-width: 48px; min-height: 48px;"
        >
          <i class="fas text-gray-600 hover:text-gray-800" :class="showFilterPanel ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
        </button>
      </div>
    </div>

    <!-- Loading overlay (fullscreen) -->
    <div v-if="isInitialLoading" class="fixed inset-0 bg-white flex items-center justify-center z-50">
      <div class="text-center">
        <div class="bg-primary-600 rounded-full p-6 mb-6 mx-auto w-24 h-24 flex items-center justify-center">
          <i class="fas fa-map-marked-alt text-white text-2xl"></i>
        </div>
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto mb-4"></div>
        <h3 class="text-xl font-medium text-gray-900 mb-2">Initializing map</h3>
        <p class="text-gray-600">Please wait, loading medical center data...</p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import GoogleMapView from './GoogleMapView.vue'
import MapFilterPanel from './MapFilterPanel.vue'

export default {
  name: 'NucleoMapApp',
  components: {
    GoogleMapView,
    MapFilterPanel
  },
  setup() {
    // Reactive state
    const isInitialLoading = ref(true)
    const isLoading = ref(false)
    const hasError = ref(false)
    const errorMessage = ref('')
    const showFilterPanel = ref(true)
    const isMobile = ref(false)
    
    // Data state
    const allCenters = ref([])
    const filteredCenters = ref([])
    const availableStates = ref([])
    const availableServices = ref([])
    
    // Filter state
    const currentFilters = reactive({
      keyword: '',
      state: 'all',
      service: 'all'
    })

    // Result statistics
    const resultStats = computed(() => ({
      total: filteredCenters.value.length,
      original: allCenters.value.length,
      filtered: filteredCenters.value.length !== allCenters.value.length
    }))

    // Check if mobile
    const checkMobile = () => {
      isMobile.value = window.innerWidth < 768
      if (isMobile.value) {
        showFilterPanel.value = false
      }
    }

    // Load map data
    const loadMapData = async () => {
      try {
        isLoading.value = true
        hasError.value = false
        
        // Build API request parameters
        const params = new URLSearchParams()
        if (currentFilters.keyword) params.append('keyword', currentFilters.keyword)
        if (currentFilters.state !== 'all') params.append('state', currentFilters.state)
        if (currentFilters.service !== 'all') params.append('service', currentFilters.service)
        
        // Call API (ensure correct protocol is used)
        const baseUrl = window.location.protocol + '//' + window.location.host
        const response = await fetch(`${baseUrl}/api/public/centers/map-data?${params}`)
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }
        
        const data = await response.json()
        
        if (data.status === 'success') {
          filteredCenters.value = data.data.centers || []
          availableStates.value = data.data.states || []
          availableServices.value = data.data.services || []
          
          // Save all center data on first load
          if (allCenters.value.length === 0) {
            allCenters.value = data.data.centers || []
          }
          
          console.log('Map data loaded successfully:', {
            centers: filteredCenters.value.length,
            states: availableStates.value.length,
            services: availableServices.value.length
          })
        } else {
          throw new Error(data.message || 'Failed to load data')
        }
        
      } catch (error) {
        console.error('Failed to load map data:', error)
        console.error('Error details:', {
          message: error.message,
          stack: error.stack,
          url: `${window.location.protocol}//${window.location.host}/api/public/centers/map-data?${params}`
        })
        hasError.value = true
        errorMessage.value = error.message || 'Unable to connect to server, please check network connection'
        filteredCenters.value = []
      } finally {
        isLoading.value = false
        isInitialLoading.value = false
      }
    }

    // Load filter options
    const loadFilterOptions = async () => {
      try {
        const baseUrl = window.location.protocol + '//' + window.location.host
        const response = await fetch(`${baseUrl}/api/public/centers/filters`)
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }
        
        const data = await response.json()
        
        if (data.status === 'success') {
          availableStates.value = data.data.states || []
          availableServices.value = data.data.services || []
          
          console.log('Filter options loaded successfully:', {
            states: availableStates.value.length,
            services: availableServices.value.length
          })
        }
        
      } catch (error) {
        console.error('Failed to load filter options:', error)
      }
    }

    // Handle filter update
    const handleFilterUpdate = (filters) => {
      Object.assign(currentFilters, filters)
      loadMapData()
    }

    // Reset filters
    const resetFilters = () => {
      currentFilters.keyword = ''
      currentFilters.state = 'all'
      currentFilters.service = 'all'
      loadMapData()
    }

    // Toggle filter panel
    const toggleFilterPanel = () => {
      showFilterPanel.value = !showFilterPanel.value
    }

    // Handle marker click
    const handleMarkerClick = (center) => {
      console.log('Marker clicked:', center)
      // Additional interactive features can be added here
    }

    // Handle map ready
    const handleMapReady = (map) => {
      console.log('Map ready:', map)
    }

    // Handle map error
    const handleMapError = (error) => {
      console.error('Map error:', error)
    }

    // Handle window resize
    const handleResize = () => {
      checkMobile()
    }

    // Component mounted
    onMounted(async () => {
      checkMobile()
      window.addEventListener('resize', handleResize)
      
      // Load filter options and map data
      await Promise.all([
        loadFilterOptions(),
        loadMapData()
      ])
    })

    // Component unmounted
    const cleanup = () => {
      window.removeEventListener('resize', handleResize)
    }

    return {
      // State
      isInitialLoading,
      isLoading,
      hasError,
      errorMessage,
      showFilterPanel,
      isMobile,
      
      // Data
      allCenters,
      filteredCenters,
      availableStates,
      availableServices,
      currentFilters,
      resultStats,
      
      // Methods
      loadMapData,
      handleFilterUpdate,
      resetFilters,
      toggleFilterPanel,
      handleMarkerClick,
      handleMapReady,
      handleMapError,
      cleanup
    }
  },
  
  beforeUnmount() {
    this.cleanup()
  }
}
</script>

<style scoped>
/* Filter panel transition animation */
.filter-panel {
  transition: width 0.3s ease-in-out, opacity 0.3s ease-in-out;
}

/* 確保面板完全隱藏時不可見 */
.filter-panel.w-0 {
  width: 0 !important;
  min-width: 0 !important;
  max-width: 0 !important;
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

/* Responsive design */
@media (max-width: 768px) {
  .nucleo-map-app {
    height: 100vh;
  }
}

/* Scrollbar styles */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style> 