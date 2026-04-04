<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\web\CartController;
use App\Http\Controllers\web\DashboardController;
use App\Http\Controllers\web\HomeController;
use App\Http\Controllers\web\PrivacyPolicyController;
use App\Http\Controllers\web\TermsOfServiceController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest.custom')->group(function () {
    Route::get('auth/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('auth/register', [AuthController::class, 'register'])->name('register.post');

    Route::get('auth/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('auth/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('auth/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy');
Route::get('terms-of-service', [TermsOfServiceController::class, 'index'])->name('terms-of-service');
Route::view('calorie-calculator', 'app.web.calorie_calculator')->name('calorie-calculator');

Route::middleware('auth.custom')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('cart', [CartController::class, 'index'])->name('cart');
});