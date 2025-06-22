<template>
    <div class="relative">
        <!-- Trigger Button -->
        <div class="profileDropdown-button hover:text-white" @click="toggleDropdown">
            Theme
        </div>

        <!-- Dropdown Menu -->
        <div v-if="showDropdown"
            class="absolute bg-bg-dark shadow-lg border border-gray-700 rounded-lg py-2 w-46 left-[-180px] top-0">
            <!-- Light Mode Option -->
            <label class="flex items-center px-4 py-2 cursor-pointer hover:bg-gray-300 dark:hover:!bg-gray-600"
                :class="{ 'bg-gray-300': !isDarkMode }">
                <input type="radio" name="theme" value="light" class="custom-radio" :checked="!isDarkMode"
                    @change="selectTheme('light')" />
                <img class="rounded-md mr-4" src="/images/svg/lightMode.svg" alt="lightMode" />
                <span>Light</span>
            </label>

            <!-- Dark Mode Option -->
            <label class="flex items-center px-4 py-2 cursor-pointer hover:bg-gray-400 dark:hover:!bg-gray-500"
                :class="{ 'bg-slate-700': isDarkMode }">
                <input type="radio" name="theme" value="dark" class="custom-radio" :checked="isDarkMode"
                    @change="selectTheme('dark')" />
                <img class="rounded-md mr-4" src="/images/svg/darkMode.svg" alt="lightMode" />

                <span>Dark</span>
            </label>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            isDarkMode: false,
            showDropdown: false,
        };
    },
    mounted() {
        this.$nextTick(() => {
            const bodyElement = document.body;
            this.isDarkMode = bodyElement.classList.contains("dark");
        });
    },
    methods: {
        toggleDropdown() {
            this.showDropdown = !this.showDropdown;
        },
        async selectTheme(theme) {
            try {
                const bodyElement = document.body;
                if (theme === "light") {
                    bodyElement.classList.remove("dark");
                    this.isDarkMode = false;
                } else {
                    bodyElement.classList.add("dark");
                    this.isDarkMode = true;
                }
                await axios.get(
                    passport_url(`/shared/private/profile/theme/update` + `?theme=${theme}`)
                );
            } catch (error) {
                console.error("Error updating theme:", error);
            }
        },
    },
};
</script>

<style scoped>
.custom-radio {
    @apply appearance-none w-2 h-2 rounded-[50%] bg-slate-500 mr-4;
    transition: background-color 0.3s, border-color 0.3s;
}

.custom-radio:checked {
    @apply bg-black dark:bg-white outline outline-[2px] outline-black dark:outline-white outline-offset-[2px];
}
</style>