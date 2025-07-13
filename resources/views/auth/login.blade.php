@extends('layouts.frontend')

@section('title', 'Log in - Exclusive')

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

        <!-- Right Side - Login Form -->
        <div class="flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <div class="mb-8">
                    <h1 class="text-3xl font-medium text-gray-900 mb-2">Log in to Exclusive</h1>
                    <p class="text-gray-600">Enter your details below</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    
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

                    <!-- Login Button and Forgot Password -->
                    <div class="flex items-center justify-between pt-4">
                        <button type="submit" 
                                class="bg-red-500 hover:bg-red-600 text-white font-medium py-3 px-8 rounded transition-colors">
                            Log In
                        </button>
                        
                        <a href="#" class="text-red-500 hover:text-red-600 text-sm">
                            Forget Password?
                        </a>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="remember" 
                               name="remember" 
                               class="h-4 w-4 text-red-500 focus:ring-red-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>
                </form>

                <!-- Sign Up Link -->
                <div class="mt-8 text-center">
                    <p class="text-gray-600">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="text-red-500 hover:text-red-600 font-medium">
                            Sign up
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
input[type="email"]:focus,
input[type="password"]:focus {
    outline: none;
    box-shadow: none;
}
</style>
@endpush