@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('CSS/showproducto.css') }}">
@endpush

@section('content')
<div class="showproducto-container">

    {{-- Imagen principal --}}
    <div>
        <img src="{{ $producto->imagen_principal ? asset('storage/'.$producto->imagen_principal) : 'https://via.placeholder.com/400x300?text=Sin+Imagen' }}"
            alt="Imagen de {{ $producto->nombre }}" class="showproducto-main-img">

        {{-- Galería --}}
        @if($producto->imagenes->isNotEmpty())
        <div class="showproducto-gallery mt-2">
            @foreach($producto->imagenes as $img)
            <img src="{{ asset('storage/'.$img->ruta_imagen) }}" alt="Galería {{ $producto->nombre }}">
            @endforeach
        </div>
        @endif
    </div>

    {{-- Información del producto --}}
    <div class="showproducto-info">
        <p><strong>ID:</strong> {{ $producto->id }}</p>

        <h1>{{ $producto->nombre }}</h1>
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

        @if($producto->descripcion)
        <p>{{ $producto->descripcion }}</p>
        @endif

        @if($producto->categoria)
        <p><strong>Categoría:</strong> {{ $producto->categoria->nombre }}</p>
        @endif

        @if(auth()->user()->rol === 'admin')
        @if($producto->proveedor)
        <p><strong>Proveedor:</strong> {{ $producto->proveedor->nombre }}</p>
        @endif

        <p><strong>Creado por:</strong> {{ $producto->creador->name ?? 'N/A' }}</p>
        <p><strong>Creado:</strong> {{ $producto->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Última actualización:</strong> {{ $producto->updated_at->format('d/m/Y H:i') }}</p>
        @endif

        @if(auth()->user()->rol === 'admin' || auth()->user()->rol === 'vendedor')
        <div class="showproducto-actions">

            {{-- Formulario vender --}}
            <form id="vender-form" action="{{ route('productos.vender', $producto->id) }}" method="POST">
                @csrf
                <input type="number" name="cantidad" min="1" max="{{ $producto->cantidad }}" value="1"
                    style="width: 4rem;">
                <button type="submit" title="Vender" class="btn-vender">Vender</button>
            </form>

            @if(auth()->user()->rol === 'admin')
            {{-- Contenedor para Editar y Eliminar debajo --}}
            <div class="admin-actions" style="margin-top: 0.5rem;">
                <a href="{{ route('productos.edit', $producto->id) }}" title="Editar">Editar</a>

                <form id="eliminar-form" action="{{ route('productos.destroy', $producto->id) }}" method="POST"
                    style="margin-top: 0.25rem;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="Eliminar">Eliminar</button>
                </form>
            </div>
            @endif
        </div>
        @endif

    </div>
</div>

{{-- Carrusel pantalla completa --}}
<div class="showproducto-lightbox" id="showproducto-lightbox">
    <span class="prev" id="prev-image">&#10094;</span>
    <img src="" id="lightbox-img" alt="Imagen producto">
    <span class="next" id="next-image">&#10095;</span>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('JS/showproducto.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const saleMessage = @json(session('success'));
    if (saleMessage) {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: saleMessage,
            confirmButtonText: 'Aceptar',
            allowOutsideClick: false
        });
    }
});
</script>
@endpush