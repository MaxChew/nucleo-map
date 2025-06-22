<template>
    <div class="relative">
        <div @click="togglePicker"
            class="form-control-border flex justify-between items-center py-[8px] px-[12px] rounded-[8px] cursor-pointer !bg-bg-dark !text-text"
            :style="{ cursor: refer ? 'not-allowed' : 'pointer' }">
            <span>{{ selectedTime || 'Select time' }}</span>
            <i class="icon-clock"></i>
        </div>

        <div v-if="isPickerOpen"
            class="absolute top-full left-0 w-[200px] bg-bg-dark border border-gray-400 rounded-[6px] p-[8px] z-[100]">
            <div class="flex justify-between">
                <div class="timePicker-column">
                    <div v-for="hour in hours" :key="hour" @click="selectHour(hour)"
                        :class="{ selected: hour === selectedHour }">
                        {{ hour }}
                    </div>
                </div>
                <div class="timePicker-column">
                    <div v-for="minute in minutes" :key="minute" @click="selectMinute(minute)"
                        :class="{ selected: minute === selectedMinute }">
                        {{ minute }}
                    </div>
                </div>
                <div class="timePicker-column">
                    <div v-for="period in periods" :key="period" @click="selectPeriod(period)"
                        :class="{ selected: period === selectedPeriod }">
                        {{ period }}
                    </div>
                </div>
            </div>
            <div class="flex justify-between mt-[15px]">
                <div class="btn" @click.stop="cancelSelection">Cancel</div>
                <div class="btn" @click.stop="confirmSelection">OK</div>
            </div>
        </div>

        <input type="hidden" :name="name" :value="selectedTime24" />
    </div>
</template>

