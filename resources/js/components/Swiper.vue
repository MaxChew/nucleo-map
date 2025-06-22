<template>
  <div class="relative group">
    <swiper 
    :modules="[Navigation, Scrollbar, Grid]" 
    :spaceBetween="10"
    :slidesPerView="no_view_sm" 
    :slidesPerGroup="no_move" 
    :freeMode="true" 
    :mousewheel="{ forceToAxis: true }"
    :grid="{ fill: 'row', rows: 1 }" 
    :breakpoints="{
        1024: { slidesPerView: no_view_lg },
        640: { slidesPerView: no_view_md },
      }" 
    :navigation="{
        enabled: true,
        prevEl: '.swiper-button-alt-prev',
        nextEl: '.swiper-button-alt-next',
      }" 
      :scrollbar="{
        hide: false,
        draggable: true,
        el: '.swiper-scrollbar',
      }" 
      @swiper="onSwiperInit"
      @slideChange="checkNavigationState" 
      @reachBeginning="isAtStart = true"
      @reachEnd="isAtEnd = true" 
      @fromEdge="resetNavigationState"
      >
      <swiper-slide v-for="(object, index) in items" :key="index">
        <slot :object="object"></slot>
      </swiper-slide>
    </swiper>

    <!-- Navigation buttons -->
    <div
      class="swiper-button-alt-prev absolute top-1/2 left-[-25px] z-10 -translate-y-1/2 cursor-pointer transition-all duration-300 hidden lg:block"
      :class="{ 'opacity-0': isAtStart }">
      <img src="/images/svg/swiper-prev-btn.svg" width="46" height="46" alt="prev" />
    </div>
    <div
      class="swiper-button-alt-next absolute top-1/2 right-[-25px] z-10 -translate-y-1/2 cursor-pointer transition-all duration-300 hidden lg:block"
      :class="{ 'opacity-0': isAtEnd }">
      <img src="/images/svg/swiper-next-btn.svg" width="46" height="46" alt="next" />
    </div>

    <!-- Scrollbar -->
    <div
      class="swiper-scrollbar mt-20 !left-[18px] lg:!left-[40px] xl:!left-[102px] !right-[18px] lg:!right-[40px] xl:!right-[102px] !w-auto block lg:hidden">
    </div>
  </div>

</template>

<script>
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/scrollbar";
import "swiper/css/grid";
import { Navigation, Scrollbar, Grid } from "swiper/modules";

export default {
  components: { Swiper, SwiperSlide },
  props: {
    items: {
      type: Array,
      required: true,
    },
    no_view_sm: {
      type: [Number, String],
      default: 1,
    },
    no_view_md: {
      type: [Number, String],
      default: 2,
    },
    no_view_lg: {
      type: [Number, String],
      default: 3,
    },
    no_move: {
      type: [Number, String],
      default: 1,
    },
  },
  data() {
    return {
      isAtStart: true,
      isAtEnd: false,
    };
  },
  setup() {
    return { Navigation, Scrollbar, Grid };
  },
  methods: {
    onSwiperInit(swiper) {
      this.swiperInstance = swiper;
      this.checkNavigationState();
    },
    checkNavigationState() {
      if (!this.swiperInstance) return;
      this.isAtStart = this.swiperInstance.isBeginning;
      this.isAtEnd = this.swiperInstance.isEnd;
    },
    resetNavigationState() {
      this.isAtStart = false;
      this.isAtEnd = false;
    },
  },
};
</script>

<style scoped></style>