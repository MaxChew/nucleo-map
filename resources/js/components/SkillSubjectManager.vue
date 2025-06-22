<template>
    <div class="space-y-4 rounded-lg">
      <div>
        <label class="block mb-2">Skills</label>
        <div class="relative">
          <div class="flex flex-wrap gap-2 bg-gray-700 p-2 rounded-lg border border-white">
            <span
              v-for="(skill, index) in selectedSkills"
              :key="index"
              class="bg-[#212A51] text-white text-sm px-2 py-1 rounded-lg flex items-center gap-2"
              style="border: 1px solid white;"
            >
              {{ skill }}
              <button class="ml-2 text-danger-500" @click="removeSkill(skill)">
                &times;
              </button>
            </span>
          </div>
          <select 
            v-model="newSkill"
            @change="addSkill"
            class="form-control w-full p-2 bg-[#212A51] text-white border border-white rounded-lg mt-2"
          >
            <option value="" selected disabled class="bg-[#212A51] text-white">Select Skills</option>
            <option
              v-for="skill in availableSkills"
              :key="skill"
              :value="skill"
              class="bg-[#212A51] text-white"
            >
              {{ skill }}
            </option>
          </select>
        </div>
        <input type="hidden" name="skills" :value="selectedSkills.join(',')" />
      </div>
  
      <div>
        <label class="block mb-2">Subject Expertise</label>
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
            <option value="" selected disabled class="bg-[#212A51] text-white">Select Subject Expertise</option>
            <option
              v-for="subject in availableSubjects"
              :key="subject"
              :value="subject"
              class="bg-[#212A51] text-white"
            >
              {{ subject }}
            </option>
          </select>
        </div>
        <input type="hidden" name="subject_expertise" :value="selectedSubjects.join(',')" />
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      initialSkills: {
        type: Array,
        default: () => [],
      },
      availableSkills: {
        type: Array,
        default: () => [],
      },
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
        selectedSkills: [...this.initialSkills],
        selectedSubjects: [...this.initialSubjects],
        newSkill: "",
        newSubject: "",
      };
    },
    methods: {
      addSkill() {
        if (!this.newSkill || this.selectedSkills.includes(this.newSkill)) {
          alert("Skill already selected or invalid.");
          this.newSkill = "";
          return;
        }
        this.selectedSkills.push(this.newSkill);
        this.newSkill = "";
      },
      removeSkill(skill) {
        this.selectedSkills = this.selectedSkills.filter((s) => s !== skill);
      },
      addSubject() {
        if (!this.newSubject || this.selectedSubjects.includes(this.newSubject)) {
          alert("Subject already selected or invalid.");
          this.newSubject = "";
          return;
        }
        this.selectedSubjects.push(this.newSubject);
        this.newSubject = "";
      },
      removeSubject(subject) {
        this.selectedSubjects = this.selectedSubjects.filter((s) => s !== subject);
      },
    },
  };
  </script>
  
  <style scoped>
  /* Add custom styles if needed */
  </style>
  