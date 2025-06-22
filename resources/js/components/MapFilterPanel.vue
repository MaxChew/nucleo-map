<template>
  <div class="map-filter-panel bg-white rounded-lg shadow-sm border border-gray-200 h-full flex flex-col">
    <!-- Panel title -->
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex-shrink-0">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-lg font-medium text-gray-900">Medical Center Filter</h2>
          <p class="mt-1 text-sm text-gray-600">Search and filter medical centers in Malaysia</p>
        </div>
        <!-- Collapse button (mobile) -->
        <button 
          @click="$emit('togglePanel')"
          class="md:hidden p-2 text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-100"
        >
          <i class="fas fa-times text-lg"></i>
        </button>
      </div>
    </div>

    <!-- Filter form -->
    <div class="flex-1 p-6 overflow-y-auto">
      <div class="space-y-6">
        
        <!-- Keyword search -->
        <div>
          <label for="keyword" class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-search text-primary-600 mr-1"></i>
            Keyword Search
          </label>
          <div class="relative">
            <input
              id="keyword"
              v-model="localFilters.keyword"
              type="text"
              placeholder="Medical center name, code, or region..."
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm pl-10"
              @input="debouncedSearch"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-search text-gray-400"></i>
            </div>
            <!-- Clear button -->
            <button
              v-if="localFilters.keyword"
              @click="clearKeyword"
              class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>

        <!-- State filter -->
        <div>
          <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-map-marker-alt text-secondary-600 mr-1"></i>
            State Filter
          </label>
          <select
            id="state"
            v-model="localFilters.state"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
            @change="applyFilters"
          >
            <option value="all">All States</option>
            <option v-for="state in availableStates" :key="state" :value="state">
              {{ state }}
            </option>
          </select>
        </div>

        <!-- Service type filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-stethoscope text-accent-600 mr-1"></i>
            Service Type
          </label>
          <select
            v-model="localFilters.service"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
            @change="applyFilters"
          >
            <option value="all">All Services</option>
            <option v-for="service in availableServices" :key="service" :value="service">
              {{ getServiceLabel(service) }}
            </option>
          </select>
        </div>

        <!-- Results statistics -->
        <div class="bg-primary-50 rounded-lg p-4 border border-primary-200">
          <div class="flex items-center">
            <div class="bg-primary-600 rounded-full p-2 mr-3">
              <i class="fas fa-chart-bar text-white text-sm"></i>
            </div>
            <div>
              <h3 class="text-sm font-medium text-primary-900">Search Results</h3>
              <p class="text-sm text-primary-700">
                Found <span class="font-bold">{{ resultStats.total }}</span> medical centers
                <span v-if="resultStats.filtered" class="text-xs text-primary-600">
                  (filtered from {{ resultStats.original }})
                </span>
              </p>
            </div>
          </div>
        </div>

        <!-- Quick filter buttons -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-3">
            <i class="fas fa-filter text-warning-600 mr-1"></i>
            Quick Filter
          </label>
          <div class="grid grid-cols-2 gap-2">
            <button
              @click="quickFilter('PET')"
              class="px-3 py-2 text-xs bg-accent-100 hover:bg-accent-200 text-accent-800 rounded-md transition-colors"
            >
              <i class="fas fa-atom mr-1"></i>
              PET Scan
            </button>
            <button
              @click="quickFilter('SPECT')"
              class="px-3 py-2 text-xs bg-secondary-100 hover:bg-secondary-200 text-secondary-800 rounded-md transition-colors"
            >
              <i class="fas fa-radiation mr-1"></i>
              SPECT
            </button>
            <button
              @click="quickFilter('RAI')"
              class="px-3 py-2 text-xs bg-warning-100 hover:bg-warning-200 text-warning-800 rounded-md transition-colors"
            >
              <i class="fas fa-pills mr-1"></i>
              RAI Treatment
            </button>
            <button
              @click="quickFilter('PRRT')"
              class="px-3 py-2 text-xs bg-primary-100 hover:bg-primary-200 text-primary-800 rounded-md transition-colors"
            >
              <i class="fas fa-syringe mr-1"></i>
              PRRT
            </button>
          </div>
        </div>

        <!-- Reset button -->
        <div class="pt-4">
          <button
            @click="resetFilters"
            class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition-colors text-sm font-medium"
          >
            <i class="fas fa-undo mr-2"></i>
            Reset All Filters
          </button>
        </div>

      </div>
    </div>

    <!-- Show/hide panel button (mobile) -->
    <div class="md:hidden fixed bottom-6 left-6 z-50">
      <button
        @click="$emit('togglePanel')"
        class="bg-primary-600 hover:bg-primary-700 text-white rounded-full p-3 shadow-lg"
      >
        <i class="fas fa-sliders-h"></i>
      </button>
    </div>
  </div>
