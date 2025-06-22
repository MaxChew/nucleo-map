<template>
  <div class="filter-manager space-y-6">
    <!-- 搜索框 -->
    <div class="space-y-2">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        搜索关键词
      </label>
      <div class="relative">
        <input
          v-model="filters.search"
          type="text"
          placeholder="输入搜索关键词..."
          class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
        >
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
      </div>
    </div>

    <!-- 类别筛选 -->
    <div class="space-y-2">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        类别
      </label>
      <vue-select
        v-model="filters.category"
        :options="categoryOptions"
        placeholder="选择类别..."
      />
    </div>

    <!-- 地区筛选 -->
    <div class="space-y-2">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        地区
      </label>
      <vue-select
        v-model="filters.region"
        :options="regionOptions"
        placeholder="选择地区..."
      />
    </div>

    <!-- 距离范围 -->
    <div class="space-y-2">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        距离范围 (公里)
      </label>
      <div class="flex items-center space-x-4">
        <input
          v-model.number="filters.distance"
          type="range"
          min="1"
          max="50"
          class="flex-1"
        >
        <span class="text-sm text-gray-600 dark:text-gray-400 min-w-0">
          {{ filters.distance }}km
        </span>
      </div>
    </div>

    <!-- 价格范围 -->
    <div class="space-y-2">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        价格范围
      </label>
      <div class="grid grid-cols-2 gap-2">
        <number-input
          v-model="filters.priceMin"
          placeholder="最低价格"
          :min="0"
        />
        <number-input
          v-model="filters.priceMax"
          placeholder="最高价格"
          :min="0"
        />
      </div>
    </div>

    <!-- 开放时间 -->
    <div class="space-y-2">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        开放时间
      </label>
      <div class="space-y-2">
        <label class="inline-flex items-center">
          <input
            v-model="filters.openNow"
            type="checkbox"
            class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
          >
          <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">现在营业</span>
        </label>
        <label class="inline-flex items-center">
          <input
            v-model="filters.open24h"
            type="checkbox"
            class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
          >
          <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">24小时营业</span>
        </label>
      </div>
    </div>

    <!-- 评分筛选 -->
    <div class="space-y-2">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        最低评分
      </label>
      <div class="space-y-2">
        <div 
          v-for="rating in [5, 4, 3, 2, 1]" 
          :key="rating"
          class="flex items-center"
        >
          <input
            v-model="filters.minRating"
            :value="rating"
            type="radio"
            :id="`rating-${rating}`"
            class="text-primary-600 focus:ring-blue-500"
          >
          <label :for="`rating-${rating}`" class="ml-2 flex items-center">
            <div class="flex">
              <svg
                v-for="i in 5"
                :key="i"
                class="w-4 h-4"
                :class="i <= rating ? 'text-warning-400' : 'text-gray-300'"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
            </div>
            <span class="ml-1 text-sm text-gray-700 dark:text-gray-300">
              {{ rating }}星以上
            </span>
          </label>
        </div>
      </div>
    </div>

    <!-- 操作按钮 -->
    <div class="flex space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
      <button
        @click="applyFilters"
        class="flex-1 bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700 transition-colors"
      >
        应用筛选
      </button>
      <button
        @click="resetFilters"
        class="flex-1 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 py-2 px-4 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors"
      >
        重置
      </button>
    </div>

    <!-- 活跃筛选标签 -->
    <div v-if="activeFiltersCount > 0" class="pt-4 border-t border-gray-200 dark:border-gray-600">
      <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
          活跃筛选 ({{ activeFiltersCount }})
        </span>
        <button
          @click="clearAllFilters"
          class="text-xs text-danger-600 hover:text-danger-700"
        >
          清除全部
        </button>
      </div>
      <div class="flex flex-wrap gap-2">
        <span
          v-for="tag in activeFilterTags"
          :key="tag.key"
          class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200"
        >
          {{ tag.label }}
          <button
            @click="removeFilter(tag.key)"
            class="ml-1 text-primary-600 hover:text-primary-800"
          >
            ×
          </button>
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch } from 'vue'

