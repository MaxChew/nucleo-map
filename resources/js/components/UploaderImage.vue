<!-- UploaderImage.vue -->
<template>
    <div 
        class="relative"
        :class="[containerClass]"
    >
        <!-- 拖拽区域 -->
        <div
            class="relative group cursor-pointer transition-all duration-300"
            :class="[
                shape === 'circle' ? 'rounded-full' : 'rounded-lg',
                {'border-2 border-dashed border-gray-300 hover:border-primary-500': !modelValue}
            ]"
            @drop.prevent="handleDrop"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            :style="computedSize"
        >
            <!-- 预览图片 -->
            <img 
                v-if="modelValue || defaultImage"
                :src="modelValue || defaultImage"
                :alt="alt"
                class="w-full h-full object-cover transition-all duration-300"
                :class="[
                    shape === 'circle' ? 'rounded-full' : 'rounded-lg',
                    {'opacity-50': isDragging || loading}
                ]"
            />

            <!-- 占位内容 -->
            <div 
                v-else
                class="absolute inset-0 flex flex-col items-center justify-center p-4"
                :class="{'bg-gray-50': isDragging}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="mt-2 text-sm text-gray-500">
                    {{ isDragging ? 'Drop image to upload' : 'Drag image or click to upload' }}
                </p>
            </div>

            <!-- 上传进度 -->
            <div 
                v-if="loading" 
                class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-20"
                :class="[shape === 'circle' ? 'rounded-full' : 'rounded-lg']"
            >
                <div class="text-white flex items-center space-x-2">
                    <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span>{{ uploadPercent }}%</span>
                </div>
            </div>

            <!-- 隐藏的文件输入 -->
            <input 
                name="image"
                type="file"
                ref="fileInput"
                class="hidden"
                @change="handleFileSelect"
                accept="image/png, image/jpeg"
                :disabled="loading"
            />

            <!-- 悬浮操作按钮 -->
            <div 
                v-if="!loading" 
                class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                :class="[shape === 'circle' ? 'rounded-full' : 'rounded-lg']"
            >
                <div class="flex space-x-2">
                    <!-- 更换图片按钮 -->
                    <button 
                        @click="triggerFileInput"
                        class="p-2 bg-white rounded-full shadow-lg hover:bg-gray-50"
                        :disabled="loading"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </button>
                    <!-- 删除按钮 -->
                    <button 
                        @click="handleRemove"
                        class="p-2 bg-white rounded-full shadow-lg hover:bg-gray-50"
                        :disabled="loading"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-danger-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { inject, ref, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
    modelValue: String,
    defaultImage: {
        type: String,
        default: '' // 默认图片的 URL
    },
    uploadUrl: {
        type: String,
        required: true
    },
    destroyUrl: {
        type: String,
        required: false,
        default: '', 
    },
    maxSize: {
        type: Number,
        default: 5 * 1024 * 1024 // 5MB
    },
    shape: {
        type: String,
        default: 'square', 
        validator: (value) => ['square', 'circle'].includes(value)
    },
    size: {
        type: [Number, String],  // 允许数字或字符串
        default: 200
    },
    // 如果需要分别控制宽高
    width: {
        type: [Number, String],
        default: 200
    },
    height: {
        type: [Number, String],
        default: 200
    },
    alt: {
        type: String,
        default: 'Uploaded image'
    },
    containerClass: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['update:modelValue', 'error', 'success', 'remove'])

const fileInput = ref(null)
const loading = ref(false)
const uploadPercent = ref(0)
const isDragging = ref(false)
const alert = inject('alert')

// 触发文件选择
const triggerFileInput = () => {
    fileInput.value.click()
}

// 处理文件选择
const handleFileSelect = (event) => {
    const file = event.target.files[0]
    if (file) {
        uploadFile(file)
    }
}

// 处理拖拽
const handleDrop = (event) => {
    isDragging.value = false
    const file = event.dataTransfer.files[0]
    if (file) {
        uploadFile(file)
    }
}

// 处理图片移除
const handleRemove = async () => {
    if (!props.destroyUrl) {
        emit('update:modelValue', '')
        emit('remove')
        alert.success('Image removed successfully')
        return
    }

    try {
        loading.value = true
        const response = await axios.delete(props.destroyUrl)
        emit('update:modelValue', response.data.meta.url)
        emit('remove')
        alert.success('Image deleted successfully')
    } catch (err) {
        const errorMessage = err.response?.data?.message || 'Delete failed, please try again'
        emit('error', errorMessage)
        alert.error(errorMessage)
    } finally {
        loading.value = false
    } 
}

// 上传文件
const uploadFile = async (file) => {
    // 验证文件类型
    if (!['image/jpeg', 'image/png'].includes(file.type)) {
        alert.error('Only JPG and PNG format images are supported')
        emit('error', 'File format not supported')
        return
    }

    // 验证文件大小
    if (file.size > props.maxSize) {
        const maxSizeMB = props.maxSize / 1024 / 1024
        alert.error(`File size cannot exceed ${maxSizeMB}MB`)
        emit('error', 'File too large')
        return
    }

    const formData = new FormData()
    formData.append('image', file)

    loading.value = true

    try {
        const response = await axios.post(props.uploadUrl, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            onUploadProgress: (progressEvent) => {
                uploadPercent.value = Math.round(
                    (progressEvent.loaded * 100) / progressEvent.total
                )
            }
        })

        emit('update:modelValue', response.data.meta.url)
        emit('success', response.data)
        alert.success('Image uploaded successfully')
    } catch (err) {
        const errorMessage = err.response?.data?.message || 'Upload failed, please try again'
        emit('error', errorMessage)
        alert.error(errorMessage)
    } finally {
        loading.value = false
        uploadPercent.value = 0
        if (fileInput.value) {
            fileInput.value.value = null
        }
    }
}

const computedSize = computed(() => {
    const addUnit = (value) => {
        if (typeof value === 'number') {
            return value + 'px'
        }
        // 如果是字符串且包含数字但没有单位，添加 px
        if (typeof value === 'string' && !isNaN(value)) {
            return value + 'px'
        }
        // 如果是百分比或其他带单位的值，直接返回
        return value
    }

    return {
        width: addUnit(props.width || props.size),
        height: addUnit(props.height || props.size)
    }
})
</script>