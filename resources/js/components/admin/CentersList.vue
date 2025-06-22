<template>
  <div class="space-y-6">
    <!-- 页面头部 -->
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">中心管理</h1>
      <router-link
        to="/centers/create"
        class="bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors"
      >
        <i class="fad fa-plus mr-2"></i>创建新中心
      </router-link>
    </div>
    
    <!-- 搜索和过滤 -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- 搜索框 -->
        <div>
          <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            搜索中心
          </label>
          <input
            id="search"
            v-model="searchQuery"
            type="text"
            placeholder="按名称、代码或地址搜索..."
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
          >
        </div>
        
        <!-- 状态过滤 -->
        <div>
          <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            状态过滤
          </label>
          <select
            id="status"
            v-model="statusFilter"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
          >
            <option value="">全部状态</option>
            <option value="active">激活</option>
            <option value="inactive">未激活</option>
          </select>
        </div>
        
        <!-- 排序 -->
        <div>
          <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            排序方式
          </label>
          <select
            id="sort"
            v-model="sortBy"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
          >
            <option value="name">按名称</option>
            <option value="code">按代码</option>
            <option value="created_at">按创建时间</option>
          </select>
        </div>
      </div>
    </div>
    
    <!-- 中心列表 -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
      <!-- 加载状态 -->
      <div v-if="isLoading" class="p-8 text-center">
        <div class="inline-flex items-center">
          <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          加载中...
        </div>
      </div>
      
      <!-- 空状态 -->
      <div v-else-if="filteredCenters.length === 0" class="p-8 text-center">
        <i class="fad fa-map-marker-alt text-gray-300 text-4xl mb-4"></i>
        <p class="text-gray-500 dark:text-gray-400">{{ searchQuery ? '没有找到匹配的中心' : '暂无中心数据' }}</p>
        <router-link
          v-if="!searchQuery"
          to="/centers/create"
          class="mt-4 inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors"
        >
          <i class="fad fa-plus mr-2"></i>创建第一个中心
        </router-link>
      </div>
      
      <!-- 中心表格 -->
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                中心信息
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                联系方式
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                位置
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                状态
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                操作
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="center in filteredCenters" :key="center.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <!-- 中心信息 -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900 rounded-lg flex items-center justify-center">
                      <i class="fad fa-building text-primary-600"></i>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ center.name }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ center.code }}</div>
                  </div>
                </div>
              </td>
              
              <!-- 联系方式 -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 dark:text-white">{{ center.phone || '-' }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ center.email || '-' }}</div>
              </td>
              
              <!-- 位置 -->
              <td class="px-6 py-4">
                <div class="text-sm text-gray-900 dark:text-white max-w-xs truncate">{{ center.address }}</div>
                <div v-if="center.latitude && center.longitude" class="text-sm text-gray-500 dark:text-gray-400">
                  {{ center.latitude }}, {{ center.longitude }}
                </div>
              </td>
              
              <!-- 状态 -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="center.is_active ? 'bg-success-100 text-success-800' : 'bg-warning-100 text-warning-800'"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                >
                  <i :class="center.is_active ? 'fad fa-check-circle' : 'fad fa-pause-circle'" class="mr-1"></i>
                  {{ center.is_active ? '激活' : '未激活' }}
                </span>
              </td>
              
              <!-- 操作 -->
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center justify-end space-x-2">
                  <button
                    @click="viewCenter(center)"
                    class="text-secondary-600 hover:text-secondary-900 dark:text-secondary-400 dark:hover:text-secondary-300"
                    title="查看详情"
                  >
                    <i class="fad fa-eye"></i>
                  </button>
                  
                  <router-link
                    :to="`/centers/${center.id}/edit`"
                    class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300"
                    title="编辑"
                  >
                    <i class="fad fa-edit"></i>
                  </router-link>
                  
                  <button
                    @click="deleteCenter(center)"
                    class="text-danger-600 hover:text-danger-900 dark:text-danger-400 dark:hover:text-danger-300"
                    title="删除"
                  >
                    <i class="fad fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- 分页 -->
    <div v-if="totalPages > 1" class="flex items-center justify-between">
      <div class="text-sm text-gray-700 dark:text-gray-300">
        显示第 {{ (currentPage - 1) * perPage + 1 }} 到 {{ Math.min(currentPage * perPage, totalItems) }} 条，共 {{ totalItems }} 条记录
      </div>
      <div class="flex items-center space-x-2">
        <button
          @click="currentPage--"
          :disabled="currentPage === 1"
          class="px-3 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          上一页
        </button>
        
        <span class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
          第 {{ currentPage }} 页，共 {{ totalPages }} 页
        </span>
        
        <button
          @click="currentPage++"
          :disabled="currentPage === totalPages"
          class="px-3 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          下一页
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, watch } from 'vue'

