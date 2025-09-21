@foreach($productos as $producto)
<div class="producto-card">
    <a href="{{ route('productos.show', $producto->id) }}">
        <img src="{{ $producto->imagen_principal ? asset('storage/'.$producto->imagen_principal) : 'https://via.placeholder.com/300x200?text=Sin+Imagen' }}"
             alt="Imagen de {{ $producto->nombre }}">
    </a>

    <div class="producto-card-content">
        <h2>{{ $producto->nombre }}</h2>
        <p class="precio">${{ number_format($producto->precio, 0, ',', '.') }} COP</p>
        <p class="cantidad">
            Cantidad:
            @if($producto->cantidad > 10)
                <span class="bg-green-100 text-green-700">{{ $producto->cantidad }}</span>
            @elseif($producto->cantidad > 0)
                <span class="bg-yellow-100 text-yellow-700">{{ $producto->cantidad }}</span>
            @else
                <span class="bg-red-100 text-red-700">Agotado</span>
            @endif
        </p>
    </div>

    <div class="producto-card-footer">
        <a href="{{ route('productos.show', $producto->id) }}" class="text-gray-600 hover:text-gray-800">
            Ver detalles ➝
        </a>

        @if(auth()->user()->rol === 'admin')
            <div class="flex gap-2">
                <a href="{{ route('productos.edit', $producto->id) }}" class="text-blue-500 hover:text-blue-700 transition-colors" title="Editar">
                    <!-- SVG lápiz -->
                </a>

                <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors" title="Eliminar">
                        <!-- SVG basura -->
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endforeach
