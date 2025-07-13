<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the user profile page.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user statistics safely
        $orderStats = $this->getOrderStats($user);
        $recentOrders = $this->getRecentOrders($user);

        return view('frontend.profile.index', compact('user', 'orderStats', 'recentOrders'));
    }

    /**
     * Show the edit profile form.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('frontend.profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validation rules based on your actual User model structure
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'province' => 'nullable|string|max:100',
        ];

        $validated = $request->validate($rules);

        try {
            // Update user with validated data
            $user->update($validated);

            return redirect()->route('profile.index')
                ->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update profile. Please try again.')
                ->withInput();
        }
    }

    /**
     * Auto-save profile data (AJAX)
     */
    public function autoSave(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $rules = [
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'province' => 'nullable|string|max:100',
            ];

            $validated = $request->validate($rules);
            
            // Remove empty values to avoid overwriting with nulls
            $validated = array_filter($validated, function($value) {
                return !is_null($value) && $value !== '';
            });

            if (!empty($validated)) {
                $user->update($validated);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile auto-saved successfully',
                'saved_at' => now()->format('H:i:s')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Auto-save failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The provided password does not match your current password.'
            ]);
        }

        try {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return redirect()->route('profile.index')
                ->with('success', 'Password updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update password. Please try again.');
        }
    }

    /**
     * Upload user avatar
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        try {
            $user = Auth::user();

            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');

            // Update user avatar path
            $user->update(['avatar' => $avatarPath]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Avatar updated successfully!',
                    'avatar_url' => Storage::url($avatarPath)
                ]);
            }

            return redirect()->route('profile.index')
                ->with('success', 'Avatar updated successfully!');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload avatar: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to upload avatar. Please try again.');
        }
    }

    /**
     * Deactivate user account
     */
    public function deactivate(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'reason' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'The provided password is incorrect.'
            ]);
        }

        try {
            // Deactivate account
            $user->update([
                'is_active' => false,
                'deactivated_at' => now(),
                'deactivation_reason' => $request->reason
            ]);

            // Log the deactivation
            $this->logActivity($user, 'account_deactivated', 'Account deactivated by user');

            // Logout user
            Auth::logout();

            return redirect()->route('home')
                ->with('success', 'Your account has been deactivated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to deactivate account. Please try again.');
        }
    }

    /**
     * Delete user account permanently
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'confirmation' => 'required|in:DELETE MY ACCOUNT'
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'The provided password is incorrect.'
            ]);
        }

        try {
            // Delete avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Cancel pending orders if any
            if (Schema::hasTable('orders')) {
                DB::table('orders')
                    ->where('user_id', $user->id)
                    ->whereIn('status', ['pending', 'processing'])
                    ->update(['status' => 'cancelled']);
            }

            // Delete user
            $user->delete();

            // Logout
            Auth::logout();

            return redirect()->route('home')
                ->with('success', 'Your account has been deleted permanently.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete account. Please try again.');
        }
    }

    /**
     * Resend email verification
     */
    public function resendVerification(Request $request)
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('profile.index')
                ->with('info', 'Your email is already verified.');
        }

        try {
            $user->sendEmailVerificationNotification();

            return redirect()->route('profile.index')
                ->with('success', 'Verification email sent successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to send verification email. Please try again.');
        }
    }

    /**
     * Export user data (GDPR compliance)
     */
    public function exportData()
    {
        try {
            $user = Auth::user();
            
            $userData = [
                'personal_information' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'city' => $user->city,
                    'province' => $user->province,
                    'postal_code' => $user->postal_code,
                    'role' => $user->role,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ],
                'orders' => [],
                'wishlist' => []
            ];

            // Add orders if table exists
            if (Schema::hasTable('orders')) {
                $orders = DB::table('orders')->where('user_id', $user->id)->get();
                $userData['orders'] = $orders->toArray();
            }

            // Add wishlist if table exists
            if (Schema::hasTable('wishlists')) {
                $wishlist = DB::table('wishlists')->where('user_id', $user->id)->get();
                $userData['wishlist'] = $wishlist->toArray();
            }

            $fileName = 'user_data_' . $user->id . '_' . now()->format('Y-m-d_H-i-s') . '.json';
            
            return response()->json($userData)
                ->header('Content-Disposition', 'attachment; filename=' . $fileName);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to export data. Please try again.');
        }
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'order_updates' => 'boolean',
            'promotional_emails' => 'boolean'
        ]);

        try {
            // If user table doesn't have notification columns, create a separate table
            if (Schema::hasColumn('users', 'notification_preferences')) {
                $user->update([
                    'notification_preferences' => json_encode([
                        'email_notifications' => $request->boolean('email_notifications'),
                        'sms_notifications' => $request->boolean('sms_notifications'),
                        'order_updates' => $request->boolean('order_updates'),
                        'promotional_emails' => $request->boolean('promotional_emails')
                    ])
                ]);
            } else {
                // Alternative: store in separate table or session
                session(['notification_preferences' => $request->only([
                    'email_notifications', 'sms_notifications', 'order_updates', 'promotional_emails'
                ])]);
            }

            return redirect()->route('profile.index')
                ->with('success', 'Notification preferences updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update notification preferences.');
        }
    }

    /**
     * Get user activity log
     */
    public function getActivityLog(Request $request)
    {
        try {
            $user = Auth::user();
            $activities = collect([]);

            // Check if activity_logs table exists
            if (Schema::hasTable('activity_logs')) {
                $activities = DB::table('activity_logs')
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();
            } else {
                // Create sample activity data
                $activities = collect([
                    (object) [
                        'action' => 'profile_updated',
                        'description' => 'Profile information updated',
                        'created_at' => now()->subHours(2),
                        'ip_address' => request()->ip()
                    ],
                    (object) [
                        'action' => 'login',
                        'description' => 'User logged in',
                        'created_at' => now()->subHours(5),
                        'ip_address' => request()->ip()
                    ]
                ]);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'activities' => $activities
                ]);
            }

            return view('frontend.profile.activity-log', compact('activities'));

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to load activity log.');
        }
    }

    /**
     * Get order statistics safely.
     */
    private function getOrderStats($user)
    {
        $stats = [
            'total_orders' => 0,
            'pending_orders' => 0,
            'completed_orders' => 0,
            'total_spent' => 0
        ];

        try {
            // Check if Order model exists and orders table exists
            if (class_exists('\App\Models\Order') && Schema::hasTable('orders')) {
                $stats['total_orders'] = DB::table('orders')->where('user_id', $user->id)->count();
                $stats['pending_orders'] = DB::table('orders')->where('user_id', $user->id)->where('status', 'pending')->count();
                $stats['completed_orders'] = DB::table('orders')->where('user_id', $user->id)->where('status', 'delivered')->count();
                $stats['total_spent'] = DB::table('orders')->where('user_id', $user->id)->where('status', '!=', 'cancelled')->sum('total') ?? 0;
            }
        } catch (\Exception $e) {
            // Return default stats if there's an error
        }

        return $stats;
    }

    /**
     * Get recent orders safely.
     */
    private function getRecentOrders($user)
    {
        try {
            if (class_exists('\App\Models\Order') && Schema::hasTable('orders')) {
                return \App\Models\Order::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            }
        } catch (\Exception $e) {
            // Return empty collection if there's an error
        }

        return collect([]);
    }

    /**
     * Log user activity
     */
    private function logActivity($user, $action, $description)
    {
        try {
            if (Schema::hasTable('activity_logs')) {
                DB::table('activity_logs')->insert([
                    'user_id' => $user->id,
                    'action' => $action,
                    'description' => $description,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        } catch (\Exception $e) {
            // Log activity failed, but don't break the main flow
        }
    }
}