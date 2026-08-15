<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExtensionOfficerController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SuperAdminController;
use App\Services\MlService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ---------------- Home / Landing ----------------
Route::get('/', function () {
    if (Auth::check()) {
        return match (Auth::user()->role) {
            'farmer' => redirect()->route('farmer.dashboard'),
            'extension_officer' => redirect()->route('officer.dashboard'),
            'supplier' => redirect()->route('supplier.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
        };
    }
    return view('welcome');
})->name('welcome');

// ---------------- Roles hub (public, works for guests AND logged-in users) ----------------
// Unlike '/', this never redirects -- a logged-in user can always come here
// to see "Enter" for their own role and "Join as ..." for the other three.
Route::get('/hub', function () {
    return view('welcome', ['isHub' => true]);
})->name('hub');

// ---------------- Public role-detail pages ----------------
Route::get('/roles/{role}', function (string $role) {
    abort_unless(in_array($role, ['farmer', 'extension_officer', 'supplier', 'admin']), 404);
    return view('roles.' . $role);
})->name('roles.show');

// ---------------- What can the models predict? (public) ----------------
Route::get('/predictions', function (MlService $ml) {
    return view('predictions', ['capabilities' => $ml->capabilities()]);
})->name('predictions');

// ---------------- Guest routes ----------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Email OTP verification (step 2 of registration)
    Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('verify-email');
    Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
    Route::post('/verify-email/resend', [AuthController::class, 'resendVerifyEmail'])->name('verify-email.resend');

    // Forgot / reset password via OTP
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetOtp'])->name('password.email');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::post('/reset-password/resend', [AuthController::class, 'resendResetOtp'])->name('password.resend');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---------------- Apply to become an admin (any logged-in user) ----------------
Route::post('/apply-admin', [SuperAdminController::class, 'applyForAdmin'])->middleware('auth')->name('apply-admin');

// ---------------- Notification bell (any logged-in user) ----------------
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::post('/{notification}/read', [NotificationController::class, 'read'])->name('read');
    Route::post('/read-all', [NotificationController::class, 'readAll'])->name('read-all');
});

// ---------------- Farmer routes ----------------
Route::middleware(['auth', 'role:farmer'])->prefix('farmer')->name('farmer.')->group(function () {
    Route::get('/dashboard', [FarmerController::class, 'dashboard'])->name('dashboard');
    Route::post('/farm-profiles', [FarmerController::class, 'storeFarmProfile'])->name('farm-profiles.store');
    Route::post('/recommend/crop', [FarmerController::class, 'recommendCrop'])->name('recommend.crop');
    Route::post('/recommend/fertilizer', [FarmerController::class, 'recommendFertilizer'])->name('recommend.fertilizer');
    Route::post('/recommend/price', [FarmerController::class, 'priceForecast'])->name('recommend.price');
    Route::post('/recommend/pest', [FarmerController::class, 'detectPest'])->name('recommend.pest');
    Route::get('/history', [FarmerController::class, 'history'])->name('history');
    Route::get('/marketplace', [FarmerController::class, 'marketplace'])->name('marketplace');
    Route::post('/orders', [FarmerController::class, 'placeOrder'])->name('orders.store');
    Route::get('/orders', [FarmerController::class, 'myOrders'])->name('orders');
    Route::post('/orders/{order}/pay', [FarmerController::class, 'submitPayment'])->name('orders.pay');
    Route::post('/feedback', [FarmerController::class, 'sendFeedback'])->name('feedback.store');
});

