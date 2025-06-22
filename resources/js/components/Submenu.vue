<template>
  <li 
    v-for="(menu, index) in menus" :key="menu.title" class="mb-2 relative"
    @mouseenter="!isMobile && showSubmenu(index)"
    @mouseleave="!isMobile && hideSubmenu(index)"
    @click="isMobile && toggleSubmenu(index)">
    <a :href="getRoutePath(menu.route)"
       :class="['flex items-center p-2 rounded-lg hover:bg-primary cursor-pointer',
               menu.routes.includes(currentRoute) ? 'bg-primary' : '']">
      <i :class="menu.logo" 
         class="w-6 h-6 flex items-center justify-center">
      </i>
      <span :class="['ml-3  flex-1', menu.routes.includes(currentRoute) ? 'text-white font-semibold' : '']" v-if="isOpen">{{ menu.title }}</span>
      <i v-if="isOpen && menu.subtitle.length" :class="[visibleSubmenus[index] ? 'fa fa-chevron-down' : 'fa fa-chevron-right']" class="ml-auto"></i>
    </a>
    <ul v-if="menu.subtitle.length && visibleSubmenus[index]"
        :class="[isOpen ? 'relative mt-2 pl-6' : 'bg-white shadow-lg rounded-lg w-48 absolute left-full top-0']">
      <li v-for="subtitle in menu.subtitle" :key="subtitle.title" class="mb-1 text-sm">
        <a :href="getRoutePath(subtitle.route)"
           :class="['block p-2 rounded-lg hover:bg-primary hover:text-white',
                   subtitle.route === currentRoute ? 'text-fourth font-bold' : '', isOpen ? 'text-secondary' : 'text-primary']">
          {{ subtitle.title }} 
        </a>
      </li>
    </ul>
    <span v-else-if="!isOpen && visibleSubmenus[index]" 
          class="absolute left-full top-0 bg-white shadow-lg rounded-lg p-2 text-primary cursor-pointer"
          :style="{ width: (menu.title.length * 12) + 'px' }">
      {{ menu.title }}
    </span>
  </li>
</template>

<script>
export default {
  props: {
    menus: {
      type: Array,
      required: true
    },
    currentRoute: {
      type: String,
      required: true
    },
    isOpen: {
      type: Boolean,
      required: true
    },
    isMobile: {
      type: Boolean,
      required: true
    },
    visibleSubmenus: {
      type: Array,
      required: true
    },
    showSubmenu: {
      type: Function,
      required: true
    },
    hideSubmenu: {
      type: Function,
      required: true
    },
    toggleSubmenu: {
      type: Function,
      required: true
    },
    getRoutePath: {
      type: Function,
      required: true
    }
  },
  mounted() {
  }
};
</script>