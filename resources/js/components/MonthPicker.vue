<template>
    <div class="relative">
        <!-- Input Field -->
        <div @click.prevent.stop="togglePicker" class="flex items-center px-3 py-2 form-control">
            <input :name="name" :value="displayValue" readonly
                class="w-full focus:outline-none !bg-transparent cursor-pointer" />
            <button  type="button" class="ml-2">
                <i class="far fa-calendar-alt"></i>
            </button>
        </div>

        <!-- Picker -->
        <div v-if="showPicker" class="absolute mt-1 border rounded-lg shadow-lg z-10 p-4 w-72 bg-bg">
            <!-- Year Control -->
            <div class="flex items-center justify-between mb-2">
                <button @click.prevent="prevYear" class="px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-md">
                    &lt;
                </button>
                <span class="font-medium">{{ selectedYear }}</span>
                <button @click.prevent="nextYear" class="px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-md">
                    &gt;
                </button>
            </div>

            <!-- Month Grid -->
            <div class="grid grid-cols-4 gap-2">
                <button v-for="(month, index) in months" :key="index" @click="selectMonth(index + 1)" :class="[
                    'px-3 py-2 rounded-md text-sm border border-gray-300 dark:border-gray-500',
                    selectedMonth === index + 1
                        ? 'bg-primary dark:bg-gray-200 text-white dark:text-black'
                        : 'text-gray-700 dark:text-gray-200 hover:dark:text-black hover:bg-gray-100',
                    isBeforeDisabled(index + 1) || isFutureDisabled(index + 1)
                        ? 'text-gray-400 cursor-not-allowed'
                        : 'cursor-pointer'
                ]" :disabled="isBeforeDisabled(index + 1) || isFutureDisabled(index + 1)">
                    {{ month }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        modelValue: {
            type: String, // Accept as "YYYY-MM"
            required: true
        },
        name: {
            type: String,
            required: true
        },
        disableBefore: {
            type: Boolean,
            default: false
        },
        disableFuture: {
            type: Boolean,
            default: false
        }
    },
    data() {
        const [year, month] = this.modelValue?.split('-') || [
            new Date().getFullYear(),
            String(new Date().getMonth() + 1).padStart(2, '0')
        ]
        return {
            showPicker: false,
            selectedYear: Number(year),
            selectedMonth: Number(month),
            months: [
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
            ]
        }
    },
    computed: {
        displayValue() {
            return `${this.selectedMonth}/${this.selectedYear}`
        }
    },
    methods: {
        togglePicker() {
            console.log("this.showPicker before", this.showPicker)
            this.showPicker = !this.showPicker
            console.log("this.showPicker before", this.showPicker)
        },
        prevYear() {
            this.selectedYear--
        },
        nextYear() {
            this.selectedYear++
        },
        selectMonth(month) {
            if (!this.isBeforeDisabled(month) && !this.isFutureDisabled(month)) {
                this.selectedMonth = month
                this.updateValue()
                this.showPicker = false
            }
        },
        updateValue() {
            const formattedValue = `${this.selectedYear}-${String(this.selectedMonth).padStart(2, '0')}`
            this.$emit('update:modelValue', formattedValue)
        },
        isBeforeDisabled(month) {
            if (!this.disableBefore) return false
            const currentDate = new Date()
            return (
                this.selectedYear < currentDate.getFullYear() ||
                (this.selectedYear === currentDate.getFullYear() &&
                    month < currentDate.getMonth() + 1)
            )
        },
        isFutureDisabled(month) {
            if (!this.disableFuture) return false
            const currentDate = new Date()
            return (
                this.selectedYear > currentDate.getFullYear() ||
                (this.selectedYear === currentDate.getFullYear() &&
                    month > currentDate.getMonth() + 1)
            )
        }
    }
}
</script>

<style scoped>
/* Tailwind styles are already applied */
</style>