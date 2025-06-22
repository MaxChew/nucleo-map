<template>
  <div class="color-picker-wrapper">
    <color-picker v-model:pureColor="pureColor" format="hex" shape="circle" />
  </div>
</template>

<script>
import { ColorPicker } from 'vue3-colorpicker';
import "vue3-colorpicker/style.css";

export default {
  components: {
    'color-picker': ColorPicker,
  },
  props: {
    modelValue: {
      type: String,
      default: '#FFFFFF',
    },
  },
  computed: {
    pureColor: {
      get() {
        return this.modelValue;
      },
      set(value) {
        this.$emit('update:modelValue', value);
      }
    }
  }
};
</script>

<style scoped>
.color-picker-wrapper {
  display: inline-block;
  position: relative;
}

.color-input {
  padding: 8px;
  width: 120px;
  margin-right: 10px;
  cursor: pointer;
  border-radius: 4px;
  border: 1px solid #ccc;
}
</style>