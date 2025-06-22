<template>
  <div class="number-input-wrapper">
    <!-- 调试信息 -->
    <div v-if="false" class="text-xs text-gray-500 mb-2">
      Props value: {{ value }} ({{ typeof value }})<br>
      Input value: {{ inputValue }} ({{ typeof inputValue }})
    </div>
    
    <!-- 主要输入区域 -->
    <div class="flex items-center relative">
      <span v-if="prefix" class="mr-2 text-gray-600">{{ prefix }}</span>
      <div class="relative" :class="width ? width : 'flex-grow'">
        <!-- 当showControls=false时使用文本输入，否则使用数字输入 -->
        <template v-if="showControls">
          <input
            type="number"
            :name="name"
            :id="id"
            :placeholder="placeholder"
            :min="min"
            :max="max"
            :step="step"
            :disabled="disabled"
            :required="required"
            v-model="inputValue"
            @input="updateValue"
            class="form-control !mt-0 w-full pr-8 no-spinner"
            :class="{ 'border-red-500': error }"
          />
          <div class="absolute inset-y-0 right-0 flex flex-col h-full">
            <button
              type="button"
              @click="increment"
              class="flex-1 flex items-center justify-center px-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 focus:outline-none border-l border-gray-300 dark:border-gray-600"
              :disabled="disabled || (max !== null && inputValue >= max)"
            >
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-3 h-3">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
              </svg>
            </button>
            <button
              type="button"
              @click="decrement"
              class="flex-1 flex items-center justify-center px-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 focus:outline-none border-l border-t border-gray-300 dark:border-gray-600"
              :disabled="disabled || (min !== null && inputValue <= min)"
            >
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-3 h-3">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
          </div>
        </template>
        <template v-else>
          <input
            type="text"
            :name="name"
            :id="id"
            :placeholder="placeholder"
            :disabled="disabled"
            :required="required"
            v-model="inputValue"
            @input="updateValue"
            @keydown="handleKeydown"
            class="form-control w-full"
            :class="{ 'border-red-500': error }"
          />
        </template>
      </div>
      <span v-if="suffix" class="ml-2 text-gray-600">{{ suffix }}</span>
    </div>
    
    <!-- 预设值快速选择 -->
    <div v-if="presets && presets.length" class="mt-2 flex flex-wrap gap-2">
      <button
        v-for="preset in presets"
        :key="preset"
        type="button"
        @click="selectPreset(preset)"
        class="px-2 py-1 text-xs rounded bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 focus:outline-none transition-colors"
        :class="{ 'bg-primary text-white hover:bg-primary-dark dark:bg-primary dark:hover:bg-primary-dark': inputValue === preset }"
      >
        {{ preset }}
      </button>
    </div>

    <!-- 错误信息 -->
    <span v-if="error" class="text-danger-500 text-xs mt-1">{{ error }}</span>
  </div>
</template>

<script>
export default {
  name: 'NumberInput',
  props: {
    value: {
      type: [Number, String],
      default: null
    },
    name: {
      type: String,
      default: ''
    },
    id: {
      type: String,
      default: ''
    },
    placeholder: {
      type: String,
      default: 'Enter value'
    },
    min: {
      type: Number,
      default: null
    },
    max: {
      type: Number,
      default: null
    },
    step: {
      type: Number,
      default: 1
    },
    disabled: {
      type: Boolean,
      default: false
    },
    required: {
      type: Boolean,
      default: false
    },
    prefix: {
      type: String,
      default: ''
    },
    suffix: {
      type: String,
      default: ''
    },
    presets: {
      type: Array,
      default: () => []
    },
    error: {
      type: String,
      default: ''
    },
    width: {
      type: String,
      default: ''
    },
    showControls: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      inputValue: this.parseValue(this.value)
    }
  },
  watch: {
    value: {
      handler(newVal) {
        this.inputValue = this.parseValue(newVal)
      },
      immediate: true
    }
  },
  mounted() {
    this.inputValue = this.parseValue(this.value)
  },
  methods: {
    parseValue(val) {
      if (val === null || val === undefined || val === '') {
        return ''
      }
      return isNaN(parseFloat(val)) ? '' : parseFloat(val)
    },
    updateValue() {
      let value
      
      if (this.inputValue === '') {
        value = null
      } else {
        // 确保值是数值
        value = parseFloat(this.inputValue)
        if (isNaN(value)) {
          value = null
        }
      }
      
      this.$emit('input', value)
      this.$emit('change', value)
    },
    increment() {
      if (this.disabled) return
      
      let newValue = (parseFloat(this.inputValue) || 0) + (this.step || 1)
      if (this.max !== null && newValue > this.max) {
        newValue = this.max
      }
      this.inputValue = newValue
      this.updateValue()
    },
    decrement() {
      if (this.disabled) return
      
      let newValue = (parseFloat(this.inputValue) || 0) - (this.step || 1)
      if (this.min !== null && newValue < this.min) {
        newValue = this.min
      }
      this.inputValue = newValue
      this.updateValue()
    },
    selectPreset(value) {
      this.inputValue = value
      this.updateValue()
    },
    handleKeydown(event) {
      // 只允许数字和特定键
      const allowed = [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 
        'Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab',
        '.', ',', '-'
      ]
      
      if (!allowed.includes(event.key)) {
        event.preventDefault()
      }
      
      // 限制只能输入一个小数点
      if ((event.key === '.' || event.key === ',') && 
          (this.inputValue.includes('.') || this.inputValue.includes(','))) {
        event.preventDefault()
      }
      
      // 限制负号只能在开头输入
      if (event.key === '-' && this.inputValue.length > 0) {
        event.preventDefault()
      }
    }
  }
}
</script>

<style scoped>
.form-control.no-spinner::-webkit-inner-spin-button,
.form-control.no-spinner::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.form-control.no-spinner {
  -moz-appearance: textfield;
}
</style> 