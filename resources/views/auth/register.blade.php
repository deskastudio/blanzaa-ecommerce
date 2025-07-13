@extends('layouts.frontend')

@section('title', 'Sign Up - Exclusive')

@section('content')
<div class="min-h-screen bg-white">
    <div class="grid grid-cols-1 lg:grid-cols-2 min-h-screen">
        <!-- Left Side - Image -->
        <div class="hidden lg:block bg-blue-50 relative">
            <div class="absolute inset-0 flex items-center justify-center p-8">
                <div class="max-w-md">
                    <!-- Shopping Cart and Phone Illustration -->
                    <div class="relative">
                        <!-- Shopping Cart -->
                        <div class="w-32 h-24 bg-gray-300 rounded-lg mb-4 mx-auto relative">
                            <div class="absolute -bottom-2 -left-2 w-6 h-6 bg-gray-400 rounded-full"></div>
                            <div class="absolute -bottom-2 -right-2 w-6 h-6 bg-gray-400 rounded-full"></div>
                            <div class="absolute top-2 left-2 right-2 h-4 bg-gray-400 rounded"></div>
                        </div>
                        
                        <!-- Phone -->
                        <div class="w-20 h-36 bg-gray-800 rounded-xl mx-auto relative">
                            <div class="absolute top-2 left-2 right-2 bottom-6 bg-gray-200 rounded-lg"></div>
                            <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 w-8 h-1 bg-gray-600 rounded-full"></div>
                        </div>
                        
                        <!-- Shopping Bags -->
                        <div class="absolute -right-4 top-8 w-12 h-16 bg-pink-300 rounded-lg"></div>
                        <div class="absolute -right-8 top-12 w-10 h-14 bg-pink-400 rounded-lg"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Register Form -->
        <div class="flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <div class="mb-8">
                    <h1 class="text-3xl font-medium text-gray-900 mb-2">Create an account</h1>
                    <p class="text-gray-600">Enter your details below</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Name Field -->
                    <div>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name') }}"
                               placeholder="Name"
                               required
                               class="w-full px-0 py-3 border-0 border-b border-gray-300 focus:border-red-500 focus:outline-none focus:ring-0 bg-transparent placeholder-gray-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               placeholder="Email or Phone Number"
                               required
                               class="w-full px-0 py-3 border-0 border-b border-gray-300 focus:border-red-500 focus:outline-none focus:ring-0 bg-transparent placeholder-gray-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Field -->
                    <div>
                        <input type="text" 
                               name="phone" 
                               value="{{ old('phone') }}"
                               placeholder="Phone Number (Optional)"
                               class="w-full px-0 py-3 border-0 border-b border-gray-300 focus:border-red-500 focus:outline-none focus:ring-0 bg-transparent placeholder-gray-500">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <input type="password" 
                               name="password"
                               placeholder="Password"
                               required
                               class="w-full px-0 py-3 border-0 border-b border-gray-300 focus:border-red-500 focus:outline-none focus:ring-0 bg-transparent placeholder-gray-500">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password Field -->
                    <div>
                        <input type="password" 
                               name="password_confirmation"
                               placeholder="Confirm Password"
                               required
                               class="w-full px-0 py-3 border-0 border-b border-gray-300 focus:border-red-500 focus:outline-none focus:ring-0 bg-transparent placeholder-gray-500">
                    </div>

                    <!-- Create Account Button -->
                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-3 px-8 rounded transition-colors">
                            Create Account
                        </button>
                    </div>

                    <!-- Sign up with Google -->
                    <div>
                        <button type="button" 
                                class="w-full border border-gray-300 hover:border-gray-400 text-gray-700 font-medium py-3 px-8 rounded transition-colors flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            <span>Sign up with Google</span>
                        </button>
                    </div>
                </form>

                <!-- Log In Link -->
                <div class="mt-8 text-center">
                    <p class="text-gray-600">
                        Already have account? 
                        <a href="{{ route('login') }}" class="text-red-500 hover:text-red-600 font-medium">
                            Log in
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Custom styles for border-bottom inputs */
.focus\:border-red-500:focus {
    border-bottom-color: #ef4444 !important;
    box-shadow: 0 1px 0 0 #ef4444;
}

/* Remove default input styling */
input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus {
    outline: none;
    box-shadow: none;
}
</style>
@endpush