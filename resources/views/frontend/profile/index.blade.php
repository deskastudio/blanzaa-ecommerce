@extends('layouts.frontend')

@section('title', 'My Profile - Exclusive Electronics Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-red-500">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900">Profile</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg border p-6">
                <div class="flex flex-col items-center mb-6">
                    <div class="w-20 h-20 bg-gray-300 rounded-full flex items-center justify-center mb-4">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="w-full h-full rounded-full object-cover">
                        @else
                            <i class="fas fa-user text-2xl text-gray-600"></i>
                        @endif
                    </div>
                    <h3 class="font-semibold text-lg">{{ $user->name }}</h3>
                    <p class="text-gray-600 text-sm">{{ $user->email }}</p>
                    <span class="inline-block px-2 py-1 text-xs font-medium rounded-full mt-2
                        {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                
                <nav class="space-y-2">
                    <a href="{{ route('profile.index') }}" 
                       class="flex items-center px-3 py-2 text-red-600 bg-red-50 rounded-lg">
                        <i class="fas fa-user mr-3"></i>Profile Overview
                    </a>
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-edit mr-3"></i>Edit Profile
                    </a>
                    @if($user->role === 'customer')
                    <a href="{{ route('orders.index') }}" 
                       class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-shopping-bag mr-3"></i>My Orders
                    </a>
                    <a href="{{ route('wishlist.index') }}" 
                       class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-heart mr-3"></i>Wishlist
                    </a>
                    @endif
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                       class="flex items-center px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg">
                        <i class="fas fa-sign-out-alt mr-3"></i>Logout
                    </a>
                </nav>
                
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-2">Welcome back, {{ $user->name }}!</h1>
                <p class="opacity-90">Manage your account, track orders, and explore our latest products.</p>
            </div>

            <!-- Quick Stats (Only for customers) -->
            @if($user->role === 'customer')
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg border p-6 text-center">
                    <div class="text-2xl font-bold text-blue-600 mb-2">{{ $orderStats['total_orders'] }}</div>
                    <div class="text-sm text-gray-600">Total Orders</div>
                </div>
                <div class="bg-white rounded-lg border p-6 text-center">
                    <div class="text-2xl font-bold text-yellow-600 mb-2">{{ $orderStats['pending_orders'] }}</div>
                    <div class="text-sm text-gray-600">Pending Orders</div>
                </div>
                <div class="bg-white rounded-lg border p-6 text-center">
                    <div class="text-2xl font-bold text-green-600 mb-2">{{ $orderStats['completed_orders'] }}</div>
                    <div class="text-sm text-gray-600">Completed Orders</div>
                </div>
                <div class="bg-white rounded-lg border p-6 text-center">
                    <div class="text-2xl font-bold text-red-600 mb-2">Rp {{ number_format($orderStats['total_spent'], 0, ',', '.') }}</div>
                    <div class="text-sm text-gray-600">Total Spent</div>
                </div>
            </div>
            @endif

            <!-- Personal Information -->
            <div class="bg-white rounded-lg border">
                <div class="p-6 border-b flex justify-between items-center">
                    <h2 class="text-xl font-semibold">Personal Information</h2>
                    <a href="{{ route('profile.edit') }}" 
                       class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
                        Edit Profile
                    </a>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Full Name</label>
                            <p class="mt-1 text-gray-900">{{ $user->name ?: 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-gray-900">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Phone</label>
                            <p class="mt-1 text-gray-900">{{ $user->phone ?: 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Role</label>
                            <p class="mt-1 text-gray-900">{{ ucfirst($user->role) }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">City</label>
                            <p class="mt-1 text-gray-900">{{ $user->city ?: 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Province</label>
                            <p class="mt-1 text-gray-900">{{ $user->province ?: 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Postal Code</label>
                            <p class="mt-1 text-gray-900">{{ $user->postal_code ?: 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Status</label>
                            <p class="mt-1">
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full
                                    {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-700">Address</label>
                            <p class="mt-1 text-gray-900">{{ $user->address ?: 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders (Only for customers) -->
            @if($user->role === 'customer')
            <div class="bg-white rounded-lg border">
                <div class="p-6 border-b flex justify-between items-center">
                    <h2 class="text-xl font-semibold">Recent Orders</h2>
                    <a href="{{ route('orders.index') }}" 
                       class="text-red-500 hover:text-red-600 font-medium">
                        View All Orders
                    </a>
                </div>
                <div class="divide-y">
                    @forelse($recentOrders as $order)
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                            <div class="flex items-center space-x-4 mb-4 md:mb-0">
                                <div>
                                    <p class="font-semibold text-gray-900">Order #{{ $order->order_number }}</p>
                                    <p class="text-sm text-gray-600">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="px-3 py-1 rounded-full text-sm font-medium
                                    {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($order->status) }}
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                                    <p class="text-sm text-gray-600">{{ $order->items->count() }} items</p>
                                </div>
                                <a href="{{ route('orders.show', $order) }}" 
                                   class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <i class="fas fa-shopping-bag text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">No orders yet</h3>
                        <p class="text-gray-500 mb-4">When you place your first order, it will appear here.</p>
                        <a href="{{ route('products.index') }}" 
                           class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 transition-colors">
                            Start Shopping
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg border">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-semibold">Quick Actions</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if($user->role === 'customer')
                        <a href="{{ route('orders.index') }}" 
                           class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-shopping-bag text-2xl text-red-500 mr-4"></i>
                            <div>
                                <h3 class="font-medium">Track Orders</h3>
                                <p class="text-sm text-gray-600">Monitor your order status</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('wishlist.index') }}" 
                           class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-heart text-2xl text-red-500 mr-4"></i>
                            <div>
                                <h3 class="font-medium">My Wishlist</h3>
                                <p class="text-sm text-gray-600">View saved products</p>
                            </div>
                        </a>
                        @endif
                        
                        <a href="{{ route('products.index') }}" 
                           class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-shopping-cart text-2xl text-red-500 mr-4"></i>
                            <div>
                                <h3 class="font-medium">Continue Shopping</h3>
                                <p class="text-sm text-gray-600">Explore our products</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('profile.edit') }}" 
                           class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-cog text-2xl text-red-500 mr-4"></i>
                            <div>
                                <h3 class="font-medium">Account Settings</h3>
                                <p class="text-sm text-gray-600">Update your profile</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection