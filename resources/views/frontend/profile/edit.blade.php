@extends('layouts.frontend')

@section('title', 'Edit Profile - Exclusive Electronics Store')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-red-500">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('profile.index') }}" class="hover:text-red-500">Profile</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900">Edit Profile</li>
        </ol>
    </nav>

    <div class="bg-white rounded-lg border p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Edit Profile</h1>
            <p class="text-gray-600">Update your personal information and account settings.</p>
        </div>

        <!-- Profile Picture Upload -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4">Profile Picture</h2>
            <div class="flex items-center space-x-6">
                <div class="w-24 h-24 bg-gray-300 rounded-full flex items-center justify-center">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="w-full h-full rounded-full object-cover" id="avatar-preview">
                    @else
                        <i class="fas fa-user text-2xl text-gray-600"></i>
                    @endif
                </div>
                <div>
                    <form id="avatar-form" enctype="multipart/form-data">
                        @csrf
                        <input type="file" id="avatar-input" name="avatar" accept="image/*" class="hidden">
                        <button type="button" onclick="document.getElementById('avatar-input').click()" 
                                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
                            Change Photo
                        </button>
                    </form>
                    <p class="text-sm text-gray-500 mt-1">JPG, PNG, GIF up to 2MB</p>
                </div>
            </div>
        </div>

        <!-- Personal Information Form -->
        <form action="{{ route('profile.update') }}" method="POST" id="profile-form">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <input type="text" id="city" name="city" value="{{ old('city', $user->city) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('city') border-red-500 @enderror">
                    @error('city')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="province" class="block text-sm font-medium text-gray-700 mb-2">Province</label>
                    <input type="text" id="province" name="province" value="{{ old('province', $user->province) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('province') border-red-500 @enderror">
                    @error('province')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('postal_code') border-red-500 @enderror">
                    @error('postal_code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Full Address</label>
                <textarea id="address" name="address" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('address') border-red-500 @enderror">{{ old('address', $user->address) }}</textarea>
                @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Auto-save indicator -->
            <div id="auto-save-indicator" class="hidden">
                <p class="text-sm text-green-600 mb-4">
                    <i class="fas fa-check-circle mr-1"></i>
                    Auto-saved at <span id="save-time"></span>
                </p>
            </div>

            <div class="flex justify-between items-center">
                <a href="{{ route('profile.index') }}" 
                   class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600 transition-colors">
                    Update Profile
                </button>
            </div>
        </form>

        <!-- Change Password Section -->
        <div class="border-t pt-8 mt-8">
            <h2 class="text-lg font-semibold mb-4">Change Password</h2>
            
            <form action="{{ route('profile.password') }}" method="POST" id="password-form">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <input type="password" id="current_password" name="current_password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('current_password') border-red-500 @enderror">
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input type="password" id="password" name="password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" 
                            class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 transition-colors">
                        Change Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Account Management Section -->
        <div class="border-t pt-8 mt-8">
            <h2 class="text-lg font-semibold mb-4 text-red-600">Account Management</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Export Data -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-medium mb-2">Export Your Data</h3>
                    <p class="text-sm text-gray-600 mb-4">Download a copy of all your personal data.</p>
                    <a href="{{ route('profile.export-data') }}" 
                       class="bg-green-500 text-white px-4 py-2 rounded text-sm hover:bg-green-600 transition-colors">
                        Export Data
                    </a>
                </div>

                <!-- Deactivate Account -->
                <div class="border border-yellow-200 rounded-lg p-4">
                    <h3 class="font-medium mb-2">Deactivate Account</h3>
                    <p class="text-sm text-gray-600 mb-4">Temporarily disable your account (can be reactivated).</p>
                    <button type="button" onclick="openDeactivateModal()" 
                            class="bg-yellow-500 text-white px-4 py-2 rounded text-sm hover:bg-yellow-600 transition-colors">
                        Deactivate Account
                    </button>
                </div>

                <!-- Delete Account -->
                <div class="border border-red-200 rounded-lg p-4 md:col-span-2">
                    <h3 class="font-medium mb-2 text-red-600">Delete Account Permanently</h3>
                    <p class="text-sm text-gray-600 mb-4">This action cannot be undone. All your data will be permanently deleted.</p>
                    <button type="button" onclick="openDeleteModal()" 
                            class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700 transition-colors">
                        Delete Account Permanently
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Deactivate Modal -->
<div id="deactivate-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold mb-4">Deactivate Account</h3>
            
            <form action="{{ route('profile.deactivate') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="deactivate_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" id="deactivate_password" name="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason (Optional)</label>
                    <textarea id="reason" name="reason" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                              placeholder="Tell us why you're deactivating your account..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDeactivateModal()" 
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 transition-colors">
                        Deactivate Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold mb-4 text-red-600">Delete Account Permanently</h3>
            <p class="text-sm text-gray-600 mb-4">This action cannot be undone. All your data, orders, and information will be permanently deleted.</p>
            
            <form action="{{ route('profile.delete') }}" method="POST">
                @csrf
                @method('DELETE')
                
                <div class="mb-4">
                    <label for="delete_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" id="delete_password" name="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                
                <div class="mb-4">
                    <label for="confirmation" class="block text-sm font-medium text-gray-700 mb-2">Type "DELETE MY ACCOUNT" to confirm</label>
                    <input type="text" id="confirmation" name="confirmation" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                           placeholder="DELETE MY ACCOUNT">
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDeleteModal()" 
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-colors">
                        Delete Permanently
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Avatar upload handling
document.getElementById('avatar-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatar-preview');
            if (preview) {
                preview.src = e.target.result;
            } else {
                // Create img element if it doesn't exist
                const avatarContainer = document.querySelector('.w-24.h-24');
                avatarContainer.innerHTML = `<img src="${e.target.result}" alt="Avatar" class="w-full h-full rounded-full object-cover" id="avatar-preview">`;
            }
        };
        reader.readAsDataURL(file);

        // Upload via AJAX
        const formData = new FormData();
        formData.append('avatar', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        fetch('{{ route("profile.avatar") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showNotification('Avatar updated successfully!', 'success');
            } else {
                showNotification(data.message || 'Failed to upload avatar', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to upload avatar', 'error');
        });
    }
});

