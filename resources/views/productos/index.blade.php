@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/productos.css') }}">
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Lista de productos</h1>

    @if($productos->isEmpty())
    <p class="text-gray-500">No hay productos registrados.</p>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
        @foreach($productos as $producto)
        <div class="producto-card">

            {{-- Imagen clickeable --}}
            <a href="{{ route('productos.show', $producto->id) }}">
                <img src="{{ $producto->imagen_principal ? asset('storage/'.$producto->imagen_principal) : 'https://via.placeholder.com/300x200?text=Sin+Imagen' }}"
                    alt="Imagen de {{ $producto->nombre }}">
            </a>

            {{-- Contenido --}}
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

            {{-- Footer --}}
            <div class="producto-card-footer">
                <a href="{{ route('productos.show', $producto->id) }}" class="text-gray-600 hover:text-gray-800">
                    Ver detalles ➝
                </a>

                @if(auth()->user()->rol === 'admin')
                <div class="flex gap-2">
                    {{-- Botón Editar --}}
                    <a href="{{ route('productos.edit', $producto->id) }}"
                        class="text-blue-500 hover:text-blue-700 transition-colors" title="Editar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path
                                d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                            <path fill-rule="evenodd"
                                d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                        </svg>
                    </a>

                    {{-- Botón Eliminar --}}
                    <form action="{{ route('productos.destroy', $producto->id) }}" method="POST"
                        onsubmit="return confirm('¿Seguro que deseas eliminar este producto?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 transition-colors"
                            title="Eliminar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                viewBox="0 0 16 16" class="bi bi-trash-fill">
                                <path
                                    d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0" />
                            </svg>
                        </button>
                    </form>
                </div>
                @endif
            </div>

        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
