<template>
  <div class="lucky-draw rounded-xl shadow-xl" ref="luckydraw">
    <Fireworks ref="fireworks" class="fireworks" :options="fireworksOptions" v-if="fireworksEnabled" />
    <div v-show="isLoading">Loading...</div>
    <div class="border-0" :class="['flex', isFullScreen ? 'flex-row' : 'flex-col']">
      <div :class="['prize-info', isFullScreen ? 'w-1/2 items-end justify-center flex flex-col ' : 'w-full']">
        
        <template v-if="isFullScreen">
          <canvas style="position: fixed; top: 0px; left: 0px; pointer-events: none; z-index: 10000000;" width="2540" height="1253"></canvas>
          <div class="flex flex-col bg-primary rounded-xl  w-2/3">
            <div class="flex flex-col my-4 text-white font-bold text-4xl text-center items-center justify-center p-4 uppercase">
              <span v-text="prizeName"></span><span class="mx-4">-</span><span v-text="prizeItem"></span>
            </div>
            <div class="min-h-24 bg-white">
              <template v-if="prizeImageUrl">
                <img :src="prizeImageUrl" alt="Prize Image" class="w-full h-auto rounded-xl" v-if="prizeImageUrl"/>
              </template> 
            </div>
            <div class="winner !text-white" v-if="showWinneBanner && isFullScreen">
              Winner: <span v-if="participants[currentIndex]">{{ participants[currentIndex].participant.name }} ( {{ participants[currentIndex].participant.mobile }})</span>
            </div>
            <div class="flex flex-row justify-center mb-4 mt-2" v-if="ableAction">
              <button @click="toggleFullScreen" class="fullscreen-button">
                {{ isFullScreen ? 'Exit Full Screen' : 'Full Screen' }}
              </button>
              <button @click="confirmWinner" :disabled="!isFinished" v-if="winner">Confirm</button>
              <button @click="redraw" :disabled="isDrawing" v-if="winner">Redraw</button>
              <button @click="startDraw" :disabled="isDrawing || prizeUniqueCode == null" v-if="!winner">{{ buttonText }}</button>
              <button @click="toggleMute" class="mute-button">{{ isMuted ? 'Sound On' : 'Sound Off' }}</button>
            </div>
          </div>
        </template> 
        <template v-else>
          <div class="flex my-4 text-primary font-bold text-4xl text-center items-center justify-center">
            <span v-text="prizeName"></span><span class="mx-4">-</span><span v-text="prizeItem"></span>
          </div>
        </template>
        <div v-if="!this.participants || this.participants.length === 0" class="text-danger-500 text-3xl">Unable to draw wheel: participants not available</div>
      </div>
      <div :class="['wheel-container', isFullScreen ? 'w-2/3' : 'w-full']">
        <canvas v-show="!isLoading" ref="wheelCanvas" @click="handleCanvasClick"></canvas>
      </div>
    </div>

    <div class="winner" v-if="showWinneBanner && !isFullScreen">
      Winner: <span v-if="participants[currentIndex]">{{ participants[currentIndex].participant.name }} ( {{ participants[currentIndex].participant.mobile }})</span>
    </div>
    <div class="flex flex-row justify-center mb-4 mt-2" v-if="ableAction && !isFullScreen">
      <button @click="toggleFullScreen" class="fullscreen-button">
        {{ isFullScreen ? 'Exit Full Screen' : 'Full Screen' }}
      </button>
      <button @click="confirmWinner" :disabled="!isFinished" v-if="winner">
        {{ isWinnerConfimed ? 'Confirmed' : 'Confirm?' }}</button>
      <button @click="redraw" :disabled="isDrawing" v-if="winner">Redraw</button>
      <button @click="startDraw" :disabled="isDrawing || prizeUniqueCode == null" v-if="!winner">{{ buttonText }}</button>
      <button @click="toggleMute" class="mute-button">{{ isMuted ? 'Sound On' : 'Sound Off' }}</button>
    </div>
    <audio ref="startSound" src="/sounds/start-sound.mp3"></audio>
    <audio ref="tickSound" src="/sounds/Tick-DeepFrozenApps-397275646.mp3"></audio>
    <audio ref="endSound" src="/sounds/end-sound.mp3"></audio>

    <div v-if="showWinnerModal" class="modal" :class="{ 'fullscreen-modal': isFullScreen }">
      <div class="modal-content">
        <h2>Congratulations to the Winner!</h2>
        <div class="winner-name">{{ winner?.participant?.name }}</div>
        <div class="winner-number">{{ winner?.participant?.mobile }}</div>
        <button @click="closeWinnerModal">CLOSE</button>
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
import confetti from 'canvas-confetti';
import { Fireworks } from '@fireworks-js/vue'

