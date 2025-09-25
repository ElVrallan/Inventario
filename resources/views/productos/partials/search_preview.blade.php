@if($productos->isEmpty())
    <p class="text-gray-500 p-2">No se encontraron resultados.</p>
@else
    <ul class="divide-y divide-gray-200">
        @foreach($productos as $producto)
            <li class="p-2 hover:bg-gray-100 flex items-center gap-3 cursor-pointer"
                onclick="window.location='{{ route('productos.show', $producto->id) }}'">
                {{-- Imagen pequeña --}}
                <img src="{{ asset('storage/'.$producto->imagen_principal) }}" 
                     alt="{{ $producto->nombre }}" 
                     class="w-12 h-12 object-cover rounded">

                {{-- Info principal --}}
                <div class="flex-1">
                    <p class="text-sm text-gray-500">ID: {{ $producto->id }}</p>
                    <p class="font-semibold text-gray-800">{{ $producto->nombre }}</p>
                    <p class="text-xs text-gray-600 truncate">{{ $producto->descripcion }}</p>
                </div>

                {{-- Precio --}}
                <span class="text-green-600 font-bold text-sm whitespace-nowrap">
                    ${{ number_format($producto->precio, 0, ',', '.') }}
                </span>
            </li>
        @endforeach
    </ul>

    <div class="text-center p-2">
        <a href="{{ route('productos.search', ['q' => request('q')]) }}" 
           class="text-blue-600 hover:underline font-semibold">
           Ver más resultados ➝
        </a>
    </div>
@endif
