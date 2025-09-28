@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/productos.css') }}">
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Lista de productos</h1>

  <div class="search-container">
    <input type="text" id="search-input" placeholder="Buscar productos...">
<button id="search-button" title="Buscar">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
    </svg>
</button>


</div>

    {{-- Productos normales con scroll infinito --}}
    <div id="productos-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @include('productos.partials.productos_grid', ['productos' => $productos])
    </div>

    <div id="loader" style="display:none; text-align:center; margin:2rem 0;">
        <p>Cargando más productos...</p>
    </div>

    {{-- Botón flotante solo para admin --}}
    @if(auth()->check() && auth()->user()->rol === 'admin')
    <a href="{{ route('productos.create') }}" class="floating-add-btn" title="Crear producto" aria-label="Crear producto">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag-plus-fill" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M10.5 3.5a2.5 2.5 0 0 0-5 0V4h5zm1 0V4H15v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4h3.5v-.5a3.5 3.5 0 1 1 7 0M8.5 8a.5.5 0 0 0-1 0v1.5H6a.5.5 0 0 0 0 1h1.5V12a.5.5 0 0 0 1 0v-1.5H10a.5.5 0 0 0 0-1H8.5z"/>
      </svg>
    </a>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/productos.js') }}"></script>
@endpush