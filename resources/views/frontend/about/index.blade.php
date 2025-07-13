@extends('layouts.frontend')

@section('title', 'About Us - Exclusive Electronics Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-red-500">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900">About</li>
        </ol>
    </nav>

    <!-- Hero Section -->
    <div class="text-center mb-16">
        <h1 class="text-5xl font-bold text-gray-900 mb-6">About Exclusive</h1>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
            Your trusted partner for premium electronics and innovative technology solutions. 
            We're passionate about bringing you the latest and greatest in consumer electronics.
        </p>
    </div>

    <!-- Our Story -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
        <div class="space-y-6">
            <h2 class="text-3xl font-bold text-gray-900">Our Story</h2>
            <p class="text-gray-700 leading-relaxed">
                Founded in 2020, Exclusive Electronics Store began as a small startup with a big vision: 
                to make cutting-edge technology accessible to everyone. What started as a passion project 
                has grown into one of Indonesia's most trusted electronics retailers.
            </p>
            <p class="text-gray-700 leading-relaxed">
                We believe that technology should enhance lives, not complicate them. That's why we 
                carefully curate our product selection, ensuring that every item meets our high standards 
                for quality, performance, and value.
            </p>
            <p class="text-gray-700 leading-relaxed">
                Today, we serve thousands of satisfied customers across Indonesia, offering everything 
                from smartphones and laptops to smart home devices and gaming equipment.
            </p>
        </div>
        
        <div class="bg-gray-100 rounded-lg flex items-center justify-center h-96">
            <div class="text-center">
                <i class="fas fa-store text-6xl text-red-500 mb-4"></i>
                <p class="text-gray-600">Our Store Image</p>
            </div>
        </div>
    </div>

    <!-- Mission & Vision -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
        <div class="bg-white rounded-lg border p-8">
            <div class="w-16 h-16 bg-red-100 rounded-lg flex items-center justify-center mb-6">
                <i class="fas fa-bullseye text-red-500 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Our Mission</h3>
            <p class="text-gray-700 leading-relaxed">
                To provide our customers with the latest technology products at competitive prices, 
                backed by exceptional customer service and expert support. We strive to make technology 
                shopping simple, enjoyable, and accessible for everyone.
            </p>
        </div>
        
        <div class="bg-white rounded-lg border p-8">
            <div class="w-16 h-16 bg-red-100 rounded-lg flex items-center justify-center mb-6">
                <i class="fas fa-eye text-red-500 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Our Vision</h3>
            <p class="text-gray-700 leading-relaxed">
                To become the leading electronics retailer in Southeast Asia, known for our innovative 
                approach, customer-centric service, and commitment to bringing the future of technology 
                to our customers today.
            </p>
        </div>
    </div>

    <!-- Values -->
    <div class="mb-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Values</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                These core values guide everything we do and shape how we serve our customers
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-heart text-red-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Customer First</h3>
                <p class="text-gray-600">
                    Our customers are at the heart of everything we do. We listen, we care, and we deliver.
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-star text-red-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Quality</h3>
                <p class="text-gray-600">
                    We never compromise on quality. Every product is carefully selected and tested.
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-rocket text-red-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Innovation</h3>
                <p class="text-gray-600">
                    We embrace new technologies and innovative solutions to better serve you.
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-handshake text-red-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Trust</h3>
                <p class="text-gray-600">
                    We build lasting relationships based on transparency, honesty, and reliability.
                </p>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="bg-red-500 rounded-lg text-white p-12 mb-16">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold mb-2">50K+</div>
                <div class="text-red-100">Happy Customers</div>
            </div>
            <div>
                <div class="text-4xl font-bold mb-2">10K+</div>
                <div class="text-red-100">Products Sold</div>
            </div>
            <div>
                <div class="text-4xl font-bold mb-2">500+</div>
                <div class="text-red-100">Product Varieties</div>
            </div>
            <div>
                <div class="text-4xl font-bold mb-2">24/7</div>
                <div class="text-red-100">Customer Support</div>
            </div>
        </div>
    </div>

    <!-- Why Choose Us -->
    <div class="mb-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Choose Exclusive?</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Here's what makes us different from other electronics retailers
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-lg border p-6 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-shipping-fast text-red-500"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Fast & Free Shipping</h3>
                <p class="text-gray-600">
                    Free shipping on orders over Rp 1.000.000. Express delivery available nationwide.
                </p>
            </div>
            
            <div class="bg-white rounded-lg border p-6 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-shield-alt text-red-500"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Warranty Protection</h3>
                <p class="text-gray-600">
                    All products come with manufacturer warranty and our extended protection plans.
                </p>
            </div>
            
            <div class="bg-white rounded-lg border p-6 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-undo text-red-500"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Easy Returns</h3>
                <p class="text-gray-600">
                    30-day hassle-free returns. Not satisfied? We'll make it right.
                </p>
            </div>
            
            <div class="bg-white rounded-lg border p-6 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-headset text-red-500"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">24/7 Support</h3>
                <p class="text-gray-600">
                    Our expert support team is available round the clock to help you.
                </p>
            </div>
            
            <div class="bg-white rounded-lg border p-6 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-certificate text-red-500"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Authentic Products</h3>
                <p class="text-gray-600">
                    100% genuine products sourced directly from authorized distributors.
                </p>
            </div>
            
            <div class="bg-white rounded-lg border p-6 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-credit-card text-red-500"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Secure Payment</h3>
                <p class="text-gray-600">
                    Multiple secure payment options including bank transfer and e-wallets.
                </p>
            </div>
        </div>
    </div>

    <!-- Team Section -->
    <div class="mb-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Meet Our Team</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                The passionate people behind Exclusive Electronics Store
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-32 h-32 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-user text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-1">John Doe</h3>
                <p class="text-red-500 mb-2">CEO & Founder</p>
                <p class="text-gray-600 text-sm">
                    Visionary leader with 15+ years experience in technology and retail.
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-32 h-32 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-user text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-1">Jane Smith</h3>
                <p class="text-red-500 mb-2">Head of Operations</p>
                <p class="text-gray-600 text-sm">
                    Operations expert ensuring smooth delivery and customer satisfaction.
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-32 h-32 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-user text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-1">Mike Johnson</h3>
                <p class="text-red-500 mb-2">Tech Director</p>
                <p class="text-gray-600 text-sm">
                    Technology enthusiast keeping us at the forefront of innovation.
                </p>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gray-50 rounded-lg p-12 text-center">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Ready to Experience the Difference?</h2>
        <p class="text-gray-600 mb-8 max-w-2xl mx-auto">
            Join thousands of satisfied customers who trust Exclusive for their technology needs. 
            Discover the latest gadgets and electronics with unmatched service.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('products.index') }}" 
               class="bg-red-500 text-white px-8 py-3 rounded-lg hover:bg-red-600 transition-colors font-medium">
                Shop Now
            </a>
            <a href="{{ route('contact') }}" 
               class="bg-white text-gray-700 border border-gray-300 px-8 py-3 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                Contact Us
            </a>
        </div>
    </div>
</div>

@endsection