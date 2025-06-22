<template>
  <div id="admin-app" class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Loading组件 -->
    <PageLoading v-if="isLoading" />
    
    <!-- 主要内容区域 -->
    <div class="flex flex-col h-screen">
      <!-- 顶部导航栏 -->
      <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between items-center h-16">
            <!-- Logo和标题 -->
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                  Nucleo Map Admin
                </h1>
              </div>
            </div>
            
            <!-- 右侧用户菜单 -->
            <div class="flex items-center space-x-4">
              <!-- 通知铃铛 -->
              <NotificationBell v-if="user" />
              
              <!-- 用户下拉菜单 -->
              <ProfileDropdown v-if="user" :user="user" />
            </div>
          </div>
        </div>
      </header>
      
      <!-- 主要内容 -->
      <main class="flex-1 overflow-y-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <!-- 路由视图 -->
          <router-view />
        </div>
      </main>
    </div>
    
    <!-- 全局警告系统 -->
    <AlertSystem />
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import PageLoading from '../PageLoading.vue'
import NotificationBell from '../NotificationBell.vue'
import ProfileDropdown from '../ProfileDropdown.vue'
import AlertSystem from '../AlertSystem.vue'

export default {
  name: 'AdminApp',
  components: {
    PageLoading,
    NotificationBell,
    ProfileDropdown,
    AlertSystem
  },
  setup() {
    const isLoading = ref(false)
    const user = ref(null)
    
    // 初始化应用
    onMounted(async () => {
      try {
        isLoading.value = true
        
        // 获取当前用户信息
        if (window.app && window.app.user) {
          user.value = window.app.user
        }
        
        // 初始化其他全局设置
        await initializeApp()
        
      } catch (error) {
        console.error('Admin app initialization failed:', error)
      } finally {
        isLoading.value = false
      }
    })
    
    // 初始化应用函数
    const initializeApp = async () => {
      // 这里可以添加其他初始化逻辑
      // 比如加载全局配置、设置主题等
      
      // 设置默认主题
      if (localStorage.getItem('theme') === 'dark' || 
          (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark')
      } else {
        document.documentElement.classList.remove('dark')
      }
    }
    
    return {
      isLoading,
      user
    }
  }
}
</script>

<style scoped>
/* 自定义样式 */
#admin-app {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* 暗色模式过渡效果 */
* {
  transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
}
</style> 