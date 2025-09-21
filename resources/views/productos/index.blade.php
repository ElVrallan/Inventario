@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/productos.css') }}">
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Lista de productos</h1>

    {{-- Barra de búsqueda --}}
    <input type="text" id="search-input" placeholder="Buscar productos..." 
           class="w-full p-2 mb-4 border border-gray-300 rounded" />

<div id="productos-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @include('productos.partials.productos_grid', ['productos' => $productos])
</div>

<div id="loader" style="display:none; text-align:center; margin:2rem 0;">
    <p>Cargando más productos...</p>
</div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/productos.js') }}"></script>
@endpush
