<?php

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VentaController;
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


// Ruta para acciones de venta (vendedor o admin)
Route::middleware(['auth', 'role:vendedor,admin'])->group(function() {
    Route::post('productos/{producto}/vender', [VentaController::class, 'store'])->name('productos.vender');
});

// Rutas de búsqueda
Route::get('/productos/search/preview', [ProductoController::class, 'searchPreview'])->name('productos.search.preview');
Route::get('/productos/search', [ProductoController::class, 'search'])->name('productos.search');

// ----------------------
// Vendedor + Admin (solo lectura para productos)
// ----------------------
Route::middleware(['auth', 'role:admin,vendedor'])->group(function () {
    Route::resource('productos', ProductoController::class)->only(['index', 'show']);
    Route::resource('categorias', CategoriaController::class)->only(['index']);
    Route::resource('proveedores', ProveedorController::class)->only(['index']);

    // Reportes básicos
    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
});

// ----------------------
// Admin (acceso completo)
// ----------------------
Route::middleware(['auth', 'role:admin'])->group(function () {
    // CRUD completo para recursos principales (admin)
    Route::resource('productos', ProductoController::class)->except(['index', 'show']);
    Route::resource('categorias', CategoriaController::class)->except(['index']);
    Route::resource('proveedores', ProveedorController::class)->except(['index', 'show']);

    // Reportes avanzados y exportar
    Route::get('reportes/avanzados', [ReporteController::class, 'avanzados'])->name('reportes.avanzados');
    Route::get('reportes/exportar', [ReporteController::class, 'exportar'])->name('reportes.exportar');
    // (si necesitas otra acción admin distinta, añádela aquí)

    // Administración de usuarios bajo prefijo /admin
    Route::prefix('admin')->group(function () {
        // Lista de usuarios y pendientes
        Route::get('usuarios', [UserManagementController::class, 'lista'])->name('admin.usuarios.lista');
        Route::get('usuarios/pendientes', [UserManagementController::class, 'pendientes'])->name('admin.usuarios.pendientes');

        // CRUD / acciones sobre usuarios
        Route::get('usuarios/crear', [UserManagementController::class, 'create'])->name('admin.usuarios.crear');
        Route::post('usuarios', [UserManagementController::class, 'store'])->name('admin.usuarios.store');
        Route::get('usuarios/{user}/editar', [UserManagementController::class, 'edit'])->name('admin.usuarios.editar');
        Route::put('usuarios/{user}', [UserManagementController::class, 'update'])->name('admin.usuarios.update');
        Route::delete('usuarios/{user}', [UserManagementController::class, 'destroy'])->name('admin.usuarios.destroy');

        // Aprobar / rechazar / cambiar rol
        Route::post('usuarios/{user}/aprobar', [UserManagementController::class, 'aprobar'])->name('admin.usuarios.aprobar');
        Route::post('usuarios/{user}/rechazar', [UserManagementController::class, 'rechazar'])->name('admin.usuarios.rechazar');
        Route::post('usuarios/rol', [UserManagementController::class, 'cambiarRol'])->name('admin.usuarios.cambiarRol');
    });
});

// ----------------------
// Perfil
// ----------------------
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
});

// Ruta para usuarios pendientes
Route::get('/pendiente', function () {
    return view('pendiente');
})->name('pendiente');

// Rutas de autenticación
require __DIR__.'/auth.php';
// Perfil
// ----------------------
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
});

// Ruta para usuarios pendientes
Route::get('/pendiente', function () {
    return view('pendiente');
})->name('pendiente');

Route::get('productos/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar');

// Rutas de autenticación
require __DIR__.'/auth.php';


