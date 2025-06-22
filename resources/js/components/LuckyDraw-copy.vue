<template>
  <div class="lucky-draw">
    <div class="slot-machine">
      <div class="slot" :class="{ 'spinning': isDrawing, 'stopped': isFinished }">
        <div class="slot-wrapper">
          <div v-for="(name, index) in visibleNames" :key="index" :class="['slot-item', `item-${index}`]">{{ name.participant.name }}</div>
        </div>
      </div>
    </div>
    <div v-if="isFinished" class="winner">
      获奖者: {{ winner.participant.name }}
    </div>
    <div class="text-danger-500 font-bold">
        <span>{{ this.message }}</span>
    </div>
    <div class="flex flex-row justify-center" v-if="this.ableAction">
      <button @click="startDraw" :disabled="isDrawing" v-if="this.winner">Confirm ?</button>
      <button @click="startDraw" :disabled="isDrawing" v-if="this.winner">Redraw ?</button>

      <button @click="startDraw" :disabled="isDrawing || this.prizeUniqueCode == null" v-if="this.winner == null">{{ buttonText }}</button>
      
      <audio ref="startSound" src="/sounds/start-sound.mp3"></audio>
      <audio ref="tickSound" src="/sounds/tick-sound.mp3"></audio>
      <audio ref="endSound" src="/sounds/end-sound.mp3"></audio>
      <button @click="toggleMute" class="mute-button">{{ isMuted ? 'Sound On' : 'Sound Off' }}</button>
    </div>

  </div>
</template>

<script>
export default {
  props: {
    names: {
      type: Array,
      required: true
    },
    prizeUniqueCode: {
      type: Array,
      required: true
    },
    ableAction: {
      type: String,
      required: true
    }
  },
  mounted() {
    if (this.prizeUniqueCode == null) {
      this.message = "Please select a prize to draw";
    }

    this.getParticipants().catch(error => {
      console.error('初始化参与者名单失败:', error);
    });
  },
  data() {
    return {
      currentName: 'Ready...',
      winner: null,
      isDrawing: false,
      isFinished: false,
      drawInterval: null,
      drawSpeed: 200,
      drawDuration: 3800,
      tickSoundInterval: null,
      isMuted: false,
      slotNames: [],
      participants: [],
      currentIndex: 0,
      message: '',
      winner: null,
      object: null,
    }
  },
  computed: {
    buttonText() {
      return this.isDrawing ? 'Processing...' : (this.isFinished ? 'Restart' : 'Draw Now');
    },
    visibleNames() {
      if (this.slotNames.length < 5) return this.slotNames;
      const indices = [-2, -1, 0, 1, 2].map(offset => 
        (this.currentIndex + offset + this.slotNames.length) % this.slotNames.length
      );
      return indices.map(index => this.slotNames[index]);
    }
  },
  methods: {
    // 新增方法: 获取参与者名单
    getParticipants() {
      return new Promise((resolve, reject) => {
        if (this.prizeUniqueCode == null) {
          reject('Prize Unique Code is required');
          return;
        }

        axios.get(passport_url('shared/public/prizes/' + this.prizeUniqueCode + '/draw/name-list'))  // 假设您的后端API路径是 '/api/participants'
          .then(response => {
            this.participants = response.data.meta.attendances;
            this.message = response.data.message;
            this.winner = response.data.data.winner; // from api. already draw but in frontend need to show the spin effect then show the winner
              // 假设API返回的数据格式是 { data: [...] }
            resolve();
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    startDraw() {
      if (this.prizeUniqueCode == null) {
        alert('Please select a prize to draw');
        return;
      }

      if (this.isFinished) {
        this.reset();
        return;
      }

      this.isDrawing = true;
      this.isFinished = false;

      console.log('开始抽奖');
      console.log(this.participants);

      // 使用 Ajax 获取参与者名单
      this.getParticipants().then(() => {
        this.slotNames = this.shuffleArray([...this.participants]);
        this.currentIndex = 0;
        
        if (!this.isMuted) {
          this.$refs.startSound.play();
        }
        
        this.drawInterval = setInterval(() => {
          this.currentIndex = (this.currentIndex + 1) % this.slotNames.length;
          if (!this.isMuted) {
            this.$refs.tickSound.play();
          }
        }, 100);
        
        setTimeout(() => {
          this.finishDraw();
        }, this.drawDuration);
      }).catch(error => {
        console.error('获取参与者名单失败:', error);
        this.isDrawing = false;
      });
    },
    finishDraw() {
      clearInterval(this.drawInterval);
      
      console.log('抽奖结束');
      console.log(this.winner);
      if (this.winner) {
        // 使用API返回的获胜者
        this.currentName = this.winner.name;
      }
      
      this.isDrawing = false;
      this.isFinished = true;
      
      if (!this.isMuted) {
        this.$refs.endSound.play();
      }
      
      this.$emit('draw-complete', this.winner);
    },
    reset() {
      this.currentName = '准备开始';
      this.winner = null;
      this.isFinished = false;
      this.slotNames = [];
      this.currentIndex = 0;
    },
    toggleMute() {
      this.isMuted = !this.isMuted;
      ['startSound', 'tickSound', 'endSound'].forEach(sound => {
        this.$refs[sound].muted = this.isMuted;
      });
    },
    shuffleArray(array) {
      for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
      }
      return array;
    }
  }
}
</script>

<style scoped>
.lucky-draw {
  text-align: center;
}
.slot-machine {
  border: 2px solid #333;
  padding: 20px;
  margin: 20px auto;
  @apply w-full;
  @apply h-80;
  overflow: hidden;
  position: relative;
  background-color: #f0f0f0;
  border-radius: 15px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
.slot {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.slot-wrapper {
  position: relative;
  transition: transform 0.1s linear;
}
.slot-item {
  height: 60px;
  line-height: 60px;
  font-weight: bold;
  transition: all 0.3s ease;
}
.item-0, .item-4 {
  font-size: 16px;
  opacity: 0.6;
}
.item-1, .item-3 {
  font-size: 24px;
  opacity: 0.8;
}
.item-2 {
  font-size: 28px;
  @apply text-white font-bold;
  @apply bg-primary;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}
.spinning .slot-wrapper {
  animation: spin v-bind('drawSpeed + "ms"') linear infinite;
}
.stopped .slot-wrapper {
  animation: none;
}
@keyframes spin {
  0% {
    transform: translateY(0);
  }
  100% {
    transform: translateY(-60px);
  }
}
.winner {
  color: #ff0000;
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
  @apply w-44;
  @apply text-lg uppercase;
  @apply h-12;
  box-shadow: .4125rem .4125rem 0 rgba(0, 0, 0, .2);
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
</style>