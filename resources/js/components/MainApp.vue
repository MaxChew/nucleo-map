<template>
  <div id="main-app" class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- 主应用容器 -->
    <div class="flex flex-col h-screen">
      <!-- 顶部导航栏 -->
      <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
              <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                Nucleo Map
              </h1>
            </div>
            <div class="flex items-center space-x-4">
              <switch-theme />
              <profile-dropdown v-if="$user" />
            </div>
          </div>
        </div>
      </header>

      <!-- 主内容区域 -->
      <main class="flex-1 flex overflow-hidden">
        <!-- 侧边栏 -->
        <aside class="w-80 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 overflow-y-auto">
          <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
              筛选选项
            </h2>
            <filter-manager />
          </div>
        </aside>

        <!-- 地图区域 -->
        <div class="flex-1 relative">
          <center-map />
        </div>
      </main>
    </div>

    <!-- 全局提醒系统 -->
    <alert-system />

    <!-- 加载组件 -->
    <loading v-if="isLoading" />
  </div>
</template>

<script>
import { ref } from 'vue'

export default {
  name: 'MainApp',
  setup() {
    const isLoading = ref(false)

    return {
      isLoading
    }
  }
}
</script>

<style scoped>
/* 主应用样式 */
#main-app {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
</style> 