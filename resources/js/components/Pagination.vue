<template>
    <div>
      <span class="btn btn-link btn-page disabled" v-if="current <= 1"></span>
      <a class="btn btn-link btn-page" v-else title="Previous Page" href="#" @click.prevent="emitPage(current - 1)">
        < 
      </a>
  
      <template v-for="(page, index) in pageItems" :key="index">
        <a 
          v-if="page" 
          class="btn btn-page" 
          :class="current === page ? 'btn-link-primary font-bold !bg-white !text-primary' : 'btn-link'" 
          :title="'Page ' + page" 
          href='#' 
          @click.prevent="emitPage(page)" 
          v-text="page"
        ></a>
        <span v-else class="px-2">...</span>
      </template>
      <span class="btn btn-link btn-page disabled" v-if="current >= last"> > </span>
      <a class="btn btn-link btn-page" v-else title="Next Page" href="#" @click.prevent=" emitPage(current + 1)">
        >
      </a>
    </div>
</template>

<script>
import { computed, watch } from 'vue';

export default {
  props: {
    current: Number,
    last: Number,
    maxPaging: {
      type: Number,
      default: 6
    }
  },

  emits: ['update:page','update:current'],
  setup(props, { emit }) {
    const pageItems = computed(() => {
      let { maxPaging: max, last, current } = props;
      let left = max - 2;
      let midpoint = Math.floor((left + 1) / 2);
      let items = [];

      if (last <= max) return range(1, last);
      if (current > (last - midpoint)) items = range(last - max + 1, last);
      if (current < midpoint) items = range(1, left);
      else {
        items = range(current - midpoint + 1, current - midpoint + left);
      }
      if (Math.max(...items) >= last - 2) {
        items = range(last - max + 1, last);
      } else {
        items.push(false); // show ellipsis
        items.push(last);
      }
      var minItem = Math.min(...items.filter(Boolean));
      if (minItem > 1) {
        if (minItem == 3) items.unshift(2);
        if (minItem > 3) items.unshift(false);
        items.unshift(1);
      }
      return items;
    });

    function range(from, to) {
      return Array.from({ length: to - from + 1 }, (_, i) => from + i);
    }

    function emitPage(page) {
      emit('update:current', page);  // 修改这里
      emit('update:page', page);  // 使用 v-model 语法
    }

    return {
      pageItems,
      range,
      emitPage
    };
  }
}
</script>