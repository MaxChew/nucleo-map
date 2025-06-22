import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { Ziggy } from './ziggy';
import { route } from 'ziggy-js';
import _ from 'lodash';
import moment from 'moment';

// Import Vue components
import Listing from './components/Listing.vue';
import Toggle from './components/Toggle.vue';
import Pagination from './components/Pagination.vue';

// Import page-specific mixins
import { pages } from './mixins/admin/index';

// Get data from global variables
const vueData = window.app?.vuedata || {};
const user = window.app?.user || {};
const globalData = window.app || {};

// Create simplified Vue app for traditional Blade templates
const app = createApp({
    // Apply page-specific mixins
    mixins: pages,
    
    data() {
        console.log('🚀 Vue app data() called');
        console.log('🚀 Available pages mixins:', pages);
        console.log('🚀 VueData from blade:', vueData);
        console.log('🚀 User data:', user);
        
        // Merge mixin data and vuedata
        const mixinData = {};
        
        // Collect data from all mixins
        if (pages && pages.length > 0) {
            pages.forEach((mixin, index) => {
                console.log(`🚀 Processing mixin ${index}:`, mixin.name || 'unnamed');
                
                if (mixin && typeof mixin.data === 'function') {
                    try {
                        const data = mixin.data();
                        if (data && typeof data === 'object') {
                            console.log(`🚀 Mixin ${index} data:`, data);
                            Object.assign(mixinData, data);
                        }
                    } catch (error) {
                        console.warn('🚀 Error getting mixin data:', error, mixin);
                    }
                }
            });
        }
        
        console.log('🚀 Combined mixin data:', mixinData);
        
        // Deep merge, vuedata has higher priority
        const mergedData = _.mergeWith(mixinData, vueData, (objValue, srcValue) => {
            // For arrays, use source value (vuedata)
            if (_.isArray(srcValue)) {
                return srcValue;
            }
            // For objects, continue recursive merge
            if (_.isObject(srcValue) && _.isObject(objValue)) {
                return undefined; // Let lodash continue recursive merge
            }
            // For other cases, use source value
            return srcValue;
        });
        
        console.log('🚀 Final merged data:', mergedData);
        
        return {
            user: user,
            ...mergedData,
        };
    },
    
    mounted() {
        console.log('🚀 Vue app mounted');
        console.log('🚀 Final component data:', this.$data);
        console.log('🚀 Available methods:', Object.getOwnPropertyNames(this).filter(name => typeof this[name] === 'function'));
        console.log('🚀 Filters in component:', this.filters);
        console.log('🚀 Current status:', this.current_status);
        console.log('🚀 Current state:', this.current_state);
        console.log('🚀 Current service:', this.current_service);
    }
});

const pinia = createPinia();
app.use(pinia);

// Register components
app.component('vue-listing', Listing);
app.component('Toggle', Toggle);
app.component('vue-pagination', Pagination);

// Global properties
app.config.globalProperties.$ziggyRoute = (name, params, absolute) => route(name, params, absolute, Ziggy);
app.config.globalProperties.$user = user;
app.config.globalProperties.$globalData = globalData;
app.config.globalProperties.$api = window.axios;
app.config.globalProperties.$passport_url = (path) => `/api/admin/private${path}`;
app.config.globalProperties.$swal = window.Swal;
app.config.globalProperties.$dayjs = moment;
app.config.globalProperties.$moment = moment;

// Detect mount point and mount application
const mountPoints = ['#admin-app', '#app'];
let mountPoint = null;

for (const point of mountPoints) {
    if (document.querySelector(point)) {
        mountPoint = point;
        break;
    }
}

if (mountPoint) {
    const vueApp = app.mount(mountPoint);
    // Save to global variable for page-level scripts
    window.vueApp = vueApp;
} else {
    console.warn('No valid mount point found for admin app');
} 