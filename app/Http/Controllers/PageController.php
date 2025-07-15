<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Category;

class PageController extends Controller
{
    /**
     * Show the home page
     */
    public function home()
    {
        // Get top categories with product count
        $topCategories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(10)
            ->get();

        // Get featured products (menggantikan best selling)
        $featuredProducts = Product::with(['category', 'images'])
            ->featured() // menggunakan scope featured
            ->active()
            ->inStock()
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Get flash sale products (produk dengan discount)
        $flashSaleProducts = Product::with(['category', 'images'])
            ->active()
            ->inStock()
            ->whereNotNull('compare_price')
            ->whereColumn('compare_price', '>', 'price')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get latest products
        $latestProducts = Product::with(['category', 'images'])
            ->active()
            ->inStock()
            ->orderBy('created_at', 'desc')
            ->take(12)
            ->get();

        return view('frontend.home', compact(
            'topCategories',
            'featuredProducts',
            'flashSaleProducts',
            'latestProducts'
        ));
    }

    /**
     * Show the contact page
     */
    public function contact()
    {
        return view('frontend.contact.index');
    }

    /**
     * Show the about page
     */
    public function about()
    {
        return view('frontend.about.index');
    }

    /**
     * Show the privacy policy page
     */
    public function privacyPolicy()
    {
        return view('frontend.privacy-policy.index');
    }

    /**
     * Show the terms of use page
     */
    public function termsOfUse()
    {
        return view('frontend.terms-of-use.index');
    }

    /**
     * Show the FAQ page
     */
    public function faq()
    {
        return view('frontend.faq.index');
    }

    public function contactSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Here you can save the contact message to database
            // and/or send email notification
            
            // Example: Save to database
            /*
            ContactMessage::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'subject' => $request->subject,
                'message' => $request->message,
            ]);
            */

            // Example: Send email notification
            /*
            Mail::send('emails.contact', $request->all(), function($message) use ($request) {
                $message->to('admin@exclusive-electronics.com')
                        ->subject('New Contact Form Submission: ' . $request->subject)
                        ->replyTo($request->email, $request->first_name . ' ' . $request->last_name);
            });
            */

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your message! We will get back to you within 24 hours.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Contact form submission failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Sorry, there was an error sending your message. Please try again later.'
            ], 500);
        }
    }

    /**
     * Show featured products page
     */
    public function featured(Request $request)
    {
        // Build query for featured products
        $query = Product::with(['category', 'images'])
            ->featured() // menggunakan scope featured
            ->active()
            ->inStock();

        // Apply sorting
        switch ($request->get('sort', 'latest')) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('created_at', 'desc'); // atau bisa menggunakan order berdasarkan views/sales
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default: // latest
                $query->orderBy('created_at', 'desc');
        }

        // Paginate results
        $products = $query->paginate(12)->withQueryString();

        return view('frontend.products.featured', compact('products'));
    }
}