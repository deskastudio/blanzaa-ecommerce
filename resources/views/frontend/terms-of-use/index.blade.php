@extends('layouts.frontend')

@section('title', 'Terms of Use - Exclusive Electronics Store')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-red-500">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900">Terms of Use</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Terms of Use</h1>
        <p class="text-gray-600">Last updated: {{ date('F d, Y') }}</p>
    </div>

    <!-- Content -->
    <div class="bg-white rounded-lg border p-8 prose max-w-none">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Acceptance of Terms</h2>
        <p class="text-gray-700 mb-6">
            By accessing and using the Exclusive Electronics Store website and services, you accept and agree to be bound 
            by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.
        </p>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Use License</h2>
        <p class="text-gray-700 mb-4">
            Permission is granted to temporarily download one copy of the materials on Exclusive Electronics Store's website 
            for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:
        </p>
        <ul class="list-disc pl-6 mb-6 text-gray-700">
            <li>modify or copy the materials</li>
            <li>use the materials for any commercial purpose or for any public display</li>
            <li>attempt to reverse engineer any software contained on the website</li>
            <li>remove any copyright or other proprietary notations from the materials</li>
        </ul>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Account Registration</h2>
        <p class="text-gray-700 mb-4">To access certain features, you must register for an account. You agree to:</p>
        <ul class="list-disc pl-6 mb-6 text-gray-700">
            <li>Provide accurate, current, and complete information</li>
            <li>Maintain and update your information</li>
            <li>Keep your password secure and confidential</li>
            <li>Accept responsibility for all activities under your account</li>
            <li>Notify us immediately of any unauthorized use</li>
        </ul>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Product Information</h2>
        <p class="text-gray-700 mb-6">
            We strive to provide accurate product information, but we do not warrant that product descriptions, prices, 
            or other content is accurate, complete, reliable, current, or error-free. We reserve the right to correct 
            errors and update information at any time.
        </p>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Orders and Payments</h2>
        <p class="text-gray-700 mb-4">By placing an order, you agree to:</p>
        <ul class="list-disc pl-6 mb-6 text-gray-700">
            <li>Provide valid payment information</li>
            <li>Pay all charges incurred by your account</li>
            <li>Accept that orders are subject to availability</li>
            <li>Understand that we may cancel orders at our discretion</li>
            <li>Comply with our return and refund policies</li>
        </ul>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Shipping and Delivery</h2>
        <p class="text-gray-700 mb-6">
            We will make reasonable efforts to deliver products within the estimated timeframe. However, delivery dates 
            are estimates and we are not liable for delays. Risk of loss and title pass to you upon delivery to the carrier.
        </p>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Returns and Refunds</h2>
        <p class="text-gray-700 mb-4">Our return policy includes:</p>
        <ul class="list-disc pl-6 mb-6 text-gray-700">
            <li>30-day return window from delivery date</li>
            <li>Items must be unused and in original packaging</li>
            <li>Return shipping costs may apply</li>
            <li>Certain items may not be returnable</li>
            <li>Refunds processed within 5-7 business days</li>
        </ul>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Prohibited Uses</h2>
        <p class="text-gray-700 mb-4">You may not use our service:</p>
        <ul class="list-disc pl-6 mb-6 text-gray-700">
            <li>For any unlawful purpose or to solicit others to perform unlawful acts</li>
            <li>To violate any international, federal, provincial, or state regulations, rules, laws, or local ordinances</li>
            <li>To infringe upon or violate our intellectual property rights or the intellectual property rights of others</li>
            <li>To harass, abuse, insult, harm, defame, slander, disparage, intimidate, or discriminate</li>
            <li>To submit false or misleading information</li>
        </ul>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Intellectual Property</h2>
        <p class="text-gray-700 mb-6">
            The service and its original content, features, and functionality are and will remain the exclusive property 
            of Exclusive Electronics Store and its licensors. The service is protected by copyright, trademark, and other laws.
        </p>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Disclaimer</h2>
        <p class="text-gray-700 mb-6">
            The materials on Exclusive Electronics Store's website are provided on an 'as is' basis. Exclusive Electronics Store 
            makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, 
            without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, 
            or non-infringement of intellectual property or other violation of rights.
        </p>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Limitations</h2>
        <p class="text-gray-700 mb-6">
            In no event shall Exclusive Electronics Store or its suppliers be liable for any damages (including, without limitation, 
            damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use 
            the materials on our website, even if we or our authorized representative has been notified orally or in writing 
            of the possibility of such damage.
        </p>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">12. Governing Law</h2>
        <p class="text-gray-700 mb-6">
            These terms and conditions are governed by and construed in accordance with the laws of Indonesia and you 
            irrevocably submit to the exclusive jurisdiction of the courts in Jakarta, Indonesia.
        </p>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">13. Changes to Terms</h2>
        <p class="text-gray-700 mb-6">
            We reserve the right to revise these terms of service at any time without notice. By using this website, 
            you are agreeing to be bound by the then current version of these terms of service.
        </p>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">14. Contact Information</h2>
        <p class="text-gray-700 mb-4">
            If you have any questions about these Terms of Use, please contact us:
        </p>
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-gray-700 mb-2"><strong>Email:</strong> legal@exclusive-electronics.com</p>
            <p class="text-gray-700 mb-2"><strong>Phone:</strong> +62 21 1234 5678</p>
            <p class="text-gray-700"><strong>Address:</strong> Jl. Sudirman No. 123, Jakarta Pusat 10220, Indonesia</p>
        </div>
    </div>
</div>

@endsection