@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Resultados de búsqueda: "{{ $query }}"</h1>

    @if($productos->isEmpty())
        <p class="text-gray-500">No se encontraron productos.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
            @include('productos.partials.search_results', ['productos' => $productos])
        </div>
    @endif
</div>
@endsection