export default {
  components: {
    Fireworks
  },
  props: {
    modelValue: {
      type: String,
      default: null
    },
    names: {
      type: Array,
      default: () => [],
    },
    prizeUniqueCode: {
      type: String,
      default: ''
    },
    ableAction: {
      type: Boolean,
      required: true
    },
    prizeName: {
      type: String,
      default: ''
    },
    prizeItem: {
      type: String,
      default: ''
    },
    prizeDesc: {
      type: String,
      default: ''
    },
    prizeImageUrl: {
      type: String,
      default: ''
    },
    slug: {
      type: String,
      required: true
    },
    backgroundUrl: {
      type: String,
      default: ''
    },
    backgroundColor: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      winner: null,
      isDrawing: false,
      isFinished: false,
      isMuted: false,
      message: '',
      participants: [
        {'participant': {'name': 'Iron Man', 'fullname': 'Iron Man'}},
        {'participant': {'name': 'Captain America', 'fullname': 'Captain America'}},
        {'participant': {'name': 'Thor', 'fullname': 'Thor'}},
        {'participant': {'name': 'Black Widow', 'fullname': 'Black Widow'}},
        {'participant': {'name': 'Hulk', 'fullname': 'Hulk'}},
        {'participant': {'name': 'Hawkeye', 'fullname': 'Hawkeye'}},
        {'participant': {'name': 'Scarlet Witch', 'fullname': 'Scarlet Witch'}},
        {'participant': {'name': 'Spider-Man', 'fullname': 'Spider-Man'}},
        {'participant': {'name': 'Doctor Strange', 'fullname': 'Doctor Strange'}},
        {'participant': {'name': 'Black Panther', 'fullname': 'Black Panther'}},
        {'participant': {'name': 'Captain Marvel', 'fullname': 'Captain Marvel'}},
      ],
      wheel: null,
      startAngle: 0,
      arc: 0,
      spinTimeout: null,
      spinArcStart: 10,
      spinTime: 0,
      spinTimeTotal: 0,
      ctx: null,
      isLoading: false,
      currentIndex: 0,
      showWinnerModal: false,
      isFullScreen: false,
      showWinneBanner:false,
      alerts: [],
      isLargeWheel: false,
      currentParticipant: null,
      audioFiles: {
        start: null,
        tick: null,
        end: null
      },
      showConfetti: false,
      isWinnerConfimed: false,
      calculatedFontSize: 15,
      fireworksEnabled: false,
      fireworksOptions: {
      },
    }
  },
  computed: {
    buttonText() {
      return this.isDrawing ? 'Processing...' : (this.isFinished ? 'Restart' : 'Draw Now');
    }
  },
  mounted() {
    this.preloadAudioFiles();
    this.isLargeWheel = true; // 在组件挂载时设置为大尺寸
    this.tryInitWheel();
    this.getAttendances().catch(error => {
      console.error('Failed to get attendances:', error);
      this.message = "Failed to load attendances. Please try again.";
    }).finally(() => {
      this.$nextTick(() => {
        this.resizeWheel();
        this.drawWheel();
      });
    });
    document.addEventListener('fullscreenchange', this.handleFullscreenChange);
    document.addEventListener('webkitfullscreenchange', this.handleFullscreenChange);
    document.addEventListener('mozfullscreenchange', this.handleFullscreenChange);
    document.addEventListener('MSFullscreenChange', this.handleFullscreenChange);
    window.addEventListener('resize', this.resizeWheel);
    this.resizeWheel(); // 初始调整大小
    this.calculateFontSize();
    
  },
  beforeUnmount() {
    document.removeEventListener('fullscreenchange', this.handleFullscreenChange);
    document.removeEventListener('webkitfullscreenchange', this.handleFullscreenChange);
    document.removeEventListener('mozfullscreenchange', this.handleFullscreenChange);
    document.removeEventListener('MSFullscreenChange', this.handleFullscreenChange);
    window.removeEventListener('resize', this.resizeWheel);

    this.initFireworks();
  },
  updated() {
    if (!this.ctx && this.$refs.wheelCanvas) {
      this.initWheel();
    }
  },
  watch: {
    prizeUniqueCode: {
      handler(newValue, oldValue) {
        if (newValue && newValue !== oldValue) {
          this.resetWheel();
          this.getParticipants().catch(error => {
            console.error('Failed to get participants:', error);
            this.message = "Failed to load participants. Please try again.";
          });

          this.isWinnerConfimed = false;

          if (this.modelValue) {
          }
        }
      },
      immediate: true
    },
    currentParticipant: {
      handler(newValue, oldValue) {
        if (newValue && newValue !== oldValue) {
          //when is change. then i need a sound.
          if (!this.isMuted && this.$refs.tickSound) {
            this.$refs.tickSound.play();
          }
        }
      },
      immediate: true
    },
    participants: {
      handler() {
        this.$nextTick(() => {
          this.calculateFontSize();
          this.drawWheel();
        });
      },
      deep: true
    },
    showWinnerModal(newValue) {
    }
  },
  methods: {
    resetWheel() {
      this.winner = null;
      this.isDrawing = false;
      this.isFinished = false;
      this.participants = [];
      this.startAngle = 0;
      this.isLoading = true;
      if (this.ctx) {
        this.ctx.clearRect(0, 0, this.$refs.wheelCanvas.width, this.$refs.wheelCanvas.height);
      }
    },
    async getAttendances() {
      this.showWinneBanner = false
      this.isLoading = true;

      try {
        const response = await axios.get(passport_url('shared/public/events/' + this.slug + '/attendances'));
        this.showAlert('success', 'name list updated');
        this.participants = response.data.data;
        this.$nextTick(() => {
          this.drawWheel();
        });
      } catch (error) {
        this.message = "获取参与者列表失败";
        console.error('Failed to get attendances list:', error);
        this.showAlert('error', this.message);
        throw error;
      } finally {
        this.isLoading = false;
      }
    },
    getParticipants() {
      if (!this.prizeUniqueCode) {
        this.message = "Please select a prize to draw";
        this.isLoading = false;
        return Promise.reject("No prize selected");
      }
      this.showWinneBanner = false
      this.isLoading = true;
      return axios.get(passport_url('shared/public/prizes/' + this.prizeUniqueCode + '/name-list'))
        .then(response => {
          this.showAlert('success', 'NameList Updated!');
          this.participants = response.data.meta.attendances;
          this.message = response.data.message;
          this.$nextTick(() => {
            this.drawWheel();
          });
          this.isLoading = false;
        })
        .catch(error => {
          console.error('Failed to get participants list:', error);
          this.message = "Failed to get participants list";
          this.showAlert('error', this.message);
          this.isLoading = false;
          throw error;
        });
    },
    tryInitWheel() {
      if (this.$refs.wheelCanvas) {
        this.initWheel();
      } else {
        console.warn('Canvas not ready, retrying in 100ms');
        setTimeout(() => this.tryInitWheel(), 100);
      }
    },
    initWheel() {
      const canvas = this.$refs.wheelCanvas;
      if (canvas) {
        this.ctx = canvas.getContext("2d");
      } else {
        console.warn('Canvas element not found. Wheel initialization failed.');
      }
    },
    drawWheel() {
      
      if (!this.ctx) {
        console.warn('Unable to draw wheel: context not available');
        return;
      }

      if (!this.participants || this.participants.length === 0) { 
        console.warn('Unable to draw wheel: participants not available');
        return;
      } 

      const ctx = this.ctx;
      const canvas = ctx.canvas;
      const centerX = canvas.width / 2;
      const centerY = canvas.height / 2;
      const radius = Math.min(centerX, centerY) * 0.8;
      
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      
      this.arc = Math.PI * 2 / this.participants.length;

      for(let i = 0; i < this.participants.length; i++) {
        const angle = this.startAngle + i * this.arc;
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, angle, angle + this.arc);
        ctx.lineTo(centerX, centerY);
        ctx.fillStyle = this.getColor(i);
        ctx.fill();
        
        ctx.save();
        ctx.translate(centerX, centerY);
        ctx.rotate(angle + this.arc / 2);
        ctx.textAlign = "right";
        ctx.fillStyle = "#fff";
        
        ctx.font = `bold ${this.calculatedFontSize}px sans-serif`;
        
        // 计算文本位置,确保不会太靠近边缘
        const textRadius = radius * 0.95;
        
        // 获取参与者名字
        const name = this.participants[i].participant.fullname;
        let  displayName = name;
        // 如果名字太长,进行截断
        const maxLength = this.calculateAverageNameLength();
        try {
          if (name.length > maxLength) {
            displayName = name.length > maxLength ? name.slice(0, maxLength - 1) + '..' : name;
          }
        } catch (error) {
          console.error('Failed to get participants:', this.participants[i].participant.name);
          console.error('Failed to get participants:', error);
          this.message = "Failed to load participants. Please try again.";
        }
        
        
        ctx.fillText(displayName, textRadius, 0);
        ctx.restore();
      }

      this.drawArrow(ctx, centerX, centerY, radius);
    },
    resizeWheel() {
      if (this.participants.length === 0) {
        return;
      }

      if (this.$refs.wheelCanvas && this.$refs.luckydraw) {
        const canvas = this.$refs.wheelCanvas;
        const container = this.isFullScreen ? this.$refs.luckydraw.querySelector('.wheel-container') : this.$refs.luckydraw;
        const containerWidth = container.clientWidth;
        const containerHeight = container.clientHeight;

        const aspectRatio = 1;
        let newWidth = this.isFullScreen ? containerWidth * 0.9 : containerWidth * 0.9;
        let newHeight = newWidth / aspectRatio;

        if (newHeight > containerHeight * 0.9) {
          newHeight = containerHeight * 0.9;
          newWidth = newHeight * aspectRatio;
        }

        canvas.width = newWidth;
        canvas.height = newHeight;

        this.calculateFontSize();
        this.drawWheel();
      }

      // 调整 confetti canvas 的大小
      const confettiCanvas = document.querySelector('.confetti-canvas');
      if (confettiCanvas) {
        confettiCanvas.width = window.innerWidth;
        confettiCanvas.height = window.innerHeight;
      }
    },
    drawArrow(ctx, centerX, centerY, radius) {
      const arrowSize = radius * 0.15;
      const arrowAngle = 0;

      ctx.save();
      ctx.translate(centerX, centerY);
      ctx.rotate(arrowAngle);

      ctx.beginPath();
      ctx.moveTo(radius, 0);
      ctx.lineTo(radius + arrowSize, -arrowSize / 2);
      ctx.lineTo(radius + arrowSize, arrowSize / 2);
      ctx.closePath();

      ctx.fillStyle = 'white';
      ctx.strokeStyle = 'black';
      ctx.lineWidth = 2;

      ctx.fill();
      ctx.stroke();

      const degrees = this.startAngle * 180 / Math.PI;
      const arcd = 360 / this.participants.length;
      this.currentIndex = Math.floor((360 - degrees % 360) / arcd) % this.participants.length;

      ctx.restore();
    },

    getColor(index) {
      const hue = (index * 137.508) % 360; // 使用黄金角来分布色相
      return `hsl(${hue}, 70%, 60%)`; // 使用 HSL 颜色模型
    },

    startDraw() {


      if (!this.prizeUniqueCode) {
        alert('Please select a prize to draw');
        return;
      }

      if (this.isDrawing) return;
      this.isDrawing = true;
      this.isFinished = false;
      this.winner = null;
      this.showWinneBanner = false;
      
      ['start', 'tick', 'end'].forEach(sound => {
        this.stopSound(sound);
      });
      
      this.playSound('start');
      
      if (!this.isMuted && this.$refs.startSound) {
        this.$refs.startSound.play();
      }
      
      this.spinAngleStart =  80;
      this.spinTime = 2;
      this.spinTimeTotal = 4 * 1000;

      this.rotateWheel();
    },
    getCurrentParticipant() {
      const degrees = this.startAngle * 180 / Math.PI;
      const arcd = 360 / this.participants.length;
      const index = Math.floor((360 - degrees % 360) / arcd) % this.participants.length;
      return this.participants[index].participant.name;
    },

    rotateWheel() {
      
      this.spinTime += 12;
      if(this.spinTime >= this.spinTimeTotal) {
        this.$refs.tickSound.pause();
        this.stopRotateWheel();
        return;
      }
      
      const progress = this.spinTime / this.spinTimeTotal;
      const easeProgress = this.easeOut(progress, 0, 1, 1);
      
      const spinAngle = this.spinAngleStart * (1 - easeProgress);
      this.startAngle += (spinAngle * Math.PI / 180);
      
      this.drawWheel();

      this.currentParticipant = this.getCurrentParticipant();

      this.spinTimeout = setTimeout(() => this.rotateWheel(), 16);
    },
    stopRotateWheel() {
      clearTimeout(this.spinTimeout);

      const degrees = this.startAngle * 180 / Math.PI;
      const arcd = 360 / this.participants.length;
      const index = Math.floor((360 - degrees % 360) / arcd) % this.participants.length;
      this.winner = this.participants[index];

      this.isDrawing = false;
      this.isFinished = true;

      this.stopSound('tick');
      this.playSound('end');

      this.$emit('draw-complete', this.winner);

      this.showWinnerModal = true;
      this.showWinneBanner = true;

      // 发送获奖者信息到API
      this.sendWinnerToApi(this.winner);

      this.launchFireworks();
    },
    easeOut(t, b, c, d) {
      t /= d;
      return c * (1 - Math.pow(1 - t, 3)) + b;
    },
    confirmWinner() {
      this.isLoading = true;

      axios.get(passport_url('shared/public/prizes/' + this.prizeUniqueCode + '/confirm/' + this.winner.id))
        .then(response => {
          this.showAlert('success', 'Winner Had been confirm.');
          this.isLoading = false;
          this.isWinnerConfimed = true;
        })
        .catch(error => {
          console.error('Failed to get participants list:', error);
          this.message = "Failed to confirm winner";
          this.showAlert('error', this.message);
          this.isLoading = false;
          throw error;
        });
    },
    
    redraw() {
      this.isLoading = true;
      this.showAlert('success', 'Redraw Now');

      this.getParticipants().catch(error => {
        console.error('Failed to get participants:', error);
        this.message = "Failed to load participants. Please try again.";
      });

      axios.get(passport_url('shared/public/prizes/' + this.prizeUniqueCode + '/reject/' + this.winner.id))
        .then(response => {
          this.$nextTick(() => {
            this.drawWheel();
          });
          this.isLoading = false;
          this.isWinnerConfimed = false
        })
        .catch(error => {
          console.error('Failed to get participants list:', error);
          this.message = "Failed to get participants list";
          this.showAlert('error', this.message);
          this.isLoading = false;
          throw error;
        });
      this.showWinneBanner = false;
      this.winner = null;
    },
    closeWinnerModal() {
      this.showWinnerModal = false;
    },

    toggleMute() {
      this.isMuted = !this.isMuted;
    },
    toggleFullScreen() {
      if (!document.fullscreenElement) {
        if (this.$refs.luckydraw.requestFullscreen) {
          this.$refs.luckydraw.requestFullscreen();
        } else if (this.$refs.luckydraw.mozRequestFullScreen) {
          this.$refs.luckydraw.mozRequestFullScreen();
        } else if (this.$refs.luckydraw.webkitRequestFullscreen) {
          this.$refs.luckydraw.webkitRequestFullscreen();
        } else if (this.$refs.luckydraw.msRequestFullscreen) {
          this.$refs.luckydraw.msRequestFullscreen();
        }
      } else {
        if (document.exitFullscreen) {
          document.exitFullscreen();
        } else if (document.mozCancelFullScreen) {
          document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
          document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
          document.msExitFullscreen();
        }
      }
    },

    handleFullscreenChange() {
      this.isFullScreen = !!document.fullscreenElement;
      const luckydraw = this.$refs.luckydraw;

      if (this.isFullScreen) {
        document.body.classList.add('fullscreen-mode');
        // 设置背景
        if (this.backgroundUrl) {
          luckydraw.style.backgroundImage = `url(${this.backgroundUrl})`;
          luckydraw.style.backgroundSize = 'cover';
          luckydraw.style.backgroundPosition = 'center';
        } else if (this.backgroundColor) {
          luckydraw.style.backgroundColor = this.backgroundColor;
        }
      } else {
        document.body.classList.remove('fullscreen-mode');
        luckydraw.style.backgroundImage = '';
        luckydraw.style.backgroundColor = '';
      }

      // 使用requestAnimationFrame确保在下一帧执行，等待DOM更新完成
      requestAnimationFrame(() => {
        this.resizeWheel();
      });
    },

    sendWinnerToApi(winner) {
      return axios.get(passport_url('shared/public/prizes/' + this.prizeUniqueCode + '/submit/' + winner.id))
      .then(response => {
        // 触发更新奖品列表的事件
        this.$emit('winner-confirmed');

        // 触发一个全局事件
        window.dispatchEvent(new CustomEvent('winnerConfirmed'));
        this.showAlert('success', 'Winner successfully submitted!');
      })
      .catch(error => {
        console.error('Failed to send winner to API:', error);
        this.showAlert('error', 'Failed to submit winner. Please try again.');
      });
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

    toggleWheelSize() {
      this.isLargeWheel = !this.isLargeWheel;
      this.$nextTick(() => {
        this.resizeWheel();
      });
    },

    preloadAudioFiles() {
      const audioSources = {
        start: '/sounds/start-sound.mp3',
        tick: '/sounds/Tick-DeepFrozenApps-397275646.mp3',
        end: '/sounds/end-sound.mp3'
      };

      for (const [key, src] of Object.entries(audioSources)) {
        this.audioFiles[key] = new Audio(src);
        this.audioFiles[key].load();
      }
    },

    // 更新播放音频的方法
    playSound(type) {
      if (this.audioFiles[type] && !this.isMuted) {
        this.audioFiles[type].play();
      }
    },

    stopSound(type) {
      if (this.audioFiles[type]) {
        this.audioFiles[type].pause();
        this.audioFiles[type].currentTime = 0;
      }
    },

    calculateFontSize() {
      const averageNameLength = this.calculateAverageNameLength();
      const textRadius = Math.min(this.$refs.wheelCanvas.width, this.$refs.wheelCanvas.height) * 0.8 * 0.85;
      const arcLength = 2 * Math.PI * textRadius / this.participants.length;
      
      let fontSize = Math.floor(arcLength / averageNameLength);

      // 根据参与者数量动态调整最小字体大小
      let minFontSize;
      let maxFontSize = 120;
      
      if (this.participants.length <= 10) {
        minFontSize = 25;  // 10人或更少时，保持较大的字体
        maxFontSize = 40;
      } else if (this.participants.length <= 30) {
        // 在10-30人之间，字体大小从30线性减小到15
        minFontSize = 30 - (this.participants.length - 10) * (15 / 20);
      } else {
        minFontSize = 15;  // 30人以上，使用较小的最小字体大小
      }
      
      this.calculatedFontSize = Math.max(minFontSize, Math.min(maxFontSize, fontSize));

    },

    calculateAverageNameLength() {
      let totalLength = 0;
      const participantCount = this.participants.length;

      // 计算所有参与者名字的总长度
      for (let participant of this.participants) {
        totalLength += participant.participant.name.length;
      }

      // 计算实际的平均名字长度
      let actualAverageLength = totalLength / participantCount;

      // 根据参与者数量调整平均长度
      if (participantCount < 6) {
        // 当参与者少于5人时，将平均长度调整为10
        return Math.min(10, actualAverageLength);
      } else if (participantCount <= 10) {
        // 当参与者在5到10人之间时，将平均长度线性调整从10到15
        const adjustmentFactor = (participantCount - 5) / 5;
        return 10 + (5 * adjustmentFactor);
      } else if (participantCount <= 30) {
        // 当参与者在11到30人之间时，保持15的平均长度
        return 15;
      } else {
        // 当参与者超过30人时，使用实际的平均长度，但不小于15
        return Math.max(15, actualAverageLength);
      }
    },

    handleCanvasClick() {
      if (this.winner) {
        this.redraw();
      } else {
        this.startDraw();
      }
    },

    initFireworks() {
      console.log('Initializing fireworks');
      this.fireworksOptions = {
        autoresize: false,
        opacity: 0.5,
        acceleration: 1.09,
        friction: 0.97,
        gravity: 3.5,
        particles: 200,
        traceLength: 3,
        traceSpeed: 10,
        explosion: 15,
        intensity: 50,
        flickering: 50,
        lineStyle: 'round',
        hue: {
          min: 20,
          max: 360
        },
        delay: {
          min: 15,
          max: 30
        },
        rocketsPoint: {
          min: 30,
          max: 70
        },
        lineWidth: {
          explosion: {
            min: 1,
            max: 3
          },
          trace: {
            min: 1,
            max: 2
          }
        },
        brightness: {
          min: 50,
          max: 80
        },
        decay: {
          min: 0.015,
          max: 0.03
        },
        mouse: {
          click: false,
          move: false,
          max: 1
        },
        sound: {
          enabled: true,
        }
      };
    },

    launchFireworks() {
      console.log('Launching fireworks');
      this.fireworksEnabled = true;
      
      // 5秒后停止烟花
      setTimeout(() => {
        this.stopFireworks();
      }, 10000);
    },

    stopFireworks() {
      this.fireworksEnabled = false;
    },

  },
}
</script>

