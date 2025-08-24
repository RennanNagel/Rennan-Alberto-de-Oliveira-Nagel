<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController; // <— adicionado

Route::get('/', fn() => redirect()->route('clients.index'));

require __DIR__ . '/auth.php'; // rotas /login, /register, /logout, etc.

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', fn() => redirect()->route('clients.index'))->name('dashboard');

    Route::resource('clients', ClientController::class);
    Route::patch('clients/{client}/toggle', [ClientController::class, 'toggle'])->name('clients.toggle');
    Route::delete('/clients', [ClientController::class, 'destroyMany'])->name('clients.destroyMany');

    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
