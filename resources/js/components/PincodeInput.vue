<template>
    <div class="flex items-center justify-center mt-4 mb-6">
        <input
            ref="pincodeInput"
            type="text"
            name="pincode"
            class="w-full h-12 px-4 py-2 text-lg text-center border rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition duration-300 ease-in-out"
            placeholder="••••••"
            autocomplete="off"
            v-model.trim="pincode"
            maxlength="6"
            autofocus
            @input="handleInput"
            @keydown.enter.prevent
            required
        />
    </div>
</template>

<script>
export default {
    data() {
        return {
            pincode: ''
        };
    },
    methods: {
        handleInput() {
            this.pincode = this.pincode.replace(/\D/g, '');
        }
    },
    mounted() {
        this.$refs.pincodeInput.selectionStart = this.$refs.pincodeInput.selectionEnd = this.$refs.pincodeInput.value.length;
    }
};
</script>

<style scoped>
.focus\:ring-primary {
    ring-color: #3182ce;
}
</style>
