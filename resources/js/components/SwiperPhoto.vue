<template>
  <div class="h-full flex items-center justify-center"> 
    <swiper
      class="mySwiper swiper-custom h-[65vh] w-full" 
      :lazy="true"
      :pagination="{ clickable: true }"
      :navigation="true"
      :modules="modules"
    >
      <swiper-slide class="!h-[90%]" v-for="(image, index) in images" :key="index">
        <img :src="image.url" :alt="image.name" loading="lazy" class="w-full h-full object-cover" />
        <div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div>
      </swiper-slide>
    </swiper>
  </div>
</template>


<script>
// Import Swiper Vue.js components
import { Swiper, SwiperSlide } from 'swiper/vue';

// Import Swiper styles
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';

// Import required modules
import { Pagination, Navigation } from 'swiper/modules';

export default {
  name: 'ImageSlider',
  components: {
    Swiper,
    SwiperSlide,
  },
  props: {
    images: {
      type: Array,
      required: true,
      validator(value) {
        return value.every(item => item.hasOwnProperty('src') && item.hasOwnProperty('alt'));
      }
    }
  },
  setup() {
    return {
      modules: [Pagination, Navigation],
    };
  },
};
</script>

<style>
.swiper-custom .swiper-button-next,
.swiper-custom .swiper-button-prev {
  @apply text-primary bg-gray-700 rounded-full w-10 h-10;
}

.swiper-custom .swiper-button-next::after,
.swiper-custom .swiper-button-prev::after {
  @apply text-xl text-white font-bold;
}
/* Swiper Pagination */
.swiper-custom .swiper-pagination-bullet {
  @apply bg-gray-700 dark:bg-white opacity-45;
}

.swiper-custom .swiper-pagination-bullet-active {
  @apply bg-gray-700 dark:bg-white opacity-100;
}
</style>