<template>
  <!-- 添加外层容器来防止页面弹跳 -->
  <div class="signature-wrapper fixed inset-0 bg-white z-50 overflow-hidden">
    <!-- 内容容器 -->
    <div class="signature-container h-full flex flex-col">
      <!-- 顶部工具栏 -->
      <div class="flex-none px-4 py-2 border-b">
        <a :href="backUrl" class="">
          <i class="fas fa-chevron-circle-left mr-1"></i>Back
        </a>
      </div>

      <!-- 画布区域 - 使用flex-1确保填充剩余空间 -->
      <div class="flex-1 relative overflow-hidden">
        <canvas
          ref="signatureCanvas"
          class="absolute inset-0 w-full h-full touch-none"
          :style="{ backgroundColor: signBoardColor }"
        ></canvas>
      </div>

      <!-- 底部工具栏 - 固定在底部 -->
      <div class="flex-none px-4 py-2 border-t bg-white">
        <div class="flex justify-between items-center">
          <button @click="eraseSignature" 
            class="px-4 py-2 bg-danger-500 text-white rounded-lg"
            v-if="!isLoading">
            Erase
          </button>
          <button @click="saveSignature" 
            class="px-4 py-2 bg-primary text-white rounded-lg">
            {{ buttonText }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import SignaturePad from 'signature_pad';
import axios from 'axios';

export default {
  props: {
    action: {
      type: String,
      required: true,
    },
    redirectUrl: {
      type: String,
      required: true,
    },
    backUrl: {
      type: String,
      required: true,
    },
    method: {
      type: String,
      default: 'POST',
    },
    signPenColor: {
      type: String,
      default: '#000000',
    },
    signBoardColor: {
      type: String,
      default: '#FFFFFF',
    },
  },
  data() {
    return {
      signaturePad: null,
      showPopup: false,
      savedImage: '',
      imageLoaded: false,
      alerts: [],
      isLoading: false,
    };
  },
  computed: {
    buttonText() {
      return this.isLoading ? 'give us a minute we are loading now...' : 'Save Signature';
    }
  },
  mounted() {
    this.initSignaturePad();

    this.preventScrolling();

    // 监听resize
    window.addEventListener('resize', this.handleResize);
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.resizeCanvas);
  },
  methods: {
    initSignaturePad() {
      const canvas = this.$refs.signatureCanvas;
      
      // 设置签名板选项
      const options = {
        penColor: this.signPenColor,
        backgroundColor: this.signBoardColor,
        velocityFilterWeight: 0.7,
        minWidth: 0.5,
        maxWidth: 2.5,
        throttle: 16, // 限制事件触发频率
        minDistance: 5 // 最小移动距离
      };

      this.signaturePad = new SignaturePad(canvas, options);
      this.resizeCanvas();
    },
    preventScrolling() {
      // 阻止移动端默认行为
      document.body.addEventListener('touchmove', (e) => {
        e.preventDefault();
      }, { passive: false });

      // 禁用双指缩放
      document.addEventListener('gesturestart', (e) => {
        e.preventDefault();
      });

      // 可选: 锁定body滚动
      document.body.style.overflow = 'hidden';
    },
    handleResize() {
      // 使用防抖处理resize事件
      if (this.resizeTimeout) {
        clearTimeout(this.resizeTimeout);
      }
      
      this.resizeTimeout = setTimeout(() => {
        this.resizeCanvas();
      }, 250);
    },
    resizeCanvas() {
      const canvas = this.$refs.signatureCanvas;
      const ratio = Math.max(window.devicePixelRatio || 1, 1);
      
      // 使用父容器的尺寸
      const parent = canvas.parentElement;
      const width = parent.clientWidth;
      const height = parent.clientHeight;

      // 设置画布尺寸
      canvas.width = width * ratio;
      canvas.height = height * ratio;
      canvas.style.width = `${width}px`;
      canvas.style.height = `${height}px`;

      // 缩放上下文
      const ctx = canvas.getContext('2d');
      ctx.scale(ratio, ratio);
    },
    async saveSignature() {
      this.isLoading = true;
      if (this.signaturePad.isEmpty()) {
        alert('Please provide a signature first.');
        this.isLoading = false;
        return;
      }

      const image = this.signaturePad.toDataURL('image/png'); // Save as PNG to maintain transparency
      this.savedImage = image;
      this.imageLoaded = false;

      let formData = new FormData();
      formData.append('signature', image);

      let options = {
        url: this.action,
        method: this.method,
        data: formData,
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      };

      axios(options)
        .then(response => {
          this.showAlert('success', 'Singnature Saved');
          window.location.href = this.redirectUrl;
        })
        .catch(err => {
          this.showAlert('error', 'Failed to submit signature. Please try again.');
          console.log('AJAX request Error:', err);
          this.isLoading = false;
        });
    },
    eraseSignature() {
      this.signaturePad.clear();
    },
    closePopup() {
      this.showPopup = false;
      this.imageLoaded = false;
    },
    resetCanvas() {
      this.signaturePad.clear();
    },

    showAlert(type, message) {
      const id = Date.now(); // 使用时间戳作为唯一ID
      this.alerts.push({ id, type, message });

      // 5秒后自动关闭警告
      setTimeout(() => {
        this.closeAlert(id);
      }, 3000);
    },

    closeAlert(id) {
      const index = this.alerts.findIndex(alert => alert.id === id);
      if (index !== -1) {
        this.alerts.splice(index, 1);
      }
    },
  },
};
</script>

