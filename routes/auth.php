<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    // ❌ Los usuarios no se registran solos
    // Volt::route('register', 'pages.auth.register')
    //     ->name('register');

    // ✅ Mantener login
    Volt::route('login', 'pages.auth.login')
        ->name('login');

    // ❌ Los usuarios no cambian clave con "olvidé mi contraseña"
    // Volt::route('forgot-password', 'pages.auth.forgot-password')
    //     ->name('password.request');

    // ❌ Tampoco resetean su contraseña por token
    // Volt::route('reset-password/{token}', 'pages.auth.reset-password')
    //     ->name('password.reset');
});

Route::middleware('auth')->group(function () {
    // ❌ Los usuarios no verifican correo
    // Volt::route('verify-email', 'pages.auth.verify-email')
    //     ->name('verification.notice');

    // ❌ Tampoco se usa la verificación por hash
    // Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
    //     ->middleware(['signed', 'throttle:6,1'])
    //     ->name('verification.verify');

    // ❌ Ni la confirmación de contraseña
    // Volt::route('confirm-password', 'pages.auth.confirm-password')
    //     ->name('password.confirm');
});