@extends('layouts.frontend')

@section('title', 'Frequently Asked Questions - Exclusive Electronics Store')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-red-500">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900">FAQ</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h1>
        <p class="text-gray-600 max-w-2xl mx-auto">
            Find answers to common questions about shopping, shipping, returns, and more.
        </p>
    </div>

    <!-- Search FAQ -->
    <div class="mb-8">
        <div class="relative">
            <input type="text" id="faqSearch" placeholder="Search FAQ..." 
                   class="w-full border border-gray-300 rounded-lg px-4 py-3 pl-12 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>
    </div>

    <!-- FAQ Categories -->
    <div class="flex flex-wrap gap-4 mb-8">
        <button onclick="filterFAQ('all')" class="faq-filter-btn active bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">
            All
        </button>
        <button onclick="filterFAQ('orders')" class="faq-filter-btn bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
            Orders
        </button>
        <button onclick="filterFAQ('shipping')" class="faq-filter-btn bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
            Shipping
        </button>
        <button onclick="filterFAQ('returns')" class="faq-filter-btn bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
            Returns
        </button>
        <button onclick="filterFAQ('payments')" class="faq-filter-btn bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
            Payments
        </button>
        <button onclick="filterFAQ('account')" class="faq-filter-btn bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
            Account
        </button>
    </div>

    <!-- FAQ Items -->
    <div class="space-y-4" id="faqContainer">
        <!-- Orders Category -->
        <div class="faq-item bg-white rounded-lg border" data-category="orders">
            <button class="w-full text-left p-6 focus:outline-none" onclick="toggleFAQ(this)">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">How can I track my order?</h3>
                    <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-200"></i>
                </div>
            </button>
            <div class="faq-content hidden px-6 pb-6">
                <p class="text-gray-700">
                    You can track your order by logging into your account and visiting the "My Orders" section. 
                    You can also use the tracking number provided in your order confirmation email on our website 
                    or the carrier's website.
                </p>
            </div>
        </div>

        <div class="faq-item bg-white rounded-lg border" data-category="orders">
            <button class="w-full text-left p-6 focus:outline-none" onclick="toggleFAQ(this)">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Can I modify or cancel my order?</h3>
                    <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-200"></i>
                </div>
            </button>
            <div class="faq-content hidden px-6 pb-6">
                <p class="text-gray-700">
                    You can cancel your order if it hasn't been processed yet. Please contact our customer service 
                    immediately or use the cancel option in your account. Once an order is shipped, it cannot be 
                    modified, but you can return it according to our return policy.
                </p>
            </div>
        </div>

        <!-- Shipping Category -->
        <div class="faq-item bg-white rounded-lg border" data-category="shipping">
            <button class="w-full text-left p-6 focus:outline-none" onclick="toggleFAQ(this)">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">What are your shipping options and costs?</h3>
                    <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-200"></i>
                </div>
            </button>
            <div class="faq-content hidden px-6 pb-6">
                <p class="text-gray-700 mb-3">We offer several shipping options:</p>
                <ul class="list-disc pl-6 text-gray-700">
                    <li><strong>Standard Shipping:</strong> 5-7 business days - Rp 25,000</li>
                    <li><strong>Express Shipping:</strong> 2-3 business days - Rp 50,000</li>
                    <li><strong>Free Shipping:</strong> Orders over Rp 1,000,000 qualify for free standard shipping</li>
                </ul>
            </div>
        </div>

        <div class="faq-item bg-white rounded-lg border" data-category="shipping">
            <button class="w-full text-left p-6 focus:outline-none" onclick="toggleFAQ(this)">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Do you ship internationally?</h3>
                    <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-200"></i>
                </div>
            </button>
            <div class="faq-content hidden px-6 pb-6">
                <p class="text-gray-700">
                    Currently, we only ship within Indonesia. We are working on expanding our shipping coverage 
                    to other countries in Southeast Asia. Please check back for updates.
                </p>
            </div>
        </div>

        <!-- Returns Category -->
        <div class="faq-item bg-white rounded-lg border" data-category="returns">
            <button class="w-full text-left p-6 focus:outline-none" onclick="toggleFAQ(this)">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">What is your return policy?</h3>
                    <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-200"></i>
                </div>
            </button>
            <div class="faq-content hidden px-6 pb-6">
                <p class="text-gray-700 mb-3">We offer a 30-day return policy with the following conditions:</p>
                <ul class="list-disc pl-6 text-gray-700">
                    <li>Items must be unused and in original packaging</li>
                    <li>Include all accessories and documentation</li>
                    <li>Some items like software and personalized products cannot be returned</li>
                    <li>Return shipping costs may apply unless the item is defective</li>
                </ul>
            </div>
        </div>

        <div class="faq-item bg-white rounded-lg border" data-category="returns">
            <button class="w-full text-left p-6 focus:outline-none" onclick="toggleFAQ(this)">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">How do I initiate a return?</h3>
                    <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-200"></i>
                </div>
            </button>
            <div class="faq-content hidden px-6 pb-6">
                <p class="text-gray-700">
                    To initiate a return, log into your account, go to "My Orders," find the order you want to return, 
                    and click "Return Item." You can also contact our customer service team for assistance. 
                    We'll provide you with a return authorization and shipping instructions.
                </p>
            </div>
        </div>

        <!-- Payments Category -->
        <div class="faq-item bg-white rounded-lg border" data-category="payments">
            <button class="w-full text-left p-6 focus:outline-none" onclick="toggleFAQ(this)">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">What payment methods do you accept?</h3>
                    <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-200"></i>
                </div>
            </button>
            <div class="faq-content hidden px-6 pb-6">
                <p class="text-gray-700 mb-3">We accept the following payment methods:</p>
                <ul class="list-disc pl-6 text-gray-700">
                    <li>Bank Transfer (BCA, Mandiri, BNI, BRI)</li>
                    <li>E-wallets (GoPay, OVO, Dana, LinkAja)</li>
                    <li>Credit/Debit Cards (Visa, Mastercard)</li>
                    <li>Cash on Delivery (selected areas only)</li>
                </ul>
            </div>
        </div>

        <div class="faq-item bg-white rounded-lg border" data-category="payments">
            <button class="w-full text-left p-6 focus:outline-none" onclick="toggleFAQ(this)">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Is my payment information secure?</h3>
                    <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-200"></i>
                </div>
            </button>
            <div class="faq-content hidden px-6 pb-6">
                <p class="text-gray-700">
                    Yes, we use industry-standard SSL encryption to protect your payment information. We partner with 
                    trusted payment processors and do not store your complete credit card information on our servers. 
                    All transactions are processed securely.
                </p>
            </div>
        </div>

        <!-- Account Category -->
        <div class="faq-item bg-white rounded-lg border" data-category="account">
            <button class="w-full text-left p-6 focus:outline-none" onclick="toggleFAQ(this)">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">How do I create an account?</h3>
                    <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-200"></i>
                </div>
            </button>
            <div class="faq-content hidden px-6 pb-6">
                <p class="text-gray-700">
                    Click "Sign Up" in the top navigation, fill in your details (name, email, password), and verify 
                    your email address. You can also create an account during the checkout process when placing your first order.
                </p>
            </div>
        </div>

        <div class="faq-item bg-white rounded-lg border" data-category="account">
            <button class="w-full text-left p-6 focus:outline-none" onclick="toggleFAQ(this)">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">I forgot my password. How do I reset it?</h3>
                    <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-200"></i>
                </div>
            </button>
            <div class="faq-content hidden px-6 pb-6">
                <p class="text-gray-700">
                    On the login page, click "Forgot Password," enter your email address, and we'll send you a 
                    password reset link. Follow the instructions in the email to create a new password.
                </p>
            </div>
        </div>

        <!-- General -->
        <div class="faq-item bg-white rounded-lg border" data-category="general">
            <button class="w-full text-left p-6 focus:outline-none" onclick="toggleFAQ(this)">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Do you offer warranty on products?</h3>
                    <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-200"></i>
                </div>
            </button>
            <div class="faq-content hidden px-6 pb-6">
                <p class="text-gray-700">
                    Yes, all our products come with manufacturer warranty. Warranty periods vary by product and brand, 
                    typically ranging from 1-3 years. Warranty information is clearly displayed on each product page. 
                    We also offer extended warranty plans for additional protection.
                </p>
            </div>
        </div>

        <div class="faq-item bg-white rounded-lg border" data-category="general">
            <button class="w-full text-left p-6 focus:outline-none" onclick="toggleFAQ(this)">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">How can I contact customer service?</h3>
                    <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-200"></i>
                </div>
            </button>
            <div class="faq-content hidden px-6 pb-6">
                <p class="text-gray-700 mb-3">You can reach our customer service team through:</p>
                <ul class="list-disc pl-6 text-gray-700">
                    <li><strong>Email:</strong> support@exclusive-electronics.com</li>
                    <li><strong>Phone:</strong> +62 21 1234 5678 (9 AM - 6 PM, Mon-Fri)</li>
                    <li><strong>WhatsApp:</strong> +62 812 3456 7890</li>
                    <li><strong>Contact Form:</strong> Available on our Contact page</li>
                </ul>
            </div>
        </div>

        <div class="faq-item bg-white rounded-lg border" data-category="general">
            <button class="w-full text-left p-6 focus:outline-none" onclick="toggleFAQ(this)">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Are your products authentic?</h3>
                    <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-200"></i>
                </div>
            </button>
            <div class="faq-content hidden px-6 pb-6">
                <p class="text-gray-700">
                    Absolutely! We only source products from authorized distributors and official brand partners. 
                    All our products are 100% authentic and come with proper documentation. We guarantee the 
                    authenticity of every item we sell.
                </p>
            </div>
        </div>
    </div>

    <!-- Still have questions -->
    <div class="mt-12 bg-red-50 rounded-lg p-8 text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Still have questions?</h2>
        <p class="text-gray-600 mb-6">
            Can't find the answer you're looking for? Our customer support team is here to help.
        </p>
        <a href="{{ route('contact') }}" 
           class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 transition-colors inline-block">
            Contact Support
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Toggle FAQ item
function toggleFAQ(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

// Filter FAQ by category
function filterFAQ(category) {
    const items = document.querySelectorAll('.faq-item');
    const buttons = document.querySelectorAll('.faq-filter-btn');
    
    // Update button styles
    buttons.forEach(btn => {
        btn.classList.remove('active', 'bg-red-500', 'text-white');
        btn.classList.add('bg-gray-100', 'text-gray-700');
    });
    
    event.target.classList.remove('bg-gray-100', 'text-gray-700');
    event.target.classList.add('active', 'bg-red-500', 'text-white');
    
    // Filter items
    items.forEach(item => {
        if (category === 'all' || item.dataset.category === category) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Search FAQ
document.getElementById('faqSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const items = document.querySelectorAll('.faq-item');
    
    items.forEach(item => {
        const question = item.querySelector('h3').textContent.toLowerCase();
        const answer = item.querySelector('.faq-content p').textContent.toLowerCase();
        
        if (question.includes(searchTerm) || answer.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
    
    // If searching, show all categories
    if (searchTerm) {
        const buttons = document.querySelectorAll('.faq-filter-btn');
        buttons.forEach(btn => {
            btn.classList.remove('active', 'bg-red-500', 'text-white');
            btn.classList.add('bg-gray-100', 'text-gray-700');
        });
    }
});

// Auto-expand if URL contains anchor
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash) {
        const targetId = window.location.hash.substring(1);
        const targetElement = document.getElementById(targetId);
        if (targetElement) {
            const button = targetElement.querySelector('button');
            if (button) {
                toggleFAQ(button);
                targetElement.scrollIntoView({ behavior: 'smooth' });
            }
        }
    }
});
</script>
@endpush