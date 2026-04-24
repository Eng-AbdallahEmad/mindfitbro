<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Web\BookingController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\PrivacyPolicyController;
use App\Http\Controllers\Web\TermsOfServiceController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest.custom')->group(function () {
    Route::get('auth/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('auth/register', [AuthController::class, 'register'])->name('register.post');

    Route::get('auth/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('auth/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('auth/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('auth/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    Route::get('auth/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('auth/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('auth/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy');
Route::get('terms-of-service', [TermsOfServiceController::class, 'index'])->name('terms-of-service');
Route::view('calorie-calculator', 'app.web.calorie_calculator')->name('calorie-calculator');

Route::middleware('auth.custom')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('coach/bookings')
        ->name('coach.bookings.')
        ->controller(DashboardController::class)
        ->group(function () {

            // PATCH Actions
            Route::patch('{booking}/confirm', 'confirmBooking')->name('confirm');
            Route::patch('{booking}/reject', 'rejectBooking')->name('reject');
            Route::patch('{booking}/meet-link', 'updateMeetLink')->name('meet-link');
        });

    Route::patch('coach/subscriptions/{subscription}/update-client', [DashboardController::class, 'updateClient'])
        ->name('coach.subscriptions.updateClient');

    Route::get('coach/bookings/{booking}/{action}', fn () => redirect()->back())
        ->where('action', 'confirm|reject|meet-link');

    Route::get('/coach/bookings', [DashboardController::class, 'bookings'])->name('coach.bookings');

    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/',                [CartController::class, 'index'])->name('index');
        Route::post('/add',            [CartController::class, 'add'])->name('add');
        Route::post('/update-qty',     [CartController::class, 'updateQuantity'])->name('updateQty');
        Route::post('/remove',         [CartController::class, 'remove'])->name('remove');
        Route::post('/toggle-yearly',  [CartController::class, 'toggleYearly'])->name('toggleYearly');
        Route::post('/apply-coupon',   [CartController::class, 'applyCoupon'])->name('applyCoupon');
    });

    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success/{id}', [CheckoutController::class, 'success'])->name('checkout.success');

    Route::get('/schedule-meeting/{subscription}', [BookingController::class, 'show'])->name('booking.show');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
    Route::put('/booking/{booking}', [BookingController::class, 'update'])->name('booking.update');
});
