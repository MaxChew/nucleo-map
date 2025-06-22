<template>
  <!-- 根容器使用条件渲染决定布局方式 -->
  <div :class="containerClass">
    <!-- 移动端布局 -->
    <template v-if="isMobileDevice">
      <div class="signature-wrapper fixed inset-0 bg-white z-50 overflow-hidden">
        <div class="signature-container h-full flex flex-col">
          <div class="flex px-4 py-2 border-b flex-row justify-between">
            <a :href="backUrl" class="text-primary text-left w-1/5">
              <i class="fas fa-chevron-circle-left mr-1"></i>Back
            </a>
            <span class="text-primary font-black w-3/5">
              Your Signature Here
            </span>
            <span class="w-1/5">&nbsp;</span>
          </div>

          <div class="flex-1 relative overflow-hidden">
            <canvas
              ref="signatureCanvas"
              class="absolute inset-0 w-full h-full touch-none"
              :style="{ backgroundColor: signBoardColor }"
            ></canvas>
          </div>

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

    <!-- 桌面端布局 -->
    <template v-else>
      <div class="flex flex-col items-center w-full">
        <div class="w-full h-full">
          <canvas
            ref="signatureCanvas"
            class="w-full rounded-lg h-[400px]"
            :style="{ backgroundColor: signBoardColor }"
          ></canvas>
        </div>

        <div class="flex flex-row justify-between items-center mt-10 mb-4 px-2 w-full">
          <a :href="backUrl" class="text-black hover:text-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 cursor-pointer font-bonk">
            <i class="fas fa-chevron-circle-left mr-1"></i>Back
          </a>
          <div>
            <button @click="eraseSignature" 
              class="mr-2 px-4 py-2 bg-danger-500 font-bold text-white rounded-2xl hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-fourth" 
              v-if="!isLoading">
              Erase
            </button>
            <button @click="saveSignature" 
              class="px-4 py-2 bg-black font-bold text-white rounded-2xl hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-fourth">
              {{ buttonText }}
            </button>
          </div>
        </div>
      </div>
    </template>

    <!-- 共用的弹窗和提示组件 -->
    <div v-if="showPopup" class="fixed inset-0 flex items-center justify-center bg-primary bg-opacity-75 z-50">
      <div class="bg-black p-6 rounded-lg shadow-lg text-center">
        <h2 class="text-xl font-bold mb-4 text-white">Saved Signature</h2>
        <img :src="savedImage" alt="Signature" class="mb-4 animated-signature" :class="{'loaded': imageLoaded}" @load="imageLoaded = true"/>
        <button @click="closePopup" class="px-4 py-2 bg-danger-500 text-white rounded-lg hover:bg-danger-600">
          Close
        </button>
      </div>
    </div>

    <!-- 提示信息容器 -->
    <transition-group name="fade" tag="div" class="alert-container">
      <div v-for="alert in alerts" :key="alert.id" :class="['alert', `alert-${alert.type}`]">
        <span class="alert-icon">
          <i v-if="alert.type === 'success'" class="fas fa-check-circle"></i>
          <i v-else-if="alert.type === 'warning'" class="fas fa-exclamation-triangle"></i>
          <i v-else-if="alert.type === 'error'" class="fas fa-times-circle"></i>
          <i v-else class="fas fa-info-circle"></i>
        </span>
        <span class="alert-message">{{ alert.message }}</span>
        <span class="alert-close" @click="closeAlert(alert.id)">&times;</span>
      </div>
    </transition-group>
  </div>
</template>

<script>
import SignaturePad from 'signature_pad';
import axios from 'axios';

