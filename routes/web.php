<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\UsersIndex;
use App\Livewire\Admin\CreateUser;
use App\Livewire\Admin\EditUser;
use App\Livewire\Tickets\Finalizados;
use App\Models\Ticket;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí se registran las rutas web de la aplicación.
|
*/

// 🔒 Grupo exclusivo para administrador
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Listar usuarios
    Route::get('/admin/users', UsersIndex::class)
        ->name('admin.users.index');

    // Crear usuario
    Route::get('/admin/users/create', CreateUser::class)
        ->name('admin.users.create');

    // Editar usuario
    Route::get('/admin/users/{user}/edit', EditUser::class)
        ->name('admin.users.edit');
});

// 🎯 Dashboard con estadísticas reales + indicadores por usuario + últimos movimientos
Route::get('/dashboard', function () {
    // Conteos globales por estado
    $stats = [
        'pendiente'   => Ticket::where('estado', 'pendiente')->count(),
        'en_proceso'  => Ticket::where('estado', 'en_proceso')->count(),
        'finalizado'  => Ticket::where('estado', 'finalizado')->count(),
    ];

    // Indicadores por usuario responsable (asignado_a)
    $indicadoresPorUsuario = Ticket::query()
        ->with('asignado') // relación en el modelo Ticket
        ->select('asignado_a')
        ->selectRaw("
            SUM(CASE WHEN estado = 'pendiente'   THEN 1 ELSE 0 END) as pendientes,
            SUM(CASE WHEN estado = 'en_proceso'  THEN 1 ELSE 0 END) as en_proceso,
            SUM(CASE WHEN estado = 'finalizado'  THEN 1 ELSE 0 END) as finalizados,
            COUNT(*) as total
        ")
        ->whereNotNull('asignado_a')
        ->groupBy('asignado_a')
        ->orderByDesc('total')
        ->get();

    // Últimos 5 tickets creados (para el panel de "Últimos movimientos")
    $latestTickets = Ticket::with('creador')
        ->orderByDesc('created_at')
        ->limit(5)
        ->get();

    return view('dashboard', compact('stats', 'latestTickets', 'indicadoresPorUsuario'));
})->middleware(['auth', 'verified'])
  ->name('dashboard');

// 🧾 Página para crear nuevo memorando / ticket
Route::view('/tickets/nuevo', 'livewire.tickets.form-page')
    ->middleware(['auth', 'verified'])
    ->name('tickets.create');

// 📋 Board de tickets (pendientes)
Route::get('/tickets/board', function () {
    return view('livewire.tickets.board-page');
})->middleware(['auth', 'verified'])
  ->name('tickets.board');

// 🔄 Tickets en proceso
Route::get('/tickets/en-proceso', function () {
    return view('livewire.tickets.in-process-page');
})->middleware(['auth', 'verified'])
  ->name('tickets.en_proceso');

// ✅ Tickets finalizados
Route::get('/tickets/finalizados', function () {
    return view('livewire.tickets.finalizados-page');
})->middleware(['auth', 'verified'])
  ->name('tickets.finalizados');

// 👤 Perfil de usuario
Route::view('/profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// 🏠 Página de inicio (pública)
Route::view('/', 'welcome');

// 🔐 Rutas de autenticación (login, registro, logout, etc.)
require __DIR__.'/auth.php';
