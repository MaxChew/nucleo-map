<template>
  <div>
    <!-- Overlay -->
    <div v-if="isMobile && modelValue" class="fixed inset-0 bg-black opacity-50 z-50" @click="overlayToggleSidebar">
    </div>

    <!-- Sidebar -->
    <div @mouseenter="toggleSidebar" @mouseleave="toggleSidebar" :class="[
      'bg-bg transition-all duration-300 py-5 px-2',
      isMobile ? 'fixed top-0 w-60 h-screen z-[70]' : 'sticky left-0 top-12',
      isMobile && modelValue ? 'left-0' : '-left-60',
      !isMobile && modelValue ? 'w-60' : 'w-16',
    ]">
              <a :href="`/${routePrefix}/dashboard`"
        :class="['menu', currentRoute.includes('dashboard') ? 'bg-primary-500 dark:bg-primary-400 text-white' : '', !modelValue ? 'justify-center' : '']">
        <div><i class="fad fa-home"></i></div>
        <span v-if="modelValue">Dashboard</span>
      </a>
      <!-- List of Menu -->
      <div v-for="(menu, index) in menus" class="relative" :key="index" @mouseenter="!isMobile && showSubmenu(index)"
        @mouseleave="!isMobile && hideSubmenu(index)" @click="isMobile && toggleSubmenu(index)">

        <a :href="getRoutePath(menu.route)"
          :class="['menu', menu.routes.includes(currentRoute) ? 'bg-primary-400' : '', !modelValue ? 'justify-center' : '']">
          <div><i :class="menu.logo"></i></div>
          <span v-if="modelValue">{{ menu.title }}</span>
          <i v-if="modelValue && menu.subtitle.length" class="absolute right-2"
            :class="[visibleSubmenus[index] ? 'fa fa-chevron-down' : 'fa fa-chevron-right']"></i>
        </a>

        <div v-if="menu.subtitle.length && visibleSubmenus[index]"
          :class="[modelValue ? 'relative mt-2 pl-6' : 'bg-white shadow-lg rounded-lg] w-48 absolute left-full top-0']">
          <div v-for="subtitle in menu.subtitle" :key="subtitle.title">
            <a :href="getRoutePath(subtitle.route)"
              :class="['block p-2 rounded-lg hover:bg-primary-500 dark:hover:bg-white hover:text-warning-500', modelValue ? 'text-primary-500' : 'text-white']">
              {{ subtitle.title }}
            </a>
          </div>
        </div>

        <span v-else-if="!modelValue && visibleSubmenus[index]"
          class="absolute left-full top-0 bg-white shadow-lg rounded-lg p-2 text-primary-500 cursor-pointer"
          :style="{ width: (menu.title.length * 12) + 'px' }">
          {{ menu.title }}
        </span>
      </div>
      <!-- Log Out -->
      <a :class="['menu', !modelValue ? 'justify-center' : '']" :href="$route('auth.logout')">
        <i class="fa fa-sign-out-alt text-lg"></i>
        <span v-if="modelValue">Log out</span>
      </a>
    </div>
  </div>
</template>

<script>
import Submenu from './Submenu.vue';

export default {
  components: {
    Submenu
  },

  props: {
    menus: {
      type: Array,
      required: true
    },
    currentRoute: {
      type: String,
      required: true
    },
    modelValue: {
      type: Boolean,
      required: false,
      default: true
    }
  },

  computed: {
    routePrefix() {
      return this.currentRoute.split('.')[0];
    }
  },

  data() {
    return {
      isMobile: false,
      visibleSubmenus: Array(this.menus.length).fill(false),
      globaldata: window.app,
    };
  },

  methods: {
    toggleSidebar() {
      if (!this.isMobile) {
        if(!this.$root.clickSidebar){
          this.$emit('update:modelValue', !this.modelValue);
        }
      }

      if (this.isMobile) {
        document.body.style.overflow = !this.modelValue ? 'hidden' : '';
      }
    },
    overlayToggleSidebar() {
      this.$emit('update:modelValue', !this.modelValue);
    },

    showSubmenu(index) {
      this.visibleSubmenus.splice(index, 1, true);
    },

    hideSubmenu(index) {
      this.visibleSubmenus.splice(index, 1, false);
    },

    toggleSubmenu(index) {
      this.visibleSubmenus.splice(index, 1, !this.visibleSubmenus[index]);
    },

    getRoutePath(routeName) {
      if (!routeName) {
        return '#';
      }
      if (routeName === 'auth.logout') {
        return '/auth/logout';
      }
      if (routeName.startsWith('admin.')) {
        return '/' + routeName.replace('index', '').replace(/\./g, '/');
      }
      if (routeName.startsWith('tutor.')) {
        return '/' + routeName.replace('index', '').replace(/\./g, '/');
      }
      if (routeName.startsWith('user.')) {
        return '/' + routeName.replace('index', '').replace(/\./g, '/');
      }
      return '#';
    },

    handleResize() {
      const wasMobile = this.isMobile;
      this.isMobile = window.innerWidth <= 640;

      // 如果从桌面版变为移动版，则close侧边栏
      if (!wasMobile && this.isMobile) {
        this.$emit('update:modelValue', false);
      }
    },
  },
  mounted() {
    this.handleResize();
    if (this.isMobile) {
      this.$emit('update:modelValue', false);
      console.log('mobile', this.modelValue);
    }
    window.addEventListener('resize', this.handleResize);
  },

  beforeUnmount() {  // 注意：在 Vue 3 中使用 beforeUnmount 而不是 beforeDestroy
    window.removeEventListener('resize', this.handleResize);
  }
};
</script>

<style scoped>
.menu {
  @apply flex gap-2 items-center p-2 rounded-lg hover:bg-primary-500 dark:hover:bg-white hover:text-warning-500 cursor-pointer mb-2;

}

.menu div {
  @apply flex justify-center w-6;
}
</style>