export default {
  name: 'AdaptiveSignature',
  
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
      isMobileDevice: false,
      resizeTimeout: null,
    };
  },

  computed: {
    buttonText() {
      return this.isLoading ? 'Processing...' : 'Save Signature';
    },
    containerClass() {
      return this.isMobileDevice 
        ? 'signature-mobile-container' 
        : 'signature-desktop-container';
    }
  },

  created() {
    // 检测设备类型
    this.detectDevice();
  },

  mounted() {
    this.initSignaturePad();
    
    if (this.isMobileDevice) {
      this.preventScrolling();
    }

    window.addEventListener('resize', this.handleResize);
  },

  beforeUnmount() {
    window.removeEventListener('resize', this.handleResize);
  },

  methods: {
    detectDevice() {
      // 使用 userAgent 检测移动设备
      const userAgent = navigator.userAgent || navigator.vendor || window.opera;
      this.isMobileDevice = /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(userAgent.toLowerCase());
    },

    initSignaturePad() {
      const canvas = this.$refs.signatureCanvas;
      
      // 确保画布支持透明度
      canvas.getContext('2d', { alpha: true });
      
      // 设置签名板选项
      const options = {
        penColor: this.signPenColor,
        backgroundColor: null,  // 设置为 null 以支持透明背景
        velocityFilterWeight: 0.7,
        minWidth: 0.5,
        maxWidth: 2.5,
        throttle: 16,
        minDistance: 5
      };

      this.signaturePad = new SignaturePad(canvas, options);
      
      // 使用 CSS 设置可视背景色
      canvas.style.backgroundColor = this.signBoardColor;
      
      this.resizeCanvas();
    },

    preventScrolling() {
      if (!this.isMobileDevice) return;
      
      document.body.style.overflow = 'hidden';
      document.addEventListener('touchmove', (e) => {
        if (e.target === this.$refs.signatureCanvas) {
          e.preventDefault();
        }
      }, { passive: false });
    },

    handleResize() {
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
      
      if (this.isMobileDevice) {
        const parent = canvas.parentElement;
        canvas.width = parent.clientWidth * ratio;
        canvas.height = parent.clientHeight * ratio;
      } else {
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
      }

      canvas.getContext('2d').scale(ratio, ratio);
      this.signaturePad.clear();
    },

    // 在 methods 中修改 saveSignature 方法:

    async saveSignature() {
      if (this.signaturePad.isEmpty()) {
        this.showAlert('error', 'Please provide a signature first.');
        return;
      }

      this.isLoading = true;
      
      try {
        // 1. 创建临时画布
        const tempCanvas = document.createElement('canvas');
        const tempCtx = tempCanvas.getContext('2d');
        
        // 2. 获取签名数据
        const signatureData = this.signaturePad.toData();
        
        if (signatureData.length > 0) {
          // 3. 计算签名的边界框
          let minX = Infinity;
          let minY = Infinity;
          let maxX = -Infinity;
          let maxY = -Infinity;
          
          // 遍历所有笔画点找出边界
          signatureData.forEach(stroke => {
            stroke.points.forEach(point => {
              minX = Math.min(minX, point.x);
              minY = Math.min(minY, point.y);
              maxX = Math.max(maxX, point.x);
              maxY = Math.max(maxY, point.y);
            });
          });
          
          // 4. 添加一些padding
          const padding = 10;
          minX = Math.max(0, minX - padding);
          minY = Math.max(0, minY - padding);
          maxX = maxX + padding;
          maxY = maxY + padding;
          
          // 5. 设置临时画布的尺寸为签名的实际大小
          const width = maxX - minX;
          const height = maxY - minY;
          
          tempCanvas.width = width;
          tempCanvas.height = height;
          
          // 6. 在临时画布上重绘签名
          const tempSignaturePad = new SignaturePad(tempCanvas, {
            penColor: this.signPenColor,
            backgroundColor: 'rgba(0,0,0,0)' // 透明背景
          });
          
          // 调整签名数据的位置
          const adjustedData = signatureData.map(stroke => ({
            ...stroke,
            points: stroke.points.map(point => ({
              ...point,
              x: point.x - minX,
              y: point.y - minY
            }))
          }));
          
          tempSignaturePad.fromData(adjustedData);
          
          // 7. 获取裁剪后的图片数据
          const image = tempCanvas.toDataURL('image/png');
          
          // 8. 发送到服务器
          const formData = new FormData();
          formData.append('signature', image);

          await axios({
            url: this.action,
            method: this.method,
            data: formData,
            headers: { 'Content-Type': 'multipart/form-data' }
          });

          this.showAlert('success', 'Signature saved successfully');
          window.location.href = this.redirectUrl;
        }
      } catch (error) {
        console.error('Failed to save signature:', error);
        this.showAlert('error', 'Failed to save signature. Please try again.');
      } finally {
        this.isLoading = false;
      }
    },

    eraseSignature() {
      this.signaturePad.clear();
    },

    showAlert(type, message) {
      const id = Date.now();
      this.alerts.push({ id, type, message });
      setTimeout(() => this.closeAlert(id), 3000);
    },

    closeAlert(id) {
      const index = this.alerts.findIndex(alert => alert.id === id);
      if (index !== -1) {
        this.alerts.splice(index, 1);
      }
    },

    closePopup() {
      this.showPopup = false;
      this.imageLoaded = false;
    },
  }
};
</script>

<style scoped>
/* 移动端样式 */
.signature-mobile-container {
  overscroll-behavior: none;
  -webkit-overscroll-behavior: none;
}

.signature-mobile-container canvas {
  touch-action: none;
  -webkit-touch-callout: none;
  -webkit-tap-highlight-color: transparent;
}

/* 桌面端样式 */
.signature-desktop-container {
  width: 100%;
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

/* 动画效果 */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s, transform 0.5s;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(30px);
}

/* 警告提示样式 */
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
  @apply bg-green-100 border-green-400 text-green-700;
}

.alert-error {
  @apply bg-red-100 border-red-400 text-red-700;
}

.alert-warning {
  @apply bg-yellow-100 border-yellow-400 text-yellow-700;
}

.alert-info {
  @apply bg-blue-100 border-blue-400 text-blue-700;
}

/* 针对微信浏览器的特殊处理 */
@supports (-webkit-overflow-scrolling: touch) {
  .signature-mobile-container {
    position: fixed;
    height: 100vh;
    padding-bottom: env(safe-area-inset-bottom);
  }
}
</style>