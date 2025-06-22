<template>
  <transition name="modal">
    <div v-show="show"
      class="modal fixed flex justify-center items-center w-full h-full bg-black bg-opacity-50 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-[80] overflow-auto px-4 py-12"
      :class="{ 'sm:py-16': size !== 'full', 'overflow-hidden modal-full': size === 'full' }"
      style="transition: opacity .3s ease;">
      <div style="transition: all .3s ease;" class="flex-none bg-white min-h-auto sm:m-auto w-full max-h-full"
        :class="['max-w-' + size, { 'rounded-xl': size !== 'full', 'h-full': size === 'full' }]" ref="container"
        @click="handleOutsideClick">
        <!-- Modal Header with Title and Close Button -->
        <div class="flex bg-bg justify-between items-center p-4 border-b border-gray-300 rounded-tl-xl rounded-tr-xl">
          <h1>{{ title }}</h1>
          <button @click="close" class="closeBtn">
            <i class="fad fa-times-circle text-lg"></i>
          </button>

        </div>
        <!-- Modal Body (Slot Content) -->
        <div class="bg-bg rounded-bl-xl rounded-br-xl">
          <slot :close="close"></slot>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import { ref, watch, onMounted, onUnmounted } from 'vue';

export default {
  props: {
    show: Boolean,
    size: {
      type: String,
      default: 'md'
    },
    static: Boolean, // if true, will not close when click outside
    keyboard: {
      // if true, will close when press Esc
      type: Boolean,
      default: true
    },
    title: {
      type: String,
      default: 'Modal Title' // Default title if not provided
    }
  },
  setup(props, { emit }) {
    const container = ref(null);

    const close = (e) => {
      if (props.show) {
        if (e) {
          e.stopImmediatePropagation();
        }
        emit('close');
      }
    };

    const handleOutsideClick = (e) => {
      if (!props.static && container.value && !container.value.contains(e.target)) {
        close(e);
      }
    };

    watch(
      () => props.show,
      (showing) => {
        const bodyClassList = document.body.classList;
        if (showing) {
          bodyClassList.add('overflow-hidden');
        } else {
          bodyClassList.remove('overflow-hidden');
          setTimeout(() => emit('closed'), 250);
        }
      },
      { immediate: true }
    );

    const onKeydown = (e) => {
      if (props.keyboard && e.key === 'Escape') {
        close(e);
      }
    };

    onMounted(() => {
      if (props.keyboard) {
        document.addEventListener('keydown', onKeydown);
      }
    });

    onUnmounted(() => {
      if (props.keyboard) {
        document.removeEventListener('keydown', onKeydown);
      }
    });

    return {
      container,
      close,
      handleOutsideClick
    };
  }
};
</script>

<style scoped></style>