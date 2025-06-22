<template>
  <div class="flex flex-col md:flex-row h-screen md:h-2/3 min-h-[65vh]">
    <!-- Fullscreen Toggle Button -->
    <div class="fullscreen-toggle">
      <button @click="toggleFullscreen" class="fullscreen-button">
        {{ isFullscreen ? 'Exit Fullscreen' : 'Enter Fullscreen' }}
      </button>
    </div>
    
    <!-- Board to Show all the signature -->
    <!-- Part 1: Rotating signature -->
    <!-- Part 2: Background with matrix or twinkling effect -->
    <div class="w-full md:w-2/3 rounded-lg h-full" ref="displayContainer">
      <!-- center main singature -->
      <div
        class="whiteboard flex flex-col items-center justify-center relative rounded-lg h-full"
        :style="{
          backgroundImage: `url(${computedBackgroundUrl})`,
          backgroundRepeat: 'no-repeat',
          backgroundPosition: 'center',
          backgroundColor: `${backgroundColor}`,
          backgroundSize: 'cover',
        }"
      >
        <div v-if="loading" class="loading-text">Loading...</div>
        <div v-else class="absolute top-5 left-0 w-full h-full">
          <transition name="fly" mode="out-in">
            <img
              v-if="signatures.length > 0"
              :src="signatures[currentIndex].signature"
              alt="Signature"
              :key="signatures[currentIndex].id"
              class="signature-image"
            />
          </transition>
        </div>

        <!-- Matrix Effect -->
        <div v-if="mode === 'matrix'" class="board-signatures absolute w-full h-full top-0 overflow-hidden">
          <div
            v-for="(signature, index) in matrixSignatures"
            :key="signature.id + '-' + index"
            :style="getMatrixStyle(index)"
            class="signature-background matrix-column"
          >
            <img :src="signature.signature" alt="Signature" class="matrix-signature" />
          </div>
        </div>

        <!-- Twinkling Stars Effect -->
        <div v-else-if="mode === 'twinkling'" class="twinkling-background absolute w-full h-full top-0 overflow-hidden">
          <div class="stars">
            <div
              v-for="(signature, index) in twinklingSignatures"
              :key="signature.id + '-' + index"
              :style="{ top: `${getRandomPercentage()}%`, left: `${getRandomPercentage()}%` }"
              class="twinkling-signature"
            >
              <img :src="signature.signature" alt="Signature" />
            </div>
          </div>
        </div>

        <div v-if="newSignatureDetected" class="new-signature-notice"></div>
        <div class="bg-white w-full h-12 flex items-center justify-center absolute top-full left-0">
          <span v-text="'Welcome, ' + signatures[currentIndex].attendance.participant.name" v-if="signatures[currentIndex]"></span>
        </div>
      </div>

      <!-- Timer and Mode Switch -->
      <div class="w-full text-center p-4 text-white text-lg">
        剩余时间: {{ countdownTime }} 秒
        <div class="mode-switch">
          <button @click="mode = 'matrix'" :class="{ active: mode === 'matrix' }">Matrix Fall</button>
          <button @click="mode = 'twinkling'" :class="{ active: mode === 'twinkling' }">Twinkling Stars</button>
        </div>
      </div>
    </div>

    <!-- Listing Board Signature -->
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
    backgroundUrl: {
      type: String,
      default: null, // Allow null or empty backgroundUrl
    },
    useDemoEffect: {
      type: Boolean,
      default: false, // Option to toggle demo_effect_dekstop.webp
    },
    backgroundColor: {
      type: String,
      default: '#FFFFFf',
    },
    interval: {
      type: Number,
      default: 60000, // Set to 1 minute
    },
    displayInterval: {
      type: Number,
      default: 9000, // Set to 9 seconds
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
      countdownTime: this.displayInterval / 1000, // Initialize countdown to displayInterval in seconds
      positions: [],
      mode: 'matrix', // Default mode is 'matrix'
      isFullscreen: false,
    };
  },
  computed: {
    computedBackgroundUrl() {
      if (this.backgroundUrl) {
        console.log("here");
        return this.backgroundUrl;
      } else if (this.useDemoEffect) {
        return '/images/demo_effect_dekstop_five.webp';
      }
      return null;// Add a default fallback image if needed
    },
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
    twinklingSignatures() {
      const minSignatures = 50;
      let twinklingSignatures = [];

      if (this.signatures.length > 0) {
        while (twinklingSignatures.length < minSignatures) {
          twinklingSignatures.push(...this.signatures);
        }

        for (let i = 0; i < 10; i++) {
          twinklingSignatures.push(...this.signatures);
        }
      }

      return twinklingSignatures;
    },
  },
  mounted() {
    this.fetchSignatures();
    this.fetchIntervalId = setInterval(this.fetchSignatures, this.interval);
    this.scheduleNextRotation();
    this.startCountdown();

    // Add fullscreen change event listener
    document.addEventListener("fullscreenchange", this.onFullscreenChange);
    document.addEventListener("webkitfullscreenchange", this.onFullscreenChange); // Safari/Chrome
    document.addEventListener("mozfullscreenchange", this.onFullscreenChange); // Firefox
    document.addEventListener("MSFullscreenChange", this.onFullscreenChange); // IE/Edge
  },
  beforeUnmount() {
    clearInterval(this.fetchIntervalId);
    clearTimeout(this.rotateTimeoutId);
    clearInterval(this.countdownIntervalId);

    // Remove fullscreen change event listener
    document.removeEventListener("fullscreenchange", this.onFullscreenChange);
    document.removeEventListener("webkitfullscreenchange", this.onFullscreenChange);
    document.removeEventListener("mozfullscreenchange", this.onFullscreenChange);
    document.removeEventListener("MSFullscreenChange", this.onFullscreenChange);
  
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
          this.currentIndex = 0; // Reset currentIndex on new data
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

    changeCurrentIndex(index) {
        this.currentIndex = index;
        this.scrollListToTop();
        this.startCountdown(); // Restart the countdown when the index is changed
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
        this.startCountdown(); // Restart countdown when rotating signatures
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
    getRandomPercentage() {
      return Math.random() * 100;
    },

    toggleFullscreen() {
      const elem = this.$refs.displayContainer;

      if (!this.isFullscreen) {
        if (elem.requestFullscreen) {
          elem.requestFullscreen();
        } else if (elem.mozRequestFullScreen) { // Firefox
          elem.mozRequestFullScreen();
        } else if (elem.webkitRequestFullscreen) { // Chrome, Safari, Opera
          elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) { // IE/Edge
          elem.msRequestFullscreen();
        }
      } else {
        if (document.exitFullscreen) {
          document.exitFullscreen();
        } else if (document.mozCancelFullScreen) { // Firefox
          document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) { // Chrome, Safari, Opera
          document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) { // IE/Edge
          document.msExitFullscreen();
        }
      }

      this.isFullscreen = !this.isFullscreen;
    },

    onFullscreenChange() {
      // Check if the document is currently in fullscreen mode
      const fullscreenElement = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;
      this.isFullscreen = !!fullscreenElement;
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

@keyframes fly-out {
  0% {
    transform: translateY(0) scale(0.8);
    opacity: 1;
  }
  50% {
    transform: translateY(-100%) scale(1.2);
    opacity: 0.5;
  }
  100% {
    transform: translateY(-200%) scale(0.5);
    opacity: 0;
  }
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

/* Twinkling Stars Effect */
.twinkling-background {
  width: 100%;
  height: 100%;
}

.stars {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.twinkling-signature {
  position: absolute;
  width: 50px;
  height: 50px;
  animation: twinkle 2s infinite ease-in-out alternate;
}

@keyframes twinkle {
  from {
    opacity: 0.5;
    transform: scale(0.8);
  }
  to {
    opacity: 1;
    transform: scale(1.2);
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
.fly-enter-active, .fly-leave-active {
  transition: opacity 1s, transform 1s;
}

.fly-enter {
  animation: fly-in 1.5s ease-out forwards;
}

.fly-leave-to {
  animation: fly-out 1.5s ease-in forwards;
}

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

.mode-switch {
  margin-top: 10px;
}

.mode-switch button {
  background-color: #666;
  color: white;
  padding: 10px 20px;
  border: none;
  cursor: pointer;
  margin: 5px;
  border-radius: 5px;
}

.mode-switch button.active {
  background-color: #333;
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
.fullscreen-toggle {
  position: absolute;
  bottom: 15px;
  left: 10px;
  z-index: 1000;
}

.fullscreen-button {
  background-color: #333;
  color: white;
  padding: 10px 20px;
  border: none;
  cursor: pointer;
  border-radius: 5px;
  transition: background-color 0.3s;
}

.fullscreen-button:hover {
  background-color: #555;
}
</style>