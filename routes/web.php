<?php

use App\Http\Controllers\Admin\CoachesController as AdminCoachesController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MembersController as AdminMembersController;
use App\Http\Controllers\Admin\PlansController as AdminPlansController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\BeforeAftersController as AdminBeforeAftersController;
use App\Http\Controllers\Admin\TestimonialsController as AdminTestimonialsController;
use App\Http\Controllers\Admin\VideosController as AdminVideosController;
use App\Http\Controllers\Admin\CouponsController as AdminCouponsController;
use App\Http\Controllers\Admin\SubscriptionsController as AdminSubscriptionsController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Web\BookingController;
use App\Http\Controllers\Web\GuestAccountController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\CurrencyController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\PrivacyPolicyController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\JourneyController;
use App\Http\Controllers\Web\PurchaseController;
use App\Http\Controllers\Web\SubscriberController;
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

Route::get('/locale/{lang}', [LocaleController::class, 'switch'])->name('locale.switch');
Route::post('/currency/switch', [CurrencyController::class, 'switch'])->name('currency.switch');

// ── Dev-only currency debug ──────────────────────────────────────
if (app()->environment('local', 'development')) {
    Route::get('/currency/debug', function () {
        $svc = app(\App\Services\Web\CurrencyService::class);
        return response()->json([
            'ip'                           => request()->ip(),
            'session_currency'             => session('currency', '(not set)'),
            'config_testing_enabled'       => config('services.location.testing_enabled'),
            'config_testing_country_code'  => config('services.location.testing_country_code'),
            'currency_service_current'     => $svc->current(),
            'currency_meta'                => $svc->jsConfig(),
            'payment_method_key'           => $svc->paymentMethodKey(),
            'payment_instructions_type'    => $svc->paymentInstructions()['type'] ?? null,
        ]);
    });
}

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy');
Route::get('terms-of-service', [TermsOfServiceController::class, 'index'])->name('terms-of-service');
Route::view('calorie-calculator', 'app.web.calorie_calculator')->name('calorie-calculator');

// ── Direct Purchase Flow ────────────────────────────────────────
Route::prefix('purchase')->name('purchase.')->group(function () {
    Route::get('/success/{id}',      [PurchaseController::class, 'success'])->name('success');
    Route::post('/check-coupon',     [PurchaseController::class, 'checkCoupon'])->name('check-coupon');
    Route::post('/check-email',      [PurchaseController::class, 'checkEmail'])->name('check-email');
    Route::get('/{plan:key}',        [PurchaseController::class, 'showForm'])->name('form');
    Route::post('/{plan:key}',       [PurchaseController::class, 'submit'])->name('submit');
});

// ── Complete Account (guest email link) ─────────────────────────
Route::get('/complete-account/{token}', [GuestAccountController::class, 'completeAccount'])->name('complete-account.show');
Route::post('/complete-account/{token}', [GuestAccountController::class, 'storeCompleteAccount'])->name('complete-account.store');