export default {
  name: 'FilterManager',
  emits: ['filtersChanged'],
  setup(props, { emit }) {
    // 筛选状态
    const filters = ref({
      search: '',
      category: '',
      region: '',
      distance: 10,
      priceMin: null,
      priceMax: null,
      openNow: false,
      open24h: false,
      minRating: null
    })

    // 选项数据
    const categoryOptions = ref([
      { value: 'restaurant', label: '餐厅' },
      { value: 'hotel', label: '酒店' },
      { value: 'shop', label: '商店' },
      { value: 'service', label: '服务' },
      { value: 'entertainment', label: '娱乐' }
    ])

    const regionOptions = ref([
      { value: 'taipei', label: '台北市' },
      { value: 'taichung', label: '台中市' },
      { value: 'kaohsiung', label: '高雄市' },
      { value: 'tainan', label: '台南市' },
      { value: 'taoyuan', label: '桃园市' }
    ])

    // 计算活跃筛选数量
    const activeFiltersCount = computed(() => {
      let count = 0
      if (filters.value.search) count++
      if (filters.value.category) count++
      if (filters.value.region) count++
      if (filters.value.distance !== 10) count++
      if (filters.value.priceMin !== null) count++
      if (filters.value.priceMax !== null) count++
      if (filters.value.openNow) count++
      if (filters.value.open24h) count++
      if (filters.value.minRating !== null) count++
      return count
    })

    // 活跃筛选标签
    const activeFilterTags = computed(() => {
      const tags = []
      
      if (filters.value.search) {
        tags.push({ key: 'search', label: `搜索: ${filters.value.search}` })
      }
      if (filters.value.category) {
        const option = categoryOptions.value.find(opt => opt.value === filters.value.category)
        tags.push({ key: 'category', label: `类别: ${option?.label}` })
      }
      if (filters.value.region) {
        const option = regionOptions.value.find(opt => opt.value === filters.value.region)
        tags.push({ key: 'region', label: `地区: ${option?.label}` })
      }
      if (filters.value.distance !== 10) {
        tags.push({ key: 'distance', label: `距离: ${filters.value.distance}km` })
      }
      if (filters.value.priceMin !== null) {
        tags.push({ key: 'priceMin', label: `最低价: $${filters.value.priceMin}` })
      }
      if (filters.value.priceMax !== null) {
        tags.push({ key: 'priceMax', label: `最高价: $${filters.value.priceMax}` })
      }
      if (filters.value.openNow) {
        tags.push({ key: 'openNow', label: '现在营业' })
      }
      if (filters.value.open24h) {
        tags.push({ key: 'open24h', label: '24小时营业' })
      }
      if (filters.value.minRating !== null) {
        tags.push({ key: 'minRating', label: `${filters.value.minRating}星以上` })
      }
      
      return tags
    })

    // 应用筛选
    const applyFilters = () => {
      emit('filtersChanged', { ...filters.value })
      console.log('应用筛选:', filters.value)
    }

    // 重置筛选
    const resetFilters = () => {
      filters.value = {
        search: '',
        category: '',
        region: '',
        distance: 10,
        priceMin: null,
        priceMax: null,
        openNow: false,
        open24h: false,
        minRating: null
      }
      applyFilters()
    }

    // 清除所有筛选
    const clearAllFilters = () => {
      resetFilters()
    }

    // 移除单个筛选
    const removeFilter = (key) => {
      switch (key) {
        case 'search':
          filters.value.search = ''
          break
        case 'category':
          filters.value.category = ''
          break
        case 'region':
          filters.value.region = ''
          break
        case 'distance':
          filters.value.distance = 10
          break
        case 'priceMin':
          filters.value.priceMin = null
          break
        case 'priceMax':
          filters.value.priceMax = null
          break
        case 'openNow':
          filters.value.openNow = false
          break
        case 'open24h':
          filters.value.open24h = false
          break
        case 'minRating':
          filters.value.minRating = null
          break
      }
      applyFilters()
    }

    // 监听筛选变化
    watch(
      filters,
      () => {
        // 可以在这里添加自动应用筛选的逻辑
      },
      { deep: true }
    )

    return {
      filters,
      categoryOptions,
      regionOptions,
      activeFiltersCount,
      activeFilterTags,
      applyFilters,
      resetFilters,
      clearAllFilters,
      removeFilter
    }
  }
}
</script>

<style scoped>
/* 自定义样式 */
input[type="range"] {
  -webkit-appearance: none;
  appearance: none;
  height: 6px;
  background: #e5e7eb;
  border-radius: 3px;
  outline: none;
}

input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 18px;
  height: 18px;
  background: #3b82f6;
  border-radius: 50%;
  cursor: pointer;
}

input[type="range"]::-moz-range-thumb {
  width: 18px;
  height: 18px;
  background: #3b82f6;
  border-radius: 50%;
  cursor: pointer;
  border: none;
}
</style> 