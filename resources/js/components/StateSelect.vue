<template>
    <vue-select 
        :name="name" 
        :options="stateList" 
        :placeholder="placeholder" 
        :model-value="modelValue"
        empty-option 
        @update:model-value="updateValue" 
        required
    />
</template>

<script setup>
import { ref, watch, onMounted, nextTick } from 'vue'
import axios from 'axios'
import { passport_url } from '@/libs/helpers' // 假设这是你的 helpers 路径

// Props 定义
const props = defineProps({
    name: String,
    placeholder: String,
    modelValue: [String, Number], // Vue 3 中使用 modelValue 替代 value
    country: [String, Number],
    list: [Array, Object],
})

// Emits 定义
const emit = defineEmits(['update:model-value'])

// 响应式状态
const stateList = ref([])
const currentValue = ref(null)

// 方法定义
const updateValue = (stateId) => {
    emit('update:model-value', stateId === '' ? null : stateId)
}

const fetchList = async (country, stateValue = null) => {
    stateList.value = []
    
    if (country) {
        try {
            const { data: result } = await axios.get(
                passport_url(`shared/public/countries/${country}/states`)
            )
            stateList.value = result.data
            await nextTick()
            currentValue.value = stateValue
            updateValue(stateValue)
        } catch (error) {
            console.error('获取州/省列表失败:', error)
        }
    } else {
        await nextTick()
        currentValue.value = stateValue
        updateValue(stateValue)
    }
}

// 监听器
watch(() => props.country, (newCountry) => {
    fetchList(newCountry)
})

watch(() => props.modelValue, (newValue) => {
    currentValue.value = newValue
})

// 生命周期钩子
onMounted(() => {
    if (props.list) {
        stateList.value = props.list
        if (props.modelValue) {
            nextTick(() => {
                currentValue.value = props.modelValue
                updateValue(props.modelValue)
            })
        }
    } else {
        fetchList(props.country, props.modelValue)
    }
})
</script>