Route::middleware('auth.custom')->group(function () {
    // Complete profile — not gated by profile.complete (would cause infinite redirect)
    Route::get('complete-profile', [ProfileController::class, 'show'])->name('complete-profile.show');
    Route::post('complete-profile', [ProfileController::class, 'store'])->name('complete-profile.store');

    Route::middleware('profile.complete')->group(function () {

        // ── Journey (post-program) — excluded from expired gate via middleware ──
        Route::prefix('journey')->name('journey.')->group(function () {
            Route::get('/{subscription}',      [JourneyController::class, 'show'])->name('show');
            Route::post('/{subscription}/rate',[JourneyController::class, 'rate'])->name('rate');
            Route::get('/{subscription}/pdf',  [JourneyController::class, 'pdf'])->name('pdf');
        });

        // ── Arabic PDF smoke-test (dev only) ─────────────────────────────────
        if (app()->environment('local', 'development')) {
            Route::get('journey-pdf-test', [JourneyController::class, 'pdfTest'])->name('journey.pdf-test');
        }

        // ── All other routes gated: expired users redirected to /journey/{id} ─
        Route::middleware('gate.expired')->group(function () {
            Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::post('dashboard/start-journey', [DashboardController::class, 'startJourney'])->name('dashboard.start-journey');

            Route::prefix('coach/bookings')
                ->name('coach.bookings.')
                ->controller(DashboardController::class)
                ->group(function () {
                    Route::patch('{booking}/confirm',   'confirmBooking')->name('confirm');
                    Route::patch('{booking}/reject',    'rejectBooking')->name('reject');
                    Route::patch('{booking}/meet-link', 'updateMeetLink')->name('meet-link');
                });

            Route::patch('coach/subscriptions/{subscription}/update-client', [DashboardController::class, 'updateClient'])
                ->name('coach.subscriptions.updateClient');

            Route::get('coach/bookings/{booking}/{action}', fn () => redirect()->back())
                ->where('action', 'confirm|reject|meet-link');

            Route::get('/coach/bookings', [DashboardController::class, 'bookings'])->name('coach.bookings');

            Route::prefix('coach/subscribers')->name('coach.subscribers.')->group(function () {
                Route::get('/',            [SubscriberController::class, 'index'])->name('index');
                Route::post('/attendance', [SubscriberController::class, 'storeAttendance'])->name('attendance');
                Route::post('/evaluation', [SubscriberController::class, 'storeEvaluation'])->name('evaluation');
                Route::get('/{userId}',    [SubscriberController::class, 'show'])->name('show')->whereNumber('userId');
            });

            Route::get('/schedule-meeting/{subscription}', [BookingController::class, 'show'])->name('booking.show');
            Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
            Route::put('/booking/{booking}', [BookingController::class, 'update'])->name('booking.update');
        }); // gate.expired

    }); // profile.complete
});

