<template>
  <div class="flex flex-col">
    <div class="flex flex-row justify-between font-bold bg-primary text-lg text-white rounded-xl border-2 shadow-2xl py-4 uppercase px-4">
      <h3 class="items-center">
        <i class="fal fa-gift mr-3"></i>Lucky Draw
      </h3>
      <a v-if="showMoreButton" :href="showMoreUrl" class="rounded-xl hover:border-2 hover:px-2 hover:bg-third flex items-center cursor-pointer">
        MORE <i class="fas fa-arrow-circle-right ml-2"></i>
      </a>
    </div>

    <div class="border-2 my-2">
      <div class="flex flex-row font-bold w-full py-2 border-b-2 bg-primary text-white text-sm md:text-xl">
        
        <span class="text-center w-1/5 border-r-2">Prize</span>
        <span class="text-center w-3/5 border-r-2">Item</span>
        <span class="text-center w-1/5">Winner</span>
      </div>
      <div v-for="prize in prizes" :key="prize.id" class="flex flex-row text-xs md:text-lg text-primary my-2">
        
        <span class="text-center w-1/5 border-r-2  flex justify-center items-center" @click="openImageModal(prize)">{{ prize.name }}</span>
          
          
        <div class="text-center w-3/5 border-r-2  flex flex-col justify-center items-center"  @click="openImageModal(prize)">
            <span>{{ prize.prize }}</span>
            <div class="w-full flex justify-center items-center">
            <img :src="prize.banner_desktop.url" class="w-auto h-16 md:h-24" alt="prize banner" v-if="prize.banner_desktop.url"/>
          </div>
          </div>
        <span class="text-center w-1/5 flex flex-row items-center justify-center">
          
          <template v-if="prize.winner">
            <div class="flex flex-col">
            {{ prize.winner ? prize.winner.name : 'N/A' }} 
            <span class="text-sm">{{ prize.winner ? prize.winner.mobile : '' }} 
            </span>
            <div class="flex flex-row justify-center">
              <a href="#" 
                class="mt-2 btn-outline px-2 rounded-xl hover:bg-third" 
                @click="selectPrize(prize)" v-if="ableAction" >
                Draw this
              </a>
            </div>
          </div>
          </template>
          <template v-else>
            <template v-if="!ableAction">
              {{ prize.winner ? prize.winner.name : 'N/A' }}
            </template>
            <template v-if="modelValue">
              <a href="#" 
                class="ml-2 btn-outline px-2 rounded-xl hover:bg-third w-24" 
                @click="selectPrize(prize)" 
                v-if="modelValue.unique_id !== prize.unique_id && ableAction">
                Draw this
              </a>
            </template>
            <template v-else>
              <a href="#" 
                class="ml-2 btn-outline px-2 rounded-xl hover:bg-third" 
                @click="selectPrize(prize)" v-if="ableAction" >
                Draw this
              </a>

            </template>
          </template>

         
          
          
        </span>
      </div>
    </div>

    <!-- 添加 Modal 组件 -->
    <modal :show="showImageModal" @close="closeImageModal" size="md">
      <div class="p-10 flex flex-col items-center justify-center" v-if="selectedPrize">
        <div v-if="selectedPrize" class="my-4 flex flex-col rounded-3xl bg-primary text-white font-bold p-4 w-full text-lg md:text-2xl uppercase">
          <span class="underline" v-text="selectedPrize.name"></span>
        </div>
        <div v-if="selectedPrize" class=" flex flex-col  font-bold p-4 w-full text-lg md:text-2xl uppercase">
          <span class="text-lg md:text-5xl mb-4" v-text="selectedPrize.prize"></span>
        </div>
        <div class="rounded-xl" v-if="selectedPrize" >
          <img v-if="selectedPrize.banner_desktop" :src="selectedPrize.banner_desktop.url" alt="Large prize image" class="w-auto max-h-[100vh] md:max-h-[200vh] rounded-xl" />
        </div>
        
        <button @click="closeImageModal" class="mt-4 px-4 py-2 bg-primary text-white rounded hover:bg-opacity-80">
          close
        </button>
      </div>
    </modal>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

export default {
  props: {
    modelValue: {
      type: String,
      default: null
    },
    ableAction: {
      type: Boolean,
      required: true
    },
    showMoreButton: {
      type: Boolean,
      required: true
    },
    showMoreUrl: {
      type: String,
      required: false
    },
    slug: {
      type: String,
      required: true
    },
  },
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    const prizes = ref([]);
    const isLoading = ref(false);
    const message = ref('');
    const alerts = ref([]);
    const showImageModal = ref(false);
    const selectedPrize = ref([]);

    const handleWinnerConfirmed = () => {
      getPrizes();
    };

    const openImageModal = (prize) => {
      selectedPrize.value = prize;
      showImageModal.value = true;
      console.log('selectedPrize', selectedPrize.value);
    };

    const closeImageModal = () => {
      showImageModal.value = false;
    };

    onMounted(() => {
      getPrizes();
      window.addEventListener('winnerConfirmed', handleWinnerConfirmed);
    });

    onUnmounted(() => {
      window.removeEventListener('winnerConfirmed', handleWinnerConfirmed);
    });

    const getPrizes = async () => {
      isLoading.value = true;
      try {
        const response = await axios.get(`/api/shared/public/events/${props.slug}/prizes`);
        prizes.value = response.data.data.prizes;
        console.log('prizes', prizes.value);
      } catch (error) {
        console.error('获取奖品列表失败:', error);
        message.value = "获取奖品列表失败";
        showAlert('error', message.value);
      } finally {
        isLoading.value = false;
      }
    };

    const selectPrize = (obj) => {
      emit('update:modelValue', obj);
    };

    const showMore = () => {
      //{{ route('user.events.prizes', ['event' => $object->slug]) }}

      // 实现显示更多的逻辑，例如跳转到详细页面
      // 这里需要根据您的路由配置来实现
    };

    const showAlert = (type, msg) => {
      const id = Date.now();
      alerts.value.push({ id, type, message: msg });
      setTimeout(() => {
        closeAlert(id);
      }, 3000);
    };

    const closeAlert = (id) => {
      const index = alerts.value.findIndex(alert => alert.id === id);
      if (index !== -1) {
        alerts.value.splice(index, 1);
      }
    };

    onMounted(() => {
      getPrizes();
    });

    return {
      prizes,
      isLoading,
      message,
      alerts,
      selectPrize,
      showAlert,
      closeAlert,
      showMore,
      openImageModal,
      closeImageModal,
      showImageModal,
      selectedPrize,
    };
  }
}
</script>

<style scoped>
/* 可以在这里添加特定的样式 */
</style>