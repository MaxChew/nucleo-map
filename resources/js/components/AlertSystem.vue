<template>
  <div class="fixed z-[1000] bottom-4 left-4 flex flex-col space-y-2 w-full max-w-[400px] pointer-events-none">
    <TransitionGroup 
      name="alert" 
      tag="div"
      class="flex flex-col space-y-2"
    >
      <div
        v-for="alert in alertStore.sortedAlerts"
        :key="alert.id"
        class="alert relative overflow-hidden"
        :class="[`alert-${alert.type}`]"
      >
        <!-- 图标 -->
        <span class="alert-icon" :class="getIconColorClass(alert.type)">
          <i 
            class="fas"
            :class="{
              'fa-check-circle': alert.type === 'success',
              'fa-exclamation-triangle': alert.type === 'warning',
              'fa-times-circle': alert.type === 'error',
              'fa-info-circle': alert.type === 'info'
            }"
          ></i>
        </span>

        <!-- 消息 -->
        <span class="alert-message text-white">{{ alert.message }}</span>

        <!-- 关闭按钮 -->
        <span 
          class="alert-close" 
          @click="alertStore.removeAlert(alert.id)"
        >&times;</span>

        <!-- 进度条 -->
        <div 
          class="progress-bar absolute bottom-0 left-0 h-1 w-full"
          :class="{
                    'bg-accent-500': alert.type === 'success',
        'bg-danger-500': alert.type === 'error',
        'bg-warning-500': alert.type === 'warning',
        'bg-secondary-500': alert.type === 'info'
          }"
          :style="progressBarStyles[alert.id] || {}"
        ></div>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { useAlertStore } from '../stores/alert'
import { onMounted, onBeforeUnmount, ref, onUpdated } from 'vue'

const alertStore = useAlertStore()
const progressBarStyles = ref({})
let progressInterval = null

// 更新所有进度条的样式
const updateProgressBars = () => {
  const now = Date.now()
  
  // 先清理过期的通知
  alertStore.cleanExpiredAlerts()
  
  // 为每个通知计算进度条宽度
  alertStore.alerts.forEach(alert => {
    if (!alert.expiresAt) return
    
    const timeLeft = Math.max(0, alert.expiresAt - now)
    const duration = alert.expiresAt - alert.timestamp
    const percent = timeLeft / duration * 100
    
    // 更新进度条样式
    progressBarStyles.value[alert.id] = {
      width: `${percent}%`
    }
    
    // 如果时间已到，移除通知
    if (timeLeft <= 0) {
      alertStore.removeAlert(alert.id)
    }
  })
}

// 在组件挂载时，设置定时器定期更新进度条
onMounted(() => {
  // 立即执行一次更新
  updateProgressBars()
  
  // 设置定时器，每50毫秒更新一次进度条
  progressInterval = setInterval(updateProgressBars, 50)
})

// 在组件卸载前，清除定时器
onBeforeUnmount(() => {
  if (progressInterval) {
    clearInterval(progressInterval)
  }
  
  // 确保sessionStorage中的通知状态与当前时间戳一致
  if (alertStore.alerts.length > 0) {
    sessionStorage.setItem('alerts', JSON.stringify(alertStore.alerts))
  }
})

// 当组件更新后，确保进度条状态正确
onUpdated(() => {
  updateProgressBars()
})

// 根据通知类型获取图标颜色类
const getIconColorClass = (type) => ({
          'success': 'text-accent-600',
        'error': 'text-danger-600',
        'warning': 'text-warning-600',
        'info': 'text-secondary-600'
}[type] || 'text-gray-600')
</script>

<style scoped>
/* Alert 基础样式 */
.alert {
  pointer-events: auto;
  margin-top: 10px;
  padding: 15px 20px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  max-width: 400px;
  background-color: #374151;
}

/* Alert 类型样式 */
.alert-success {
  border-left: 4px solid #28a745;
}

.alert-warning {
  border-left: 4px solid #ffc107;
}

.alert-error {
  border-left: 4px solid #dc3545;
}

.alert-info {
  border-left: 4px solid #17a2b8;
}

/* Alert 图标样式 */
.alert-icon {
  margin-right: 10px;
  font-size: 18px;
}

/* Alert 消息样式 */
.alert-message {
  flex-grow: 1;
}

/* Alert 关闭按钮样式 */
.alert-close {
  cursor: pointer;
  padding-left: 15px;
  font-size: 20px;
}

/* 进度条动画基础样式 */
.progress-bar {
  transform-origin: left;
  transition: width 50ms linear;
}

/* Alert 动画效果 */
.alert-enter-active,
.alert-leave-active {
  transition: all 0.3s ease;
}

.alert-enter-from,
.alert-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}

.alert-enter-to,
.alert-leave-from {
  opacity: 1;
  transform: translateX(0);
}
</style>