// ---------------- Extension Officer routes ----------------
Route::middleware(['auth', 'role:extension_officer'])->prefix('officer')->name('officer.')->group(function () {
    Route::get('/dashboard', [ExtensionOfficerController::class, 'dashboard'])->name('dashboard');
    Route::post('/farm-profiles/{farmProfile}/verify', [ExtensionOfficerController::class, 'verifyFarmProfile'])->name('farm-profiles.verify');
    Route::post('/recommendations/{recommendation}/override', [ExtensionOfficerController::class, 'overrideRecommendation'])->name('recommendations.override');
    Route::post('/advisory', [ExtensionOfficerController::class, 'sendAdvisory'])->name('advisory.send');
    Route::post('/alerts', [ExtensionOfficerController::class, 'sendAlert'])->name('alerts.send');
    Route::post('/training', [ExtensionOfficerController::class, 'scheduleTraining'])->name('training.store');
    Route::get('/trends', [ExtensionOfficerController::class, 'regionalTrends'])->name('trends');
});

// ---------------- Supplier routes ----------------
Route::middleware(['auth', 'role:supplier'])->prefix('supplier')->name('supplier.')->group(function () {
    Route::get('/dashboard', [SupplierController::class, 'dashboard'])->name('dashboard');
    Route::get('/my-products', [SupplierController::class, 'myProducts'])->name('my-products');
    Route::get('/my-orders', [SupplierController::class, 'myFarmerOrders'])->name('my-orders');
    Route::get('/my-inquiries', [SupplierController::class, 'myInquiries'])->name('my-inquiries');
    Route::post('/bkash', [SupplierController::class, 'updateBkash'])->name('bkash.update');
    Route::post('/products', [SupplierController::class, 'storeItem'])->name('products.store');
    Route::post('/products/{product}/stock', [SupplierController::class, 'updateStock'])->name('products.stock');
    Route::post('/orders/{order}/fulfil', [SupplierController::class, 'fulfilOrder'])->name('orders.fulfil');
    Route::post('/orders/{order}/verify-payment', [SupplierController::class, 'verifyPayment'])->name('orders.verify-payment');
    Route::post('/inquiries/{inquiry}/respond', [SupplierController::class, 'respondInquiry'])->name('inquiries.respond');
    Route::get('/demand-forecast', [SupplierController::class, 'demandForecast'])->name('demand-forecast');
});

// ---------------- Admin routes ----------------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/removed-history/{email}', [AdminController::class, 'removedHistory'])->name('users.removed-history');
    Route::post('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.role');
    Route::post('/users/{user}/approve', [AdminController::class, 'approveUser'])->name('users.approve');
    Route::post('/users/{user}/restrict', [AdminController::class, 'restrictUser'])->name('users.restrict');
    Route::post('/users/{user}/remove', [AdminController::class, 'removeUser'])->name('users.remove');
    Route::get('/activity', [AdminController::class, 'activityLog'])->name('activity');
    Route::post('/zones', [AdminController::class, 'storeZone'])->name('zones.store');
    Route::post('/crops', [AdminController::class, 'storeCrop'])->name('crops.store');
    Route::post('/retrain', [AdminController::class, 'triggerRetrain'])->name('retrain.trigger');
    Route::post('/backup', [AdminController::class, 'triggerBackup'])->name('backup.trigger');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
    Route::post('/analytics/snapshot', [AdminController::class, 'takeSnapshot'])->name('analytics.snapshot');
});

// ---------------- Admins directory (any admin / super-admin can view) ----------------
Route::middleware(['auth', 'role:admin'])->prefix('admins')->name('admins.')->group(function () {
    Route::get('/directory', [SuperAdminController::class, 'directory'])->name('directory');
});

// ---------------- Super Admin routes ----------------
Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/applications/{user}/approve', [SuperAdminController::class, 'approveAdminApplication'])->name('applications.approve');
    Route::post('/applications/{user}/reject', [SuperAdminController::class, 'rejectAdminApplication'])->name('applications.reject');
    Route::post('/nominations', [SuperAdminController::class, 'createNomination'])->name('nominations.create');
    Route::post('/nominations/{nomination}/approve', [SuperAdminController::class, 'approveNomination'])->name('nominations.approve');
    Route::post('/nominations/{nomination}/reject', [SuperAdminController::class, 'rejectNomination'])->name('nominations.reject');
});