<style scoped>
.lucky-draw {
  text-align: center;
  overflow: visible !important;
  position: relative;
}

.winner {
  font-size: 24px;
  color: #ff0000;
  margin: 20px 0;
}

button {
  font-size: 18px;
  padding: 10px 20px;
  cursor: pointer;
  margin: 5px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 5px;
  transition: background-color 0.3s;
  width: 11rem;
  text-transform: uppercase;
  height: 3rem;
  box-shadow: 0.4125rem 0.4125rem 0 rgba(0, 0, 0, .2);
}
button:hover {
  background-color: #45a049;
}
button:disabled {
  cursor: not-allowed;
  background-color: #cccccc;
}
.mute-button {
  font-size: 14px;
  padding: 5px 10px;
  background-color: #f44336;
}
.mute-button:hover {
  background-color: #da190b;
}
canvas {
  border: 0px solid #000;
  margin: 0 auto;
  display: block;
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  width: 90vw;
  height: 90vw;
  max-width: 90vh;
  max-height: 90vh;
}
.modal {
  position: fixed;
  z-index: 9999;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.5); /* Semi-transparent background */
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background-color: #fefefe;
  padding: 20px;
  border: 1px solid #888;
  width: 90%;
  max-width: 400px; /* Limit the maximum width */
  max-height: 80vh; /* Limit the maximum height */
  text-align: center;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Adjust font sizes for fullscreen mode */
