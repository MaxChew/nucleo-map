<template>
    <!-- Header -->
    <div class="h-full">
        <div v-if="this.$root.dateSchedule && Object.keys(this.$root.dateSchedule).length">
            <!-- All TimeSlot, Admin View-->
            <div v-if="!shouldShowSingleDate" class="sticky top-0 px-4">
                <div v-if="!shouldShowSingleDate" class="flex p-4 justify-center items-center space-x-3 md:space-x-6">
                    <div @click="prevPage" class="cursor-pointer" :disabled="currentPage === 0">
                        <i class="fas fa-chevron-left"></i>
                    </div>
                    <div class="overflow-x-auto flex space-x-2 md:space-x-6">
                        <div v-for="(session, index) in allSessions" :key="'all-time-' + index" :class="[
                            'px-2 py-1 md:px-4 md:py-2 text-xs md:text-sm text-center font-semibold rounded-lg cursor-pointer transition-all duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]',
                            currentSessionTimes.includes(session) ? 'bg-primary-yellow dark:bg-primary-yellow text-black' : 'bg-gray-200 dark:!bg-gray-700 text-black dark:text-white'
                        ]" @click="goToSession(index)">
                            {{ session }}
                        </div>
                    </div>

                    <div @click="nextPage" class="cursor-pointer"
                        :disabled="currentPage + itemsPerPage >= allSessions.length">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>

                <!-- Sticky Top TimeSlot -->
                <div v-if="!shouldShowSingleDate" class="drawerSchedule-row-wraper !border-0 mb-0">
                    <div class="col-span-1"></div>
                    <div v-for="(session, index) in paginatedSessions" :key="'time-' + index"
                        class="border border-opacity-20 border-gray-200 dark:border-gray-700 rounded">
                        <div class="grid grid-cols-3">
                            <div v-for="hour in getSessionHours(session.time)" :key="'hour-' + hour"
                                class="border-t last:border-r-0 border-r border-opacity-20 border-gray-200 dark:border-gray-700 p-2 text-center">
                                <p class="text-xs md:text-sm text-gray-600 dark:text-gray-200">{{ hour }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 pb-6 overflow-x-hidden">
                <!-- Admin/Client view -->
                <div v-if="!shouldShowSingleDate">
                    <!-- Rows -->
                    <div v-for="(sessions, tutor) in this.$root.dateSchedule" :key="tutor"
                        class="drawerSchedule-row-wraper border-t border-opacity-20 border-gray-200 dark:border-gray-700">
                        <div class="col-span-1">
                            <h4 class="text-[14px] md:text-[16px] font-bold text-center">{{ tutor }}</h4>
                        </div>
                        <!-- Columns 2, 3, 4: Session Details -->
                        <div v-for="(session, index) in paginatedSessions" :key="'detail-' + index"
                            class="relative border-l border-opacity-20 border-gray-200 dark:border-gray-700 h-[60px]">

                            <div v-for="classDetail in sessions[session.time]" :key="classDetail.code">
                                <a href="#"
                                    @click.prevent="$root.isLessonDrawerOpen = true; $root.classDetailObj = classDetail;"
                                    class="drawerSchedule-classInfoWrapper group h-full"
                                    :style="getAppleStyleOverlay(classDetail, session.time)">
                                    <div class="w-full overflow-hidden">
                                        <div class="drawerSchedule-classInfo-apple">
                                            <span class="w-2 h-2 rounded-full mr-1.5 flex-shrink-0" :class="getDotClass(classDetail.status)"></span>
                                            <span>{{ classDetail.subject_name }}</span>
                                        </div>
                                    </div>
                                    <div class="drawerSchedule-tooltip left-[-140px]">
                                        <p>{{ classDetail.time }}</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Non-admin view -->
                <div v-if="shouldShowSingleDate" class="pt-6">
                    {{ console.log('sessions', this.$root.dateSchedule) }}
                    <div v-for="(sessions, time) in this.$root.dateSchedule" :key="time" class="">
                        <div class="flex rounded">
                            <!-- Left side: Time -->
                            <div
                                class="w-[35%] md:w-[30%] flex justify-start items-center border-t border-opacity-20 border-gray-200 dark:border-gray-700">
                                <p class="text-sm md:text-base ml-2 font-semibold">{{ time }}</p>
                            </div>
                            <!-- Right side: Sessions -->
                            
                            <div class="w-[65%] md:w-[70%] relative">
                                <div class="flex flex-col">
                                    <div v-for="hour in getSessionHours(time)" :key="'hour-' + hour"
                                        class="border-t border-l border-opacity-20 border-gray-200 dark:border-gray-700 h-[60px]">
                                        <p
                                            class="absolute left-[-33px] mt-[5px] text-[11px] md:text-[12px] text-gray-400">
                                            {{ hour }}</p>
                                    </div>
                                    <div v-for="classDetail in sessions" :key="classDetail.code" class="">
                                        <a href="#" class="drawerSchedule-classInfoWrapper group w-full"
                                            @click.prevent="$root.isLessonDrawerOpen = true; $root.classDetailObj = classDetail;"
                                            :style="getAppleStyleOverlay(classDetail, time)">
                                            <div class="drawerSchedule-classInfo-apple">
                                                <span class="w-2 h-2 rounded-full mr-1.5 flex-shrink-0" :class="getDotClass(classDetail.status)"></span>
                                                <span>{{ classDetail.subject_name }}</span>
                                                <span class="text-xs ml-1" v-if="classDetail.student">{{ classDetail.student.name }}</span>
                                            </div>
                                            <div class="drawerSchedule-tooltip left-[-15px]">
                                                <p>{{ classDetail.time }}</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="h-[90%] flex flex-col items-center gap-[20px] justify-center py-10">
            <i class="fas fa-map-marker-exclamation text-[120px]"></i>
            <p class="text-base md:text-lg">No Class Found</p>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        showSingleDate: {
            type: Boolean,
            default: false
        }
    },
    data() {
        const itemsPerPageMobile = 2;
        const itemsPerPageDesktop = 3;
        return {
            currentPage: 0,
            itemsPerPageMobile,
            itemsPerPageDesktop,
            itemsPerPage: null,
            colorPalette: [
                "#4F709C", "#BCCCDC", "#81BFDA", "#B3C8CF",
                "#508C9B", "#D1D8C5", "#DED0B6", "#B6C4B6",
                "#B6BBC4", "#B0A695", "#4F709C"
            ],
            // 状态颜色映射
            statusColors: {
                'ACTIVE': 'rgba(96, 165, 250, 0.7)',
                'LIVE': 'rgba(34, 197, 94, 0.7)',
                'DONE': 'rgba(75, 85, 99, 0.7)',
                'RESCHEDULING': 'rgba(245, 158, 11, 0.7)',
                'PENDING': 'rgba(234, 179, 8, 0.7)',
                'REJECTED': 'rgba(239, 68, 68, 0.7)',
                'PENDING_REPORT': 'rgba(250, 204, 21, 0.7)',
                'COMPLETED': 'rgba(55, 65, 81, 0.7)',
                'DROP': 'rgba(156, 163, 175, 0.7)',
                'DELETED': 'rgba(185, 28, 28, 0.7)',
                'PENDING_APPROVED': 'rgba(217, 119, 6, 0.7)',
                'PENDING_PAYMENT': 'rgba(180, 83, 9, 0.7)',
                'EXPIRED': 'rgba(107, 114, 128, 0.7)',
                'default': 'rgba(96, 165, 250, 0.7)'
            },
            // 圆点颜色映射
            dotClasses: {
                'ACTIVE': 'bg-blue-500',
                'LIVE': 'bg-green-600',
                'DONE': 'bg-gray-700',
                'RESCHEDULING': 'bg-amber-600',
                'PENDING': 'bg-yellow-600',
                'REJECTED': 'bg-danger-600',
                'PENDING_REPORT': 'bg-yellow-500',
                'COMPLETED': 'bg-white',
                'DROP': 'bg-gray-500',
                'DELETED': 'bg-danger-600',
                'PENDING_APPROVED': 'bg-amber-600',
                'PENDING_PAYMENT': 'bg-amber-700',
                'EXPIRED': 'bg-gray-600',
            }
        };
    },
    mounted() {
        this.itemsPerPage = this.isMobileScreen ? this.itemsPerPageMobile : this.itemsPerPageDesktop;
    },
    watch: {
        screenSize() {
            this.itemsPerPage = this.isMobileScreen ? this.itemsPerPageMobile : this.itemsPerPageDesktop;
        },
    },
    computed: {
        allSessions() {
            return [...new Set(Object.keys(this.$root.dateSchedule).flatMap(tutor => Object.keys(this.$root.dateSchedule[tutor])))];
        },
        paginatedSessions() {
            const start = this.currentPage;
            return this.allSessions.slice(start, start + this.itemsPerPage).map(time => ({ time }));
        },
        currentSessionTimes() {
            return this.paginatedSessions.map(session => session.time);
        },
        // 动态计算是否应该显示单日期模式
        shouldShowSingleDate() {
            if (this.showSingleDate) {
                return true;
            } else {
                if (this.$root.app_domain === 'tutor') {
                    return true;
                } else if (this.$root.app_domain === 'admin' || this.$root.app_domain === 'user') {
                    return false;
                } else {
                    return false;
                }
            }
        },
    },
    methods: {
        getSessionHours(sessionTime) {
            const [startTime, endTime] = sessionTime.split('-');

            console.log('sessionTime', sessionTime)
            const startHour = this.convertTo12HourFormat(startTime.trim());
            const endHour = this.convertTo12HourFormat(endTime.trim());

            let hours = [];
            for (let hour = startHour; hour <= endHour; hour++) {
                if (hour <= endHour) {
                    hours.push(this.formatHour(hour));
                }
            }

            return hours;
        },

        convertTo12HourFormat(time) {
            const match = time.match(/(\d{1,2})(am|pm)/);
            if (!match) {
                return null;
            }

            let hour = parseInt(match[1]);
            const period = match[2];

            if (period === "pm" && hour !== 12) {
                hour += 12;
            }

            if (period === "am" && hour === 12) {
                hour = 0;
            }

            return hour;
        },

        parseTimeToHour(time) {
            let { hour, ampm } = time;
            if (ampm === 'pm' && hour !== 12) {
                hour += 12;
            } else if (ampm === 'am' && hour === 12) {
                hour = 0;
            }
            return hour;
        },

        formatHour(hour) {
            const ampm = hour >= 12 ? 'pm' : 'am';
            const hour12 = hour % 12 || 12;
            return `${hour12}${ampm}`;
        },
        nextPage() {
            if (this.currentPage + this.itemsPerPage < this.allSessions.length) {
                this.currentPage++;
            }
        },
        prevPage() {
            if (this.currentPage > 0) {
                this.currentPage--;
            }
        },
        goToSession(index) {
            this.currentPage = Math.min(index, this.allSessions.length - this.itemsPerPage);
        },
        getDotClass(status) {
            return this.dotClasses[status] || 'bg-blue-500';
        },
        getAppleStyleOverlay(classDetail, sessionTime) {
            const baseStyle = this.getOverlayStyle(classDetail, sessionTime);
            
            return {
                ...baseStyle,
                backgroundColor: '#1e2748', // 深蓝色背景，类似于图片中的背景色
                borderRadius: '6px',
                border: '1px solid rgba(255, 255, 255, 0.1)', // 轻微的白色边框
                color: '#ffffff' // 白色文字
            };
        },
        getTextColorForStatus(status) {
            // 所有状态使用统一白色文字
            return '#ffffff';
        },
        getOverlayStyle(classDetail, sessionTime) {
            const convertToMinutes = (time) => {
                if (!time || typeof time !== 'string') {
                    return 0;
                }

                const regex = /(\d{1,2}):?(\d{2})?(am|pm)/i;
                const matches = time.match(regex);
                if (!matches) {
                    return 0;
                }

                let [_, hour, minute, amPm] = matches;
                if (!minute) minute = '00';

                hour = parseInt(hour, 10);
                minute = parseInt(minute, 10);

                if (amPm === 'pm' && hour !== 12) {
                    hour += 12;
                } else if (amPm === 'am' && hour === 12) {
                    hour = 0;
                }

                return hour * 60 + minute;
            };
            console.log('sessionTime', sessionTime)
            console.log('classDetail', classDetail)
            const [startSessionTime, endSessionTime] = sessionTime.split('-');
            const sessionStartTimeInMinutes = convertToMinutes(startSessionTime.trim());
            const sessionEndTimeInMinutes = convertToMinutes(endSessionTime.trim());
            const sessionDuration = sessionEndTimeInMinutes - sessionStartTimeInMinutes + 60;

            const [classStartTime, classEndTime] = classDetail.time.split('-');

            const classStartTimeInMinutes = convertToMinutes(classStartTime.trim());
            const classEndTimeInMinutes = convertToMinutes(classEndTime.trim());
            const classDuration = classEndTimeInMinutes - classStartTimeInMinutes;

            if (sessionDuration <= 0) {
                console.error('Invalid session duration (zero or negative duration)');
                return {};
            }

            const leftPercentage = ((classStartTimeInMinutes - sessionStartTimeInMinutes) / sessionDuration) * 100;
            const widthPercentage = (classDuration / sessionDuration) * 100;

            const colorIndex = classDetail.code
                ? parseInt(classDetail.code.replace(/\D/g, ""), 10) % this.colorPalette.length
                : 0;

            const backgroundColor = this.colorPalette[colorIndex];

            if (!this.shouldShowSingleDate) {
                return {
                    left: `${leftPercentage}%`,
                    width: `${widthPercentage}%`,
                    backgroundColor: backgroundColor,

                };
            } else {
                const topPercentage = classStartTimeInMinutes - sessionStartTimeInMinutes;
                const heightPercentage = (classDuration / sessionDuration) * 100;
                return {
                    top: `${topPercentage}px`,
                    height: `${heightPercentage}%`,
                    backgroundColor: backgroundColor,
                };
            }
        },

    },
};
</script>

<style>
.drawerSchedule-row-wraper {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    align-items: center;
    border-radius: 0.25rem;
}

@media (min-width: 768px) {
    .drawerSchedule-row-wraper {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}

.drawerSchedule-session-wraper {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.drawerSchedule-tooltip {
    position: absolute;
    background-color: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
    z-index: 50;
    top: -30px;
}

.drawerSchedule-classInfoWrapper {
    position: absolute;
    height: auto;
    width: 100%;
    transition: all 0.3s ease;
    overflow: hidden;
}

.drawerSchedule-classInfoWrapper:hover .drawerSchedule-tooltip {
    opacity: 1;
    visibility: visible;
}

.drawerSchedule-classInfoWrapper:hover {
    transform: scale(1.02);
    z-index: 10;
}

/* 新的Apple Calendar风格类 */
.drawerSchedule-classInfo-apple {
    display: flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    height: 100%;
}

/* 针对有圆点的项目添加一点左内边距 */
.drawerSchedule-classInfo-apple {
    padding-left: 0.5rem;
}
</style>
