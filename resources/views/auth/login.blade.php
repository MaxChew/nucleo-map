<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-600 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">{{ config('app.name') }}</h1>
            <p class="text-gray-600 mt-2">Please login to your account</p>
        </div>

        <!-- Development Credentials -->
        @if(config('app.env') === 'local' || config('app.env') === 'development')
            <div class="bg-gradient-to-r from-primary-50 to-secondary-50 border border-primary-200 rounded-xl p-6 mb-6 shadow-sm">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-semibold text-gray-800">🚀 Development Test Accounts</h3>
                        <p class="text-sm text-gray-600">Choose any of the following accounts to quickly login</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Super Admin -->
                    <div class="group bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-all duration-200 hover:border-indigo-300">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="font-semibold text-gray-800">Super Admin</h4>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    All Permissions
                                </span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-sm text-gray-600 flex-shrink-0">Email:</span>
                                <code class="text-xs bg-gray-100 px-3 py-2 rounded font-mono text-gray-800 cursor-pointer hover:bg-gray-200 transition-colors break-all text-right flex-1 min-w-0" 
                                      onclick="navigator.clipboard.writeText('superadmin@example.com'); this.classList.add('bg-accent-100'); setTimeout(() => this.classList.remove('bg-accent-100'), 1000);"
                                      title="Click to copy">
                                    superadmin@example.com
                                </code>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Password:</span>
                                <code class="text-xs bg-gray-100 px-3 py-2 rounded font-mono text-gray-800 cursor-pointer hover:bg-gray-200 transition-colors" 
                                      onclick="navigator.clipboard.writeText('password123'); this.classList.add('bg-accent-100'); setTimeout(() => this.classList.remove('bg-accent-100'), 1000);"
                                      title="Click to copy">
                                    password123
                                </code>
                            </div>
                        </div>
                        <button type="button" 
                                onclick="document.getElementById('email').value='superadmin@example.com'; document.getElementById('password').value='password123';"
                                class="mt-3 w-full bg-gradient-to-r from-purple-500 to-pink-500 text-white py-2 px-3 rounded-md text-sm font-medium hover:from-purple-600 hover:to-pink-600 transition-all duration-200 transform hover:scale-105">
                            Quick Fill
                        </button>
                    </div>

                    <!-- Regular Admin -->
                    <div class="group bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-all duration-200 hover:border-indigo-300">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="font-semibold text-gray-800">Regular Admin</h4>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                    Basic Permissions
                                </span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-sm text-gray-600 flex-shrink-0">Email:</span>
                                <code class="text-xs bg-gray-100 px-3 py-2 rounded font-mono text-gray-800 cursor-pointer hover:bg-gray-200 transition-colors break-all text-right flex-1 min-w-0" 
                                      onclick="navigator.clipboard.writeText('admin@example.com'); this.classList.add('bg-accent-100'); setTimeout(() => this.classList.remove('bg-accent-100'), 1000);"
                                      title="Click to copy">
                                    admin@example.com
                                </code>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Password:</span>
                                <code class="text-xs bg-gray-100 px-3 py-2 rounded font-mono text-gray-800 cursor-pointer hover:bg-gray-200 transition-colors" 
                                      onclick="navigator.clipboard.writeText('password123'); this.classList.add('bg-accent-100'); setTimeout(() => this.classList.remove('bg-accent-100'), 1000);"
                                      title="Click to copy">
                                    password123
                                </code>
                            </div>
                        </div>
                        <button type="button" 
                                onclick="document.getElementById('email').value='admin@example.com'; document.getElementById('password').value='password123';"
                                class="mt-3 w-full bg-gradient-to-r from-blue-500 to-indigo-500 text-white py-2 px-3 rounded-md text-sm font-medium hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 transform hover:scale-105">
                            Quick Fill
                        </button>
                    </div>
                </div>
                
                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-500">💡 Tip: Click credentials to copy, click "Quick Fill" button to auto-fill form</p>
                </div>
            </div>
        @endif

        <!-- Login Form -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            @if(session('status'))
                <div class="mb-4 p-4 bg-accent-50 border border-accent-200 rounded-lg">
                    <p class="text-accent-800">{{ session('status') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-indigo-500 transition-colors @error('email') border-red-500 @enderror"
                        placeholder="Enter your email address"
                        required
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-indigo-500 transition-colors @error('password') border-red-500 @enderror"
                        placeholder="Enter your password"
                        required
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-primary-600 hover:text-primary-500">
                        Forgot password?
                    </a>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full bg-primary-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
                >
                    Login
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-sm text-gray-500">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html> 