@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <div class="header-content">
                <h2 class="header-title">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </h2>
                <p class="header-subtitle">
                    Bienvenido de vuelta, {{ auth()->user()->name }}
                </p>
            </div>
            <div class="header-actions">
                <div class="user-role-badge">
                    <i class="bi bi-person-badge"></i>
                    {{ ucfirst(auth()->user()->rol) }}
                </div>
            </div>
        </div>
    </x-slot>

    <div class="dashboard-container">
        <div class="dashboard-content">
            
            <!-- Estadísticas Rápidas -->
            <div class="stats-grid">
                <div class="stat-card productos-stat">
                    <div class="stat-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number">{{ \App\Models\Producto::count() }}</h3>
                        <p class="stat-label">Productos</p>
                    </div>
                </div>
                
                <div class="stat-card categorias-stat">
                    <div class="stat-icon">
                        <i class="bi bi-tags"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number">{{ \App\Models\Categoria::count() }}</h3>
                        <p class="stat-label">Categorías</p>
                    </div>
                </div>
                
                @if(auth()->user()->rol === 'admin')
                <div class="stat-card proveedores-stat">
                    <div class="stat-icon">
                        <i class="bi bi-truck"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number">{{ \App\Models\Proveedor::count() }}</h3>
                        <p class="stat-label">Proveedores</p>
                    </div>
                </div>
                
                <div class="stat-card usuarios-stat">
                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number">{{ \App\Models\User::count() }}</h3>
                        <p class="stat-label">Usuarios</p>
                        @php
                            $pendientes = \App\Models\User::where('rol', 'pendiente')->count();
                        @endphp
                        @if($pendientes > 0)
                        <span class="stat-badge">{{ $pendientes }} pendientes</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Acciones Principales -->
            <div class="main-actions">
                <h3 class="section-title">
                    <i class="bi bi-lightning"></i>
                    Acciones Rápidas
                </h3>
                
                <div class="actions-grid">
                    <!-- Productos -->
                    <div class="action-card productos-card">
                        <div class="action-header">
                            <div class="action-icon">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <h4>Productos</h4>
                        </div>
                        <p class="action-description">Gestiona el inventario completo</p>
                        <div class="action-buttons">
                            <a href="{{ route('productos.index') }}" class="btn-primary">
                                <i class="bi bi-list-ul"></i>
                                Ver Productos
                            </a>
                            <a href="{{ route('productos.create') }}" class="btn-secondary">
                                <i class="bi bi-plus-lg"></i>
                                Crear Producto
                            </a>
                        </div>
                    </div>

                    <!-- Categorías -->
                    <div class="action-card categorias-card">
                        <div class="action-header">
                            <div class="action-icon">
                                <i class="bi bi-tags"></i>
                            </div>
                            <h4>Categorías</h4>
                        </div>
                        <p class="action-description">Organiza productos por categorías</p>
                        <div class="action-buttons">
                            <a href="{{ route('categorias.index') }}" class="btn-primary">
                                <i class="bi bi-list-ul"></i>
                                Ver Categorías
                            </a>
                            <a href="{{ route('categorias.create') }}" class="btn-secondary">
                                <i class="bi bi-plus-lg"></i>
                                Nueva Categoría
                            </a>
                        </div>
                    </div>

                    @if(auth()->user()->rol === 'admin')
                    <!-- Proveedores -->
                    <div class="action-card proveedores-card">
                        <div class="action-header">
                            <div class="action-icon">
                                <i class="bi bi-truck"></i>
                            </div>
                            <h4>Proveedores</h4>
                        </div>
                        <p class="action-description">Administra la red de proveedores</p>
                        <div class="action-buttons">
                            <a href="{{ route('proveedores.index') }}" class="btn-primary">
                                <i class="bi bi-list-ul"></i>
                                Ver Proveedores
                            </a>
                            <a href="{{ route('proveedores.create') }}" class="btn-secondary">
                                <i class="bi bi-plus-lg"></i>
                                Nuevo Proveedor
                            </a>
                        </div>
                    </div>

                    <!-- Usuarios -->
                    <div class="action-card usuarios-card">
                        <div class="action-header">
                            <div class="action-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <h4>Usuarios</h4>
                            @if($pendientes > 0)
                            <span class="notification-badge">{{ $pendientes }}</span>
                            @endif
                        </div>
                        <p class="action-description">Gestiona usuarios y permisos</p>
                        <div class="action-buttons">
                            <a href="{{ route('admin.usuarios.lista') }}" class="btn-primary">
                                <i class="bi bi-people"></i>
                                Gestionar Usuarios
                            </a>
                            @if($pendientes > 0)
                            <a href="{{ route('admin.usuarios.lista') }}" class="btn-warning">
                                <i class="bi bi-person-exclamation"></i>
                                {{ $pendientes }} Pendientes
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actividad Reciente -->
            <div class="recent-activity">
                <h3 class="section-title">
                    <i class="bi bi-clock-history"></i>
                    Actividad Reciente
                </h3>
                
                <div class="activity-list">
                    @php
                        $recentProducts = \App\Models\Producto::orderBy('created_at', 'desc')->limit(5)->get();
                    @endphp
                    
                    @forelse($recentProducts as $product)
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="activity-content">
                            <p class="activity-text">
                                <strong>{{ $product->nombre }}</strong> fue agregado al inventario
                            </p>
                            <span class="activity-time">{{ $product->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>No hay actividad reciente</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animaciones de entrada para las estadísticas
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.4s ease-out';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
            
            // Efecto contador para las estadísticas
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach(element => {
                const finalNumber = parseInt(element.textContent);
                let currentNumber = 0;
                const increment = Math.ceil(finalNumber / 30);
                
                const timer = setInterval(() => {
                    currentNumber += increment;
                    if (currentNumber >= finalNumber) {
                        currentNumber = finalNumber;
                        clearInterval(timer);
                    }
                    element.textContent = currentNumber;
                }, 50);
            });
        });
    </script>
</x-app-layout>
