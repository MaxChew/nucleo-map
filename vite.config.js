import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import dotenv from 'dotenv';
import path from 'path';

const envFile = process.env.NODE_ENV === 'production' ? '.env.production' : '.env';
dotenv.config({ path: path.resolve(process.cwd(), envFile) });

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/admin.js',
                'resources/js/user.js',
                'resources/js/tutor.js',
                'resources/js/auth.js',
                'resources/js/map.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
    define: {
        'process.env.APP_URL': JSON.stringify(process.env.APP_URL),
    },
    optimizeDeps: {
        include: ['vue', 'quill']
    },
    build: {
        sourcemap: true,
        cssCodeSplit: true,
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['vue', 'axios', 'lodash', 'moment'],
                    'editor': ['quill'],
                    'styles': ['quill/dist/quill.snow.css']
                }
            }
        },
        chunkSizeWarningLimit: 1600
    },
    css: {
        devSourcemap: true,
        preprocessorOptions: {
            css: {
                charset: false
            }
        }
    }
});
