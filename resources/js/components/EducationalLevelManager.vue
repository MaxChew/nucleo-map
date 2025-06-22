<template>
  <div>
    <div class="relative">
      <div class="flex flex-wrap gap-2 bg-gray-700 p-2 rounded-lg border border-white">
        <span
          v-for="(level, index) in selectedEducationalLevels"
          :key="index"
          class="bg-[#212A51] text-white text-sm px-2 py-1 rounded-lg flex items-center gap-2"
          style="border: 1px solid white;"
        >
          {{ level }}
          <button class="ml-2 text-danger-500" @click="removeEducationalLevel(level)">
            &times;
          </button>
        </span>
      </div>
      <select
        v-model="newEducationalLevel"
        @change="addEducationalLevel"
        class="form-control w-full p-2 bg-[#212A51] text-white border border-white rounded-lg mt-2"
      >
        <option value="" disabled>Select Educational Level</option>
        <option v-for="level in availableEducationalLevels" :key="level" :value="level">
          {{ level }}
        </option>
      </select>
    </div>
    <input type="hidden" name="educational_levels" :value="selectedEducationalLevels.join(',')" />
  </div>
</template>

<script>
export default {
  props: {
    initialLevels: {
      type: Array,
      default: () => [],
    },
    availableLevels: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      selectedEducationalLevels: [...this.initialLevels],
      newEducationalLevel: "",
    };
  },
  methods: {
    addEducationalLevel() {
        console.log("Selected Educational Level:", this.newEducationalLevel); 
        if (!this.newEducationalLevel || this.selectedEducationalLevels.includes(this.newEducationalLevel)) {
            alert("Educational Level already selected or invalid.");
            return;
        }
        this.selectedEducationalLevels.push(this.newEducationalLevel);
        console.log("Updated Educational Levels:", this.selectedEducationalLevels); 
        this.newEducationalLevel = "";
    },
    removeEducationalLevel(level) {
        this.selectedEducationalLevels = this.selectedEducationalLevels.filter((item) => item !== level);
        console.log("After Removal Educational Levels:", this.selectedEducationalLevels); 
    },
},
};
</script>
