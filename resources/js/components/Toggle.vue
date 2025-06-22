<template>
  <slot 
    :state="state" 
    :toggle="toggle"
    :is-active="state"
  >
    <!-- 默认内容 -->
  </slot>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  initial: {
    type: Boolean,
    default: false
  },
  modelValue: {
    type: Boolean,
    default: undefined
  },
  disabled: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'toggled', 'on', 'off', 'change'])

const state = ref(props.initial)

watch(() => props.modelValue, (newVal) => {
  if (newVal !== undefined && newVal !== state.value) {
    state.value = newVal
  }
})

  const toggle = (newState) => {

    if (props.disabled) {
      console.log('Toggle is disabled, returning')
      return
    }

    const nextState = newState === undefined ? !state.value : newState

    state.value = nextState

    emit('update:modelValue', nextState)
    emit('toggled', nextState)
    emit(nextState ? 'on' : 'off')
    emit('change', nextState)

  }

defineExpose({
  toggle,
  state
})
</script>