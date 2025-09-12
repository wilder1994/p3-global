<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Tickets\Finalizados;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí se registran las rutas web de la aplicación.
|
*/


Route::get('/tickets/finalizados', function () {
    return view('livewire.tickets.finalizados-page');
})->middleware(['auth', 'verified'])
  ->name('tickets.finalizados');

Route::view('/', 'welcome');

// Dashboard con solo el formulario
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Ruta para ver el board completo de tickets
Route::get('/tickets/board', function () {
    return view('livewire.tickets.board-page');
})->middleware(['auth', 'verified'])
  ->name('tickets.board');



// Perfil de usuario
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