// ── Admin Auth ──────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/', fn () => redirect()->route('admin.dashboard'));

    Route::middleware('admin.guest')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login'])->name('login.post');
    });

    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('admin.auth')->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('subscriptions', [AdminSubscriptionsController::class, 'index'])->name('subscriptions.index');
        Route::get('subscriptions/{subscription}/receipt', [AdminSubscriptionsController::class, 'viewReceipt'])->name('subscriptions.receipt');
        Route::post('subscriptions/{subscription}/approve', [AdminSubscriptionsController::class, 'approve'])->name('subscriptions.approve');
        Route::post('subscriptions/{subscription}/reject', [AdminSubscriptionsController::class, 'reject'])->name('subscriptions.reject');
        Route::get('subscriptions/{subscription}', [AdminSubscriptionsController::class, 'show'])->name('subscriptions.show');
        Route::put('subscriptions/{subscription}', [AdminSubscriptionsController::class, 'update'])->name('subscriptions.update');
        Route::delete('subscriptions/{subscription}', [AdminSubscriptionsController::class, 'destroy'])->name('subscriptions.destroy');

        Route::get('coaches', [AdminCoachesController::class, 'index'])->name('coaches.index');
        Route::get('coaches/{coach}', [AdminCoachesController::class, 'show'])->name('coaches.show');
        Route::put('coaches/{coach}', [AdminCoachesController::class, 'update'])->name('coaches.update');
        Route::patch('coaches/{coach}/status', [AdminCoachesController::class, 'updateStatus'])->name('coaches.status');
        Route::delete('coaches/{coach}', [AdminCoachesController::class, 'destroy'])->name('coaches.destroy');

        // Plans
        Route::get('plans', [AdminPlansController::class, 'index'])->name('plans.index');
        Route::post('plans', [AdminPlansController::class, 'store'])->name('plans.store');
        Route::put('plans/{plan}', [AdminPlansController::class, 'update'])->name('plans.update');
        Route::patch('plans/{plan}/toggle', [AdminPlansController::class, 'toggleActive'])->name('plans.toggle');
        Route::delete('plans/{plan}', [AdminPlansController::class, 'destroy'])->name('plans.destroy');

        // Features
        Route::post('features', [AdminPlansController::class, 'storeFeature'])->name('features.store');
        Route::put('features/{feature}', [AdminPlansController::class, 'updateFeature'])->name('features.update');
        Route::delete('features/{feature}', [AdminPlansController::class, 'destroyFeature'])->name('features.destroy');

        // Coupons
        Route::get('coupons', [AdminCouponsController::class, 'index'])->name('coupons.index');
        Route::post('coupons', [AdminCouponsController::class, 'store'])->name('coupons.store');
        Route::put('coupons/{coupon}', [AdminCouponsController::class, 'update'])->name('coupons.update');
        Route::patch('coupons/{coupon}/toggle', [AdminCouponsController::class, 'toggle'])->name('coupons.toggle');
        Route::delete('coupons/{coupon}', [AdminCouponsController::class, 'destroy'])->name('coupons.destroy');

        // Settings
        Route::get('settings', [AdminSettingsController::class, 'index'])->name('settings.index');
        Route::post('settings', [AdminSettingsController::class, 'update'])->name('settings.update');

        // Videos (managed from settings page)
        Route::post('videos', [AdminVideosController::class, 'store'])->name('videos.store');
        Route::put('videos/{video}', [AdminVideosController::class, 'update'])->name('videos.update');
        Route::patch('videos/{video}/toggle', [AdminVideosController::class, 'toggleActive'])->name('videos.toggle');
        Route::delete('videos/{video}', [AdminVideosController::class, 'destroy'])->name('videos.destroy');

        // Before/After (managed from settings page)
        Route::post('before-afters', [AdminBeforeAftersController::class, 'store'])->name('before-afters.store');
        Route::put('before-afters/{beforeAfter}', [AdminBeforeAftersController::class, 'update'])->name('before-afters.update');
        Route::patch('before-afters/{beforeAfter}/toggle', [AdminBeforeAftersController::class, 'toggleActive'])->name('before-afters.toggle');
        Route::delete('before-afters/{beforeAfter}', [AdminBeforeAftersController::class, 'destroy'])->name('before-afters.destroy');

        // Testimonials (managed from settings page)
        Route::post('testimonials', [AdminTestimonialsController::class, 'store'])->name('testimonials.store');
        Route::put('testimonials/{testimonial}', [AdminTestimonialsController::class, 'update'])->name('testimonials.update');
        Route::patch('testimonials/{testimonial}/toggle', [AdminTestimonialsController::class, 'toggleActive'])->name('testimonials.toggle');
        Route::delete('testimonials/{testimonial}', [AdminTestimonialsController::class, 'destroy'])->name('testimonials.destroy');

        Route::get('members', [AdminMembersController::class, 'index'])->name('members.index');
        Route::get('members/create', [AdminMembersController::class, 'create'])->name('members.create');
        Route::post('members', [AdminMembersController::class, 'store'])->name('members.store');
        Route::get('members/verify-otp/{token}', [AdminMembersController::class, 'showVerifyOtp'])->name('members.verify-otp');
        Route::post('members/verify-otp/{token}', [AdminMembersController::class, 'verifyOtp'])->name('members.verify-otp.post');
        Route::post('members/resend-otp/{token}', [AdminMembersController::class, 'resendOtp'])->name('members.resend-otp');
        Route::get('members/{member}', [AdminMembersController::class, 'show'])->name('members.show');
        Route::put('members/{member}', [AdminMembersController::class, 'update'])->name('members.update');
        Route::patch('members/{member}/status', [AdminMembersController::class, 'updateStatus'])->name('members.status');
        Route::delete('members/{member}', [AdminMembersController::class, 'destroy'])->name('members.destroy');
    });
});
