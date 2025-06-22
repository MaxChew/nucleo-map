<template>
  <div class="flex flex-col md:flex-row h-screen md:h-2/3 min-h-[65vh]">
   
    
    <!-- Board to Show all the signature -->
    <!-- Part 1: Rotating signature -->
    <!-- Part 2: Background with matrix or twinkling effect -->
    <div class="w-full md:w-2/3 rounded-xl h-full" ref="displayContainer">
      <!-- center main singature -->
      <div
        class="whiteboard flex flex-col items-center justify-center relative rounded-3xl h-full"
        :class="{ 'fullscreen-mode': isFullscreen }"
        :style="{
          backgroundImage: `url(${computedBackgroundUrl})`,
          backgroundRepeat: 'no-repeat',
          backgroundPosition: 'center',
          backgroundColor: `${backgroundColor}`,
          backgroundSize: 'cover',
        }"
      >
      <button @click="toggleFullscreen" class="fullscreen-button !absolute bottom-2 left-2 z-10" :class="{ 'fullscreenvisible': showFullscreenButton }" >
          {{ isFullscreen ? 'Exit Full Screen' : 'Full Screen' }}
      </button>

        
        <div class="signature-container absolute top-5 left-0 w-full h-full flex items-center justify-center">
          <transition name="fly" mode="out-in">
            <img
              v-if="signatures.length > 0 && currentIndex >= 0 && isSignatureVisible"
              :src="signatures[currentIndex].signature"
              alt="Signature"
              :key="signatures[currentIndex].id"
              class="signature-image"
              :class="{
                'flying-in': isFlyingIn,
                'flying-out': isFlyingOut
              }"
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
      <div class="flex flex-col w-full text-center p-2 text-white text-lg">
        Countdown Timer: {{ countdownTime }} Second
        <span v-show="this.$root.user.is_admin" v-if="this.$root.user">Current Index : {{ currentIndex }} Position</span>
        <div class="mode-switch">
          <button @click="setLoopMode('once')" :class="{ active: loopMode === 'once' }">Once</button>
          <button @click="setLoopMode('unlimited')" :class="{ active: loopMode === 'unlimited' }">Unlimited Loop</button>
          <button @click="setLoopMode('stop')" :class="{ active: loopMode === 'stop' }">Stop Loop</button>
          <button @click="mode = 'normal'" :class="{ active: mode === 'normal' }">Normal</button>
          <button @click="mode = 'matrix'" :class="{ active: mode === 'matrix' }">Matrix Fall</button>
          <button @click="mode = 'twinkling'" :class="{ active: mode === 'twinkling' }">Twinkling Stars</button>
        </div>
      </div>
    </div>

    <!-- Listing Board Signature -->
    <div ref="scrollableList" class="scrollableList w-full ml-0 md:ml-4 mt-4 md:mt-0 md:w-1/3 rounded-2xl shadow-md bg-white h-full overflow-auto" style="max-height: 900px;">
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
            <template v-if="signatures.length > 0 ">
              <div
                v-for="signature in signatures"
                :key="signature.id"
                class="mb-3 text-left mt-2 py-4 px-4 cursor-pointer hover:primary-light hover:text-lg signature-item"
                :class="{ 'bg-primary-light shadow-md flow mx-auto text-base md:text-lg': currentIndex != -1 && signatures[currentIndex].id === signature.id }"
              >
                <a @click="changeCurrentIndex(signatures.indexOf(signature))"
                  v-motion="{ initial: { scale: 1 }, hover: { scale: 1.1 } }">
                  <div class="flex justify-between">
                    <h3 class="text-sm md:text-base text-black font-semibold">{{ signature.attendance.participant.name }}</h3>
                    <div class="text-[10px] md:text-xs text-right flex flex-row items-center rounded-xl bg-primary text-white p-2 w-20 md:w-24 justify-center">
                      <span class="font-semibold">{{ signature.attendance.CheckInTimeForHumans }}</span>
                    </div>
                  </div>
                  <div class="flex justify-between">
                    <p class="text-xs text-gray-400">{{ signature.attendance.participant.comp_name }}</p>
                    
                    <div class="text-[8px] md:text-[10px] text-primary text-right flex flex-row items-center" v-if="signature.attendance.display_at">
                      Looped at {{ signature.attendance.display_at }}
                      <i class="far fa-clock ml-2"  
                        ></i>
                    </div>
                  </div>
                </a>
              </div>
            </template>
            <template v-else>
              <p></p>
            </template>
          </template>
        </div>
      </div>
    </div>

    <transition-group name="fade" tag="div" class="alert-container" :class="{ 'fullscreen-alerts': isFullscreen }" v-show="true">
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
import axios from 'axios';
import { nextTick } from 'vue';
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
    signPenRgba: {
      type: String,
      default: '255, 255, 255',
    },
    
    interval: {
      type: Number,
      default: 30000, // Set to 30 sec
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
      checkLoopLastID: null,
      fetchIntervalId: null,
      rotateTimeoutId: null,
      countdownIntervalId: null,
      countdownTime: this.displayInterval / 1000, // Initialize countdown to displayInterval in seconds
      positions: [],
      mode: 'normal', // 将默认模式改为 'normal'
      isFullscreen: false,
      isButtonVisible: false,
      buttonHideTimeout: null,
      loopMode: 'unlimited', // 可以是 'once', 'unlimited', 或 'stop'
      isLooping: false,
      loopCount: 0,
      isFlyingIn: false,
      isFlyingOut: false,
      alerts: [],
      isSignatureVisible: true,
    };
  },
  computed: {
    computedBackgroundUrl() {
      if (this.backgroundUrl) {
        return this.backgroundUrl;
      } else if (this.useDemoEffect) {
        console.log("need use here");
        return '/images/board/demo_effect_dekstop_five.webp';
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
    showFullscreenButton() {
      return !this.isFullscreen || this.isButtonVisible;
    }
  },
  mounted() {
    this.setLoopMode('unlimited'); // 默认为循环一次
    this.fetchIntervalId = setInterval(this.fetchNewSignatures, this.interval);
    this.checkLoopLastID = setInterval(this.checkLoopLastIndex, (this.interval + 10000));
    this.scheduleNextRotation();
    this.startCountdown();
    

    // Add fullscreen change event listener
    document.addEventListener("fullscreenchange", this.onFullscreenChange);
    document.addEventListener("webkitfullscreenchange", this.onFullscreenChange); // Safari/Chrome
    document.addEventListener("mozfullscreenchange", this.onFullscreenChange); // Firefox
    document.addEventListener("MSFullscreenChange", this.onFullscreenChange); // IE/Edge
    document.addEventListener('mousemove', this.handleMouseMove);
  },
  beforeUnmount() {
    clearInterval(this.fetchIntervalId);
    clearTimeout(this.rotateTimeoutId);
    clearInterval(this.countdownIntervalId);
    clearInterval(this.checkLoopLastID);

    // Remove fullscreen change event listener
    document.removeEventListener("fullscreenchange", this.onFullscreenChange);
    document.removeEventListener("webkitfullscreenchange", this.onFullscreenChange);
    document.removeEventListener("mozfullscreenchange", this.onFullscreenChange);
    document.removeEventListener("MSFullscreenChange", this.onFullscreenChange);
    document.removeEventListener('mousemove', this.handleMouseMove);

    this.stopLooping();
  
  },
  watch: {
    isSignatureVisible(newValue, oldValue) {
      if (newValue) {
        // 如果变为可见,开始循环
        this.startLooping();
        
        // 如果是从不可见变为可见,重置倒计时
        if (!oldValue) {
          clearInterval(this.countdownIntervalId);
          this.startCountdown();
          this.currentIndex = this.signatures.length - 1;
        }
      } else {
        // 如果变为不可见,停止循环
        this.stopLooping();
      }
    },
    currentIndex: {
      handler(newIndex, oldIndex) {
        console.log('当前索引发生变化', '旧索引:', oldIndex, '新索引:', newIndex);

        if (newIndex !== oldIndex) {
          this.handleIndexChange(newIndex, oldIndex);
        }
      },
      immediate: true // 组件创建时立即执行一次
    }
  },
  
  methods: {
    async fetchSignatures() {
      try {
        this.loading = true;
        const response = await axios.get(this.apiUrl + '?mode=' + this.loopMode);
        const newSignatures = response.data.data;

        if (Array.isArray(newSignatures)) {
          this.checkForNewSignatures(newSignatures);
          if (this.newSignatureDetected) {
            this.signatures = newSignatures;
            this.currentIndex = this.signatures.length - 1;
            this.showAlert('info', 'Welcome, ' + this.signatures[this.currentIndex].attendance.participant.name ); // 添加这行
            if (this.newSignatureDetected) {
              setTimeout(() => {
                this.newSignatureDetected = false;
              }, 5000);
            }
          }
          this.positions = newSignatures.map(() => this.getRandomPosition());
        }
      } catch (error) {
        console.log('Error fetching signatures:', error);
      } finally {
        this.loading = false;
      }
    },

    async fetchOnceSignatures() {
      try {
        this.loading = true;
        this.signatures = [];
        const response = await axios.get(this.apiUrl + '?mode=once');
        const newSignatures = response.data.data;

        console.log('fetchOnceSignatures' , newSignatures);

        if (Array.isArray(newSignatures) && newSignatures.length > 0) {
          this.signatures = newSignatures;
          this.currentIndex = 0;  // 从第一个签名开始
          
          console.log('fetchOnceSignatures2' , this.signatures);

          // 为每个签名生成随机位置
          this.positions = newSignatures.map(() => this.getRandomPosition());
          
          // 触发新签名检测的相关动作
          this.triggerNewSignatureActions();
        } else {
         
        }
      } catch (error) {
        console.error('获取签名时出错:', error);
        this.showAlert('error', '获取签名失败');
      } finally {
        this.loading = false;
      }
    },

    async fetchNewSignatures() {
      try {
        console.log('检查新签名');
        this.loading = true;
        const response = await axios.get(this.apiUrl + '?mode=' + this.loopMode + '&method=new');
        const newSignatures = response.data.data;

        if (Array.isArray(newSignatures) && newSignatures.length > 0) {
          // 遍历新签名,检查是否已存在,如果不存在则添加
          newSignatures.forEach(newSignature => {
            const existingIndex = this.signatures.findIndex(s => s.id === newSignature.id);
            if (existingIndex === -1) {
              // 如果签名不存在,则添加到数组末尾
              this.signatures.push(newSignature);
              this.newSignatureDetected = true;
              this.showAlert('info', '欢迎, ' + newSignature.attendance.participant.name);
              
              // 为新签名生成随机位置
              this.positions.push(this.getRandomPosition());

              // 如果当前没有显示签名,则显示新签名
              if (this.currentIndex === -1) {
                this.currentIndex = this.signatures.length - 1;
                if (this.currentIndex < 5) {
                  this.scrollListToTop();
                } else {
                  this.scrollListToBottom();
                }
                
                this.startLooping();
              }
            }
          });

          if (this.newSignatureDetected) {
            setTimeout(() => {
              this.newSignatureDetected = false;
            }, 5000);
          }
        } else {
          console.log('没有新签名');
        }
      } catch (error) {
        console.error('获取签名时出错:', error);
        this.showAlert('error', '获取签名失败');
      } finally {
        this.loading = false;
      }
    },

    // 新方法：触发新签名相关的动作
    triggerNewSignatureActions() {
      this.newSignatureDetected = true;
      this.startLooping();
      
      // 5秒后重置新签名检测状态
      setTimeout(() => {
        this.newSignatureDetected = false;
        this.isSignatureVisible = true;
      }, 2000);
    },

    scheduleNextRotation() {
      clearTimeout(this.rotateTimeoutId);
      this.rotateTimeoutId = setTimeout(this.rotateSignatures, this.displayInterval);
    },

    changeCurrentIndex(index) {
        this.stopLooping();

        this.currentIndex = index;
        this.scrollListToTop();
        this.startCountdown(); // Restart the countdown when the index is changed
         // 设置定时器,在一定时间后触发飞出动画
        setTimeout(() => {
          this.triggerFlyOut();
        }, this.displayInterval);
    },

    startCountdown() {
      clearInterval(this.countdownIntervalId); // 清除现有的倒计时
      this.countdownTime = this.displayInterval / 1000;
      this.countdownIntervalId = setInterval(() => {
        if (this.countdownTime > 0) {
          this.countdownTime--;
        } else {
          clearInterval(this.countdownIntervalId);
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

    handleIndexChange(newIndex, oldIndex) {
      if (this.signatures[newIndex] && this.signatures[newIndex].attendance) {
        if (this.signatures[newIndex].attendance?.display_at == null) {
          console.log('Updating signature display time');
          this.postSignatureUpdateAt(newIndex).catch(error => {
            console.error('Failed to update signature display time:', error);
          });
        }
      }

      console.log('Current Index Changed:', newIndex, oldIndex);
    },

    foundNextIndex(changeIndex) {
        let nextIndex = this.currentIndex;
        let foundUnshown = false;
  
        if (this.loopMode === 'unlimited') {
          return true;
        }
  
        // 首先在当前索引之后查找未显示的签名
        for (let i = nextIndex + 1; i < this.signatures.length; i++) {
          if (this.signatures[i]?.attendance?.display_at === null) {
            if (changeIndex) {
              this.currentIndex = i;
            }
            return true
          }
        }
  
        // 如果没找到，从头开始查找
        if (!foundUnshown && this.loopMode === 'once') {
          for (let i = 0; i <= this.currentIndex; i++) {
            if (this.signatures[i]?.attendance?.display_at === null) {
              if (changeIndex) {
                this.currentIndex = i;
              }
              return true
            }
          }
        }
  
        return false;
      },

    rotateSignatures() {
        console.log('Rotating signatures', this.currentIndex);
  
        if (this.signatures.length > 0 && this.isSignatureVisible) {
          let foundUnshown = false;
  
          if (this.loopMode === 'once') {
            foundUnshown = this.foundNextIndex(true);
            if (!foundUnshown) {
              console.log('所有签名都已显示过');
              this.triggerFlyOut();
              return;
            }
          } else if (this.loopMode === 'unlimited') {
            // 在无限循环模式下，总是移动到下一个索引
            this.currentIndex = (this.currentIndex + 1) % this.signatures.length;
            // 重置当前签名的 display_at 属性
            if (this.signatures[this.currentIndex]?.attendance) {
              this.signatures[this.currentIndex].attendance.display_at = null;
            }
          }
  
          console.log('New current index:', this.currentIndex);
  
          if (this.currentIndex < 5) {
            this.scrollListToTop();
          } else {
            this.scrollListToBottom();
          }
  
          this.startCountdown();
  
          if (this.isLooping) {
            this.scheduleNextRotation();
          }
        }
      },

    checkForNewSignatures(newSignatures) {
      const existingSignatureIds = this.signatures.map((s) => s.id);
      const newSignatureIds = newSignatures.map((s) => s.id);
      const newSignature = newSignatures.find(s => !existingSignatureIds.includes(s.id));
      
      if (newSignature) {
        this.newSignatureDetected = true;
        this.showNewSignature(newSignature);
      }
    },

    showNewSignature(newSignature) {
      // 停止当前的循环(如果有的话)
      //this.stopLooping();
      
      // 设置新签名为当前显示的签名
      this.currentIndex = this.signatures.length;
      this.signatures.push(newSignature);
      
      // 触发飞入动画
      this.$nextTick(() => {
        this.triggerFlyIn();
      });
      
      // 设置定时器,在一定时间后触发飞出动画
      setTimeout(() => {
        this.triggerFlyOut();
      }, this.displayInterval);
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
        elem.classList.add('fullscreen-mode');
        if (elem.requestFullscreen) {
          elem.requestFullscreen();
        } else if (elem.mozRequestFullScreen) { // Firefox
          elem.mozRequestFullScreen();
        } else if (elem.webkitRequestFullscreen) { // Chrome, Safari, Opera
          elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) { // IE/Edge
          elem.msRequestFullscreen();
        }
        this.isFullscreen = true;
        this.isButtonVisible = true;
        this.$nextTick(() => {
          setTimeout(() => {
            this.isButtonVisible = false;
          }, 3000);
        });
        this.$nextTick(() => {
          const alertContainer = document.querySelector('.alert-container');
          if (alertContainer) {
            this.$refs.displayContainer.appendChild(alertContainer);
          }
        });
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
        elem.classList.remove('fullscreen-mode');
        this.isFullscreen = false;
        this.isButtonVisible = true;

        this.$nextTick(() => {
          const alertContainer = document.querySelector('.alert-container');
          if (alertContainer) {
            document.body.appendChild(alertContainer);
          }
        });
      }

      this.isFullscreen = !this.isFullscreen;
    },

    onFullscreenChange() {
      const fullscreenElement = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;
      this.isFullscreen = !!fullscreenElement;
      
      if (this.isFullscreen) {
        this.$refs.displayContainer.classList.add('fullscreen-mode');
        this.isButtonVisible = true;
        setTimeout(() => {
          this.isButtonVisible = false;
        }, 3000);
        this.$nextTick(() => {
          const alertContainer = document.querySelector('.alert-container');
          if (alertContainer) {
            alertContainer.classList.toggle('fullscreen-mode', this.isFullscreen);
          }
        });
      } else {
        this.$refs.displayContainer.classList.remove('fullscreen-mode');
        this.isButtonVisible = true;
      }
    },

    async setLoopMode(mode) {
      this.loopMode = mode;
      this.stopLooping(); // 先停止当前的循环

      console.log('开始重置所有设置');
      //await this.resetAll(); // 等待重置完成
      console.log('重置完成，继续执行后续操作');

      if (mode === 'once') {
        // this.isSignatureVisible = false; // 移除这行
        console.log('Starting once loop after delay');
        this.fetchOnceSignatures();
      } else if (mode === 'unlimited') {
        console.log('Starting unlimited loop');
        if (this.signatures.length < 1) {
          this.fetchSignatures();
        }
        this.startLooping();
      } else if (mode === 'stop') {
        this.stopLooping();
        this.isButtonVisible = false;
        this.currentIndex = -1;
      }

      this.startCountdown();
    },

    startLooping() {
      this.isLooping = true;
      this.loopCount = 0; 
      this.rotateSignatures(); // 直接调用rotateSignatures
    },

    stopLooping() {
      this.isLooping = false;
      this.loopCount = 0;
      clearTimeout(this.rotateTimeoutId);
      clearInterval(this.countdownIntervalId);
    },

    triggerFlyIn() {
      this.isFlyingIn = true;
      
      setTimeout(() => {
        this.isFlyingIn = false;
      }, 1700); // 与飞入动画持续时间一致
    },

    triggerFlyOut() {
      this.isFlyingOut = true;
      
      setTimeout(() => {
        this.isFlyingOut = false;
        console.log('飞出动画结束');
        console.log('当前索引:', this.currentIndex);

        // 检查是否还有未显示的签名
        const nextUnshownIndex = this.signatures.findIndex((signature, index) => {
          return index > this.currentIndex && signature.attendance?.display_at === null;
        });

        if (nextUnshownIndex !== -1) {
          // 如果找到了下一个未显示的签名，将索引设置为该签名的索引
          console.log('找到下一个未显示的签名，索引为:', nextUnshownIndex);
          this.currentIndex = nextUnshownIndex;
        } else {
          // 如果所有签名都已显示，检查是否需要重新开始
          const firstUnshownIndex = this.signatures.findIndex(signature => signature.attendance?.display_at === null);
          if (firstUnshownIndex !== -1) {
            console.log('所有后续签名都已显示，重新开始，新索引为:', firstUnshownIndex);
            this.currentIndex = firstUnshownIndex;
          } else {
            console.log('所有签名都已显示，设置索引为 -1');
            this.currentIndex = -1;
          }
        }

        this.newSignatureDetected = false;
        
        if (this.loopMode === 'unlimited') {
          this.startLooping();
        } else if (this.loopMode === 'once' && this.currentIndex !== -1) {
          // 如果是 'once' 模式且还有未显示的签名，继续循环
          this.scheduleNextRotation();
        } else {
          this.stopLooping();
        }
      }, 1700);
    },

    handleMouseMove() {
      if (this.isFullscreen) {
        this.isButtonVisible = true;
        clearTimeout(this.buttonHideTimeout);
        this.buttonHideTimeout = setTimeout(() => {
          this.isButtonVisible = false;
        }, 3000);
      }
    },

    showAlert(type, message) {
      const id = Date.now();
      this.alerts.push({ id, type, message });
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

    async postSignatureUpdateAt(index) {
      try {
        // 获取当前签名的ID
        const currentSignatureCode = this.signatures[index]?.attendance?.code;
        
        if (!currentSignatureCode) {
          return;
        }

        if (this.signatures[index]?.attendance?.display_at) {
          return;
        }
        
        // 构建API URL
        const apiUrl = passport_url('shared/public/attendances/' + currentSignatureCode + '/update-displaytime');

        // 发送POST请求
        const response = await axios.post(apiUrl);
        console.log('更新签名显示时间成功:', response.data.status);

        if (response.data.status === 'success') {
          if (this.signatures[index]?.attendance) {
            this.signatures[index].attendance.display_at = new Date().toISOString();
          }
          return;
        }
        return;

      } catch (error) {
        // 处理错误
        this.showAlert('error', '更新显示时间失败: ' + (error.response?.data?.message || error.message));
        console.error('更新签名显示时间失败:', error);

        return;
      }
    },

    async resetAll() {
        // 停止所有循环和定时器
        this.stopLooping();
        clearInterval(this.fetchIntervalId);
        clearTimeout(this.rotateTimeoutId);
        clearInterval(this.countdownIntervalId);
        clearInterval(this.checkLoopLastID);
  
        // 重置倒计时
        this.countdownTime = this.displayInterval / 1000;
  
        // 重置索引和签名状态
        this.currentIndex = 0;
        this.newSignatureDetected = false;
        this.isSignatureVisible = false;
  
        // 重置动画状态
        this.isFlyingIn = false;
        this.isFlyingOut = false;
  
        // 重新开始定期检查新签名
        this.fetchIntervalId = setInterval(this.fetchNewSignatures, this.interval);
  
        // 重新开始倒计时
        this.startCountdown();
  
        // 清除所有警告
        this.alerts = [];
  
        console.log('所有设置已重置');
  
        return new Promise(resolve => setTimeout(resolve, 100));
      },
  },
};
</script>

<style scoped>
.fullscreen-mode .whiteboard {
  background-size: contain !important;
  background-position: center center !important;
  background-repeat: no-repeat !important;
  width: 100vw !important;
  height: 100vh !important;
  display: flex;
  flex-direction: column;
  justify-content: center; /* 确保内容垂直居中 */
  align-items: center; /* 确保内容水平居中 */
  position: relative; /* 添加这行 */
  background-color: #000; /* 或其他适合的颜色 */
  background-size: cover !important;
}

.fullscreen-mode .signature-image {
  max-width: 90vw;
  max-height: 90vh;
  width: auto;
  height: auto;
  position: absolute; /* 添加这行 */
  top: 50%; /* 添加这行 */
  left: 50%; /* 添加这行 */
  transform: translate(-50%, -50%); /* 添加这行 */
}

.fullscreen-mode .mode-switch,
.fullscreen-mode .bg-white {
  display: none; /* 在全屏模式下隐藏这些元素 */
}

.fullscreen-mode .absolute {
  position: static !important; /* 防止绝对定位影响布局 */
}

@media screen and (display-mode: fullscreen) {
  .whiteboard {
    background-size: contain;
    background-position: center center;
    background-repeat: no-repeat;
    width: 100vw;
    height: 100vh;
  }
}

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
  transition: background-color 0.5s ease;
}

.signature-image {
  display: block;
  margin: 0 auto;
  width: 90%;
  height: 90%;
  object-fit: contain;
  object-position: center;
  color: rgba(v-bind(signPenRgba), 1); /* 使用动态绑定的颜色 */
}

.signature-background {
  position: absolute;
  width: 15%;
  animation: matrix-fall linear infinite;
}

.signature-container {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
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
    opacity: 0.5;
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
  transition: opacity 1.2s, transform 0.8s;
}

.fly-enter-to, .fly-enter {
  animation: fly-in 1.7s ease-out forwards;
}

.fly-leave-to {
  animation: fly-out 1.7s ease-in forwards;
}

@keyframes fly-in {
  0% {
    transform: scale(0) translate3d(0, -1500px, 0);
    opacity: 0;
    background: rgba(255, 255, 255, 0.3);
    box-shadow: 0 0 100px 100px rgba(255, 255, 255, 0.3);
    border-radius: 50%;
  }
  85%{
    color: rgba(255, 255, 255, 0.8);
    text-shadow: 0 0 5px rgba(255, 255, 255, 0.8);
    transform: scale(0.85) translate3d(0, 0, 0);
    border-radius: 80%;
  }
  100% {
    transform: scale(1) translate3d(0, 0, 0);
    opacity: 1;
    
    background: transparent;
    box-shadow: 0 0 50px 50px rgba(255, 255, 255, 0.3);
  }
}

@keyframes fly-out {
  0% {
    color: rgba(255, 255, 255, 0.8);
    text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
    transform: scale(1) translate3d(0, 0, 0);
    background: transparent;
    box-shadow: none;
  }
  100% {
    transform: scale(0) translate3d(0, 1500px, 0);
    background: rgba(255, 255, 255, 0.3);
    box-shadow: 0 0 100px 100px rgba(255, 255, 255, 0.2);
    border-radius: 50%;
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
  transition: background-color 0.3s;
}

.mode-switch button.active {
  @apply text-primary bg-white;
}

.mode-switch button:hover {
  @apply text-primary bg-white;
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
  background-color: rgba(0, 0, 0, 0.7); /* 半透明背景 */
  color: white;
  padding: 10px 20px;
  border: none;
  cursor: pointer;
  border-radius: 5px;
  transition: background-color 0.3s, opacity 0.3s;
  font-size: 0.8rem;
  opacity: 0.8;
  @apply font-bold text-lg md:text-2xl;
  transition: opacity 0.3s ease-in-out;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.3s, visibility 0.3s;
}

.fullscreen-button.fullscreenvisible {
  opacity: 1 !important;
  visibility: visible;
}

.fullscreen-mode .fullscreen-button {
  opacity: 0;
}

.fullscreen-mode .fullscreen-button:hover,
.fullscreen-mode .fullscreen-button:focus {
  opacity: 1;
}

.fullscreen-button:hover {
  background-color: rgba(247, 126, 13, 1);
  opacity: 1;
}

.fullscreen-mode .whiteboard {
  background-size: contain !important;
  background-position: center center !important;
  background-repeat: no-repeat !important;
  width: 100vw !important;
  height: 100vh !important;
  transition: background-image 0.3s ease-in-out;
}

.fullscreen-mode .signature-container {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 90%;
  height: 90%;
}

.fullscreen-mode .signature-image {
  max-width: 100%;
  max-height: 100%;
  width: auto;
  height: auto;
}

.fullscreen-mode .mode-switch,
.fullscreen-mode .bg-white {
  display: none;
}

/* 调整全屏模式下的飞入飞出动画 */
@keyframes fullscreen-fly-in {
  0% {
    transform: scale(0) translate(-50%, -50%);
    opacity: 0;
    background: rgba(255, 255, 255, 0.3);
    box-shadow: 0 0 100px 100px rgba(255, 255, 255, 0.3);
    border-radius: 50%;
  }
  80% {
    transform: scale(1) translate(-50%, -50%);
    opacity: 1;
    color: rgba(255, 255, 255, 0.8);
    text-shadow: 0 0 5px rgba(255, 255, 255, 0.8);
    border-radius: 80%;
  }
  100% {
    transform: scale(1) translate(-50%, -50%);
    opacity: 1;
    background: transparent;
    box-shadow: 0 0 50px 50px rgba(255, 255, 255, 0.3);
  }
}

@keyframes fullscreen-fly-out {
  0% {
    transform: scale(1) translate(-30%, -50%);
    opacity: 1;
    filter: blur(0px);
    background: transparent;
    box-shadow: none;
    color: rgba(255, 255, 255, 0.8);
    text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
  }
  50% {
    transform: scale(0.5) translate(-60%, 80%);
    opacity: 0.7;
    filter: blur(2px);
    background: rgba(255, 255, 255, 0.1);
    box-shadow: 0 0 50px 50px rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.6);
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
  }
  100% {
    transform: scale(0) translate(-100%, 150%);
    opacity: 0;
    filter: blur(5px);
    background: rgba(255, 255, 255, 0.3);
    box-shadow: 0 0 100px 100px rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    color: transparent;
    text-shadow: none;
  }
}

.fullscreen-mode .fly-enter-active,
.fullscreen-mode .fly-leave-active {
  position: absolute;
  top: 50%;
  left: 50%;
  transform-origin: center;
}

.fullscreen-mode .fly-enter-to,
.fullscreen-mode .fly-enter {
  animation: fullscreen-fly-in 1.7s ease-out forwards;
}

.fullscreen-mode .fly-leave-to {

  animation: fullscreen-fly-out 1.7s ease-in forwards;
}

.loop-button {
  background-color: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 10px 20px;
  border: none;
  cursor: pointer;
  border-radius: 5px;
  transition: background-color 0.3s, opacity 0.3s;
  font-size: 0.8rem;
  opacity: 0.8;
  position: absolute;
  bottom: 15px;
  right: 10px;
  z-index: 1000;
}

.loop-button:hover {
  background-color: rgba(247, 126, 13, 1);
  opacity: 1;
}

.flying-in {
  animation: fly-in 1.7s ease-out forwards;
}

.flying-out {
}

.alert-container {
  position: fixed;
  bottom: 20px;
  left: 20px;
  z-index: 10000; /* 增加 z-index 确保它在其他元素之上 */
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  pointer-events: none; /* 允许点击穿透 */
}

.fullscreen-mode .alert-container {
  position: absolute;
  bottom: 70px;
  left: 20px;
  z-index: 10001;
}

.fullscreen-mode .alert {
  background-color: rgba(0, 0, 0, 0.7);
  color: white;
}

.alert {
  pointer-events: auto; /* 恢复警告框的点击事件 */
  margin-top: 10px;
  padding: 15px 20px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  max-width: 400px;
  background-color: rgba(255, 255, 255, 0.9); /* 半透明背景 */
}

.alert-success {
  border-left: 4px solid #28a745;
}

.alert-warning {
  border-left: 4px solid #ffc107;
}

.alert-error {
  border-left: 4px solid #dc3545;
}

.alert-info {
  border-left: 4px solid #17a2b8;
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

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s, transform 0.5s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
  transform: translateY(30px);
}
</style>