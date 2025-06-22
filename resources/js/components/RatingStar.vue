<template>
  <div class="flex items-center gap-2" :class="cssclass">
    <div
      v-for="value in range"
      :key="value"
      @click="editable && rate(value)"
      :class="[
        'w-8 h-8 flex items-center justify-center transition-colors font-semibold',
        value <= currentRating ? 'text-warning-400' : 'text-gray-400',
        editable ? 'hover:text-warning-500 cursor-pointer' : ''
      ]"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="currentColor"
        viewBox="0 0 24 24"
        class="w-6 h-6"
      >
        <path
          d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"
        />
      </svg>
    </div>
  </div>
  <input type="hidden" :name="name" :value="currentRating" />
</template>

<script>
export default {
  props: {
    start: {
      type: Number,
      required: true,
    },
    end: {
      type: Number,
      required: true,
    },
    modelValue: {
      type: Number,
      default: null,
    },
    name: String,
    cssclass: String,
    editable: {
      type: Boolean,
      default: true, // Defaults to true (interactive mode)
    },
  },
  computed: {
    range() {
      return Array.from({ length: this.end - this.start + 1 }, (_, i) => this.start + i);
    },
    currentRating() {
      return this.modelValue;
    },
  },
  methods: {
    rate(value) {
      if (this.editable) {
        if (this.modelValue === value) {
          this.$emit("update:modelValue", null);
        } else {
          this.$emit("update:modelValue", value);
        }
      }
    },
  },
};
</script>
