<template>
  <div class="flex flex-col md:flex-row h-screen md:h-2/3 min-h-[65vh]">
    <!-- Board to Show all the signature-->
    <!-- Part 1. Will keep rotate the signature-->
    <!-- Part 2. Behind will show all the signature and effect like matrix fall-->
    <div class="w-full md:w-2/3 rounded-lg h-full">
      <div
        class="whiteboard flex flex-col items-center justify-center relative rounded-lg h-full"
        :style="{
          backgroundImage: `url(${backgroundUrl})`,
          backgroundRepeat: 'no-repeat',
          backgroundPosition: 'center',
          backgroundColor: `${backgroundColor}`,
          backgroundSize: 'cover',
        }"
      >
        <div v-if="loading" class="loading-text">Loading...</div>
        <div v-else class="absolute top-5 left-0 w-full h-full">
          <transition name="fly-in" mode="out-in">
            <img
              v-if="signatures.length > 0"
              :src="signatures[currentIndex].signature"
              alt="Signature"
              :key="signatures[currentIndex].id"
              class="signature-image"
            />
          </transition>
        </div>
        <div class="board-signatures absolute w-full h-full top-0 overflow-hidden">
          <!-- Add the matrix effect in here -->
          <div
            v-for="(signature, index) in matrixSignatures"
            :key="signature.id + '-' + index"
            :style="getMatrixStyle(index)"
            class="signature-background matrix-column"
          >
            <img :src="signature.signature" alt="Signature" class="matrix-signature" />
          </div>
        </div>
        <div v-if="newSignatureDetected" class="new-signature-notice"></div>
        <div class="bg-white w-full h-12 flex items-center justify-center absolute top-full left-0">
          <span v-text="'Welcome, ' + signatures[currentIndex].attendance.participant.name" v-if="signatures[currentIndex]"></span>
        </div>
      </div>
      <div class="w-full text-center p-4 text-white text-lg">
        剩余时间: {{ countdownTime }} 秒
      </div>
    </div>
    <!-- Listing Board Signature-->
    <div ref="scrollableList" class="scrollableList w-full ml-0 md:ml-4 mt-4 md:mt-0 md:w-1/3 rounded-xl shadow-md bg-white h-full overflow-auto">
      <div class="content">
        <div class="border-b pb-4 mb-4 pt-6 px-6">
          <div class="flex justify-between items-center">
            <h2 class="text-base lg:text-lg font-bold text-primary">Attendances 
                <span class="text-xs">({{ signatures.length }})</span></h2>
          </div>
        </div>
        <div class="mb-6" v-motion
            :initial="{ opacity: 0, y: 100 }"
            :enter="{ opacity: 1, y: 0, scale: 1 }"
            :variants="{ custom: { scale: 2 } }"
            :delay="200"
            :duration="1200">
          <template v-if="loading">
            <div v-for="i in 3" :key="i" class="mb-3 text-left mt-2 py-4 px-4 bg-gray-100 animate-pulse">
              <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
              <div class="h-3 bg-gray-200 rounded w-2/3"></div>
            </div>
          </template>
          <template v-else>
            <template v-if="signatures.length > 0">
              <div
                v-for="signature in signatures"
                :key="signature.id"
                class="mb-3 text-left mt-2 py-4 px-4 cursor-pointer hover:bg-third hover:text-lg signature-item"
                :class="{ 'bg-third shadow-md flow mx-auto text-base md:text-lg': signatures[currentIndex].id === signature.id }"
              >
                <a @click="changeCurrentIndex(signatures.indexOf(signature))"
                  v-motion="{ initial: { scale: 1 }, hover: { scale: 1.1 } }">
                  <h3 class="text-black font-semibold">{{ signature.attendance.participant.name }}</h3>
                  <div class="flex justify-between">
                    <p class="text-xs text-gray-400">{{ signature.attendance.participant.comp_name }}</p>
                    <p class="text-xs text-primary font-semibold text-right">{{ signature.attendance.CheckInTimeForHumans }}</p>
                  </div>
                </a>
              </div>
            </template>
            <template v-else>
              <p>{{ noResults }}</p>
            </template>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useMotion } from '@vueuse/motion';

