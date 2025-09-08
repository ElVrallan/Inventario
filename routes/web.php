<?php

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Ruta principal
Route::get('/', function () {
    if (auth()->check()) {
        $rol = auth()->user()->rol;
        if ($rol == 'vendedor' || $rol == 'admin') {
            return redirect()->route('dashboard');
        }
    }
    return view('welcome');
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas protegidas para productos
Route::middleware(['auth', 'role:admin,vendedor'])->group(function () {
    Route::resource('productos', ProductController::class);
});

// Rutas de perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
});

// Administración de usuarios
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('usuarios', [UserManagementController::class, 'lista'])->name('admin.usuarios.lista');
    Route::get('usuarios/pendientes', [UserManagementController::class, 'pendientes'])->name('admin.usuarios.pendientes');
    Route::post('usuarios/{user}/aprobar', [UserManagementController::class, 'aprobar'])->name('admin.usuarios.aprobar');
    Route::post('usuarios/{user}/rechazar', [UserManagementController::class, 'rechazar'])->name('admin.usuarios.rechazar');
    Route::post('usuarios/rol', [UserManagementController::class, 'cambiarRol'])->name('admin.usuarios.cambiarRol');
});

// Ruta para usuarios pendientes
Route::get('/pendiente', function () {
    return view('pendiente');
})->name('pendiente');

// Rutas de autenticación
require __DIR__.'/auth.php';
