<template>
    <div class="bg-bg rounded-xl shadow-md p-4 w-full  relative">
        <!-- 头部区域 -->
        <div class="border-b pb-2 mb-2">
            <!-- 刷新控制区域 -->
            <div class="flex justify-end">
                <div class="flex items-center space-x-2 text-xs" v-if="showRefreshControl">
                    <button @click="toggleAutoRefresh" class="px-2 py-1 rounded text-xs transition-colors duration-200"
                        :class="autoRefresh ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-300 hover:bg-gray-400 text-gray-700'">
                        {{ autoRefresh ? 'Auto' : 'Stoped' }}
                    </button>
                    <span class="text-xs text-gray-500">
                        {{ refreshTimeDisplay }}
                        <template v-if="autoRefresh">
                            ({{ countdown }}s)
                        </template>
                    </span>
                </div>
            </div>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold ">{{ title }}</h2>
            </div>
            <div v-if="subtitle">
                <span v-if="subtitle" class="text-xs text-fourth font-semibold">{{ subtitle }}</span>
            </div>
            <div v-if="metasubtitle">
                <span v-if="metasubtitle" class="text-xs text-fourth font-semibold">{{ getPropertyValue(meta,
                    metasubtitle)
                    }}</span>
            </div>
        </div>

        <!-- 列表内容区域 -->
        <div class="mt-3 mb-6 overflow-y-auto max-h-[calc(100vh-200px)] relative text-xs">
            <template v-if="listings.length > 0">
                <div v-for="listing in listings" :key="listing.id"
                    class="mb-3  hover:bg-gray-50 rounded-lg transition-colors duration-200"
                    :class="{ 'opacity-50': isLoading }">
                    <a :href="index_url + '?keyword=' + getPropertyValue(listing, heading)" target="_blank"
                        class="flex justify-between cursor-pointer hover:text-fifth hover:underline">

                        <div class="flex flex-col w-1/2 mt-0">
                            <!-- 主标题 -->
                            <div>
                                <span class="font-semibold">{{ getPropertyValue(listing, heading) }}</span>
                            </div>
                            <!-- 数值显示区域 -->
                            <div class="flex flex-col ">
                                <!-- 副标题 -->
                                <span class="text-xxs text-gray-400" v-if="subheading">{{ getPropertyValue(listing, subheading) }}</span>
                                <!-- 第三标题(可选) -->
                                <template v-if="thirdheading">
                                    <span class="text-xxs text-gray-400"> - {{ getPropertyValue(listing, thirdheading)
                                        }}</span>
                                </template>

                            </div>

                        </div>

                        <div class="flex flex-col items-end mt-0 w-1/2">
                            <div>
                                <span class="text-xs font-semibold">{{ getPropertyValue(listing, number) }}</span>
                            </div>
                            <!-- 数值显示区域 -->
                            <div class="flex flex-col text-right">
                                <span v-if="twonumber" class="text-xxs text-gray-400 font-semibold">
                                    {{ getPropertyValue(listing, twonumber) }}
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            </template>
            <!-- 无数据显示 -->
            <template v-else>
                <div class="flex flex-col items-center justify-center py-8">
                    <p class="text-gray-500 text-center">{{ noResults }}</p>
                </div>
            </template>

            <!-- 加载状态遮罩层 -->
            <div v-if="isLoading"
                class="absolute inset-0 bg-white bg-opacity-60 flex items-center justify-center backdrop-blur-sm">
                <div class="flex flex-col items-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                    <span class="mt-2 text-sm text-gray-600">Updating...</span>
                </div>
            </div>
        </div>

        <!-- 错误提示 -->
        <div v-if="error"
            class="absolute bottom-4 left-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
            <p class="text-sm">{{ error }}</p>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'LongListings',

    props: {
        url: {
            type: String,
            required: true
        },
        index_url: {
            type: String,
            required: false,
            default: ''
        },
        sort: {
            type: String,
            required: true,
            default: ''
        },
        title: {
            type: String,
            required: true,
            default: 'Title'
        },
        subtitle: {
            type: String,
            required: false,
            default: ''
        },
        metasubtitle: {
            type: String,
            required: false,
            default: ''
        },
        heading: {
            type: String,
            required: true,
            default: 'heading'
        },
        subheading: {
            type: String,
            required: true,
            default: 'subheading'
        },
        thirdheading: {
            type: String,
            required: false,
            default: ''
        },
        number: {
            type: String,
            required: true,
            default: 'number'
        },
        twonumber: {
            type: String,
            required: false,
            default: ''
        },
        noResults: {
            type: String,
            required: false,
            default: 'No listings available.'
        },
        defaultRefreshTime: {
            type: Number,
            required: false,
            default: 5000
        },
        defaultAutoRefresh: {
            type: Boolean,
            required: false,
            default: false
        },
        showRefreshControl: {
            type: Boolean,
            required: false,
            default: true
        }
    },

    data() {
        return {
            listings: [],
            meta: [],
            isLoading: false,
            autoRefresh: this.defaultAutoRefresh,
            refreshInterval: null,
            refreshTime: this.defaultRefreshTime,
            error: null,
            countdown: 0,
            countdownInterval: null
        };
    },

    computed: {
        refreshTimeDisplay() {
            return `${this.refreshTime / 1000} sec`;
        }
    },

    watch: {
        defaultRefreshTime(newVal) {
            this.refreshTime = newVal;
            if (this.autoRefresh) {
                this.restartAutoRefresh();
                this.countdown = Math.floor(newVal / 1000);
            }
        },
        defaultAutoRefresh(newVal) {
            this.autoRefresh = newVal;
            if (newVal) {
                this.startAutoRefresh();
            } else {
                this.clearRefreshInterval();
            }
        },
        url() {
            this.fetchListings();
        }
    },

    mounted() {
        this.fetchListings();
        if (this.autoRefresh) {
            this.startAutoRefresh();
        }
    },

    beforeUnmount() {
        this.clearRefreshInterval();
        this.clearCountdown();
    },

    methods: {
        startCountdown() {
            this.countdown = Math.floor(this.refreshTime / 1000);
            if (this.countdownInterval) {
                clearInterval(this.countdownInterval);
            }
            this.countdownInterval = setInterval(() => {
                this.countdown--;
                if (this.countdown <= 0) {
                    this.countdown = Math.floor(this.refreshTime / 1000);
                }
            }, 1000);
        },

        clearCountdown() {
            if (this.countdownInterval) {
                clearInterval(this.countdownInterval);
                this.countdownInterval = null;
            }
            this.countdown = 0;
        },

        startAutoRefresh() {
            if (this.autoRefresh && !this.refreshInterval) {
                this.refreshInterval = setInterval(() => {
                    this.fetchListings();
                }, this.refreshTime);
                this.startCountdown();
            }
        },

        clearRefreshInterval() {
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
                this.refreshInterval = null;
            }
            this.clearCountdown();
        },

        restartAutoRefresh() {
            this.clearRefreshInterval();
            this.startAutoRefresh();
        },

        toggleAutoRefresh() {
            this.autoRefresh = !this.autoRefresh;
            if (this.autoRefresh) {
                this.fetchListings();
                this.startAutoRefresh();
            } else {
                this.clearRefreshInterval();
            }
            this.$emit('refresh-toggled', this.autoRefresh);
        },

        async fetchListings() {
            if (this.isLoading) return;

            this.isLoading = true;
            this.error = null;

            try {
                let fullUrl = this.url;
                if (this.sort) {
                    fullUrl += (fullUrl.includes('?') ? '&' : '?') +
                        `sorts%5B0%5D=${encodeURIComponent(this.sort)}`;
                }

                console.log(`[${this.title}] Fetching data from:`, fullUrl);

                const response = await axios.get(fullUrl);
                this.listings = response.data.data;
                this.meta = response.data.meta;

                if (this.autoRefresh) {
                    this.countdown = Math.floor(this.refreshTime / 1000);
                }

                this.$emit('updated', this.listings);
                this.$emit('updated', this.meta);

            } catch (error) {
                console.error(`[${this.title}] Error:`, error);
                this.error = error.message;
                this.$emit('error', error);
            } finally {
                setTimeout(() => {
                    this.isLoading = false;
                }, 500); // 添加最小加载时间以确保加载动画可见
            }
        },

        getPropertyValue(obj, propPath) {
            if (!propPath) return '';
            const props = propPath.split('.');
            return props.reduce((acc, key) => (acc && acc[key] !== undefined) ? acc[key] : '', obj);
        }
    }
};
</script>

<style scoped>
.transition-colors {
    transition-property: background-color, border-color, color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}
</style>