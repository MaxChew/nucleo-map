<template>
    <div>
      <div class="flex justify-around bg-bg rounded-t-lg border border-gray-700">
        <button
          @click="switchTab('reviews')"
          :class="{
            'border-b-2 border-white': activeTab === 'reviews',
            'text-gray-400 hover:text-gray-600 focus:text-white border-transparent': activeTab !== 'reviews',
          }"
          class="w-full py-3 text-center font-semibold"
        >
          Reviews
        </button>
        <button
          @click="switchTab('about-me')"
          :class="{
            'border-b-2 border-white': activeTab === 'about-me',
            'text-gray-400 hover:text-gray-600 focus:text-white border-transparent': activeTab !== 'about-me',
          }"
          class="w-full py-3 text-center font-semibold"
        >
          About Me
        </button>
      </div>
  
      <div v-show="activeTab === 'reviews'" class="p-6">
        <slot name="reviews"></slot>
      </div>
      <div v-show="activeTab === 'about-me'" class="p-6">
        <slot name="aboutMe"></slot>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        activeTab: 'reviews', 
      };
    },
    methods: {
      switchTab(tab) {
        this.activeTab = tab;
      },
    },
  };
  </script>