<style scoped>
@keyframes showSignature {
  0% {
    transform: perspective(500px) rotateY(190deg) scale(0) translateX(-100%);
  }
  50% {
    transform: perspective(500px) rotateY(90deg) scale(0.5) translateX(50%);
  }
  100% {
    transform: perspective(500px) rotateY(0deg) scale(1) translateX(0);
  }
}

.animated-signature {
  transform: perspective(500px) rotateY(190deg) scale(0) translateX(-100%);
  transition: transform 3s;
}

.animated-signature.loaded {
  animation: showSignature 3s forwards;
}

div[style] img {
  display: block;
  margin: 0 auto;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s, transform 0.5s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
  transform: translateY(30px);
}

.alert-container {
  position: fixed;
  bottom: 20px;
  left: 20px;
  z-index: 1000;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.alert {
  margin-top: 10px;
  padding: 15px 20px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  max-width: 400px;
}

.alert-success {
  background-color: #d4edda;
  border-color: #c3e6cb;
  color: #155724;
}

.alert-warning {
  background-color: #fff3cd;
  border-color: #ffeeba;
  color: #856404;
}

.alert-error {
  background-color: #f8d7da;
  border-color: #f5c6cb;
  color: #721c24;
}

.alert-info {
  background-color: #d1ecf1;
  border-color: #bee5eb;
  color: #0c5460;
}

.alert-icon {
  margin-right: 10px;
  font-size: 18px;
}

.alert-message {
  flex-grow: 1;
}

.alert-close {
  cursor: pointer;
  padding-left: 15px;
  font-size: 20px;
}

.signature-wrapper {
  /* 防止iOS橡皮筋效果 */
  overscroll-behavior: none;
  -webkit-overscroll-behavior: none;
}

.signature-container {
  /* 防止内容溢出 */
  touch-action: none;
  -webkit-touch-callout: none;
  -webkit-user-select: none;
  user-select: none;
}

canvas {
  /* 优化触摸体验 */
  touch-action: none;
  -webkit-touch-callout: none;
  -webkit-tap-highlight-color: transparent;
}

/* 针对微信浏览器的特殊处理 */
@supports (-webkit-overflow-scrolling: touch) {
  .signature-wrapper {
    position: fixed;
    height: 100vh;
    /* 处理iOS底部安全区域 */
    padding-bottom: env(safe-area-inset-bottom);
  }
}
</style>