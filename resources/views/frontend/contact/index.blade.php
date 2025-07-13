@extends('layouts.frontend')

@section('title', 'Contact Us - Exclusive Electronics Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-red-500">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900">Contact</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Contact Us</h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            We'd love to hear from you. Get in touch with us for any questions, support, or feedback.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Contact Form -->
        <div class="bg-white rounded-lg border p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Send us a Message</h2>
            
            <form id="contactForm" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                        <input type="text" id="first_name" name="first_name" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                    <input type="email" id="email" name="email" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" id="phone" name="phone"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                    <select id="subject" name="subject" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">Select a subject</option>
                        <option value="general">General Inquiry</option>
                        <option value="order">Order Support</option>
                        <option value="product">Product Question</option>
                        <option value="technical">Technical Support</option>
                        <option value="complaint">Complaint</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                    <textarea id="message" name="message" rows="6" required
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Please describe your message..."></textarea>
                </div>
                
                <button type="submit" 
                        class="w-full bg-red-500 text-white py-3 px-6 rounded-lg hover:bg-red-600 transition-colors font-medium">
                    <span id="submitText">Send Message</span>
                    <span id="submitLoading" class="hidden">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Sending...
                    </span>
                </button>
            </form>
        </div>

        <!-- Contact Information -->
        <div class="space-y-8">
            <!-- Contact Details -->
            <div class="bg-white rounded-lg border p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Get in Touch</h2>
                
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-red-500 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">Address</h3>
                            <p class="text-gray-600">
                                Jl. Sudirman No. 123<br>
                                Jakarta Pusat 10220<br>
                                Indonesia
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-phone text-red-500 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">Phone</h3>
                            <p class="text-gray-600">
                                <a href="tel:+6221123456789" class="hover:text-red-500">+62 21 1234 5678</a><br>
                                <a href="tel:+6281234567890" class="hover:text-red-500">+62 812 3456 7890</a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-envelope text-red-500 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">Email</h3>
                            <p class="text-gray-600">
                                <a href="mailto:info@exclusive-electronics.com" class="hover:text-red-500">info@exclusive-electronics.com</a><br>
                                <a href="mailto:support@exclusive-electronics.com" class="hover:text-red-500">support@exclusive-electronics.com</a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-red-500 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">Business Hours</h3>
                            <p class="text-gray-600">
                                Monday - Friday: 9:00 AM - 6:00 PM<br>
                                Saturday: 9:00 AM - 4:00 PM<br>
                                Sunday: Closed
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Media -->
            <div class="bg-white rounded-lg border p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Follow Us</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <a href="#" class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition-colors">
                        <i class="fab fa-facebook text-blue-600 text-2xl"></i>
                        <span class="font-medium text-gray-900">Facebook</span>
                    </a>
                    
                    <a href="#" class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition-colors">
                        <i class="fab fa-instagram text-pink-600 text-2xl"></i>
                        <span class="font-medium text-gray-900">Instagram</span>
                    </a>
                    
                    <a href="#" class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition-colors">
                        <i class="fab fa-twitter text-blue-400 text-2xl"></i>
                        <span class="font-medium text-gray-900">Twitter</span>
                    </a>
                    
                    <a href="#" class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition-colors">
                        <i class="fab fa-whatsapp text-green-600 text-2xl"></i>
                        <span class="font-medium text-gray-900">WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="mt-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
            <p class="text-gray-600">Common questions and answers to help you quickly</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-lg border p-6">
                <h3 class="font-semibold text-gray-900 mb-3">How can I track my order?</h3>
                <p class="text-gray-600">You can track your order by logging into your account and visiting the "My Orders" section, or by using the tracking number provided in your order confirmation email.</p>
            </div>
            
            <div class="bg-white rounded-lg border p-6">
                <h3 class="font-semibold text-gray-900 mb-3">What is your return policy?</h3>
                <p class="text-gray-600">We offer a 30-day return policy for unused items in original packaging. Please contact our support team to initiate a return.</p>
            </div>
            
            <div class="bg-white rounded-lg border p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Do you offer warranty on products?</h3>
                <p class="text-gray-600">Yes, all our products come with manufacturer warranty. Warranty period varies by product and is clearly mentioned on each product page.</p>
            </div>
            
            <div class="bg-white rounded-lg border p-6">
                <h3 class="font-semibold text-gray-900 mb-3">How long does shipping take?</h3>
                <p class="text-gray-600">Standard shipping takes 3-7 business days. Express shipping options are available for faster delivery within 1-2 business days.</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const submitText = document.getElementById('submitText');
    const submitLoading = document.getElementById('submitLoading');
    const submitButton = form.querySelector('button[type="submit"]');
    
    // Show loading state
    submitText.classList.add('hidden');
    submitLoading.classList.remove('hidden');
    submitButton.disabled = true;
    
    // Simulate form submission (replace with actual endpoint)
    setTimeout(() => {
        // Success simulation
        showNotification('Thank you for your message! We will get back to you soon.', 'success');
        form.reset();
        
        // Hide loading state
        submitText.classList.remove('hidden');
        submitLoading.classList.add('hidden');
        submitButton.disabled = false;
    }, 2000);
    
    // Actual implementation would be:
    /*
    fetch('/contact', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Thank you for your message! We will get back to you soon.', 'success');
            form.reset();
        } else {
            showNotification(data.message || 'Error sending message', 'error');
        }
    })
    .catch(error => {
        showNotification('Error sending message. Please try again.', 'error');
    })
    .finally(() => {
        submitText.classList.remove('hidden');
        submitLoading.classList.add('hidden');
        submitButton.disabled = false;
    });
    */
});

function showNotification(message, type = 'success') {
    if (typeof window.showNotification === 'function') {
        window.showNotification(message, type);
    } else {
        alert(message);
    }
}
</script>
@endpush