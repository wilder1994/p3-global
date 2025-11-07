<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\UsersIndex;
use App\Livewire\Admin\CreateUser;
use App\Livewire\Admin\EditUser; // 👈 cuando lo creemos, lo activamos
use App\Livewire\Tickets\Finalizados;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí se registran las rutas web de la aplicación.
|
*/

// Grupo exclusivo para administrador
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Listar usuarios
    Route::get('/admin/users', UsersIndex::class)
        ->name('admin.users.index');

    // Crear usuario
    Route::get('/admin/users/create', CreateUser::class)
        ->name('admin.users.create');

    // Editar usuario (activar cuando tengamos el componente)
     Route::get('/admin/users/{user}/edit', EditUser::class)
         ->name('admin.users.edit');
});

// Tickets finalizados
Route::get('/tickets/finalizados', function () {
    return view('livewire.tickets.finalizados-page');
})->middleware(['auth', 'verified'])
  ->name('tickets.finalizados');

// Dashboard (simple)
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Board de tickets
Route::get('/tickets/board', function () {
    return view('livewire.tickets.board-page');
})->middleware(['auth', 'verified'])
  ->name('tickets.board');

// Tickets en proceso
Route::get('/tickets/en-proceso', function () {
    return view('livewire.tickets.in-process-page');
})->middleware(['auth', 'verified'])
  ->name('tickets.en_proceso');

// Perfil de usuario
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Página de inicio
Route::view('/', 'welcome');

// Rutas de autenticación (login, logout, etc.)
require __DIR__.'/auth.php';
