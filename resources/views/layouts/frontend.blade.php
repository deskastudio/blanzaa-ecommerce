<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-authenticated" content="{{ auth()->check() ? 'true' : 'false' }}">
    <title>@yield('title', 'Blanzaa - Electronics Store')</title>
    
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        }
                    }
                }
            }
        }
    </script>
    
    @vite(['resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-black">
                        Blanzaa
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-black hover:text-red-500 pb-1 {{ request()->routeIs('home') ? 'border-b-2 border-red-500' : '' }}">Home</a>
                    <a href="{{ route('products.index') }}" class="text-black hover:text-red-500 pb-1 {{ request()->routeIs('products.*') ? 'border-b-2 border-red-500' : '' }}">Products</a>
                    <a href="{{ route('contact') }}" class="text-black hover:text-red-500 pb-1 {{ request()->routeIs('contact') ? 'border-b-2 border-red-500' : '' }}">Contact</a>
                    <a href="{{ route('about') }}" class="text-black hover:text-red-500 pb-1 {{ request()->routeIs('about') ? 'border-b-2 border-red-500' : '' }}">About</a>
                    @guest
                        <a href="{{ route('register') }}" class="text-black hover:text-red-500">Sign Up</a>
                    @endguest
                </div>

                <!-- Search and Icons -->
                <div class="flex items-center space-x-4">
                    <!-- Search Bar -->
                    <div class="relative hidden md:block">
                        <form action="{{ route('products.search') }}" method="GET">
                            <input type="text" 
                                   name="q"
                                   placeholder="What are you looking for?" 
                                   class="bg-gray-100 pl-4 pr-10 py-2 rounded border-0 focus:outline-none focus:ring-2 focus:ring-red-500 w-64">
                            <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                <i class="fas fa-search text-gray-500"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Icons -->
                    <div class="flex items-center space-x-4">
                        <!-- Cart -->
                        <a href="{{ route('cart.index') }}" class="relative">
                            <i class="fas fa-shopping-cart text-xl hover:text-red-500"></i>
                            <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $cartCount ?? 0 }}
                            </span>
                        </a>
                        
                        <!-- User Menu -->
                        @auth
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-2 hover:text-red-500">
                                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center text-white text-sm">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                                
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border">
                                    
                                    <!-- Profile Link -->
                                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i>Manage My Account
                                    </a>
                                    
                                    <!-- Orders Link - Only for customers -->
                                    @if(auth()->user()->role === 'customer')
                                        <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-shopping-bag mr-2"></i>My Orders
                                        </a>
                                    @endif
                                    
                                    <!-- Divider -->
                                    <div class="border-t border-gray-100 my-1"></div>
                                    
                                    <!-- Logout -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <!-- Guest User -->
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('login') }}" class="text-black hover:text-red-500 text-sm">
                                    <i class="far fa-user text-xl"></i>
                                </a>
                                <a href="{{ route('login') }}" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 text-sm">
                                    Login
                                </a>
                            </div>
                        @endauth
                    </div>

                    <!-- Mobile Menu Button -->
                    <button class="md:hidden" x-data="{ open: false }" @click="open = !open">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Navigation -->
    <div class="md:hidden bg-white border-t" x-show="mobileMenuOpen" x-data="{ mobileMenuOpen: false }">
        <div class="px-4 py-2 space-y-1">
            <a href="{{ route('home') }}" class="block py-2 text-black">Home</a>
            <a href="{{ route('products.index') }}" class="block py-2 text-black">Products</a>
            <a href="#" class="block py-2 text-black">Contact</a>
            <a href="#" class="block py-2 text-black">About</a>
            @guest
                <a href="{{ route('register') }}" class="block py-2 text-black">Sign Up</a>
                <a href="{{ route('login') }}" class="block py-2 text-black">Login</a>
            @endguest
        </div>
    </div>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-black text-white">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Exclusive Column -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Blanzaa</h3>
                    <p class="mb-4">Subscribe</p>
                    <p class="text-gray-400 mb-4">Get 10% off your first order</p>
                    <div class="relative">
                        <input type="email" placeholder="Enter your email" 
                               class="bg-transparent border border-gray-600 px-4 py-2 rounded w-full pr-10">
                        <button class="absolute right-3 top-1/2 transform -translate-y-1/2">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>

                <!-- Support Column -->
                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <div class="space-y-2 text-gray-400">
                        <p>111 Bijoy sarani, Dhaka,<br>DH 1515, Bangladesh.</p>
                        <p>blanzaa@gmail.com</p>
                        <p>+88015-88888-9999</p>
                    </div>
                </div>

                <!-- Account Column -->
                <div>
                    <h4 class="font-semibold mb-4">Account</h4>
                    <ul class="space-y-2 text-gray-400">
                        @auth
                            <li><a href="{{ route('profile') }}" class="hover:text-white">My Account</a></li>
                            @if(auth()->user()->role === 'customer')
                                <li><a href="{{ route('orders.index') }}" class="hover:text-white">My Orders</a></li>
                            @endif
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-white">Login / Register</a></li>
                        @endauth
                        <li><a href="{{ route('cart.index') }}" class="hover:text-white">Cart</a></li>
                        <li><a href="{{ route('products.index') }}" class="hover:text-white">Shop</a></li>
                    </ul>
                </div>

                <!-- Quick Link Column -->
                <div>
                    <h4 class="font-semibold mb-4">Quick Link</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('privacy-policy') }}" class="hover:text-white">Privacy Policy</a></li>
                        <li><a href="{{ route('terms-of-use') }}" class="hover:text-white">Terms Of Use</a></li>
                        <li><a href="{{ route('faq') }}" class="hover:text-white">FAQ</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white">Contact</a></li>
                        <div class="flex space-x-4">
                            <i class="fab fa-facebook text-xl hover:text-blue-500 cursor-pointer"></i>
                            <i class="fab fa-twitter text-xl hover:text-blue-400 cursor-pointer"></i>
                            <i class="fab fa-instagram text-xl hover:text-pink-500 cursor-pointer"></i>
                            <i class="fab fa-linkedin text-xl hover:text-blue-600 cursor-pointer"></i>
                        </div>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; Copyright Blanzaa Electronics 2025. All right reserved</p>
            </div>
        </div>
    </footer>

    <!-- Alert Messages -->
    @if(session('success'))
        <div id="alert-success" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div id="alert-error" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('message'))
        <div id="alert-info" class="fixed top-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300">
            <div class="flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                {{ session('message') }}
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <script>
        // Auto hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('[id^="alert-"]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert) {
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            alert.remove();
                        }, 300);
                    }
                }, 5000);
            });
        });

        // CSRF token for AJAX requests
        window.csrfToken = '{{ csrf_token() }}';

        // Check if user is logged in
        function isUserLoggedIn() {
            const authMeta = document.querySelector('meta[name="user-authenticated"]');
            return authMeta && authMeta.getAttribute('content') === 'true';
        }

        // Update cart count function
        function updateCartCount() {
            fetch('/cart/count')
                .then(response => response.json())
                .then(data => {
                    const cartCountElement = document.getElementById('cart-count');
                    if (cartCountElement) {
                        cartCountElement.textContent = data.count || 0;
                        
                        // Add animation effect when count changes
                        if (data.count > 0) {
                            cartCountElement.classList.add('animate-bounce');
                            setTimeout(() => {
                                cartCountElement.classList.remove('animate-bounce');
                            }, 500);
                        }
                    }
                })
                .catch(error => console.log('Error updating cart count:', error));
        }

        // Global notification function
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            const icons = {
                success: 'fas fa-check-circle',
                error: 'fas fa-exclamation-circle',
                info: 'fas fa-info-circle',
                warning: 'fas fa-exclamation-triangle'
            };
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                info: 'bg-blue-500',
                warning: 'bg-yellow-500'
            };
            
            notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-full`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="${icons[type]} mr-2"></i>
                    ${message}
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Slide in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 5000);
        }

        // NEW: Enhanced addToCart function with direct login redirect
        function addToCart(productId, quantity = 1) {
            // Check if user is logged in first
            if (!isUserLoggedIn()) {
                // Langsung redirect ke login tanpa alert
                window.location.href = '/login';
                return;
            }

            // If logged in, proceed with adding to cart
            fetch(`/cart/add/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Product added to cart!', 'success');
                    updateCartCount();
                } else {
                    showNotification(data.message || 'Error adding product to cart', 'error');
                }
            })
            .catch(error => {
                showNotification('Error adding product to cart', 'error');
            });
        }

        // Update cart quantity
        function updateCartQuantity(cartItemId, quantity) {
            if (!isUserLoggedIn()) {
                window.location.href = '/login';
                return;
            }

            fetch(`/cart/update/${cartItemId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Cart updated successfully', 'success');
                    updateCartCount();
                    // Reload cart page if we're on cart page
                    if (window.location.pathname.includes('/cart')) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                } else {
                    showNotification(data.message || 'Error updating cart', 'error');
                }
            })
            .catch(error => {
                showNotification('Error updating cart', 'error');
            });
        }

        // Remove item from cart
        function removeFromCart(cartItemId) {
            if (!isUserLoggedIn()) {
                window.location.href = '/login';
                return;
            }

            if (confirm('Are you sure you want to remove this item from cart?')) {
                fetch(`/cart/remove/${cartItemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Item removed from cart', 'success');
                        updateCartCount();
                        // Reload cart page if we're on cart page
                        if (window.location.pathname.includes('/cart')) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    } else {
                        showNotification(data.message || 'Error removing item', 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error removing item from cart', 'error');
                });
            }
        }

        // Clear entire cart
        function clearCart() {
            if (!isUserLoggedIn()) {
                window.location.href = '/login';
                return;
            }

            if (confirm('Are you sure you want to clear your entire cart?')) {
                fetch('/cart/clear', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Cart cleared successfully', 'success');
                        updateCartCount();
                        // Reload cart page if we're on cart page
                        if (window.location.pathname.includes('/cart')) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    } else {
                        showNotification(data.message || 'Error clearing cart', 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error clearing cart', 'error');
                });
            }
        }

        // Functions for other cart features (from search.blade.php and category.blade.php)
        function toggleWishlist(productId) {
            if (!isUserLoggedIn()) {
                window.location.href = '/login';
                return;
            }

            fetch(`/wishlist/toggle/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const icon = document.getElementById(`wishlist-${productId}`);
                    if (icon) {
                        if (data.added) {
                            icon.classList.remove('far');
                            icon.classList.add('fas', 'text-red-500');
                            showNotification('Added to wishlist!', 'success');
                        } else {
                            icon.classList.remove('fas', 'text-red-500');
                            icon.classList.add('far');
                            showNotification('Removed from wishlist!', 'success');
                        }
                    }
                }
            })
            .catch(error => {
                showNotification('Error updating wishlist', 'error');
            });
        }

        function quickView(productId) {
            fetch(`/products/${productId}/quick-view`)
            .then(response => response.text())
            .then(html => {
                const quickViewContent = document.getElementById('quickViewContent');
                const quickViewModal = document.getElementById('quickViewModal');
                if (quickViewContent && quickViewModal) {
                    quickViewContent.innerHTML = html;
                    quickViewModal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                }
            })
            .catch(error => {
                showNotification('Error loading product details', 'error');
            });
        }

        // Initialize cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });

        // Make functions globally available
        window.updateCartCount = updateCartCount;
        window.showNotification = showNotification;
        window.addToCart = addToCart;
        window.updateCartQuantity = updateCartQuantity;
        window.removeFromCart = removeFromCart;
        window.clearCart = clearCart;
        window.toggleWishlist = toggleWishlist;
        window.quickView = quickView;
        window.isUserLoggedIn = isUserLoggedIn;
    </script>

    @stack('scripts')
    @auth
    @include('components.chat-widget')
@endauth
</body>
</html>