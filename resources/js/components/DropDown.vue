<template>
  <slot
    :state="state"
    :toggle="toggle"
  ></slot>
</template>

<script setup>
// Vue 3 组合式API版本
import { ref, onMounted, onBeforeUnmount } from 'vue';

// 定义组件属性
const props = defineProps({
  initialState: Boolean,
  ignoreClickOutside: Boolean,
  disableBodyScroll: Boolean,
});

// 定义状态
const state = ref(props.initialState || false);

// 切换下拉菜单状态方法
const toggle = (newState) => {
  if (newState === undefined) newState = !state.value;
  state.value = newState;
  
  // 如果需要禁用body滚动
  if (props.disableBodyScroll && window.app && window.app.toggleBodyOverflow) {
    window.app.toggleBodyOverflow(state.value);
  }
};

// 处理ESC键关闭菜单
const handleEscKey = (e) => {
  if (e.key === 'Escape' && state.value) {
    toggle(false);
  }
};

// 组件挂载时添加ESC键监听
onMounted(() => {
  document.addEventListener('keydown', handleEscKey);
});

// 组件卸载前移除ESC键监听
onBeforeUnmount(() => {
  document.removeEventListener('keydown', handleEscKey);
});

// 向父组件暴露方法和状态
defineExpose({
  state,
  toggle,
  handleEscKey
});
</script>

<style>
.dropdown-container {
  position: relative;
}
</style> 