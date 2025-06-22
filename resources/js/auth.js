import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { Ziggy } from './ziggy';
import { route } from 'ziggy-js';

// 从全局变量获取数据
const vueData = window.app?.vuedata || {};
const user = window.app?.user || {};
const globalData = window.app || {};

// 创建简化的Vue应用，用于传统Blade模板
const app = createApp({
    data() {
        return {
            user: user,
            ...vueData,  // 所有 @vuedata 的数据都自动展开到这里
        };
    }
});

const pinia = createPinia();
app.use(pinia);

// 全局属性
app.config.globalProperties.$ziggyRoute = route;
app.config.globalProperties.$user = user;
app.config.globalProperties.$globalData = globalData;

// 检查是否有auth-app挂载点，否则使用app
const mountPoint = document.getElementById('auth-app') ? '#auth-app' : '#app';

// 挂载应用并保存实例
const vueApp = app.mount(mountPoint);

// 保存到全局变量供页面级脚本使用
window.vueApp = vueApp; 