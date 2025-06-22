<template>
    <div>
      <div class="flex justify-around bg-[#212A51] rounded-t-lg border border-white">
        <button
          @click="switchTab('basic-info')"
          :class="{
            'border-b-2 border-white text-white': activeTab === 'basic-info',
            'text-gray-300': activeTab !== 'basic-info'
          }"
          class="w-full py-3 text-center font-semibold"
        >
          Basic Info
        </button>
        <button
          @click="switchTab('address')"
          :class="{
            'border-b-2 border-white text-white': activeTab === 'address',
            'text-gray-300': activeTab !== 'address'
          }"
          class="w-full py-3 text-center font-semibold"
        >
          Address
        </button>
        <button
          @click="switchTab('students')"
          :class="{
            'border-b-2 border-white text-white': activeTab === 'students',
            'text-gray-300': activeTab !== 'students'
          }"
          class="w-full py-3 text-center font-semibold"
        >
          Students
        </button>
      </div>
  
      <div v-show="activeTab === 'basic-info'" class="p-6 bg-gray-800 border border-white rounded-lg">
        <slot name="basic-info"></slot>
      </div>
      <div v-show="activeTab === 'address'" class="p-6 bg-gray-800 border border-white rounded-lg">
        <slot name="address"></slot>
      </div>
      <div v-show="activeTab === 'students'" class="p-6 bg-gray-800 border border-white rounded-lg">
        <slot name="students"></slot>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        activeTab: 'basic-info',
      };
    },
    methods: {
      switchTab(tab) {
        this.activeTab = tab;
      },
    },
  };
  </script>
  

  