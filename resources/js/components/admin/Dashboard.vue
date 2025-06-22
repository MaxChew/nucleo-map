<template>
  <div class="space-y-6">
    <!-- 页面标题 -->
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">仪表板</h1>
      <div class="text-sm text-gray-500 dark:text-gray-400">
        {{ currentDateTime }}
      </div>
    </div>
    
    <!-- 统计卡片 -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- 总用户数 -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-lg flex items-center justify-center">
              <i class="fad fa-users text-primary-600 text-xl"></i>
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">总用户数</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.totalUsers }}</p>
          </div>
        </div>
      </div>
      
      <!-- 活跃用户 -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-accent-100 dark:bg-accent-900 rounded-lg flex items-center justify-center">
              <i class="fad fa-user-check text-accent-600 text-xl"></i>
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">活跃用户</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.activeUsers }}</p>
          </div>
        </div>
      </div>
      
      <!-- 中心数量 -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-secondary-100 dark:bg-secondary-900 rounded-lg flex items-center justify-center">
              <i class="fad fa-map-marker-alt text-secondary-600 text-xl"></i>
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">中心数量</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.totalCenters }}</p>
          </div>
        </div>
      </div>
      
      <!-- 今日活动 -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-warning-100 dark:bg-warning-900 rounded-lg flex items-center justify-center">
              <i class="fad fa-chart-line text-warning-600 text-xl"></i>
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">今日活动</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.todayActivities }}</p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- 快速操作 -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">快速操作</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <router-link
          to="/centers/create"
          class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors"
        >
          <i class="fad fa-plus-circle text-primary-600 text-2xl mb-2"></i>
          <span class="text-sm font-medium text-gray-700 dark:text-gray-300">创建中心</span>
        </router-link>
        
        <router-link
          to="/map"
          class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-accent-500 hover:bg-accent-50 dark:hover:bg-accent-900/20 transition-colors"
        >
          <i class="fad fa-map text-accent-600 text-2xl mb-2"></i>
          <span class="text-sm font-medium text-gray-700 dark:text-gray-300">查看地图</span>
        </router-link>
        
        <router-link
          to="/filters"
          class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-secondary-500 hover:bg-secondary-50 dark:hover:bg-secondary-900/20 transition-colors"
        >
          <i class="fad fa-filter text-secondary-600 text-2xl mb-2"></i>
          <span class="text-sm font-medium text-gray-700 dark:text-gray-300">管理过滤器</span>
        </router-link>
        
        <router-link
          to="/reports"
          class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-warning-500 hover:bg-warning-50 dark:hover:bg-warning-900/20 transition-colors"
        >
          <i class="fad fa-chart-bar text-warning-600 text-2xl mb-2"></i>
          <span class="text-sm font-medium text-gray-700 dark:text-gray-300">查看报告</span>
        </router-link>
      </div>
    </div>
    
    <!-- 最近活动 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- 最新用户 -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">最新用户</h3>
        <div class="space-y-3">
          <div v-for="user in recentUsers" :key="user.id" class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center">
              <span class="text-xs font-medium text-white">{{ user.name.charAt(0).toUpperCase() }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ user.name }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ user.email }}</p>
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
              {{ formatDate(user.created_at) }}
            </div>
          </div>
        </div>
      </div>
      
      <!-- 系统状态 -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">系统状态</h3>
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600 dark:text-gray-400">数据库连接</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800">
              <i class="fad fa-check-circle mr-1"></i>正常
            </span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600 dark:text-gray-400">缓存系统</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800">
              <i class="fad fa-check-circle mr-1"></i>正常
            </span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600 dark:text-gray-400">队列处理</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800">
              <i class="fad fa-check-circle mr-1"></i>正常
            </span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600 dark:text-gray-400">存储空间</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning-100 text-warning-800">
              <i class="fad fa-exclamation-triangle mr-1"></i>75% 已使用
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue'

export default {
  name: 'Dashboard',
  setup() {
    const stats = reactive({
      totalUsers: 0,
      activeUsers: 0,
      totalCenters: 0,
      todayActivities: 0
    })
    
    const recentUsers = ref([])
    
    // 当前日期时间
    const currentDateTime = computed(() => {
      return new Date().toLocaleString('zh-CN', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    })
    
    // 格式化日期
    const formatDate = (dateString) => {
      const date = new Date(dateString)
      const now = new Date()
      const diffTime = Math.abs(now - date)
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
      
      if (diffDays === 1) {
        return '今天'
      } else if (diffDays === 2) {
        return '昨天'
      } else if (diffDays <= 7) {
        return `${diffDays} 天前`
      } else {
        return date.toLocaleDateString('zh-CN')
      }
    }
    
    // 加载统计数据
    const loadStats = async () => {
      try {
        // 这里应该调用API获取统计数据
        // const response = await api.get('/admin/stats')
        // Object.assign(stats, response.data)
        
        // 临时模拟数据
        stats.totalUsers = 1250
        stats.activeUsers = 890
        stats.totalCenters = 45
        stats.todayActivities = 127
      } catch (error) {
        console.error('Failed to load stats:', error)
      }
    }
    
    // 加载最新用户
    const loadRecentUsers = async () => {
      try {
        // 这里应该调用API获取最新用户
        // const response = await api.get('/admin/recent-users')
        // recentUsers.value = response.data
        
        // 临时模拟数据
        recentUsers.value = [
          { id: 1, name: '张三', email: 'zhangsan@example.com', created_at: new Date().toISOString() },
          { id: 2, name: '李四', email: 'lisi@example.com', created_at: new Date(Date.now() - 86400000).toISOString() },
          { id: 3, name: '王五', email: 'wangwu@example.com', created_at: new Date(Date.now() - 172800000).toISOString() },
          { id: 4, name: '赵六', email: 'zhaoliu@example.com', created_at: new Date(Date.now() - 259200000).toISOString() }
        ]
      } catch (error) {
        console.error('Failed to load recent users:', error)
      }
    }
    
    // 组件挂载时加载数据
    onMounted(() => {
      loadStats()
      loadRecentUsers()
    })
    
    return {
      stats,
      recentUsers,
      currentDateTime,
      formatDate
    }
  }
}
</script>

<style scoped>
/* 自定义样式 */
.router-link-active {
  background-color: rgba(160, 90, 255, 0.1);
  border-color: #A05AFF;
}

/* 悬停效果 */
.hover\:border-primary-500:hover {
  border-color: #A05AFF;
}

.hover\:bg-primary-50:hover {
  background-color: rgba(160, 90, 255, 0.05);
}
</style> 