</template>

<script>
import { ref, reactive, watch, computed } from 'vue'
import { debounce } from 'lodash'

export default {
  name: 'MapFilterPanel',
  props: {
    filters: {
      type: Object,
      default: () => ({
        keyword: '',
        state: 'all',
        service: 'all'
      })
    },
    availableStates: {
      type: Array,
      default: () => []
    },
    availableServices: {
      type: Array,
      default: () => []
    },
    resultStats: {
      type: Object,
      default: () => ({
        total: 0,
        original: 0,
        filtered: false
      })
    }
  },
  emits: ['updateFilters', 'togglePanel'],
  setup(props, { emit }) {
    // Local filter state
    const localFilters = reactive({
      keyword: props.filters.keyword || '',
      state: props.filters.state || 'all',
      service: props.filters.service || 'all'
    })

    // Service type label mapping
    const serviceLabels = {
      'SPECT': 'SPECT Scan',
      'PET': 'PET Scan',
      'RAI': 'Radioactive Iodine Treatment',
      'PRRT': 'Peptide Receptor Radionuclide Therapy',
      'PSMA': 'PSMA Treatment',
      'SIRT': 'Radioembolization Treatment',
      'MIBG': 'MIBG Treatment',
      'BONE-P': 'Bone Pain Relief Treatment',
      'RSO': 'Radiosynovectomy',
      'AC': 'Articular Cartilage Treatment'
    }

    // Get service label
    const getServiceLabel = (service) => {
      return serviceLabels[service] || service
    }

    // Debounced search
    const debouncedSearch = debounce(() => {
      applyFilters()
    }, 300)

    // Apply filters
    const applyFilters = () => {
      emit('updateFilters', { ...localFilters })
    }

    // Clear keyword
    const clearKeyword = () => {
      localFilters.keyword = ''
      applyFilters()
    }

    // Quick filter
    const quickFilter = (service) => {
      localFilters.service = service
      applyFilters()
    }

    // Reset filters
    const resetFilters = () => {
      localFilters.keyword = ''
      localFilters.state = 'all'
      localFilters.service = 'all'
      applyFilters()
    }

    // Watch for external filter changes
    watch(() => props.filters, (newFilters) => {
      Object.assign(localFilters, {
        keyword: newFilters.keyword || '',
        state: newFilters.state || 'all',
        service: newFilters.service || 'all'
      })
    }, { deep: true })

    // Watch for local filter non-input changes
    watch([() => localFilters.state, () => localFilters.service], () => {
      applyFilters()
    })

    return {
      localFilters,
      getServiceLabel,
      debouncedSearch,
      applyFilters,
      clearKeyword,
      quickFilter,
      resetFilters
    }
  }
}
</script>

<style scoped>
/* Scrollbar styles */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Mobile fixed position adjustment */
@media (max-width: 768px) {
  .map-filter-panel {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 40;
    border-radius: 0;
  }
}

/* Transition animation */
.transition-colors {
  transition-property: background-color, border-color, color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}
</style> 