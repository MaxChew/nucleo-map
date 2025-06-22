<template>
    <vue-select 
        :name="name" 
        :no-empty="noEmpty" 
        :placeholder="placeholder" 
        :options="countryList" 
        :model-value="modelValue"
        group-by="region" 
        empty-option 
        @update:model-value="updateValue"
    />
</template>

<script setup>
import { ref, watch, onMounted, nextTick } from 'vue'
import axios from 'axios'
import { passport_url } from '@/libs/helpers'
import _ from 'lodash'

// Props 定义
const props = defineProps({
    name: String,
    placeholder: String,
    modelValue: [String, Number], // 替换原来的 value
    list: [Array, Object],
    noEmpty: Boolean,
})

// Emits 定义
const emit = defineEmits(['update:model-value'])

// 响应式状态
const currentValue = ref(null)
const countryList = ref(props.list || [])

// 方法定义
const updateValue = (countryId) => {
    emit('update:model-value', countryId === '' ? null : _.toInteger(countryId))
}

// 监听器
watch(() => props.modelValue, (newValue) => {
    currentValue.value = newValue
})

// 生命周期钩子
onMounted(async () => {
    if (!props.list) {
        try {
            const { data: result } = await axios.get(passport_url('shared/public/countries'))
            countryList.value = result.data
            await nextTick()
            currentValue.value = props.modelValue
        } catch (error) {
            console.error('获取国家列表失败:', error)
        }
    } else {
        currentValue.value = props.modelValue
    }
})
</script>