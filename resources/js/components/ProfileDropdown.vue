<template>
    <div class="relative" v-click-outside="closeDropdown">
      <!-- Profile Button -->
      <button 
        class="flex items-center space-x-2 focus:outline-none" 
        @click="toggleDropdown"
      >
        <img 
          :src="profileImage" 
          alt="profile pic" 
          class="w-8 h-8 lg:w-8 rounded-full object-cover"
        >
        <div class="font-bold text-xs lg:text-md text-left">
          <span class="block font-semibold">{{ name }}</span>
          <span class="block text-xxs text-gray-400">{{ email }}</span>
        </div>
        <i class="fas fa-chevron-down"></i>
      </button>
  
      <!-- Dropdown Menu -->
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
          class="absolute right-0 mt-2 bg-bg border border-white rounded-lg shadow-lg w-48 z-50"
        >
          <!-- Profile Info -->
          <div class="px-4 py-2">
            <p class="font-semibold">{{ name }}</p>
            <p class="text-sm text-gray-400">{{ email }}</p>
          </div>
          
          <hr class="border-t border-gray-600">
          
          <!-- Menu Items -->
          <ul class="py-2">
            <!-- Admin Platform Link -->
            <li v-if="isAdmin" class="hover:text-white">
              <a 
                href="/admin"
                class="profileDropdown-button"
                @click="closeDropdown"
              >
                Admin Platform
              </a>
            </li>
            
            <!-- Tutor Platform Link -->
            <li v-if="isTutor" class="hover:text-white">
              <a 
                href="/tutor"
                class="profileDropdown-button"
                @click="closeDropdown"
              >
                Tutor Platform
              </a>
            </li>
            
            <!-- Client Platform Link -->
            <li v-if="isClient" class="hover:text-white">
              <a 
                href="/user"
                class="profileDropdown-button"
                @click="closeDropdown"
              >
                Client Platform
              </a>
            </li>

             <!-- Client Platform Link -->
             <li class="hover:text-white">
              <a 
                :href="profileUrl"
                class="profileDropdown-button"
              >
                Profile
              </a>
            </li>
            
            <switch-theme/>
            
            <!-- Regular Menu Items -->
            <li v-for="item in menuItems" :key="item.url" class="hover:text-white">
              <template v-if="item.name === 'Log out'">
                <form :action="item.url" method="POST" class="w-full">
                  <input type="hidden" name="_token" :value="csrfToken">
                  <button 
                    type="submit"
                    class="w-full text-left px-4 py-2 rounded-lg transition-colors duration-150 hover:bg-secondary"
                  >
                    {{ item.name }}
                  </button>
                </form>
              </template>
              <a 
                v-else
                :href="item.url" 
                class="profileDropdown-button "
                @click="closeDropdown"
              >
                {{ item.name }}
              </a>
            </li>
          </ul>
        </div>
      </transition>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, onBeforeUnmount } from 'vue'
  
  // Props 定义
  const props = defineProps({
    name: {
      type: String,
      required: true
    },
    email: {
      type: String,
      required: true
    },
    profileImage: {
      type: String,
      default: '/images/profile.jpg'
    },
    profileUrl: {
      type: String,
      default: '#'
    },
    isAdmin: {
      type: Boolean,
      default: false
    },
    isTutor: {
      type: Boolean,
      default: false
    },
    isClient: {
      type: Boolean,
      default: false
    },
    menuItems: {
      type: Array,
      required: true
    },
    csrfToken: {
      type: String,
      required: true
    }
  })
  
  // 响应式状态
  const isOpen = ref(false)
  
  // 方法
  const toggleDropdown = () => {
    isOpen.value = !isOpen.value
  }
  
  const closeDropdown = () => {
    isOpen.value = false
  }
  
  // 点击外部关闭
  const handleClickOutside = (event) => {
    const dropdown = event.target.closest('.relative')
    if (!dropdown) {
      closeDropdown()
    }
  }
  
  // 生命周期钩子
  onMounted(() => {
    document.addEventListener('click', handleClickOutside)
  })
  
  onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
  })
  </script>