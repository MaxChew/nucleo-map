import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { Ziggy } from './ziggy';
import { route } from 'ziggy-js';

// Get data from global variables
const vueData = window.app?.vuedata || {};
const user = window.app?.user || {};
const globalData = window.app || {};

// Create simplified Vue app for traditional Blade templates
const app = createApp({
    data() {
        return {
            user: user,
            ...vueData,  // All @vuedata data is automatically spread here
        };
    }
});

const pinia = createPinia();
app.use(pinia);

    // Global properties
    app.config.globalProperties.$ziggyRoute = route;
    app.config.globalProperties.$user = user;
    app.config.globalProperties.$globalData = globalData;

    // Check if tutor-app mount point exists, otherwise use app
    const mountPoint = document.getElementById('tutor-app') ? '#tutor-app' : '#app';

    // Mount application and save instance
    const vueApp = app.mount(mountPoint);

    // Save to global variable for page-level scripts
window.vueApp = vueApp; 