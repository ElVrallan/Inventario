@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Nuevo Producto</h1>

    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-2">
            <label class="block">Nombre</label>
            <input type="text" name="nombre" class="border p-2 w-full" required>
        </div>
        <div class="mb-2">
            <label class="block">Descripción</label>
            <textarea name="descripcion" class="border p-2 w-full"></textarea>
        </div>
        <div class="mb-2">
            <label class="block">Precio</label>
            <input type="number" step="0.01" name="precio" class="border p-2 w-full" required>
        </div>
        <div class="mb-2">
            <label class="block">Cantidad</label>
            <input type="number" name="cantidad" class="border p-2 w-full" required>
        </div>

        <div class="mb-2">
            <label class="block">Imagen Principal (imagen o video visual: jpg, png, gif, webp, mp4)</label>
            <input type="file" id="imagen_principal" name="imagen_principal" accept="image/*,video/mp4" class="border p-2 w-full">
            <div id="preview-principal" style="margin-top:.5rem;"></div>
        </div>

        <div class="mb-2">
            <label class="block">Galería (opcional, varias imágenes o videos)</label>
            <input type="file" name="galeria[]" id="galeria" accept="image/*,video/mp4" class="border p-2 w-full" multiple>
            <div id="preview-galeria" style="display:flex; gap:.5rem; flex-wrap:wrap; margin-top:.5rem;"></div>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Guardar</button>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('imagen_principal')?.addEventListener('change', function(e){
    const out = document.getElementById('preview-principal');
    out.innerHTML = '';
    const file = e.target.files[0];
    if (!file) return;
    const url = URL.createObjectURL(file);
    if (file.type.startsWith('video/')) {
        const v = document.createElement('video');
        v.src = url; v.controls = true; v.width = 240;
        out.appendChild(v);
    } else {
        const img = document.createElement('img');
        img.src = url; img.style.maxWidth = '240px'; img.style.maxHeight = '160px';
        out.appendChild(img);
    }
});

document.getElementById('galeria')?.addEventListener('change', function(e){
    const out = document.getElementById('preview-galeria');
    out.innerHTML = '';
    Array.from(e.target.files).forEach(file => {
        const url = URL.createObjectURL(file);
        const wrapper = document.createElement('div');
        wrapper.style.width = '120px';
        wrapper.style.height = '90px';
        wrapper.style.overflow = 'hidden';
        if (file.type.startsWith('video/')) {
            const v = document.createElement('video');
            v.src = url; v.controls = true; v.style.width = '100%'; v.style.height = '100%';
            wrapper.appendChild(v);
        } else {
            const img = document.createElement('img');
            img.src = url; img.style.width = '100%'; img.style.height = '100%'; img.style.objectFit = 'cover';
            wrapper.appendChild(img);
        }
        out.appendChild(wrapper);
    });
});
</script>
@endpush

@endsection
