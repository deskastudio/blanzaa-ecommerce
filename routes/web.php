<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ChatController;  // ADD THIS IMPORT
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Product Routes
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/search', [ProductController::class, 'search'])->name('search');
    Route::get('/category/{category:slug}', [ProductController::class, 'category'])->name('category');
    Route::get('/{product:slug}', [ProductController::class, 'show'])->name('show');
    Route::get('/{product}/quick-view', [ProductController::class, 'quickView'])->name('quick-view');
    Route::get('/products/featured', [PageController::class, 'featured'])->name('featured');
});

// FIXED: Cart Routes - gunakan parameter yang konsisten
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::get('/count', [CartController::class, 'count'])->name('count');
    
    // FIXED: Route add menggunakan product ID langsung, bukan product model binding
    Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{cartItem}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{cartItem}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
});

// Static Pages
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms-of-use', [PageController::class, 'termsOfUse'])->name('terms-of-use');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');

// Profile routes
Route::get('/profile', function () {
    if (!auth()->check()) {
        return redirect()->route('login')->with('message', 'Please login to access your profile.');
    }
    return redirect()->route('orders.index');
})->name('profile');

Route::get('/profile/dashboard', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    return redirect()->route('orders.index');
})->name('profile.index')->middleware('auth');

// CHECKOUT ROUTES
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/failed', [CheckoutController::class, 'failed'])->name('checkout.failed');
    Route::get('/checkout/bank-transfer/{order}', [CheckoutController::class, 'bankTransfer'])->name('checkout.bank-transfer');
});

// Protected Customer Routes
Route::middleware(['auth'])->group(function () {
    // Order Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::post('/{order}/reorder', [OrderController::class, 'reorder'])->name('reorder');
        Route::get('/{order}/track', [OrderController::class, 'track'])->name('track');
        Route::get('/{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');
        Route::post('/{order}/upload-payment', [OrderController::class, 'uploadPayment'])->name('upload-payment');
    });
    
    // CHAT ROUTES - ADD THIS SECTION
    Route::prefix('chat')->name('chat.')->group(function () {
        // Customer routes
        Route::post('/conversation', [ChatController::class, 'getOrCreateConversation'])->name('conversation');
        Route::post('/send', [ChatController::class, 'sendMessage'])->name('send');
        Route::get('/messages', [ChatController::class, 'getMessages'])->name('messages');
        Route::post('/mark-read', [ChatController::class, 'markAsRead'])->name('mark-read');
        // TAMBAHAN: Route yang diperlukan widget
        Route::get('/unread-count', [ChatController::class, 'getUnreadCount'])->name('unread-count');
        
        // Admin routes (access controlled in controller)
        Route::get('/admin/conversations', [ChatController::class, 'getConversationsForAdmin'])->name('admin.conversations');
        Route::post('/admin/close', [ChatController::class, 'closeConversation'])->name('admin.close');
    });
});

// API Routes
Route::prefix('api')->name('api.')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/orders/{order}/payment-status', [OrderController::class, 'paymentStatus'])->name('orders.payment-status');
    });
});

// Fallback route
Route::fallback(function () {
    return redirect()->route('home')->with('error', 'Page not found.');
});