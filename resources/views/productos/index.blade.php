@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('CSS/productos_modern.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@section('content')
<div class="productos-container">
    <!-- Header con estadísticas -->
    <div class="productos-header">
        <div class="header-content">
            <div class="header-info">
                <h1 class="page-title">
                    <i class="bi bi-box-seam"></i>
                    Inventario de Productos
                </h1>
                <p class="page-subtitle">
                    Gestiona tu inventario de forma eficiente
                </p>
            </div>
            
            <div class="header-stats">
                <div class="stat-item">
                    <span class="stat-number">{{ $productos->total() }}</span>
                    <span class="stat-label">Total</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ \App\Models\Producto::where('cantidad', '>', 10)->count() }}</span>
                    <span class="stat-label">En Stock</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ \App\Models\Producto::where('cantidad', 0)->count() }}</span>
                    <span class="stat-label">Agotados</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de herramientas -->
    <div class="toolbar">
        <div class="search-section">
            <div class="search-container">
                <i class="bi bi-search search-icon"></i>
                <input type="text" id="search-input" placeholder="Buscar productos por nombre, código o categoría..." class="search-input">
                <button id="search-clear" class="search-clear" style="display: none;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">
                    <i class="bi bi-grid"></i>
                    Todos
                </button>
                <button class="filter-btn" data-filter="in-stock">
                    <i class="bi bi-check-circle"></i>
                    En Stock
                </button>
                <button class="filter-btn" data-filter="low-stock">
                    <i class="bi bi-exclamation-triangle"></i>
                    Stock Bajo
                </button>
                <button class="filter-btn" data-filter="out-of-stock">
                    <i class="bi bi-x-circle"></i>
                    Agotado
                </button>
            </div>
        </div>

        <div class="view-controls">
            <div class="view-toggle">
                <button class="view-btn active" data-view="grid">
                    <i class="bi bi-grid-3x3-gap"></i>
                </button>
                <button class="view-btn" data-view="list">
                    <i class="bi bi-list"></i>
                </button>
            </div>
            
            <div class="sort-controls">
                <select id="sort-select" class="sort-select">
                    <option value="name-asc">Nombre A-Z</option>
                    <option value="name-desc">Nombre Z-A</option>
                    <option value="price-asc">Precio: Menor a Mayor</option>
                    <option value="price-desc">Precio: Mayor a Menor</option>
                    <option value="stock-asc">Stock: Menor a Mayor</option>
                    <option value="stock-desc">Stock: Mayor a Menor</option>
                    <option value="date-desc">Más Recientes</option>
                    <option value="date-asc">Más Antiguos</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Contenedor de productos -->
    <div class="productos-content">
        <div id="productos-container" class="productos-grid">
            @include('productos.partials.productos_grid', ['productos' => $productos])
        </div>

        <!-- Loading indicator -->
        <div id="loader" class="loader">
            <div class="loader-spinner"></div>
            <p>Cargando más productos...</p>
        </div>

        <!-- Empty state -->
        <div id="empty-state" class="empty-state" style="display: none;">
            <div class="empty-icon">
                <i class="bi bi-inbox"></i>
            </div>
            <h3>No se encontraron productos</h3>
            <p>Intenta ajustar los filtros o realizar una nueva búsqueda</p>
            <button class="btn-reset-filters">
                <i class="bi bi-arrow-counterclockwise"></i>
                Limpiar Filtros
            </button>
        </div>
    </div>

    <!-- Botón flotante para agregar producto -->
    @if(Auth::user() && Auth::user()->rol === 'admin')
    <div class="fab-container">
        <button class="fab-main" id="fab-main">
            <i class="bi bi-plus-lg fab-icon"></i>
            <i class="bi bi-x-lg fab-close"></i>
        </button>
        
        <div class="fab-options">
            <a href="{{ route('productos.create') }}" class="fab-option" data-tooltip="Agregar Producto">
                <i class="bi bi-box-seam"></i>
            </a>
            <a href="{{ route('categorias.create') }}" class="fab-option" data-tooltip="Nueva Categoría">
                <i class="bi bi-tags"></i>
            </a>
            <button class="fab-option" id="bulk-actions" data-tooltip="Acciones en Lote">
                <i class="bi bi-check2-square"></i>
            </button>
        </div>
    </div>
    @endif
</div>

<!-- Modal para acciones en lote -->
<div id="bulk-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>
                <i class="bi bi-check2-square"></i>
                Acciones en Lote
            </h3>
            <button class="modal-close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="modal-body">
            <p>Selecciona los productos y elige una acción:</p>
            <div class="bulk-actions">
                <button class="bulk-btn" data-action="export">
                    <i class="bi bi-download"></i>
                    Exportar Seleccionados
                </button>
                <button class="bulk-btn" data-action="update-category">
                    <i class="bi bi-tags"></i>
                    Cambiar Categoría
                </button>
                <button class="bulk-btn danger" data-action="delete">
                    <i class="bi bi-trash"></i>
                    Eliminar Seleccionados
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/productos.js') }}"></script>
<script>
// JavaScript adicional para las nuevas funcionalidades
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar funcionalidades
    initializeFilters();
    initializeViewToggle();
    initializeFAB();
    initializeBulkActions();
    initializeSearch();
});

function initializeFilters() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            // Implementar lógica de filtrado
        });
    });
}

function initializeViewToggle() {
    const viewBtns = document.querySelectorAll('.view-btn');
    const container = document.getElementById('productos-container');
    
    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            viewBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const view = this.dataset.view;
            container.className = view === 'grid' ? 'productos-grid' : 'productos-list';
        });
    });
}

function initializeFAB() {
    const fabMain = document.getElementById('fab-main');
    const fabContainer = document.querySelector('.fab-container');
    
    if (fabMain) {
        fabMain.addEventListener('click', function() {
            fabContainer.classList.toggle('active');
        });
    }
}

function initializeBulkActions() {
    const bulkBtn = document.getElementById('bulk-actions');
    const modal = document.getElementById('bulk-modal');
    const closeBtn = modal?.querySelector('.modal-close');
    
    if (bulkBtn) {
        bulkBtn.addEventListener('click', function() {
            modal.style.display = 'flex';
        });
    }
    
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    }
}

function initializeSearch() {
    const searchInput = document.getElementById('search-input');
    const clearBtn = document.getElementById('search-clear');
    
    searchInput.addEventListener('input', function() {
        clearBtn.style.display = this.value ? 'flex' : 'none';
    });
    
    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        this.style.display = 'none';
        // Resetear búsqueda
    });
}
</script>
@endpush