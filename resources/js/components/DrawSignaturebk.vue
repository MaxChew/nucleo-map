<template>
  <div class="flex flex-col items-center w-full md:w-1/3 h-96 md:h-1/2">
    <div class="w-full h-full">
      <canvas
        ref="signatureCanvas"
        class="w-full rounded-lg h-full"
        :style="{ backgroundColor: signBoardColor }"
        width="400"
        height="400"
      ></canvas>
    </div>

    <div class="flex flex-row justify-between items-center mt-10 mb-4 px-2 w-full">
      <a :href="backUrl" class="text-white hover:text-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 cursor-pointer font-bonk">
        <i class="fas fa-chevron-circle-left mr-1"></i>Back
      </a>
      <div>
        <button @click="eraseSignature" class="mr-2 px-4 py-2 bg-danger-500 font-bold text-white rounded-2xl hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-fourth" v-if="!isLoading">
          Erase
        </button>
        <button @click="saveSignature" class="px-4 py-2 bg-black font-bold text-white rounded-2xl hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-fourth">
          {{ buttonText }}
        </button>
      </div>
    </div>

    <div v-if="showPopup" class="fixed inset-0 flex items-center justify-center bg-primary bg-opacity-75">
      <div class="bg-black p-6 rounded-lg shadow-lg text-center">
        <h2 class="text-xl font-bold mb-4 text-white">Saved Signature</h2>
        <img :src="savedImage" alt="Signature" class="mb-4 animated-signature" :class="{'loaded': imageLoaded}" @load="imageLoaded = true"/>
        <button @click="closePopup" class="px-4 py-2 bg-danger-500 text-white rounded-lg hover:bg-danger-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
          Close
        </button>
      </div>
    </div>
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
    const canvas = this.$refs.signatureCanvas;
    this.signaturePad = new SignaturePad(canvas, {
      penColor: this.signPenColor, // Ensure pen color is set
    });
    this.resizeCanvas();
    window.addEventListener('resize', this.resizeCanvas);
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.resizeCanvas);
  },
  methods: {
    resizeCanvas() {
      const canvas = this.$refs.signatureCanvas;
      const ratio = Math.max(window.devicePixelRatio || 1, 1);
      canvas.width = canvas.offsetWidth * ratio;
      canvas.height = canvas.offsetHeight * ratio;
      canvas.getContext('2d').scale(ratio, ratio);
      this.signaturePad.clear();
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
</style>