.fullscreen-modal .modal-content h2 {
  font-size: 24px;
}

.fullscreen-modal .modal-content .winner-name {
  font-size: 20px;
}

.fullscreen-modal .modal-content .winner-number {
  font-size: 18px;
}

.fullscreen-modal .modal-content button {
  font-size: 16px;
  padding: 10px 20px;
}

.modal-content h2 {
  color: #4CAF50;
  margin-bottom: 20px;
}
.modal-content button {
  background-color: #4CAF50;
  color: white;
  padding: 15px 30px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 18px;
  transition: background-color 0.3s;
}
.modal-content button:hover {
  background-color: #45a049;
}
.fullscreen-button:hover {
  background-color: #007B9A;
}
.fullscreen-button {
  @apply w-auto;
  position: absolute;
  top: 10px;
  right: 10px;
  z-index: 1000;
  background-color: #008CBA;
}
:fullscreen .lucky-draw,
:-webkit-full-screen .lucky-draw,
:-moz-full-screen .lucky-draw {
  width: 100vw;
  height: 100vh;
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  padding: 20px;
  box-sizing: border-box;
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  overflow: visible !important; 
}

:fullscreen .prize-info,
:-webkit-full-screen .prize-info,
:-moz-full-screen .prize-info {
  display: flex;
  flex-direction: column;
  justify-content: center;
  height: 100%;
}

