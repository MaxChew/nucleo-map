<template>
  <div class="lucky-draw border-2 rounded-xl shadow-xl">
    <div v-show="isLoading">Loading...</div>
    <div class="flex my-4 text-primary font-bold text-4xl text-center items-center justify-center" v-if="prizeName">
      <span v-text="prizeName"></span><span class="mx-4">-</span><span v-text="prizeItem"></span>
    </div>
    <div class="flex my-4 text-primary font-bold text-4xl text-center items-center justify-center" v-else>
      <span class="mx-4">Please select a prize</span>
    </div>

    <canvas v-show="!isLoading" ref="wheelCanvas" width="800" height="600" @click="startDraw"></canvas>
    <div class="winner flex flex-row justify-center">
     
      <div class="border-2 border-red-700 flex flex-col w-1/3">
        <h3 class="font-semibold uppercase mt-2 mb-4 underline text-3xl">Winner</h3>
        <div class="font-bold text-4xl flex flex-col"  v-if="participants[currentIndex]">
          <span class="mr-2">{{ participants[currentIndex].participant.name }}</span>
          <span>{{ participants[currentIndex].participant.mobile }}</span>
        </div>
      </div>
    </div>
    <!--
    <div class="current-info">
      <div class="current-index">Current Index: <span>{{ currentIndex }}</span></div>
      <div v-if="participants[currentIndex]" class="current-name">
        Current Name: <span>{{ participants[currentIndex].participant.name }}</span>
      </div>
    </div>
   
    <div class="text-danger-500 font-bold">
      <span>{{ message }}</span>
    </div>
     -->
    <div class="flex flex-row justify-center mb-4 mt-2" v-if="ableAction">
      <button @click="confirmWinner" :disabled="!isFinished" v-if="winner">Confirm</button>
      <button @click="redraw" :disabled="isDrawing" v-if="winner">Redraw</button>
      <button @click="startDraw" :disabled="isDrawing || prizeUniqueCode == null" v-if="!winner">{{ buttonText }}</button>
      <button @click="toggleMute" class="mute-button">{{ isMuted ? 'Sound On' : 'Sound Off' }}</button>
    </div>
    <audio ref="startSound" src="/sounds/start-sound.mp3"></audio>
    <audio ref="tickSound" src="/sounds/tick-sound.mp3"></audio>
    <audio ref="endSound" src="/sounds/end-sound.mp3"></audio>
  </div>
</template>

