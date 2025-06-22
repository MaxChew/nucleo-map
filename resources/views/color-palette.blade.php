<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Palette - Nucleo Map</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Title -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Nucleo Map Color Palette
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                Modern color scheme based on Purple design system
            </p>
        </div>

        <!-- Primary Colors -->
        <div class="mb-16">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Primary Colors</h2>
            <div class="grid grid-cols-2 gap-8">
                <!-- Primary Purple -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Primary - Purple</h3>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between bg-primary-50 p-3 rounded-lg">
                            <span class="text-gray-800">50</span>
                            <span class="font-mono text-sm">#f8f4ff</span>
                        </div>
                        <div class="flex items-center justify-between bg-primary-100 p-3 rounded-lg">
                            <span class="text-gray-800">100</span>
                            <span class="font-mono text-sm">#ede5ff</span>
                        </div>
                        <div class="flex items-center justify-between bg-primary-200 p-3 rounded-lg">
                            <span class="text-gray-800">200</span>
                            <span class="font-mono text-sm">#ddd0ff</span>
                        </div>
                        <div class="flex items-center justify-between bg-primary-300 p-3 rounded-lg">
                            <span class="text-gray-800">300</span>
                            <span class="font-mono text-sm">#c5abff</span>
                        </div>
                        <div class="flex items-center justify-between bg-primary-400 p-3 rounded-lg">
                            <span class="text-white">400</span>
                            <span class="font-mono text-sm text-white">#a878ff</span>
                        </div>
                        <div class="flex items-center justify-between bg-primary-500 p-3 rounded-lg">
                            <span class="text-white font-bold">500</span>
                            <span class="font-mono text-sm text-white font-bold">#A05AFF</span>
                        </div>
                        <div class="flex items-center justify-between bg-primary-600 p-3 rounded-lg">
                            <span class="text-white">600</span>
                            <span class="font-mono text-sm text-white">#8e42ff</span>
                        </div>
                        <div class="flex items-center justify-between bg-primary-700 p-3 rounded-lg">
                            <span class="text-white">700</span>
                            <span class="font-mono text-sm text-white">#7c2aff</span>
                        </div>
                        <div class="flex items-center justify-between bg-primary-800 p-3 rounded-lg">
                            <span class="text-white">800</span>
                            <span class="font-mono text-sm text-white">#6b1fff</span>
                        </div>
                        <div class="flex items-center justify-between bg-primary-900 p-3 rounded-lg">
                            <span class="text-white">900</span>
                            <span class="font-mono text-sm text-white">#5a15d4</span>
                        </div>
                    </div>
                </div>

                <!-- Accent Green -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Accent - Green</h3>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between bg-accent-50 p-3 rounded-lg">
                            <span class="text-gray-800">50</span>
                            <span class="font-mono text-sm">#edfffe</span>
                        </div>
                        <div class="flex items-center justify-between bg-accent-100 p-3 rounded-lg">
                            <span class="text-gray-800">100</span>
                            <span class="font-mono text-sm">#d5fffc</span>
                        </div>
                        <div class="flex items-center justify-between bg-accent-200 p-3 rounded-lg">
                            <span class="text-gray-800">200</span>
                            <span class="font-mono text-sm">#aefff9</span>
                        </div>
                        <div class="flex items-center justify-between bg-accent-300 p-3 rounded-lg">
                            <span class="text-gray-800">300</span>
                            <span class="font-mono text-sm">#79fff5</span>
                        </div>
                        <div class="flex items-center justify-between bg-accent-400 p-3 rounded-lg">
                            <span class="text-gray-800">400</span>
                            <span class="font-mono text-sm">#3dffee</span>
                        </div>
                        <div class="flex items-center justify-between bg-accent-500 p-3 rounded-lg">
                            <span class="text-white font-bold">500</span>
                            <span class="font-mono text-sm text-white font-bold">#1BCFB4</span>
                        </div>
                        <div class="flex items-center justify-between bg-accent-600 p-3 rounded-lg">
                            <span class="text-white">600</span>
                            <span class="font-mono text-sm text-white">#13a392</span>
                        </div>
                        <div class="flex items-center justify-between bg-accent-700 p-3 rounded-lg">
                            <span class="text-white">700</span>
                            <span class="font-mono text-sm text-white">#0f8a7a</span>
                        </div>
                        <div class="flex items-center justify-between bg-accent-800 p-3 rounded-lg">
                            <span class="text-white">800</span>
                            <span class="font-mono text-sm text-white">#0d7063</span>
                        </div>
                        <div class="flex items-center justify-between bg-accent-900 p-3 rounded-lg">
                            <span class="text-white">900</span>
                            <span class="font-mono text-sm text-white">#0b5c52</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supporting Colors -->
        <div class="mb-16">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Support Colors</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Secondary Blue -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Secondary - Blue</h3>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between bg-secondary-400 p-3 rounded-lg">
                            <span class="text-white font-bold">400</span>
                            <span class="font-mono text-sm text-white font-bold">#4BCBEB</span>
                        </div>
                        <div class="flex items-center justify-between bg-secondary-500 p-3 rounded-lg">
                            <span class="text-white">500</span>
                            <span class="font-mono text-sm text-white">#3bb8d8</span>
                        </div>
                        <div class="flex items-center justify-between bg-secondary-600 p-3 rounded-lg">
                            <span class="text-white">600</span>
                            <span class="font-mono text-sm text-white">#2e9bb8</span>
                        </div>
                    </div>
                </div>

                <!-- Warning Pink -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Warning - Pink</h3>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between bg-warning-400 p-3 rounded-lg">
                            <span class="text-white font-bold">400</span>
                            <span class="font-mono text-sm text-white font-bold">#FE9496</span>
                        </div>
                        <div class="flex items-center justify-between bg-warning-500 p-3 rounded-lg">
                            <span class="text-white">500</span>
                            <span class="font-mono text-sm text-white">#fe7b7e</span>
                        </div>
                        <div class="flex items-center justify-between bg-warning-600 p-3 rounded-lg">
                            <span class="text-white">600</span>
                            <span class="font-mono text-sm text-white">#f85e61</span>
                        </div>
                    </div>
                </div>

                <!-- Danger Purple -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Danger - Purple</h3>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between bg-danger-500 p-3 rounded-lg">
                            <span class="text-white font-bold">500</span>
                            <span class="font-mono text-sm text-white font-bold">#9E58FF</span>
                        </div>
                        <div class="flex items-center justify-between bg-danger-600 p-3 rounded-lg">
                            <span class="text-white">600</span>
                            <span class="font-mono text-sm text-white">#8e42ff</span>
                        </div>
                        <div class="flex items-center justify-between bg-danger-700 p-3 rounded-lg">
                            <span class="text-white">700</span>
                            <span class="font-mono text-sm text-white">#7a2aff</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usage Examples -->
        <div class="mb-16">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Usage Examples</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Primary Button -->
                <button class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    Primary Button
                </button>
                
                <!-- Accent Button -->
                <button class="bg-accent-500 hover:bg-accent-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    Accent Button
                </button>
                
                <!-- Secondary Button -->
                <button class="bg-secondary-400 hover:bg-secondary-500 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    Secondary Button
                </button>
                
                <!-- Warning Button -->
                <button class="bg-warning-400 hover:bg-warning-500 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    Warning Button
                </button>
            </div>
        </div>

        <!-- Cards Demo -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl p-6 text-white">
                <h3 class="text-lg font-semibold mb-2">Primary Card</h3>
                                    <p class="text-primary-100">Example card using primary colors</p>
            </div>
            
            <div class="bg-gradient-to-br from-accent-500 to-accent-600 rounded-xl p-6 text-white">
                <h3 class="text-lg font-semibold mb-2">Accent Card</h3>
                                    <p class="text-accent-100">Example card using accent colors</p>
            </div>
            
            <div class="bg-gradient-to-br from-secondary-400 to-secondary-500 rounded-xl p-6 text-white">
                <h3 class="text-lg font-semibold mb-2">Secondary Card</h3>
                                    <p class="text-secondary-100">Example card using secondary colors</p>
            </div>
        </div>
    </div>
</body>
</html> 