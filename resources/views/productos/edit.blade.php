@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">Editar producto</h1>

    <form action="{{ route('productos.update', $producto->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        {{-- Datos básicos --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre) }}" class="mt-1 border p-2 w-full rounded" required>
                @error('nombre')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Precio</label>
                <input type="number" step="0.01" name="precio" value="{{ old('precio', $producto->precio) }}" class="mt-1 border p-2 w-full rounded" required>
                @error('precio')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Cantidad</label>
                <input type="number" name="cantidad" value="{{ old('cantidad', $producto->cantidad) }}" class="mt-1 border p-2 w-full rounded" required>
                @error('cantidad')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Categoría</label>
                <select name="categoria_id" class="mt-1 border p-2 w-full rounded">
                    <option value="">-- Sin categoría --</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" @selected(old('categoria_id', $producto->categoria_id) == $cat->id)>{{ $cat->nombre }}</option>
                    @endforeach
                </select>
                @error('categoria_id')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Descripción</label>
                <textarea name="descripcion" rows="4" class="mt-1 border p-2 w-full rounded">{{ old('descripcion', $producto->descripcion) }}</textarea>
                @error('descripcion')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Proveedor</label>
                <select name="proveedor_id" class="mt-1 border p-2 w-full rounded">
                    <option value="">-- Sin proveedor --</option>
                    @foreach($proveedores as $prov)
                        <option value="{{ $prov->id }}" @selected(old('proveedor_id', $producto->proveedor_id) == $prov->id)>{{ $prov->nombre }}</option>
                    @endforeach
                </select>
                @error('proveedor_id')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Galería existente y nuevos archivos --}}
        <div class="border rounded p-4">
            <label class="block text-sm font-medium">Galería (imágenes/videos)</label>
            <input type="hidden" name="galeria_principal" id="galeria_principal" value="">

            {{-- Galería existente --}}
            @if($producto->imagen_principal || $producto->imagenes->isNotEmpty())
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Archivos actuales:</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3" id="galeria-existente">
                    {{-- Imagen principal --}}
                    @if($producto->imagen_principal)
                        <div class="border rounded p-2 flex flex-col items-center gap-2" data-type="principal">
                            @php $ext = pathinfo($producto->imagen_principal, PATHINFO_EXTENSION); @endphp
                            @if(in_array(strtolower($ext), ['mp4']))
                                <video src="{{ asset('storage/'.$producto->imagen_principal) }}" class="w-24 h-24 object-cover rounded" controls></video>
                            @else
                                <img src="{{ asset('storage/'.$producto->imagen_principal) }}" class="w-24 h-24 object-cover rounded" />
                            @endif
                            <div class="text-xs font-medium text-green-600">PRINCIPAL</div>
                        </div>
                    @endif

                    {{-- Galería --}}
                    @foreach($producto->imagenes as $img)
                        <div class="border rounded p-2 flex flex-col items-center gap-2" data-image-id="{{ $img->id }}">
                            <div class="w-full flex items-center justify-between">
                                <span class="text-xs text-gray-600">ID: {{ $img->id }}</span>
                                <button type="button" class="delete-image-btn text-xs text-red-600 bg-red-100 border border-red-300 rounded px-2 py-1 hover:bg-red-200" data-image-id="{{ $img->id }}">
                                    Eliminar
                                </button>
                            </div>
                            @php $ext = pathinfo($img->ruta, PATHINFO_EXTENSION); @endphp
                            @if(strtolower($ext) === 'mp4')
                                <video src="{{ asset('storage/'.$img->ruta) }}" class="w-24 h-24 object-cover rounded" controls></video>
                            @else
                                <img src="{{ asset('storage/'.$img->ruta) }}" class="w-24 h-24 object-cover rounded" />
                            @endif
                            <label class="flex items-center gap-1 text-xs">
                                <input type="radio" name="galeria_principal_radio" value="{{ $img->id }}" @checked($img->es_principal) onclick="document.getElementById('galeria_principal').value=this.value;">
                                Marcar como principal
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            @else
                <p class="text-sm text-gray-500 mb-4">No hay archivos en la galería aún.</p>
            @endif

            {{-- Agregar nuevos archivos --}}
            <div class="border-t pt-4">
                <label class="block text-sm font-medium">Agregar nuevos archivos a la galería</label>
                <input id="galeria-input" type="file" name="galeria[]" accept="image/*,video/mp4" class="mt-1 border p-2 w-full rounded" multiple>
                <p class="text-xs text-gray-500 mt-1">Los nuevos archivos se agregarán a la galería existente.</p>

                <div id="galeria-preview" class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3"></div>
                @error('galeria.*')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('productos.show', $producto->id) }}" class="btn-cancelar">Cancelar</a>
            <button type="submit" class="btn-guardar">Actualizar</button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/crearProducto.css') }}">
@endpush

@push('scripts')
    {{-- Config mínima para el JS externo (URL base y CSRF) --}}
    <script>
        window.productosEdit = {
            deleteUrlBase: "{{ url('productos/images') }}",
            csrfToken: "{{ csrf_token() }}"
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/editProducto.js') }}"></script>
@endpush
