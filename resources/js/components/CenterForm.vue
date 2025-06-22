<template>
  <div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg">
      <!-- 表单头部 -->
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
          {{ isEdit ? '编辑中心' : '创建新中心' }}
        </h2>
      </div>
      
      <!-- 表单内容 -->
      <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
        <!-- 基本信息 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- 中心名称 -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              中心名称 <span class="text-danger-500">*</span>
            </label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
              placeholder="请输入中心名称"
            >
            <p v-if="errors.name" class="mt-1 text-sm text-danger-600">{{ errors.name }}</p>
          </div>
          
          <!-- 中心代码 -->
          <div>
            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              中心代码 <span class="text-danger-500">*</span>
            </label>
            <input
              id="code"
              v-model="form.code"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
              placeholder="请输入中心代码"
            >
            <p v-if="errors.code" class="mt-1 text-sm text-danger-600">{{ errors.code }}</p>
          </div>
        </div>
        
        <!-- 地址信息 -->
        <div>
          <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            地址 <span class="text-danger-500">*</span>
          </label>
          <textarea
            id="address"
            v-model="form.address"
            rows="3"
            required
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
            placeholder="请输入完整地址"
          ></textarea>
          <p v-if="errors.address" class="mt-1 text-sm text-danger-600">{{ errors.address }}</p>
        </div>
        
        <!-- 联系信息 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- 电话 -->
          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              电话
            </label>
            <input
              id="phone"
              v-model="form.phone"
              type="tel"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
              placeholder="请输入联系电话"
            >
            <p v-if="errors.phone" class="mt-1 text-sm text-danger-600">{{ errors.phone }}</p>
          </div>
          
          <!-- 邮箱 -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              邮箱
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
              placeholder="请输入邮箱地址"
            >
            <p v-if="errors.email" class="mt-1 text-sm text-danger-600">{{ errors.email }}</p>
          </div>
        </div>
        
        <!-- 地理位置 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- 纬度 -->
          <div>
            <label for="latitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              纬度
            </label>
            <input
              id="latitude"
              v-model.number="form.latitude"
              type="number"
              step="any"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
              placeholder="请输入纬度"
            >
            <p v-if="errors.latitude" class="mt-1 text-sm text-danger-600">{{ errors.latitude }}</p>
          </div>
          
          <!-- 经度 -->
          <div>
            <label for="longitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              经度
            </label>
            <input
              id="longitude"
              v-model.number="form.longitude"
              type="number"
              step="any"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
              placeholder="请输入经度"
            >
            <p v-if="errors.longitude" class="mt-1 text-sm text-danger-600">{{ errors.longitude }}</p>
          </div>
        </div>
        
        <!-- 状态 -->
        <div>
          <label class="flex items-center">
            <input
              v-model="form.is_active"
              type="checkbox"
              class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
            >
            <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">激活状态</span>
          </label>
        </div>
        
        <!-- 描述 -->
        <div>
          <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            描述
          </label>
          <textarea
            id="description"
            v-model="form.description"
            rows="4"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
            placeholder="请输入中心描述信息"
          ></textarea>
          <p v-if="errors.description" class="mt-1 text-sm text-danger-600">{{ errors.description }}</p>
        </div>
        
        <!-- 提交按钮 -->
        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
          <button
            type="button"
            @click="$router.push('/centers')"
            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
          >
            取消
          </button>
          <button
            type="submit"
            :disabled="isSubmitting"
            class="px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="isSubmitting" class="flex items-center">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              提交中...
            </span>
            <span v-else>{{ isEdit ? '更新' : '创建' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

export default {
  name: 'CenterForm',
  props: {
    id: [String, Number]
  },
  setup(props) {
    const route = useRoute()
    const router = useRouter()
    
    const isSubmitting = ref(false)
    const errors = ref({})
    
    // 表单数据
    const form = reactive({
      name: '',
      code: '',
      address: '',
      phone: '',
      email: '',
      latitude: null,
      longitude: null,
      is_active: true,
      description: ''
    })
    
    // 是否为编辑模式
    const isEdit = computed(() => {
      return !!(props.id || route.params.id)
    })
    
    // 获取中心数据
    const fetchCenter = async (id) => {
      try {
        // 这里应该调用API获取中心数据
        // const response = await api.get(`/centers/${id}`)
        // Object.assign(form, response.data)
        
        // 临时模拟数据
        console.log('Fetching center data for ID:', id)
      } catch (error) {
        console.error('Failed to fetch center:', error)
      }
    }
    
    // 提交表单
    const handleSubmit = async () => {
      try {
        isSubmitting.value = true
        errors.value = {}
        
        // 表单验证
        if (!form.name.trim()) {
          errors.value.name = '中心名称不能为空'
          return
        }
        
        if (!form.code.trim()) {
          errors.value.code = '中心代码不能为空'
          return
        }
        
        if (!form.address.trim()) {
          errors.value.address = '地址不能为空'
          return
        }
        
        // 提交数据
        if (isEdit.value) {
          // 更新中心
          // await api.put(`/centers/${props.id || route.params.id}`, form)
          console.log('Updating center:', form)
        } else {
          // 创建中心
          // await api.post('/centers', form)
          console.log('Creating center:', form)
        }
        
        // 成功后跳转
        router.push('/centers')
        
      } catch (error) {
        console.error('Submit failed:', error)
        // 处理API错误
        if (error.response && error.response.data.errors) {
          errors.value = error.response.data.errors
        }
      } finally {
        isSubmitting.value = false
      }
    }
    
    // 组件挂载时
    onMounted(() => {
      const centerId = props.id || route.params.id
      if (centerId) {
        fetchCenter(centerId)
      }
    })
    
    return {
      form,
      errors,
      isSubmitting,
      isEdit,
      handleSubmit
    }
  }
}
</script>

<style scoped>
/* 自定义样式 */
.form-group {
  margin-bottom: 1.5rem;
}

/* 输入框聚焦效果 */
input:focus,
textarea:focus,
select:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(160, 90, 255, 0.1);
}
</style> 