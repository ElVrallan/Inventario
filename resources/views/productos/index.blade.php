@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Lista de productos</h1>

    @if($productos->isEmpty())
    <p class="text-gray-500">No hay productos registrados.</p>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($productos as $producto)
        <a href="{{ route('productos.show', $producto->id) }}"
            class="block bg-white shadow-md rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">

            {{-- Imagen --}}
            <img src="{{ $producto->imagen_principal ? asset('storage/'.$producto->imagen_principal) : 'https://via.placeholder.com/300x200?text=Sin+Imagen' }}"
                alt="Imagen de {{ $producto->nombre }}" class="w-full h-48 object-cover">

            {{-- Contenido --}}
            <div class="p-4">
                <h2 class="text-lg font-semibold text-gray-800 truncate">{{ $producto->nombre }}</h2>
                <p class="text-green-600 font-bold mt-2">
                    ${{ number_format($producto->precio, 0, ',', '.') }} COP
                </p>
                <p class="mt-1 text-sm text-gray-600">
                    Cantidad:
                    @if($producto->cantidad > 10)
                    <span
                        class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">{{ $producto->cantidad }}</span>
                    @elseif($producto->cantidad > 0)
                    <span
                        class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-medium">{{ $producto->cantidad }}</span>
                    @else
                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-medium">Agotado</span>
                    @endif
                </p>
            </div>

            {{-- Footer con acciones --}}
            <div class="px-4 pb-4 flex justify-between items-center text-sm">
                <span class="text-gray-500">Ver detalles ➝</span>

                @if(auth()->user()->rol === 'admin')
                <form action="{{ route('productos.destroy', $producto->id) }}" method="POST"
                    onsubmit="return confirm('¿Seguro que deseas eliminar este producto?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors" title="Eliminar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            viewBox="0 0 16 16" class="bi bi-trash-fill">
                            <path
                                d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0" />
                        </svg>
                    </button>
                </form>
                @endif
            </div>

        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection