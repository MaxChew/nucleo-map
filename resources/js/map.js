import { createApp } from 'vue'
import './bootstrap'

// 引入主要組件
import NucleoMapApp from './components/NucleoMapApp.vue'

// 引入工具庫
import { debounce } from 'lodash'
import moment from 'moment'

// 引入 Alert 系統
import { SimpleAlert } from './libs/Alert'

console.log('🗺️ Nucleo Map 應用正在初始化...')

// 創建 Vue 應用實例
const app = createApp({
    data() {
        return {
            isAppReady: false,
            googleMapsReady: false,
            appError: null
        }
    },
    
    components: {
        NucleoMapApp
    },
    
    template: `
        <div>
            <!-- 應用未準備好時顯示載入狀態 -->
            <div v-if="!isAppReady" class="flex items-center justify-center min-h-screen bg-gray-50">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-primary-600 mx-auto mb-4"></div>
                    <p class="text-gray-600 text-lg">正在初始化應用...</p>
                </div>
            </div>
            
            <!-- 應用錯誤狀態 -->
            <div v-else-if="appError" class="flex items-center justify-center min-h-screen bg-gray-50">
                <div class="text-center max-w-md">
                    <div class="bg-danger-100 rounded-full p-6 mb-6 mx-auto w-24 h-24 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-3xl text-danger-600"></i>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900 mb-4">應用初始化失敗</h3>
                    <p class="text-gray-600 mb-6">{{ appError }}</p>
                    <button 
                        @click="retryInit"
                        class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors"
                    >
                        <i class="fas fa-redo mr-2"></i>
                        重新載入
                    </button>
                </div>
            </div>
            
            <!-- 主應用組件 -->
            <NucleoMapApp v-else />
        </div>
    `,
    
    mounted() {
        this.initializeApp()
    },
    
    methods: {
        async initializeApp() {
            try {
                console.log('📱 開始初始化 Vue 應用...')
                
                // 檢查必要的全域變數
                if (!window.mapConfig) {
                    throw new Error('地圖配置未找到')
                }
                
                if (!window.initialFilters) {
                    throw new Error('初始篩選配置未找到')
                }
                
                // 等待 Google Maps API 載入
                await this.waitForGoogleMaps()
                
                // 模擬初始化時間（為了更好的用戶體驗）
                await new Promise(resolve => setTimeout(resolve, 500))
                
                this.isAppReady = true
                console.log('✅ Vue 應用初始化完成')
                
                // 隱藏初始載入畫面
                if (window.hideInitialLoading) {
                    window.hideInitialLoading()
                }
                
            } catch (error) {
                console.error('❌ 應用初始化失敗:', error)
                this.appError = error.message || '未知錯誤'
                
                // 隱藏初始載入畫面
                if (window.hideInitialLoading) {
                    window.hideInitialLoading()
                }
            }
        },
        
        waitForGoogleMaps() {
            return new Promise((resolve, reject) => {
                // 如果已經載入，直接解決
                if (window.google && window.google.maps) {
                    this.googleMapsReady = true
                    console.log('✅ Google Maps API 已準備就緒')
                    resolve()
                    return
                }
                
                // 監聽 Google Maps 載入事件
                const handleMapsLoaded = () => {
                    this.googleMapsReady = true
                    console.log('✅ Google Maps API 載入完成')
                    window.removeEventListener('google-maps-loaded', handleMapsLoaded)
                    window.removeEventListener('google-maps-error', handleMapsError)
                    resolve()
                }
                
                const handleMapsError = (event) => {
                    console.error('❌ Google Maps API 載入失敗:', event.detail)
                    window.removeEventListener('google-maps-loaded', handleMapsLoaded)
                    window.removeEventListener('google-maps-error', handleMapsError)
                    reject(new Error(event.detail?.message || 'Google Maps API 載入失敗'))
                }
                
                window.addEventListener('google-maps-loaded', handleMapsLoaded)
                window.addEventListener('google-maps-error', handleMapsError)
                
                // 設定超時
                setTimeout(() => {
                    if (!this.googleMapsReady) {
                        window.removeEventListener('google-maps-loaded', handleMapsLoaded)
                        window.removeEventListener('google-maps-error', handleMapsError)
                        reject(new Error('Google Maps API 載入超時'))
                    }
                }, 15000)
            })
        },
        
        retryInit() {
            this.appError = null
            this.isAppReady = false
            this.googleMapsReady = false
            this.initializeApp()
        }
    }
})

// 設定全域屬性
app.config.globalProperties.$alert = SimpleAlert
app.config.globalProperties.$moment = moment
app.config.globalProperties.$debounce = debounce

// 設定全域變數
app.config.globalProperties.$mapConfig = window.mapConfig || {}
app.config.globalProperties.$initialFilters = window.initialFilters || {}
app.config.globalProperties.$pageConfig = window.pageConfig || {}

// API 基礎配置
app.config.globalProperties.$apiBaseUrl = window.mapConfig?.api_base_url || '/api/public'

// 全域錯誤處理
app.config.errorHandler = (err, vm, info) => {
    console.error('Vue 全域錯誤:', err, info)
    
    // 顯示用戶友好的錯誤訊息
    if (SimpleAlert) {
        SimpleAlert.error('應用發生錯誤', '請重新整理頁面或聯繫技術支援')
    }
}

// 全域警告處理（開發模式）
if (import.meta.env.DEV) {
    app.config.warnHandler = (msg, vm, trace) => {
        console.warn('Vue 警告:', msg, trace)
    }
}

// 掛載應用
const mountApp = () => {
    try {
        const appElement = document.getElementById('nucleo-map-app')
        if (!appElement) {
            throw new Error('無法找到應用掛載點 #nucleo-map-app')
        }
        
        // 移除 v-cloak 隱藏類
        appElement.classList.remove('v-cloak--hidden')
        
        // 掛載 Vue 應用
        const mountedApp = app.mount('#nucleo-map-app')
        
        console.log('🎯 Vue 應用已成功掛載到 #nucleo-map-app')
        
        // 設定全域應用實例（用於除錯）
        if (import.meta.env.DEV) {
            window.vueMapApp = mountedApp
        }
        
        return mountedApp
        
    } catch (error) {
        console.error('❌ Vue 應用掛載失敗:', error)
        
        // 顯示錯誤訊息
        const appElement = document.getElementById('nucleo-map-app')
        if (appElement) {
            appElement.innerHTML = `
                <div class="flex items-center justify-center min-h-screen bg-gray-50">
                    <div class="text-center max-w-md">
                        <div class="bg-danger-100 rounded-full p-6 mb-6 mx-auto w-24 h-24 flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-3xl text-danger-600"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900 mb-4">應用啟動失敗</h3>
                        <p class="text-gray-600 mb-6">${error.message}</p>
                        <button 
                            onclick="location.reload()" 
                            class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors"
                        >
                            <i class="fas fa-redo mr-2"></i>
                            重新載入頁面
                        </button>
                    </div>
                </div>
            `
            appElement.classList.remove('v-cloak--hidden')
        }
        
        // 隱藏初始載入畫面
        if (window.hideInitialLoading) {
            window.hideInitialLoading()
        }
        
        throw error
    }
}

// 等待 DOM 載入完成後掛載應用
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mountApp)
} else {
    mountApp()
}

// 匯出應用實例（用於測試）
export default app 