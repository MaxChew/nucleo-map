<template>
  <div class="space-y-6">
    <!-- 页面标题 -->
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">数据报告</h1>
      <button
        @click="exportReport"
        class="bg-accent-600 hover:bg-accent-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors"
      >
        <i class="fad fa-download mr-2"></i>导出报告
      </button>
    </div>
    
    <!-- 时间范围选择 -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">时间范围</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label for="start-date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            开始日期
          </label>
          <input
            id="start-date"
            v-model="dateRange.start"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
          >
        </div>
        
        <div>
          <label for="end-date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            结束日期
          </label>
          <input
            id="end-date"
            v-model="dateRange.end"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
          >
        </div>
        
        <div class="flex items-end">
          <button
            @click="loadReports"
            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors"
          >
            <i class="fad fa-search mr-2"></i>生成报告
          </button>
        </div>
      </div>
    </div>
    
    <!-- 概览卡片 -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-lg flex items-center justify-center">
              <i class="fad fa-users text-primary-600 text-xl"></i>
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">新增用户</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ reportData.newUsers }}</p>
            <p class="text-xs text-success-600">
              <i class="fad fa-arrow-up mr-1"></i>+12%
            </p>
          </div>
        </div>
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-accent-100 dark:bg-accent-900 rounded-lg flex items-center justify-center">
              <i class="fad fa-map-marker-alt text-accent-600 text-xl"></i>
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">新增中心</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ reportData.newCenters }}</p>
            <p class="text-xs text-success-600">
              <i class="fad fa-arrow-up mr-1"></i>+8%
            </p>
          </div>
        </div>
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-secondary-100 dark:bg-secondary-900 rounded-lg flex items-center justify-center">
              <i class="fad fa-chart-line text-secondary-600 text-xl"></i>
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">活跃度</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ reportData.activityRate }}%</p>
            <p class="text-xs text-warning-600">
              <i class="fad fa-arrow-down mr-1"></i>-3%
            </p>
          </div>
        </div>
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-warning-100 dark:bg-warning-900 rounded-lg flex items-center justify-center">
              <i class="fad fa-eye text-warning-600 text-xl"></i>
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">页面访问</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ reportData.pageViews }}</p>
            <p class="text-xs text-success-600">
              <i class="fad fa-arrow-up mr-1"></i>+15%
            </p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- 图表区域 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- 用户增长趋势 -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">用户增长趋势</h3>
        <div class="h-64 flex items-center justify-center bg-gray-50 dark:bg-gray-700 rounded-lg">
          <div class="text-center">
            <i class="fad fa-chart-line text-gray-400 text-4xl mb-2"></i>
            <p class="text-gray-500 dark:text-gray-400">图表组件位置</p>
            <p class="text-xs text-gray-400">这里可以集成 Chart.js 或其他图表库</p>
          </div>
        </div>
      </div>
      
      <!-- 中心分布 -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">中心地理分布</h3>
        <div class="h-64 flex items-center justify-center bg-gray-50 dark:bg-gray-700 rounded-lg">
          <div class="text-center">
            <i class="fad fa-map text-gray-400 text-4xl mb-2"></i>
            <p class="text-gray-500 dark:text-gray-400">地图组件位置</p>
            <p class="text-xs text-gray-400">这里可以集成地图组件</p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- 详细数据表格 -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">详细数据</h3>
      </div>
      
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                日期
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                新增用户
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                活跃用户
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                页面访问
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                转化率
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="row in reportData.dailyStats" :key="row.date" class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ formatDate(row.date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ row.newUsers }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ row.activeUsers }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ row.pageViews }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="row.conversionRate >= 5 ? 'text-success-600' : row.conversionRate >= 3 ? 'text-warning-600' : 'text-danger-600'"
                  class="text-sm font-medium"
                >
                  {{ row.conversionRate }}%
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'

