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

// Props 定义
const props = defineProps({
    name: String,
    placeholder: String,
    modelValue: [String, Number], // 替换原来的 value
    country: [String, Number],
    state: [String, Number],
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

const fetchList = async (country, state, cityValue = null) => {
    stateList.value = []
    
    if (state) {
        try {
            const { data: result } = await axios.get(
                `/api/shared/public/countries/${country}/states/${state}/citys`
            )
            stateList.value = result.data
            await nextTick()
            currentValue.value = cityValue
            updateValue(cityValue)
        } catch (error) {
            console.error('获取城市列表失败:', error)
        }
    } else {
        await nextTick()
        currentValue.value = cityValue
        updateValue(cityValue)
    }
}

// 监听器
watch(() => props.state, (newState) => {
    fetchList(props.country, props.state, props.modelValue)
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
        fetchList(props.country, props.state, props.modelValue)
    }
})
</script>