// Initialize motion
useMotion();
export default {
  props: {
    apiUrl: {
      type: String,
      required: true,
    },
    interval: {
      type: Number,
      default: 60000, // Set to 1 minute
    },
    displayInterval: {
      type: Number,
      default: 9000, // Set to 8 seconds
    },
    backgroundUrl: {
      type: String,
      default: 'https://i.pinimg.com/1200x/ec/5c/d9/ec5cd9f0428c94b39a271f0d73fa5e50.jpg',
    },
    backgroundColor: {
      type: String,
      default: '#FFFFFf',
    },
  },
  data() {
    return {
      signatures: [],
      loading: true,
      currentIndex: 0,
      newSignatureDetected: false,
      fetchIntervalId: null,
      rotateTimeoutId: null,
      countdownIntervalId: null,
      countdownTime: this.displayInterval / 1000, // 初始化倒计时为 displayInterval 的秒数
      positions: [],
    };
  },
  computed: {
    // 计算生成的签名数组，用于矩阵效果
    matrixSignatures() {
      const minSignatures = 50;
      let matrixSignatures = [];

      if (this.signatures.length > 0) {
        while (matrixSignatures.length < minSignatures) {
          matrixSignatures.push(...this.signatures);
        }

        for (let i = 0; i < 10; i++) {
          matrixSignatures.push(...this.signatures);
        }
      }

      return matrixSignatures;
    },
  },
  mounted() {
    this.fetchSignatures(); // First time fetch
    this.fetchIntervalId = setInterval(this.fetchSignatures, this.interval); // Fetch signatures every interval
    this.scheduleNextRotation();
    this.startCountdown();
  },
  beforeUnmount() {
    clearInterval(this.fetchIntervalId);
    clearTimeout(this.rotateTimeoutId);
    clearInterval(this.countdownIntervalId);
  },
  methods: {
    async fetchSignatures() {
      try {
        this.loading = true;
        const response = await axios.get(this.apiUrl);
        const newSignatures = response.data.data;

        if (Array.isArray(newSignatures)) {
          this.checkForNewSignatures(newSignatures);
          this.signatures = newSignatures;
          this.currentIndex = 0;
          this.positions = newSignatures.map(() => this.getRandomPosition());
        }
      } catch (error) {
        console.log('Error fetching signatures:', error);
      } finally {
        this.loading = false;
      }
    },
    scheduleNextRotation() {
      this.rotateTimeoutId = setTimeout(this.rotateSignatures, this.displayInterval);
    },
    startCountdown() {
      this.countdownTime = this.displayInterval / 1000;
      clearInterval(this.countdownIntervalId);
      this.countdownIntervalId = setInterval(() => {
        if (this.countdownTime > 0) {
          this.countdownTime--;
        }
      }, 1000);
    },
    changeCurrentIndex(index) {
      this.currentIndex = index;
      this.scrollListToTop();
    },
    scrollListToBottom() {
      const listElement = this.$refs.scrollableList;
      listElement.scrollTop = listElement.scrollHeight;
    },
    scrollListToTop() {
      const listElement = this.$refs.scrollableList;
      listElement.scrollTop = 0;
    },
    rotateSignatures() {
      if (this.signatures.length > 0) {
        if (this.currentIndex === this.signatures.length - 1) {
          this.currentIndex = 0;
          this.scrollListToTop();
        } else {
          this.currentIndex++;
          this.scrollListToBottom();
        }
        this.startCountdown(); // 旋转签名时重新开始倒计时
        this.scheduleNextRotation();
      }
    },
    checkForNewSignatures(newSignatures) {
      const existingSignatureIds = this.signatures.map((s) => s.id);
      const newSignatureIds = newSignatures.map((s) => s.id);
      this.newSignatureDetected = newSignatureIds.some((id) => !existingSignatureIds.includes(id));

      if (this.newSignatureDetected) {
        setTimeout(() => {
          this.newSignatureDetected = false;
        }, 5000);
      }
    },
    getRandomPosition() {
      const top = Math.random() * 100;
      const left = Math.random() * 100;
      const delay = Math.random() * 5;
      return {
        top: `${top}%`,
        left: `${left}%`,
        animationDelay: `${delay}s`,
      };
    },
    handleAnimationIteration(index) {
      const newPosition = this.getRandomPosition();
      this.positions.splice(index, 1, newPosition);
    },
    getMatrixStyle(index) {
      const column = index % 10;
      const animationDelay = Math.random() * 2;
      const animationDuration = 5 + Math.random() * 8;
      return {
        left: `${column * 10}%`,
        animationDelay: `${animationDelay}s`,
        animationDuration: `${animationDuration}s`,
      };
    },
  },
};
</script>

<style scoped>
.whiteboard {
  border: 2px solid #ccc;
  width: 100%;
  height: 100%;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.signature-image {
  display: block;
  margin: 0 auto;
  width: 90%;
  height: 90%;
  object-fit: contain;
  object-position: center;
  animation: fly-in 1.5s ease-out forwards;
}

.signature-background {
  position: absolute;
  width: 15%;
  animation: matrix-fall linear infinite;
}

.matrix-column {
  position: absolute;
  top: -100%;
  width: 15%;
  animation: matrix-fall 10s linear infinite;
}

.matrix-signature {
  width: 100%;
  opacity: 1;
  transform: scale(0.8);
}

@keyframes matrix-fall {
  0% {
    top: -100%;
    opacity: 0;
  }
  10% {
    opacity: 1;
  }
  100% {
    top: 100%;
    opacity: 0;
  }
}

/* Fly-in and fade transition */
@keyframes fly-in {
  0% {
    transform: translateY(-100%) scale(0.5);
    opacity: 0;
  }
  50% {
    transform: translateY(0) scale(1.2);
    opacity: 1;
  }
  100% {
    transform: translateY(0) scale(0.8);
    opacity: 1;
  }
}

.loading-text {
  font-size: 1.5em;
  color: #666;
}

.new-signature-notice {
  position: absolute;
  bottom: 10px;
  font-size: 1.2em;
  color: red;
}

.fly-in-enter-active,
.fly-in-leave-active {
  transition: opacity 1s, transform 1s;
}
.fly-in-enter,
.fly-in-leave-to {
  opacity: 0;
  transform: translateY(-100%) scale(0.5);
}

.loading-enter-active,
.loading-leave-active {
  transition: opacity 0.3s;
}
.loading-enter,
.loading-leave-to {
  opacity: 0;
}

@keyframes flow {
  0% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
  100% {
    transform: translateY(0);
  }
}

.flow {
  animation: flow 1s ease-in-out infinite;
  transform: scale(112);
}

.signature-item {
  transition: transform 0.3s ease, z-index 0.3s ease;
}

.signature-item:hover {
  transform: scale(1.2);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  @apply mx-auto;
  @apply w-10/12;
}

.scrollableList {
  overflow-y: auto;
  overflow-x: hidden;
}

.content {
  border-radius: inherit;
}

.timer {
  position: absolute;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  font-size: 1.5rem;
  color: white;
  background-color: rgba(0, 0, 0, 0.5);
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
}
</style>