export default {
  name: 'Reports',
  setup() {
    const isLoading = ref(false)
    
    // 日期范围
    const dateRange = reactive({
      start: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
      end: new Date().toISOString().split('T')[0]
    })
    
    // 报告数据
    const reportData = reactive({
      newUsers: 0,
      newCenters: 0,
      activityRate: 0,
      pageViews: 0,
      dailyStats: []
    })
    
    // 格式化日期
    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('zh-CN', {
        month: 'short',
        day: 'numeric'
      })
    }
    
    // 生成模拟数据
    const generateMockData = () => {
      const startDate = new Date(dateRange.start)
      const endDate = new Date(dateRange.end)
      const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24))
      
      const dailyStats = []
      let totalNewUsers = 0
      let totalPageViews = 0
      
      for (let i = 0; i <= days; i++) {
        const date = new Date(startDate)
        date.setDate(date.getDate() + i)
        
        const newUsers = Math.floor(Math.random() * 50) + 10
        const activeUsers = Math.floor(Math.random() * 200) + 100
        const pageViews = Math.floor(Math.random() * 1000) + 500
        const conversionRate = (Math.random() * 8 + 1).toFixed(1)
        
        totalNewUsers += newUsers
        totalPageViews += pageViews
        
        dailyStats.push({
          date: date.toISOString().split('T')[0],
          newUsers,
          activeUsers,
          pageViews,
          conversionRate: parseFloat(conversionRate)
        })
      }
      
      return {
        dailyStats,
        totalNewUsers,
        totalPageViews
      }
    }
    
    // 加载报告数据
    const loadReports = async () => {
      try {
        isLoading.value = true
        
        // 这里应该调用API获取报告数据
        // const response = await api.get('/admin/reports', {
        //   params: {
        //     start_date: dateRange.start,
        //     end_date: dateRange.end
        //   }
        // })
        // Object.assign(reportData, response.data)
        
        // 临时模拟数据
        const mockData = generateMockData()
        
        reportData.newUsers = mockData.totalNewUsers
        reportData.newCenters = Math.floor(Math.random() * 10) + 5
        reportData.activityRate = (Math.random() * 30 + 60).toFixed(1)
        reportData.pageViews = mockData.totalPageViews
        reportData.dailyStats = mockData.dailyStats
        
      } catch (error) {
        console.error('Failed to load reports:', error)
      } finally {
        isLoading.value = false
      }
    }
    
    // 导出报告
    const exportReport = () => {
      // 这里应该调用API导出报告
      // 临时模拟导出功能
      const csvContent = [
        ['日期', '新增用户', '活跃用户', '页面访问', '转化率'],
        ...reportData.dailyStats.map(row => [
          row.date,
          row.newUsers,
          row.activeUsers,
          row.pageViews,
          `${row.conversionRate}%`
        ])
      ].map(row => row.join(',')).join('\n')
      
      const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
      const link = document.createElement('a')
      const url = URL.createObjectURL(blob)
      link.setAttribute('href', url)
      link.setAttribute('download', `report_${dateRange.start}_${dateRange.end}.csv`)
      link.style.visibility = 'hidden'
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      
      console.log('Exporting report...')
    }
    
    // 组件挂载时加载数据
    onMounted(() => {
      loadReports()
    })
    
    return {
      isLoading,
      dateRange,
      reportData,
      formatDate,
      loadReports,
      exportReport
    }
  }
}
</script>

<style scoped>
/* 自定义样式 */
.chart-placeholder {
  background: linear-gradient(45deg, #f3f4f6 25%, transparent 25%),
              linear-gradient(-45deg, #f3f4f6 25%, transparent 25%),
              linear-gradient(45deg, transparent 75%, #f3f4f6 75%),
              linear-gradient(-45deg, transparent 75%, #f3f4f6 75%);
  background-size: 20px 20px;
  background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
}

.dark .chart-placeholder {
  background: linear-gradient(45deg, #374151 25%, transparent 25%),
              linear-gradient(-45deg, #374151 25%, transparent 25%),
              linear-gradient(45deg, transparent 75%, #374151 75%),
              linear-gradient(-45deg, transparent 75%, #374151 75%);
  background-size: 20px 20px;
  background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
}
</style> 