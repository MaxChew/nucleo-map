<template>
    <div class="relative inline-block">
        <!-- Truncated text -->
        <template v-if="isURL">
            <a :href="value" target="_blank" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                class="text-primary-600 underline hover:text-primary-800 truncate cursor-pointer inline-block" :title="value">
                {{ truncatedText }}
            </a>
        </template>
        <template v-else>
            <span v-text="truncatedText" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                class="truncate cursor-pointer"></span>
        </template>

        <!-- Tooltip -->
        <div v-if="showTooltip" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
            class="absolute z-50 bg-gray-400 dark:bg-gray-700 rounded-lg p-2 shadow-lg"
            style="min-width: 200px; max-width: 300px; word-wrap: break-word;">
            <div class="flex justify-between items-center gap-4">
                <span class="break-all text-white">{{ value }}</span>
                <button @click="copyToClipboard(value)" class="btn !text-xs !border-0">
                    Copy
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'TruncatedTextWithTooltip',
    props: {
        value: {
            type: String,
            required: true,
        },
        truncateLength: {
            type: Number,
            default: 15,
        },
    },
    data() {
        return {
            showTooltip: false,
        };
    },
    computed: {
        truncatedText() {
            if (!this.value) return ''; 

            const length = this.truncateLength;

            return this.value.length > length
                ? `${this.value.substring(0, length)}...`
                : this.value;
        },
        isURL() {
            const urlPattern = /^(https?:\/\/)?([a-zA-Z0-9.-]+)\.[a-zA-Z]{2,}.*$/;
            return urlPattern.test(this.value);
        },
    },
    methods: {
        copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(
                () => {
                    this.$alert?.success?.('Copied to clipboard!');
                },
                () => {
                    this.$alert?.error?.('Failed to copy!');
                }
            );
        },
    },
};
</script>

<style scoped>
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 200px;
    display: inline-block;
    vertical-align: top;
}
</style>
