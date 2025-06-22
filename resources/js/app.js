import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import Swal from 'sweetalert2';
import axios from 'axios';
import _ from 'lodash';
import { Ziggy } from './ziggy';
import { route } from 'ziggy-js';
import { MotionPlugin } from '@vueuse/motion';
import { useAlert } from './plugins/alert';
import { useAlertStore } from './stores/alert';

// Core component imports
import MainApp from './components/MainApp.vue';
import CenterMap from './components/CenterMap.vue';
import FilterManager from './components/FilterManager.vue';

// Basic UI components
import Loading from './components/Loading.vue';
import AlertSystem from './components/AlertSystem.vue';
import Modal from './components/Modal.vue';
import Card from './components/Card.vue';
import Toggle from './components/Toggle.vue';
import Form from './components/Form.vue';
import Form2 from './components/Form2.vue';
import Select from './components/Select.vue';
import DatePicker from './components/DatePicker.vue';
import TimePicker from './components/TimePicker.vue';
import Pagination from './components/Pagination.vue';
import SideBar from './components/SideBar.vue';
import ProfileDropdown from './components/ProfileDropdown.vue';
import SwitchTheme from './components/SwitchTheme.vue';
import NumberInput from './components/NumberInput.vue';
import DropDown from './components/DropDown.vue';

// Loading directive and styles
import loadingDirective from './directives/loading';
import '../css/loading.css';

// Global variable settings
window._ = _;
window.Swal = Swal;

// Get data from global variables
const vueData = window.app?.vuedata || {};
const user = window.app?.user || {};
const globalData = window.app || {};

// Axios global configuration
window.axios = axios;
window.axios.defaults.headers.common["X-Locale"] = globalData.locale || 'en';
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// CSRF Token configuration
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
} else {
    console.error("CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token");
}

// Access token configuration
if (globalData.accessToken) {
    window.axios.defaults.headers.common["Authorization"] = `Bearer ${globalData.accessToken}`;
}

// Axios response interceptor
window.axios.interceptors.response.use(
    response => {
        const { status, message } = response.data || {};
        if (status && status !== "success") {
            const error = new Error(message || "Invalid response data");
            error.response = response;
            error.detail = response.data;
            throw error;
        }
        return response;
    },
    error => {
        const { status, message, data } = error.response?.data || {};
        if (status === 401) {
            // Handle unauthorized errors
            console.warn('Unauthorized access detected');
        }
        if (message) error.message = message;
        if (data) error.detail = data;
        return Promise.reject(error);
    }
);

// Create Pinia instance
const pinia = createPinia();

// Create Vue application
const app = createApp({
    ...MainApp,
    data() {
        return {
            user: user,
            ...vueData,  // All @vuedata data is automatically spread here
            ...(MainApp.data?.() || {})
        };
    }
});

// Data injection
app.provide('globalData', globalData);

// Global data
app.config.globalProperties.$user = user;
app.config.globalProperties.$globalData = globalData;
app.config.globalProperties.$appUrl = import.meta.env.VITE_APP_URL || window.location.origin;

// Ziggy route configuration - use different name to avoid conflict with Vue Router's $route
app.config.globalProperties.$ziggyRoute = route;

// Register basic components
app.component('CenterMap', CenterMap);
app.component('FilterManager', FilterManager);
app.component('Loading', Loading);
app.component('alert-system', AlertSystem);
app.component('modal', Modal);
app.component('card-component', Card);
app.component('toggle', Toggle);
app.component('vue-form', Form);
app.component('vue-form2', Form2);
app.component('vue-select', Select);
app.component('date-picker', DatePicker);
app.component('time-picker', TimePicker);
app.component('vue-pagination', Pagination);
app.component('side-bar', SideBar);
app.component('profile-dropdown', ProfileDropdown);
app.component('switch-theme', SwitchTheme);
app.component('number-input', NumberInput);
app.component('drop-down', DropDown);

// Register directives
app.directive('loading', loadingDirective);

// Use plugins
app.use(pinia);
app.use(MotionPlugin);
app.use(useAlert);

// Initialization after application mount
app.mixin({
    mounted() {
        // Theme settings
        const userTheme = this.$user?.theme || 'light';
        if (!document.body.classList.contains(userTheme)) {
            document.body.classList.remove('light', 'dark');
            document.body.classList.add(userTheme);
        }
        
        // Clear expired notifications
        if (this.$pinia) {
            const alertStore = useAlertStore();
            alertStore.cleanExpiredAlerts();
            
            // Handle alerts in session
            const alertData = JSON.parse(sessionStorage.getItem('alert') || 'null');
            if (alertData) {
                alertStore.addAlert(alertData.message, alertData.type, 3000);
                sessionStorage.removeItem('alert');
            }
        }
    }
});

// Mount application and save instance
const vueApp = app.mount('#app');

// Save to global variable for page-level scripts
window.vueApp = vueApp;