:fullscreen .wheel-container,
:-webkit-full-screen .wheel-container,
:-moz-full-screen .wheel-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
}

:fullscreen .lucky-draw canvas.confetti-canvas,
:-webkit-full-screen .lucky-draw canvas.confetti-canvas,
:-moz-full-screen .lucky-draw canvas.confetti-canvas,
:-ms-fullscreen .lucky-draw canvas.confetti-canvas {
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  z-index: 10000 !important;
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
.size-toggle-button {
  position: absolute;
  top: 10px;
  left: 10px;
  z-index: 1000;
  background-color: #4CAF50;
}

:fullscreen .lucky-draw,
:-webkit-full-screen .lucky-draw,
:-moz-full-screen .lucky-draw {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
}

/* 确保 canvas-confetti 的 canvas 元素在全屏模式下正确定位 */
:fullscreen canvas[style*="position: fixed"],
:-webkit-full-screen canvas[style*="position: fixed"],
:-moz-full-screen canvas[style*="position: fixed"] {
  top: 0 !important;
  left: 0 !important;
  width: 100vw !important;
  height: 100vh !important;
}

.fireworks {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  z-index: 10000; /* 确保烟花在最上层 */
}

:fullscreen .fireworks,
:-webkit-full-screen .fireworks,
:-moz-full-screen .fireworks {
  width: 100vw;
  height: 100vh;
}

</style>