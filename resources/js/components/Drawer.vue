<template>
  <div class="drawer" :class="{ 'is-open': isOpen, 'is-visible': isVisible }">
    <div class="drawer__overlay" @click="closeDrawer" :style="{ transitionDuration: `${speed}ms` }"></div>
    <div class="drawer__content !bg-bg" :style="drawerStyle"
      :class="{ 'drawer--bottom': isMobile }">
      <div class="sticky top-0 z-[60] flex justify-between items-center p-4 border-b-2 border-gray-200 bg-bg"
        v-if="title">
        <h1 v-text="title"></h1>
        <button @click="closeDrawer" class="closeBtn">
          <i class="fad fa-times-circle text-lg"></i>
        </button>
      </div>
      <slot></slot>
    </div>
  </div>
</template>

<script>
import { directive } from "vue3-click-away";

export default {
  name: "Drawer",

  directives: {
    ClickAway: directive,
  },

  props: {
    title: {
      type: String,
      required: false,
      default: '',
    },
    isOpen: {
      type: Boolean,
      required: false,
      default: false,
    },
    speed: {
      type: Number,
      required: false,
      default: 300,
    },
    width: {
      type: String,
      required: false,
      default: "100%",
    },
    height: {
      type: String,
      required: false,
      default: "65%",
    },
  },

  data() {
    return {
      isVisible: false,
      isTransitioning: false,
      isMobile: false,
    };
  },

  computed: {
    drawerStyle() {
      return {
        maxWidth: this.isMobile ? "100%" : this.width,
        transitionDuration: `${this.speed}ms`,
        height: this.isMobile ? this.height : "100%",
      };
    },
  },

  watch: {
    isOpen(val) {
      this.isTransitioning = true;

      if (val) {
        this.toggleBackgroundScrolling(true);
        this.isVisible = true;
      } else {
        this.toggleBackgroundScrolling(false);
        setTimeout(() => (this.isVisible = false), this.speed);
      }

      setTimeout(() => (this.isTransitioning = false), this.speed);
    },
  },

  methods: {
    toggleBackgroundScrolling(enable) {
      const body = document.querySelector("body");

      body.style.overflow = enable ? "hidden" : null;
    },

    closeDrawer() {
      if (!this.isTransitioning) {
          this.$emit("close");
        }
    },

    checkScreenSize() {
      this.isMobile = window.innerWidth < 768;
    },
  },

  mounted() {
    this.isVisible = this.isOpen;
    this.checkScreenSize();
    window.addEventListener("resize", this.checkScreenSize);
  },

  beforeUnmount() {
    window.removeEventListener("resize", this.checkScreenSize);
  },
};
</script>

<style lang="scss" scoped>
.drawer {
  visibility: hidden;

  &.is-visible {
    visibility: visible;
  }

  &.is-open {
    .drawer__overlay {
      opacity: 0.5;
    }

    .drawer__content {
      transform: translateX(0);
    }

    .drawer--bottom {
      transform: translateY(0);
    }
  }

  &__overlay {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    width: 100%;
    z-index: 60;
    opacity: 0;
    transition-property: opacity;
    background-color: #000000;
    user-select: none;
  }

  &__content {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    height: 100%;
    width: 100%;
    z-index: 60;
    overflow: auto;
    transition-property: transform;
    display: flex;
    flex-direction: column;
    transform: translateX(100%);
    box-shadow: 0 2px 6px #777;
  }

  &__content.drawer--bottom {
    top: auto;
    right: 0;
    bottom: 0;
    left: 0;
    height: 50%;
    transform: translateY(100%);
  }
}
</style>