@if($productos->count())
    <div id="resultados-relevantes" class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            Resultados más relevantes para "{{ $query }}"
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @include('productos.partials.productos_grid', ['productos' => $productos])
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('productos.buscar', ['q' => $query, 'full' => 1]) }}" 
               class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 transition">
               Ver más ++
            </a>
        </div>
    </div>
@endif
