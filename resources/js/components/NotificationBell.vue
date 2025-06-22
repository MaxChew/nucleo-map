<template>
  <div class="relative" v-click-outside="closeDropdown">
    <!-- Notification Bell Button -->
    <button 
      class="relative flex items-center text-gray-400 hover:text-white focus:outline-none" 
      @click="toggleDropdown"
    >
      <i class="fas fa-bell text-xl"></i>
      <!-- Badge with count -->
      <span 
        v-if="unreadCount > 0" 
        class="absolute -top-2 -right-2 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-danger-500 rounded-full"
      >
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
    </button>

    <!-- Notification Dropdown -->
    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div 
        v-show="isOpen" 
        class="absolute right-0 mt-2 bg-bg border border-white rounded-lg shadow-lg w-80 z-50"
      >
        <div class="px-4 py-2 flex justify-between items-center border-b border-gray-600">
          <h3 class="font-semibold">Notifications</h3>
          <button 
            v-if="unreadCount > 0"
            @click="markAllAsRead" 
            class="text-xs text-blue-400 hover:text-blue-300"
          >
            Mark all as read
          </button>
        </div>
        
        <!-- Empty State -->
        <div v-if="notifications.length === 0" class="py-8 text-center text-gray-400">
          <i class="far fa-bell-slash text-2xl mb-2"></i>
          <p>No notifications</p>
        </div>
        
        <!-- Notifications List -->
        <div v-else class="max-h-80 overflow-y-auto">
          <div 
            v-for="notification in notifications" 
            :key="notification.id"
            class="p-4 border-b border-gray-600 hover:bg-bg-dark cursor-pointer"
            :class="{'bg-blue-900 bg-opacity-10': !notification.is_read}"
            @click="goToNotification(notification)"
          >
            <div class="flex items-start gap-3">
              <div class="flex-shrink-0 text-lg text-blue-400">
                <i :class="notification.icon || 'fas fa-info-circle'"></i>
              </div>
              <div class="flex-grow">
                <div class="flex items-start justify-between">
                  <h4 class="font-medium mb-1">{{ notification.title }}</h4>
                  <div v-if="!notification.is_read" class="w-2 h-2 bg-blue-500 rounded-full"></div>
                </div>
                <p class="text-sm text-gray-400 mb-1">{{ notification.message }}</p>
                <p class="text-xs text-gray-500">{{ notification.time_ago }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'

      // Reactive state
const isOpen = ref(false)
const unreadCount = ref(0)
const notifications = ref([])
const isLoading = ref(false)
const refreshInterval = ref(null)

      // Methods
const toggleDropdown = () => {
  isOpen.value = !isOpen.value
  
  if (isOpen.value) {
    fetchNotifications()
  }
}

const closeDropdown = () => {
  isOpen.value = false
}

const fetchNotifications = async () => {
  if (isLoading.value) return
  
  isLoading.value = true
  
  try {
    const response = await axios.get('/api/shared/private/notifications')
    
    if (response.data.status === 'success') {
              notifications.value = response.data.data.data // Paginated data structure
      unreadCount.value = response.data.unread_count
    }
  } catch (error) {
    console.error('Failed to fetch notifications', error)
  } finally {
    isLoading.value = false
  }
}

const fetchUnreadCount = async () => {
  try {
    const response = await axios.get('/api/shared/private/notifications/unread-count')
    
    if (response.data.status === 'success') {
      unreadCount.value = response.data.data.count
    }
  } catch (error) {
    console.error('Failed to fetch unread notification count', error)
  }
}

const markAsRead = async (notificationId) => {
  try {
    const response = await axios.post(`/api/shared/private/notifications/${notificationId}/mark-as-read`)
    
    if (response.data.status === 'success') {
      const notification = notifications.value.find(n => n.id === notificationId)
      if (notification) {
        notification.is_read = true
      }
      
      unreadCount.value = response.data.data.unread_count
    }
  } catch (error) {
    console.error('Failed to mark notification as read', error)
  }
}

const markAllAsRead = async () => {
  try {
    const response = await axios.post('/api/shared/private/notifications/mark-all-as-read')
    
    if (response.data.status === 'success') {
      notifications.value.forEach(notification => {
        notification.is_read = true
      })
      
      unreadCount.value = 0
    }
  } catch (error) {
    console.error('Failed to mark all notifications as read', error)
  }
}

const goToNotification = (notification) => {
          // Mark as read
  if (!notification.is_read) {
    markAsRead(notification.id)
  }
  
          // If there's a URL, navigate to the related page
  if (notification.url) {
    window.location.href = notification.url;
  }
}

// Periodically refresh unread notification count
const startRefreshInterval = () => {
  refreshInterval.value = setInterval(fetchUnreadCount, 3 * 60 * 1000) // Refresh every 3 minutes
}

// Lifecycle hooks
onMounted(() => {
  fetchUnreadCount()
  startRefreshInterval()
})

onBeforeUnmount(() => {
  if (refreshInterval.value) {
    clearInterval(refreshInterval.value)
  }
})
</script> 