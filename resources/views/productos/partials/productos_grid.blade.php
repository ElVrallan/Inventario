@foreach($productos as $producto)
<div class="producto-card" data-stock="{{ $producto->cantidad }}" data-price="{{ $producto->precio }}" data-name="{{ strtolower($producto->nombre) }}">
    <!-- Checkbox para selección múltiple -->
    @if(auth()->user()->rol === 'admin')
    <div class="card-checkbox">
        <input type="checkbox" class="product-select" value="{{ $producto->id }}">
    </div>
    @endif
    
    <!-- Imagen del producto -->
    <div class="card-image">
        <a href="{{ route('productos.show', $producto->id) }}">
            <img src="{{ $producto->imagen_principal ? asset('storage/'.$producto->imagen_principal) : 'https://via.placeholder.com/300x200?text=Sin+Imagen' }}"
                 alt="Imagen de {{ $producto->nombre }}"
                 loading="lazy">
        </a>
        
        <!-- Badge de stock -->
        <div class="stock-badge 
            @if($producto->cantidad > 10) stock-good
            @elseif($producto->cantidad > 0) stock-low
            @else stock-out
            @endif">
            @if($producto->cantidad > 10)
                <i class="bi bi-check-circle"></i> En Stock
            @elseif($producto->cantidad > 0)
                <i class="bi bi-exclamation-triangle"></i> Bajo Stock
            @else
                <i class="bi bi-x-circle"></i> Agotado
            @endif
        </div>
    </div>

    <!-- Contenido de la tarjeta -->
    <div class="card-content">
        <div class="card-header">
            <h3 class="product-title">
                <a href="{{ route('productos.show', $producto->id) }}">
                    {{ $producto->nombre }}
                </a>
            </h3>
            
            @if($producto->categoria)
            <span class="category-tag">
                <i class="bi bi-tag"></i>
                {{ $producto->categoria->nombre }}
            </span>
            @endif
        </div>

        <div class="product-info">
            <div class="price-section">
                <span class="current-price">${{ number_format($producto->precio, 0, ',', '.') }}</span>
                <span class="currency">COP</span>
            </div>
            
            <div class="stock-info">
                <span class="stock-label">Stock:</span>
                <span class="stock-value 
                    @if($producto->cantidad > 10) text-green-600
                    @elseif($producto->cantidad > 0) text-yellow-600
                    @else text-red-600
                    @endif">
                    {{ $producto->cantidad > 0 ? $producto->cantidad . ' unidades' : 'Agotado' }}
                </span>
            </div>
        </div>

        @if($producto->descripcion)
        <p class="product-description">
            {{ Str::limit($producto->descripcion, 80) }}
        </p>
        @endif
    </div>

    <!-- Acciones de la tarjeta -->
    <div class="card-actions">
        <div class="primary-actions">
            <a href="{{ route('productos.show', $producto->id) }}" class="btn-view">
                <i class="bi bi-eye"></i>
                Ver Detalles
            </a>
        </div>

        @if(auth()->user()->rol === 'admin')
        <div class="admin-actions">
            <div class="action-group">
                <a href="{{ route('productos.edit', $producto->id) }}" 
                   class="action-btn edit-btn" 
                   title="Editar producto"
                   data-tooltip="Editar">
                    <i class="bi bi-pencil-square"></i>
                </a>

                <button type="button" 
                        class="action-btn delete-btn" 
                        title="Eliminar producto"
                        data-tooltip="Eliminar"
                        data-product-id="{{ $producto->id }}"
                        data-product-name="{{ $producto->nombre }}">
                    <i class="bi bi-trash"></i>
                </button>
                
                <button type="button" 
                        class="action-btn more-btn" 
                        title="Más opciones"
                        data-tooltip="Más opciones"
                        data-product-id="{{ $producto->id }}">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
            </div>
        </div>
        @endif
    </div>

    <!-- Información adicional (visible en vista de lista) -->
    <div class="list-info">
        <div class="list-item">
            <span class="list-label">Código:</span>
            <span class="list-value">{{ $producto->codigo ?? 'N/A' }}</span>
        </div>
        <div class="list-item">
            <span class="list-label">Proveedor:</span>
            <span class="list-value">{{ $producto->proveedor->nombre ?? 'N/A' }}</span>
        </div>
        <div class="list-item">
            <span class="list-label">Agregado:</span>
            <span class="list-value">{{ $producto->created_at->format('d/m/Y') }}</span>
        </div>
    </div>
</div>
@endforeach

<!-- Formulario oculto para eliminaciones -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
// Script para manejar eliminaciones
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            
            Swal.fire({
                title: '¿Eliminar producto?',
                html: `¿Estás seguro de que quieres eliminar <strong>${productName}</strong>?<br><small class="text-gray-500">Esta acción no se puede deshacer.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('delete-form');
                    form.action = `/productos/${productId}`;
                    form.submit();
                }
            });
        });
    });
    
    // Tooltips para botones de acción
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            // Implementar tooltip si es necesario
        });
    });
});
</script>
        </div>
        @endif
    </div>
</div>
@endforeach
