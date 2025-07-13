{{-- Buat file: resources/views/frontend/checkout/bank-transfer.blade.php --}}

@extends('layouts.frontend')

@section('title', 'Bank Transfer Payment - Exclusive Electronics Store')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="text-center mb-8">
        <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-university text-4xl text-blue-600"></i>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Complete Your Payment</h1>
        <p class="text-gray-600">Please transfer the amount to complete your order</p>
    </div>

    <!-- Order Details -->
    <div class="bg-white rounded-lg border p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Order #{{ $order->order_number }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-gray-600">Total Amount</p>
                <p class="text-2xl font-bold text-red-500">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-gray-600">Order Date</p>
                <p class="font-semibold">{{ $order->created_at->format('M d, Y g:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Bank Transfer Instructions -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
        <h3 class="font-semibold text-yellow-800 mb-4">Bank Transfer Instructions</h3>
        <div class="space-y-4">
            <div class="bg-white rounded p-4">
                <h4 class="font-medium text-gray-900 mb-3">Transfer Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Bank Name</p>
                        <p class="font-semibold">Bank Central Asia (BCA)</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Account Number</p>
                        <p class="font-semibold">1234567890</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Account Name</p>
                        <p class="font-semibold">Exclusive Electronics Store</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Amount</p>
                        <p class="font-semibold text-red-500">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 rounded p-4">
                <h4 class="font-medium text-blue-900 mb-2">Important Notes:</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Please transfer the EXACT amount: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</li>
                    <li>• Include your order number ({{ $order->order_number }}) in the transfer description</li>
                    <li>• Upload your payment proof below after completing the transfer</li>
                    <li>• Payment verification usually takes 1-24 hours</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Upload Payment Proof -->
    <div class="bg-white rounded-lg border p-6 mb-6">
        <h3 class="font-semibold text-gray-900 mb-4">Upload Payment Proof</h3>
        <form id="paymentProofForm" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Proof Image</label>
                    <input type="file" name="payment_proof" accept="image/*" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <p class="text-xs text-gray-500 mt-1">Upload a clear image of your transfer receipt (Max: 2MB, JPG/PNG)</p>
                </div>
                
                <button type="submit" class="w-full bg-red-500 text-white py-3 rounded-lg hover:bg-red-600 transition-colors font-medium">
                    <span id="uploadText">Upload Payment Proof</span>
                    <span id="uploadLoading" class="hidden">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Uploading...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('orders.show', $order) }}" 
           class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-colors text-center">
            <i class="fas fa-eye mr-2"></i>View Order Details
        </a>
        <a href="{{ route('orders.index') }}" 
           class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition-colors text-center">
            <i class="fas fa-list mr-2"></i>View All Orders
        </a>
    </div>
</div>

@endsection

@push('scripts')
// GANTI script di bank-transfer.blade.php dengan ini:
// GANTI script di bank-transfer.blade.php dengan ini:
<script>
document.getElementById('paymentProofForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const fileInput = form.querySelector('input[type="file"]');
    const uploadText = document.getElementById('uploadText');
    const uploadLoading = document.getElementById('uploadLoading');
    const submitButton = form.querySelector('button[type="submit"]');
    
    // Validate file selection
    if (!fileInput.files.length) {
        alert('Please select a file to upload');
        return;
    }
    
    const file = fileInput.files[0];
    
    // Validate file size (2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('File size must be less than 2MB');
        return;
    }
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
        alert('Please upload an image file (JPG, PNG, GIF)');
        return;
    }
    
    // Show loading state
    uploadText.classList.add('hidden');
    uploadLoading.classList.remove('hidden');
    submitButton.disabled = true;
    
    // FIXED: Gunakan route orders upload payment, bukan checkout
    fetch(`{{ route('orders.upload-payment', $order) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => {
        // Handle both success and error responses
        return response.json().then(data => {
            return {
                ok: response.ok,
                status: response.status,
                data: data
            };
        });
    })
    .then(result => {
        console.log('Upload result:', result); // Debug log
        
        if (result.ok && result.data.success) {
            // Success
            alert(result.data.message || 'Payment proof uploaded successfully!');
            
            // Show success message on page
            const successDiv = document.createElement('div');
            successDiv.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4';
            successDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>${result.data.message}</span>
                </div>
            `;
            form.parentNode.insertBefore(successDiv, form);
            
            // Hide form after successful upload
            form.style.display = 'none';
            
            // Optional: redirect after 3 seconds
            setTimeout(() => {
                window.location.href = `{{ route('orders.show', $order) }}`;
            }, 3000);
            
        } else {
            // Error from server
            const errorMessage = result.data.message || 'Upload failed';
            alert('Error: ' + errorMessage);
            
            // Show detailed error if available
            if (result.data.error_details && {{ config('app.debug') ? 'true' : 'false' }}) {
                console.error('Upload error details:', result.data.error_details);
            }
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        alert('Network error occurred. Please check your internet connection and try again.');
    })
    .finally(() => {
        // Hide loading state
        uploadText.classList.remove('hidden');
        uploadLoading.classList.add('hidden');
        submitButton.disabled = false;
    });
});

// Add file preview functionality
document.querySelector('input[type="file"]').addEventListener('change', function(e) {
    const file = e.target.files[0];
    
    // Remove existing preview
    const existingPreview = document.getElementById('filePreview');
    if (existingPreview) {
        existingPreview.remove();
    }
    
    if (file) {
        // Show file info
        const fileInfo = document.createElement('div');
        fileInfo.id = 'filePreview';
        fileInfo.className = 'mt-2 p-2 bg-gray-100 rounded text-sm';
        fileInfo.innerHTML = `
            <div class="flex items-center justify-between">
                <span>📎 ${file.name}</span>
                <span class="text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
            </div>
        `;
        this.parentNode.appendChild(fileInfo);
        
        // Show image preview if it's an image
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'mt-2 max-w-xs max-h-32 object-contain border rounded';
                img.alt = 'Payment proof preview';
                fileInfo.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    }
});
</script>
@endpush