<script>
export default {
  props: {
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

  },
  data() {
    return {
      winner: null,
      isDrawing: false,
      isFinished: false,
      isMuted: false,
      message: '',
      participants:[
          {'participant' : {'name' : 'Iron Man'} },
          {'participant' : {'name' : 'Captain America'} },
          {'participant' : {'name' : 'Thor'} },
          {'participant' : {'name' : 'Black Widow'} },
          {'participant' : {'name' : 'Hulk'} },
          {'participant' : {'name' : 'Hawkeye'} },
          {'participant' : {'name' : 'Scarlet Witch'} },
          {'participant' : {'name' : 'Spider-Man'} },
          {'participant' : {'name' : 'Black Panther'} },
          {'participant' : {'name' : 'Captain Marvel'} },
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
      presetWinner: null,
      initialRotation: 0,
      totalRotation: 0,
    }
  },
  computed: {
    buttonText() {
      return this.isDrawing ? 'Processing...' : (this.isFinished ? 'Restart' : 'Draw Now');
    }
  },
  mounted() {
    this.tryInitWheel();
    this.getParticipants().catch(error => {
      console.error('Failed to get participants:', error);
      this.message = "Failed to load participants. Please try again.";
    }).finally(() => {
      this.$nextTick(() => {
        this.drawWheel();
      });
    });
  },
  updated() {
    console.log('Component updated, isLoading:', this.isLoading);
    if (!this.ctx && this.$refs.wheelCanvas) {
      console.log('Initializing wheel in updated hook');
      this.initWheel();
    }
  },
  watch: {
    prizeUniqueCode: {
      handler(newValue, oldValue) {
        console.log('prizeUniqueCode changed:', newValue);
        if (newValue && newValue !== oldValue) {
          this.resetWheel();
          this.getParticipants().catch(error => {
            console.error('Failed to get participants:', error);
            this.message = "Failed to load participants. Please try again.";
          });
        }
      },
      immediate: true
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
    getParticipants() {
      if (!this.prizeUniqueCode) {
        this.message = "Please select a prize to draw";
        this.isLoading = false;
        return Promise.reject("No prize selected");
      }

      this.isLoading = true;
      return axios.get(passport_url('shared/public/prizes/' + this.prizeUniqueCode + '/draw'))
        .then(response => {
          this.participants = response.data.meta.attendances;
          this.message = response.data.message;
          this.presetWinner = response.data.data.winner;
          console.log('Preset Winner:', this.presetWinner);
          this.$nextTick(() => {
            this.drawWheel();
          });
          this.isLoading = false;
        })
        .catch(error => {
          console.error('Failed to get participants list:', error);
          this.message = "Failed to get participants list";
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
        console.log('Wheel initialized successfully');
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

      this.arc = (2 * Math.PI) / this.participants.length;
      
      for(let i = 0; i < this.participants.length; i++) {

        const angle = this.startAngle + (i * this.arc);
        const endAngle = angle + this.arc;


        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, angle, endAngle);
        ctx.lineTo(centerX, centerY);
        ctx.fillStyle = this.getColor(i);
        ctx.fill();
        
        ctx.save();
        ctx.translate(centerX, centerY);
        ctx.rotate(angle + (this.arc / 2));
        ctx.textAlign = "right";
        ctx.fillStyle = "#fff";
        ctx.font = "bold 12px sans-serif";
        ctx.fillText(this.participants[i].participant.name, radius - 10, 0);
        ctx.restore();

        console.log(`Participant: ${this.participants[i].participant.name}, Start Angle: ${angle.toFixed(4)}, End Angle: ${endAngle.toFixed(4)}`);

      }

      this.drawArrow(ctx, centerX, centerY, radius);
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
      const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];
      return colors[index % colors.length];
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
      
      // 停止所有声音
      ['startSound', 'tickSound', 'endSound'].forEach(sound => {
        if (this.$refs[sound]) {
          this.$refs[sound].pause();
          this.$refs[sound].currentTime = 0;
        }
      });
      
      if (!this.isMuted && this.$refs.startSound) {
        this.$refs.startSound.play();
      }
      
      this.spinAngleStart = 20; // 固定初始角速度
      this.spinTime = 0;
      this.spinTimeTotal = 5 * 1000; // 固定为3.5秒

      console.log('Initial startAngle in startDraw:', this.startAngle);

      // 找到预设获奖者的索引
      const winnerIndex = this.participants.findIndex(p => p.id === this.presetWinner.id);
      
      if (winnerIndex !== -1) {
        this.targetRotation = (winnerIndex * this.arc) + (this.arc / 2);
        // 确保轮盘至少旋转8圈
        this.totalRotation = 8 * Math.PI * 2 + (2 * Math.PI - this.targetRotation);
      } else {
        console.warn('Preset winner not found in participants list');
        this.totalRotation = 8 * Math.PI * 2 + Math.random() * 2 * Math.PI;
      }

      this.initialRotation = this.startAngle;
      this.rotateWheel();
    },

    rotateWheel() {
      if (!this.isMuted) {
        this.$refs.tickSound.play();
      }
      
      this.spinTime += 16;
      if (this.spinTime >= this.spinTimeTotal) {
        this.$refs.tickSound.pause();
        this.stopRotateWheel();
        return;
      }
      
      const progress = this.spinTime / this.spinTimeTotal;
      const easeProgress = this.easeInOutQuint(progress);

      const currentRotation = this.initialRotation + (this.totalRotation * easeProgress);
      this.startAngle = currentRotation % (2 * Math.PI);

      console.log('Progress:', progress.toFixed(4), 'Angle:', this.startAngle.toFixed(4));

      this.drawWheel();
      this.spinTimeout = setTimeout(() => this.rotateWheel(), 16);
    },

    // 新的缓动函数
    easeInOutQuint(t) {
      return t < 0.5 ? 16 * t * t * t * t * t : 1 - Math.pow(-2 * t + 2, 5) / 2;
    },

    stopRotateWheel() {
      clearTimeout(this.spinTimeout);
      
      // 确保轮盘停在预设获奖者位置
      const winnerIndex = this.participants.findIndex(p => p.participant.id === this.presetWinner.id);
      if (winnerIndex !== -1) {
        this.startAngle = 2 * Math.PI - ((winnerIndex * this.arc) + (this.arc / 2));
        this.currentIndex = winnerIndex;
        this.drawWheel();
      }
      
      this.winner = this.presetWinner;
      this.isDrawing = false;
      this.isFinished = true;
      
      if (this.$refs.tickSound) {
        this.$refs.tickSound.pause();
        this.$refs.tickSound.currentTime = 0;
      }
      
      if (!this.isMuted && this.$refs.endSound) {
        this.$refs.endSound.play();
      }
      
      this.$emit('draw-complete', this.winner);
    },
    
    stopRotateWheel() {
      clearTimeout(this.spinTimeout);
      
      // 确保轮盘停在预设获奖者位置
      const winnerIndex = this.participants.findIndex(p => p.participant.id === this.presetWinner.id);
      if (winnerIndex !== -1) {
        // 设置角度使指针指向获奖者区域的中心
        this.startAngle = 2 * Math.PI - ((winnerIndex * this.arc) + (this.arc / 2));
        this.currentIndex = winnerIndex;
        this.drawWheel();
      }
      
      this.winner = this.presetWinner;
      this.isDrawing = false;
      this.isFinished = true;
      
      if (this.$refs.tickSound) {
        this.$refs.tickSound.pause();
        this.$refs.tickSound.currentTime = 0;
      }
      
      if (!this.isMuted && this.$refs.endSound) {
        this.$refs.endSound.play();
      }
      
      this.$emit('draw-complete', this.winner);
    },
    easeOut(t, b, c, d) {
      t /= d;
      return c * (1 - Math.pow(1 - t, 3)) + b;
    },
    confirmWinner() {
      Swal.fire({
        title: 'Are you sure you want to confirm this winner?',
        text: "This action cannot be undone!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, confirm winner!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          // User confirmed, execute the logic to confirm the winner
          this.finalizeWinner();
        }
      });
    },
    redraw() {
      // Use SweetAlert2 to display a confirmation dialog
      Swal.fire({
        title: 'Are you sure you want to redraw?',
        text: "This will reset the current draw result!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, redraw!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          // If user confirms, execute the redraw logic
          this.performRedraw();
        }
      });
    },
    finalizeWinner() {
      // Implement the logic to confirm the winner
      // This could involve an API call to update the backend
      axios.post(passport_url('shared/public/prizes/' + this.prizeUniqueCode + '/confirm'), {
        winner: this.winner
      })
      .then(response => {
        console.log('Winner confirmed:', this.winner);
        Swal.fire('Success!', 'The winner has been confirmed.', 'success');
        // You might want to update some component state here
        this.isFinished = true;
        this.isDrawing = false;
      })
      .catch(error => {
        console.error('Failed to confirm winner:', error);
        Swal.fire('Error', 'Failed to confirm the winner. Please try again.', 'error');
      });
    },
    performRedraw() {
      axios.get(passport_url('shared/public/prizes/' + this.prizeUniqueCode + '/redraw'))
        .then(response => {
          this.participants = response.data.meta.attendances;
          this.message = response.data.message;
          this.presetWinner = response.data.data.winner;
          console.log('Preset Winner:', this.presetWinner);
          this.$nextTick(() => {
            this.drawWheel();
          });
          this.isLoading = false;
          // 重置相关状态
          this.winner = null;
          this.isFinished = false;
          Swal.fire('Redraw Successful!', 'The lucky draw wheel has been reset.', 'success');
        })
        .catch(error => {
          console.error('Failed to get participants list:', error);
          this.message = "获取参与者列表失败";
          this.isLoading = false;
          Swal.fire('Error', 'Failed to redraw. Please try again later.', 'error');
        });
    }
  }
}
</script>

<style scoped>
.lucky-draw {
  text-align: center;
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
}
.current-info {
  margin: 20px 0;
  font-size: 18px;
}
.current-index, .current-name {
  display: inline-block;
  margin: 0 10px;
}
.current-index span, .current-name span {
  font-weight: bold;
  color: #D40E0E;
}
</style>