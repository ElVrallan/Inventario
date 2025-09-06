<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\UserManagementController;

Route::get('/', function () {
    if (auth()->check()) { // Verifica si está logueado
        $rol = auth()->user()->rol;
        if ($rol == 'vendedor' || $rol == 'admin') {
            return redirect()->route('dashboard'); // Redirige al dashboard
        }
    }
    return view('welcome'); // Si no está logueado o es pendiente
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:admin,vendedor'])->group(function () {
    Route::resource('productos', ProductController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/admin/usuarios', [UserManagementController::class, 'lista'])
    ->name('admin.usuarios.lista')
    ->middleware(['auth', 'role:admin']);

Route::get('/pendiente', function () {
    return view('pendiente');
})->name('pendiente');

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('usuarios/pendientes', [UserManagementController::class, 'pendientes'])->name('admin.usuarios.pendientes');
    Route::post('usuarios/{user}/aprobar', [UserManagementController::class, 'aprobar'])->name('admin.usuarios.aprobar');
    Route::post('usuarios/{user}/rechazar', [UserManagementController::class, 'rechazar'])->name('admin.usuarios.rechazar');
});

Route::post('/admin/usuarios/{id}/rol', [UserManagementController::class, 'cambiarRol'])
    ->name('admin.usuarios.cambiarRol')
    ->middleware(['auth', 'role:admin']);

require __DIR__.'/auth.php';
