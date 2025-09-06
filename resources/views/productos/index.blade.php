@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Productos</h1>

    <a href="{{ route('productos.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Nuevo Producto</a>

    @if(session('success'))
        <div class="bg-green-200 text-green-800 p-2 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full border border-gray-300">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 border">ID</th>
                <th class="p-2 border">Nombre</th>
                <th class="p-2 border">Imagen</th>
                <th class="p-2 border">Cantidad</th>
                <th class="p-2 border">Precio</th>
                <th class="p-2 border">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td class="p-2 border">{{ $product->id }}</td>
                <td class="p-2 border">{{ $product->name }}</td>
                <td class="p-2 border">
            @if($product->main_image)
                <div class="mb-2">
                    <strong>Principal:</strong>
                    <img src="{{ asset('storage/' . $product->main_image) }}" class="w-16 h-16 object-cover">
                </div>
            @endif

            @if($product->images->count())
                <div>
                    <strong>Galería:</strong>
                    @foreach($product->images as $img)
                        <img src="{{ asset('storage/' . $img->path) }}" class="w-12 h-12 object-cover inline-block mr-1 mb-1">
                    @endforeach
                </div>
            @endif

                </td>
                <td class="p-2 border">{{ $product->quantity }}</td>
                <td class="p-2 border">${{ $product->price }}</td>
                <td class="p-2 border">
                    <a href="{{ route('productos.edit', $product) }}" class="bg-yellow-400 px-2 py-1 rounded">Editar</a>
                    @if(auth()->user()->rol === 'admin')
                    <form action="{{ route('productos.destroy', $product) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 px-2 py-1 text-white rounded">Eliminar</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
