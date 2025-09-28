@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Nuevo Producto</h1>

    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-2">
            <label class="block">Nombre</label>
            <input type="text" name="name" class="border p-2 w-full" required>
        </div>
        <div class="mb-2">
            <label class="block">Descripción</label>
            <textarea name="description" class="border p-2 w-full"></textarea>
        </div>
        <div class="mb-2">
            <label class="block">Precio</label>
            <input type="number" step="0.01" name="price" class="border p-2 w-full" required>
        </div>
        <div class="mb-2">
            <label class="block">Cantidad</label>
            <input type="number" name="quantity" class="border p-2 w-full" required>
        </div>
        <div class="mb-2">
            <label class="block">Imagen Principal</label>
            <input type="file" name="main_image" accept="image/*" class="border p-2 w-full">
        </div>
        <div class="mb-2">
            <label class="block">Galería (opcional, varias imágenes)</label>
            <input type="file" name="gallery_images[]" accept="image/*" class="border p-2 w-full" multiple>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Guardar</button>
    </form>
</div>
@endsection
