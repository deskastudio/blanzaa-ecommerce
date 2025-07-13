@extends('layouts.frontend')

@section('title', 'Privacy Policy - Exclusive Electronics Store')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-red-500">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900">Privacy Policy</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Privacy Policy</h1>
        <p class="text-gray-600">Last updated: {{ date('F d, Y') }}</p>
    </div>

    <!-- Content -->
    <div class="bg-white rounded-lg border p-8 prose max-w-none">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Information We Collect</h2>
        <p class="text-gray-700 mb-6">
            We collect information you provide directly to us, such as when you create an account, make a purchase, 
            or contact us. This may include:
        </p>
        <ul class="list-disc pl-6 mb-6 text-gray-700">
            <li>Personal information (name, email address, phone number, shipping address)</li>
            <li>Payment information (credit card details, billing address)</li>
            <li>Account information (username, password, preferences)</li>
            <li>Communication data (messages, support tickets, reviews)</li>
        </ul>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">2. How We Use Your Information</h2>
        <p class="text-gray-700 mb-4">We use the information we collect to:</p>
        <ul class="list-disc pl-6 mb-6 text-gray-700">
            <li>Process and fulfill your orders</li>
            <li>Provide customer service and support</li>
            <li>Send you order confirmations and shipping updates</li>
            <li>Improve our products and services</li>
            <li>Send marketing communications (with your consent)</li>
            <li>Detect and prevent fraud</li>
            <li>Comply with legal obligations</li>
        </ul>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Information Sharing</h2>
        <p class="text-gray-700 mb-4">We do not sell, trade, or rent your personal information to third parties. We may share your information in the following circumstances:</p>
        <ul class="list-disc pl-6 mb-6 text-gray-700">
            <li><strong>Service Providers:</strong> With trusted third-party service providers who help us operate our business</li>
            <li><strong>Legal Requirements:</strong> When required by law or to protect our rights</li>
            <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of assets</li>
            <li><strong>Consent:</strong> With your explicit consent for other purposes</li>
        </ul>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Data Security</h2>
        <p class="text-gray-700 mb-6">
            We implement appropriate technical and organizational measures to protect your personal information against 
            unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the 
            internet is 100% secure.
        </p>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Your Rights</h2>
        <p class="text-gray-700 mb-4">You have the right to:</p>
        <ul class="list-disc pl-6 mb-6 text-gray-700">
            <li>Access your personal information</li>
            <li>Correct inaccurate information</li>
            <li>Delete your account and personal information</li>
            <li>Object to processing of your information</li>
            <li>Data portability</li>
            <li>Withdraw consent at any time</li>
        </ul>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Cookies and Tracking</h2>
        <p class="text-gray-700 mb-6">
            We use cookies and similar tracking technologies to enhance your browsing experience, analyze website traffic, 
            and personalize content. You can control cookie preferences through your browser settings.
        </p>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Data Retention</h2>
        <p class="text-gray-700 mb-6">
            We retain your personal information for as long as necessary to fulfill the purposes outlined in this policy, 
            unless a longer retention period is required by law.
        </p>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Children's Privacy</h2>
        <p class="text-gray-700 mb-6">
            Our services are not intended for children under 13 years of age. We do not knowingly collect personal 
            information from children under 13.
        </p>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Changes to This Policy</h2>
        <p class="text-gray-700 mb-6">
            We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new 
            Privacy Policy on this page and updating the "Last updated" date.
        </p>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Contact Us</h2>
        <p class="text-gray-700 mb-4">
            If you have any questions about this Privacy Policy, please contact us:
        </p>
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-gray-700 mb-2"><strong>Email:</strong> privacy@exclusive-electronics.com</p>
            <p class="text-gray-700 mb-2"><strong>Phone:</strong> +62 21 1234 5678</p>
            <p class="text-gray-700"><strong>Address:</strong> Jl. Sudirman No. 123, Jakarta Pusat 10220, Indonesia</p>
        </div>
    </div>
</div>

@endsection