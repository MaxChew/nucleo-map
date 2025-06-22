<template>
    <div>
      <div class="relative">
        <div class="flex flex-wrap gap-2 bg-gray-700 p-2 rounded-lg border border-white">
          <span
            v-for="(subject, index) in selectedSubjects"
            :key="index"
            class="bg-[#212A51] text-white text-sm px-2 py-1 rounded-lg flex items-center gap-2"
            style="border: 1px solid white;"
          >
            {{ subject }}
            <button class="ml-2 text-danger-500" @click="removeSubject(subject)">
              &times;
            </button>
          </span>
        </div>
        <select
          v-model="newSubject"
          @change="addSubject"
          class="form-control w-full p-2 bg-[#212A51] text-white border border-white rounded-lg mt-2"
        >
          <option value="" disabled>Select Subject</option>
          <option v-for="subject in availableSubjects" :key="subject" :value="subject">
            {{ subject }}
          </option>
        </select>
      </div>
      <input type="hidden" name="subjects" :value="selectedSubjects.join(',')" />
    </div>
  </template>
  
  <script>
  export default {
    props: {
      initialSubjects: {
        type: Array,
        default: () => [],
      },
      availableSubjects: {
        type: Array,
        default: () => [],
      },
    },
    data() {
      return {
        selectedSubjects: [...this.initialSubjects],
        newSubject: "",
      };
    },
    methods: {
      addSubject() {
        if (!this.newSubject || this.selectedSubjects.includes(this.newSubject)) {
          alert("Subject already selected.");
          return;
        }
        this.selectedSubjects.push(this.newSubject);
        this.newSubject = "";
      },
      removeSubject(subject) {
        this.selectedSubjects = this.selectedSubjects.filter((item) => item !== subject);
      },
    },
  };
  </script>
  