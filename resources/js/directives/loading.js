function toggleLoading(el, binding) {
  if (binding.value) {
    // 创建loading元素
    const loadingElement = document.createElement('div');
    loadingElement.className = 'v-loading-overlay';
    
    const spinner = document.createElement('div');
    spinner.className = 'v-loading-spinner';
    
    // 创建三个点
    for (let i = 0; i < 3; i++) {
      const dot = document.createElement('div');
      dot.className = 'dot';
      spinner.appendChild(dot);
    }
    
    loadingElement.appendChild(spinner);
    
    // 设置父元素样式
    if (getComputedStyle(el).position === 'static') {
      el.style.position = 'relative';
    }
    
    // 添加loading元素
    el.classList.add('v-loading-parent');
    el.appendChild(loadingElement);
  } else {
    // 移除loading元素
    const loadingElement = el.querySelector('.v-loading-overlay');
    if (loadingElement) {
      el.removeChild(loadingElement);
      el.classList.remove('v-loading-parent');
    }
  }
}

export default {
  mounted(el, binding) {
    toggleLoading(el, binding);
  },
  updated(el, binding) {
    if (binding.value !== binding.oldValue) {
      toggleLoading(el, binding);
    }
  },
  unmounted(el) {
    const loadingElement = el.querySelector('.v-loading-overlay');
    if (loadingElement) {
      el.removeChild(loadingElement);
      el.classList.remove('v-loading-parent');
    }
  }
}; 