// Auto-save functionality
let autoSaveTimeout;
const autoSaveFields = ['name', 'phone', 'address', 'city', 'province', 'postal_code'];

autoSaveFields.forEach(fieldName => {
    const field = document.getElementById(fieldName);
    if (field) {
        field.addEventListener('input', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(autoSave, 2000); // Auto-save after 2 seconds of inactivity
        });
    }
});

function autoSave() {
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    autoSaveFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field && field.value) {
            formData.append(fieldName, field.value);
        }
    });

    fetch('{{ route("profile.auto-save") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAutoSaveIndicator(data.saved_at);
        }
    })
    .catch(error => {
        console.error('Auto-save error:', error);
    });
}

function showAutoSaveIndicator(time) {
    const indicator = document.getElementById('auto-save-indicator');
    const timeSpan = document.getElementById('save-time');
    
    if (indicator && timeSpan) {
        timeSpan.textContent = time;
        indicator.classList.remove('hidden');
        
        setTimeout(() => {
            indicator.classList.add('hidden');
        }, 3000);
    }
}

// Modal functions
function openDeactivateModal() {
    document.getElementById('deactivate-modal').classList.remove('hidden');
}

function closeDeactivateModal() {
    document.getElementById('deactivate-modal').classList.add('hidden');
}

function openDeleteModal() {
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
}

// Notification function
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.id === 'deactivate-modal') {
        closeDeactivateModal();
    }
    if (e.target.id === 'delete-modal') {
        closeDeleteModal();
    }
});
</script>
@endpush