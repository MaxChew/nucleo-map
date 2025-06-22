<template>
    <div v-if="loading" class="loading-overlay">
        <div class="loading-spinner">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    loading: {
        type: Boolean,
        default: false
    }
});
</script>

<style scoped>
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.loading-spinner {
    display: flex;
    align-items: center;
    justify-content: center;
}

.loading-spinner .dot {
    width: 10px;
    height: 10px;
    margin: 0 4px;
    border-radius: 50%;
    background-color: #3498db;
    display: inline-block;
    animation: dots 1.4s infinite ease-in-out both;
}

.loading-spinner .dot:nth-child(1) {
    animation-delay: -0.32s;
}

.loading-spinner .dot:nth-child(2) {
    animation-delay: -0.16s;
}

@keyframes dots {
    0%, 80%, 100% {
        transform: scale(0);
        opacity: 0;
    }
    40% {
        transform: scale(1);
        opacity: 1;
    }
}
</style> 