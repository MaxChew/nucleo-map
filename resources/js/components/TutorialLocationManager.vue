<template>
    <div class="space-y-4 rounded-lg">
      <div>
        <div class="relative">
          <div class="flex flex-wrap gap-2 bg-gray-700 p-2 rounded-lg border border-white">
            <span
              v-for="(location, index) in selectedLocations"
              :key="index"
              class="bg-[#212A51] text-white text-sm px-2 py-1 rounded-lg flex items-center gap-2"
              style="border: 1px solid white;"
            >
              {{ location }}
              <button class="ml-2 text-danger-500" @click="removeLocation(location)">
                &times;
              </button>
            </span>
          </div>
          <select 
            v-model="newLocation"
            @change="addLocation"
            class="form-control w-full p-2 bg-[#212A51] text-white border border-white rounded-lg mt-2"
          >
            <option value="" selected disabled class="bg-[#212A51] text-white">Select Location</option>
            <option
              v-for="location in availableLocations"
              :key="location"
              :value="location"
              class="bg-[#212A51] text-white"
            >
              {{ location }}
            </option>
          </select>
        </div>
        <input type="hidden" name="tutorial_locations" :value="selectedLocations.join(',')" />
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      initialLocations: {
        type: Array,
        default: () => [],
      },
      availableLocations: {
        type: Array,
        default: () => [],
      },
    },
    data() {
      return {
        selectedLocations: [...this.initialLocations],
        newLocation: "",
      };
    },
    methods: {
      addLocation() {
        if (!this.newLocation || this.selectedLocations.includes(this.newLocation)) {
          alert("Location already selected or invalid.");
          this.newLocation = "";
          return;
        }
        this.selectedLocations.push(this.newLocation);
        this.newLocation = "";
      },
      removeLocation(location) {
        this.selectedLocations = this.selectedLocations.filter((l) => l !== location);
      },
    },
  };
  </script>
  
  