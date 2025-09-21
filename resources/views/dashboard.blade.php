@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Productos (admin y vendedor) -->
                <a href="{{ route('productos.index') }}" class="dashboard-card p-6 bg-white border rounded-lg shadow hover:shadow-lg transition">
                    <h3 class="text-lg font-semibold mb-2">📦 Productos</h3>
                    <p class="text-gray-600">Gestiona el inventario de productos.</p>
                </a>

                <!-- Categorías (admin y vendedor) -->
                <a href="{{ route('categorias.index') }}" class="dashboard-card p-6 bg-white border rounded-lg shadow hover:shadow-lg transition">
                    <h3 class="text-lg font-semibold mb-2">🏷️ Categorías</h3>
                    <p class="text-gray-600">Organiza los productos en categorías.</p>
                </a>

                <!-- Proveedores (solo admin) -->
                @if(auth()->user()->rol === 'admin')
                <a href="{{ route('proveedores.index') }}" class="dashboard-card p-6 bg-white border rounded-lg shadow hover:shadow-lg transition">
                    <h3 class="text-lg font-semibold mb-2">🚚 Proveedores</h3>
                    <p class="text-gray-600">Administra la lista de proveedores.</p>
                </a>
                @endif

                <!-- Reportes (solo admin) -->
                @if(auth()->user()->rol === 'admin')
                <a href="{{ route('reportes.index') }}" class="dashboard-card p-6 bg-white border rounded-lg shadow hover:shadow-lg transition">
                    <h3 class="text-lg font-semibold mb-2">📊 Reportes</h3>
                    <p class="text-gray-600">Genera reportes y estadísticas.</p>
                </a>
                @endif

                <!-- Usuarios (solo admin) -->
                @if(auth()->user()->rol === 'admin')
                <a href="{{ route('admin.usuarios.lista') }}" class="dashboard-card p-6 bg-white border rounded-lg shadow hover:shadow-lg transition">
                    <h3 class="text-lg font-semibold mb-2">👥 Usuarios</h3>
                    <p class="text-gray-600">Gestiona usuarios y roles del sistema.</p>
                </a>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