<script>
export default {
    name: "TimePicker",
    props: {
        modelValue: {
            type: String,
            default: '',
        },
        refer: {
            type: String,
            default: '',
        },
        bechanged: {
            type: Boolean,
            default: false,
        },
        isdisable: {
            type: Boolean,
            default: false,
        },
        name: String,
    },
    data() {
        return {
            isPickerOpen: false,
            selectedTime: this.modelValue || '',
            selectedHour: null,
            selectedMinute: null,
            selectedPeriod: null,
            hours: Array.from({ length: 12 }, (_, i) => String(i + 1).padStart(2, "0")),
            minutes: Array.from({ length: 60 }, (_, i) => String(i).padStart(2, "0")),
            periods: ["AM", "PM"],
        };
    },
    computed: {
        // Converts the selected time to 24-hour format (HH:mm)
        selectedTime24() {
            if (this.selectedHour && this.selectedMinute !== null && this.selectedPeriod) {
                let hour = this.selectedHour;
                let minute = this.selectedMinute;

                hour = Number(hour);

                if (this.selectedPeriod === 'PM' && hour !== 12) {
                    hour += 12;
                } else if (this.selectedPeriod === 'AM' && hour === 12) {
                    hour = 0;
                }

                return `${hour}:${minute}`
            }
            return '';
        }
    },
    watch: {
        modelValue(newTime) {
            this.updateSelectedTime(newTime);
        },
        "$root.object.start_time": {
            handler(newStartTime) {
                if (newStartTime) {
                    this.updateSelectedTime(newStartTime);
                    if (this.bechanged && this.$root.object.total_minutes) {
                        this.addMinutesToTime(this.$root.object.total_minutes);
                    }
                }
            },
            immediate: true,
        },
        "$root.rowModal.start_time": {
            handler(newStartTime) {
                if (newStartTime) {
                    this.updateSelectedTime(newStartTime);
                    if (this.bechanged && this.$root.rowModal.total_minutes) {
                        this.addMinutesToTime(this.$root.rowModal.total_minutes);
                    }
                }
            },
            immediate: true,
        },
        "$root.classDetailObj.start_time": {
            handler(newStartTime) {
                if (newStartTime) {
                    this.updateSelectedTime(newStartTime);
                    if (this.bechanged && this.$root.classDetailObj.total_minutes) {
                        this.addMinutesToTime(this.$root.classDetailObj.total_minutes);
                    }
                }
            },
            immediate: true,
        },
        "$root.object.total_minutes": {
            handler(newValue) {
                if (newValue) {
                    this.clearSelectedTime();
                }
            }
        },
        "$root.rowModal.total_minutes": {
            handler(newValue) {
                if (newValue) {
                    this.clearSelectedTime();
                }
            }
        },
        "$root.classDetailObj.total_minutes": {
            handler(newValue) {
                if (newValue && this.bechanged && this.refer) {
                    this.addMinutesToTime(parseInt(newValue));
                }
            }
        },
        "$root.newLessonObj.start_time": {
            handler(newStartTime) {
                if (newStartTime) {
                    this.updateSelectedTime(newStartTime);
                    if (this.bechanged && this.$root.newLessonObj.total_minutes) {
                        this.addMinutesToTime(this.$root.newLessonObj.total_minutes);
                    }
                }
            },
            immediate: true,
        },
        "$root.newLessonObj.total_minutes": {
            handler(newValue) {
                if (newValue && this.bechanged && this.refer) {
                    this.addMinutesToTime(parseInt(newValue));
                }
            }
        }
    },
    methods: {
        togglePicker() {
            if (this.isdisable) {
                this.isPickerOpen = false;
                return;
            }
            this.isPickerOpen = !this.isPickerOpen;
        },
        selectHour(hour) {
            this.selectedHour = hour;
        },
        selectMinute(minute) {
            this.selectedMinute = minute;
        },
        selectPeriod(period) {
            this.selectedPeriod = period;
        },
        cancelSelection() {
            this.selectedHour = null;
            this.selectedMinute = null;
            this.selectedPeriod = null;
            this.isPickerOpen = false;
        },
        confirmSelection() {
            if (this.selectedHour && this.selectedMinute && this.selectedPeriod) {
                const formattedTime = `${this.selectedHour}:${this.selectedMinute}${this.selectedPeriod.toLowerCase()}`;
                this.selectedTime = formattedTime;
                this.$emit('update:modelValue', formattedTime);
                this.isPickerOpen = false;
            }
        },
        updateSelectedTime(time) {
            console.log('updateSelectedTime called with:', time);
            if (time) {
                // 使用更精确的正则表达式来匹配时间格式
                const timeRegex = /^(\d{1,2}):(\d{2})(am|pm)$/i;
                const matches = time.match(timeRegex);
                
                if (matches) {
                    const [, hour, minute, period] = matches;
                    // 确保小时格式与 hours 数组中的格式一致（两位数）
                    this.selectedHour = hour.padStart(2, '0');
                    this.selectedMinute = minute;
                    this.selectedPeriod = period.toUpperCase();
                    
                    // 更新显示的时间
                    const formattedTime = `${this.selectedHour}:${this.selectedMinute}${this.selectedPeriod.toLowerCase()}`;
                    this.selectedTime = formattedTime;
                    
                    console.log('Parsed time:', {
                        hour: this.selectedHour,
                        minute: this.selectedMinute,
                        period: this.selectedPeriod,
                        formattedTime: this.selectedTime,
                        hoursArray: this.hours,
                        hourExists: this.hours.includes(this.selectedHour)
                    });
                } else {
                    console.warn('Failed to parse time format:', time);
                }
            }
        },
        addMinutesToTime(minutesToAdd) {
            let hour, minute, period;

            if (this.refer) {
                const [time, amPm] = this.refer.match(/(\d{1,2}:\d{2})(am|pm)/i).slice(1, 3);
                const [referHour, referMinute] = time.split(':');
                hour = parseInt(referHour);
                minute = parseInt(referMinute);
                period = amPm.toUpperCase();
            } else {
                console.warn("No valid time or refer value provided.");
                return;
            }

            if (period === "PM" && hour !== 12) {
                hour += 12;
            } else if (period === "AM" && hour === 12) {
                hour = 0;
            }

            const totalMinutes = hour * 60 + minute + minutesToAdd;

            const adjustedHour = Math.floor((totalMinutes % 720) / 60) || 12; // Wrap around to 12-hour format
            const adjustedMinute = totalMinutes % 60;
            const adjustedPeriod = Math.floor(totalMinutes / 720) % 2 === 0 ? "AM" : "PM";

            this.selectedHour = String(adjustedHour).padStart(2, "0");
            this.selectedMinute = String(adjustedMinute).padStart(2, "0");
            this.selectedPeriod = adjustedPeriod;

            // Confirm the selection
            this.confirmSelection();

        },
        clearSelectedTime() {
            this.selectedHour = null;
            this.selectedMinute = null;
            this.selectedPeriod = null;
            this.selectedTime = '';
            this.$emit('update:modelValue', '');
        },
    },
    mounted() {
        // 检查多个可能的数据源
        if (this.$root.classDetailObj?.start_time) {
            this.updateSelectedTime(this.$root.classDetailObj.start_time);
            if (this.bechanged && this.$root.classDetailObj.total_minutes) {
                this.addMinutesToTime(this.$root.classDetailObj.total_minutes);
            }
        } else if (this.$root.newLessonObj?.start_time) {
            this.updateSelectedTime(this.$root.newLessonObj.start_time);
            if (this.bechanged && this.$root.newLessonObj.total_minutes) {
                this.addMinutesToTime(this.$root.newLessonObj.total_minutes);
            }
        } else if (this.$root.object?.start_time) {
            this.updateSelectedTime(this.$root.object.start_time);
            if (this.bechanged && this.$root.object.total_minutes) {
                this.addMinutesToTime(this.$root.object.total_minutes);
            }
        }
    },
};
</script>

<style scoped>
/* Add your custom styles here */
</style>