export default {
  name: 'CentersList',
  setup() {
    const isLoading = ref(false)
    const centers = ref([])
    const searchQuery = ref('')
    const statusFilter = ref('')
    const sortBy = ref('name')
    const currentPage = ref(1)
    const perPage = ref(10)
    const totalItems = ref(0)
    
    // 计算属性
    const filteredCenters = computed(() => {
      let filtered = centers.value
      
      // 搜索过滤
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(center =>
          center.name.toLowerCase().includes(query) ||
          center.code.toLowerCase().includes(query) ||
          center.address.toLowerCase().includes(query)
        )
      }
      
      // 状态过滤
      if (statusFilter.value) {
        filtered = filtered.filter(center =>
          statusFilter.value === 'active' ? center.is_active : !center.is_active
        )
      }
      
      // 排序
      filtered.sort((a, b) => {
        if (sortBy.value === 'name') {
          return a.name.localeCompare(b.name)
        } else if (sortBy.value === 'code') {
          return a.code.localeCompare(b.code)
        } else if (sortBy.value === 'created_at') {
          return new Date(b.created_at) - new Date(a.created_at)
        }
        return 0
      })
      
      return filtered
    })
    
    const totalPages = computed(() => {
      return Math.ceil(filteredCenters.value.length / perPage.value)
    })
    
    // 加载中心数据
    const loadCenters = async () => {
      try {
        isLoading.value = true
        
        // 这里应该调用API获取中心数据
        // const response = await api.get('/centers')
        // centers.value = response.data.data
        // totalItems.value = response.data.total
        
        // 临时模拟数据
        centers.value = [
          {
            id: 1,
            name: '北京中心',
            code: 'BJ001',
            address: '北京市朝阳区建国门外大街1号',
            phone: '010-12345678',
            email: 'beijing@example.com',
            latitude: 39.9042,
            longitude: 116.4074,
            is_active: true,
            created_at: '2025-01-01T00:00:00Z'
          },
          {
            id: 2,
            name: '上海中心',
            code: 'SH001',
            address: '上海市浦东新区陆家嘴环路1000号',
            phone: '021-87654321',
            email: 'shanghai@example.com',
            latitude: 31.2304,
            longitude: 121.4737,
            is_active: true,
            created_at: '2025-01-02T00:00:00Z'
          },
          {
            id: 3,
            name: '广州中心',
            code: 'GZ001',
            address: '广州市天河区珠江新城花城大道85号',
            phone: '020-11111111',
            email: 'guangzhou@example.com',
            latitude: 23.1291,
            longitude: 113.2644,
            is_active: false,
            created_at: '2025-01-03T00:00:00Z'
          }
        ]
        totalItems.value = centers.value.length
        
      } catch (error) {
        console.error('Failed to load centers:', error)
      } finally {
        isLoading.value = false
      }
    }
    
    // 查看中心详情
    const viewCenter = (center) => {
      // 这里可以打开模态框或跳转到详情页
      console.log('Viewing center:', center)
    }
    
    // 删除中心
    const deleteCenter = async (center) => {
      if (confirm(`确定要删除中心 "${center.name}" 吗？此操作不可恢复。`)) {
        try {
          // 这里应该调用API删除中心
          // await api.delete(`/centers/${center.id}`)
          
          // 临时从列表中移除
          const index = centers.value.findIndex(c => c.id === center.id)
          if (index > -1) {
            centers.value.splice(index, 1)
            totalItems.value--
          }
          
          console.log('Center deleted:', center)
        } catch (error) {
          console.error('Failed to delete center:', error)
        }
      }
    }
    
    // 监听搜索和过滤变化，重置到第一页
    watch([searchQuery, statusFilter], () => {
      currentPage.value = 1
    })
    
    // 组件挂载时加载数据
    onMounted(() => {
      loadCenters()
    })
    
    return {
      isLoading,
      centers,
      searchQuery,
      statusFilter,
      sortBy,
      currentPage,
      perPage,
      totalItems,
      filteredCenters,
      totalPages,
      viewCenter,
      deleteCenter
    }
  }
}
</script>

<style scoped>
/* 自定义样式 */
.table-hover tbody tr:hover {
  background-color: rgba(160, 90, 255, 0.05);
}
</style> 