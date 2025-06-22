<template>
  <div class="center-map h-full w-full relative">
    <!-- 地图容器 -->
    <div 
      ref="mapContainer" 
      class="w-full h-full bg-gray-100 dark:bg-gray-700 rounded-lg shadow-inner"
    >
      <!-- 地图加载中状态 -->
      <div v-if="!mapReady" class="flex items-center justify-center h-full">
        <div class="text-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
          <p class="text-gray-600 dark:text-gray-400">正在加载地图...</p>
        </div>
      </div>

      <!-- 地图内容 -->
      <div v-else class="h-full w-full flex items-center justify-center">
        <div class="text-center">
          <div class="bg-primary-100 dark:bg-primary-900 rounded-full p-8 mb-4 mx-auto w-24 h-24 flex items-center justify-center">
            <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
            地图组件
          </h3>
          <p class="text-gray-600 dark:text-gray-400 mb-4">
            地图功能将在这里显示
          </p>
          <button 
            @click="initializeMap"
            class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors"
          >
            初始化地图
          </button>
        </div>
      </div>
    </div>

    <!-- 地图控件 -->
    <div class="absolute top-4 right-4 space-y-2">
      <button 
        class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-2 hover:shadow-xl transition-shadow"
        title="放大"
      >
        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
      </button>
      <button 
        class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-2 hover:shadow-xl transition-shadow"
        title="缩小"
      >
        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
        </svg>
      </button>
    </div>

    <!-- 底部信息栏 -->
    <div class="absolute bottom-4 left-4 bg-white dark:bg-gray-800 rounded-lg px-3 py-2 shadow-lg">
      <p class="text-sm text-gray-600 dark:text-gray-400">
        坐标: {{ coordinates.lat.toFixed(6) }}, {{ coordinates.lng.toFixed(6) }}
      </p>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'

export default {
  name: 'CenterMap',
  setup() {
    const mapContainer = ref(null)
    const mapReady = ref(false)
    const coordinates = ref({
      lat: 25.0330,  // 台北市坐标
      lng: 121.5654
    })

    const initializeMap = () => {
      // 模拟地图初始化
      setTimeout(() => {
        mapReady.value = true
        console.log('地图已初始化')
      }, 1000)
    }

    onMounted(() => {
      // 自动初始化地图
      initializeMap()
    })

    return {
      mapContainer,
      mapReady,
      coordinates,
      initializeMap
    }
  }
}
</script>

<style scoped>
.center-map {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style> 