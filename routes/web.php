<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\web\HomeController;
use App\Http\Controllers\web\PrivacyPolicyController;
use App\Http\Controllers\web\TermsOfServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy');
Route::get('terms-of-service', [TermsOfServiceController::class, 'index'])->name('terms-of-service');

// Route::get('auth/register', [AuthController::class, 'showRegister'])->name('register');
// Route::post('auth/register', [AuthController::class, 'register'])->name('register.post');

Route::get('auth/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('auth/login', [AuthController::class, 'login'])->name('login.post');

// Route::get('/dashboard', [AuthController::class, 'dashboard'])
//     ->middleware('auth.custom')
//     ->name('dashboard');

// Route::post('auth/logout', [AuthController::class, 'logout'])->name('logout');