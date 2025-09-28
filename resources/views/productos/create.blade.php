@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">Crear producto</h1>

    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        {{-- Datos básicos --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" class="mt-1 border p-2 w-full rounded" required>
                @error('nombre')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Precio</label>
                <input type="number" step="0.01" name="precio" value="{{ old('precio') }}" class="mt-1 border p-2 w-full rounded" required>
                @error('precio')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Cantidad</label>
                <input type="number" name="cantidad" value="{{ old('cantidad', 0) }}" class="mt-1 border p-2 w-full rounded" required>
                @error('cantidad')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Categoría</label>
                <select name="categoria_id" class="mt-1 border p-2 w-full rounded">
                    <option value="">-- Sin categoría --</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" @selected(old('categoria_id') == $cat->id)>{{ $cat->nombre }}</option>
                    @endforeach
                </select>
                @error('categoria_id')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Descripción</label>
                <textarea name="descripcion" rows="4" class="mt-1 border p-2 w-full rounded">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Proveedor</label>
                <select name="proveedor_id" class="mt-1 border p-2 w-full rounded">
                    <option value="">-- Sin proveedor --</option>
                    @foreach($proveedores as $prov)
                        <option value="{{ $prov->id }}" @selected(old('proveedor_id') == $prov->id)>{{ $prov->nombre }}</option>
                    @endforeach
                </select>
                @error('proveedor_id')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Galería --}}
        <div class="border rounded p-4">
            <label class="block text-sm font-medium">Galería (imágenes/videos múltiples)</label>
            <input id="galeria-input" type="file" name="galeria[]" accept="image/*,video/mp4" class="mt-1 border p-2 w-full rounded" multiple>
            <input type="hidden" name="galeria_principal" id="galeria_principal" value="">
            <p class="text-xs text-gray-500 mt-1">Puedes seleccionar uno como principal. Si no eliges, se tomará el primer archivo como principal.</p>

            <div id="galeria-preview" class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3"></div>
            @error('galeria.*')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('productos.index') }}" class="px-4 py-2 rounded border" style="display:inline-block;border:1px solid #d1d5db;color:#111827;background:#ffffff;">Cancelar</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded" style="background:#2563eb;color:#ffffff;border-radius:0.375rem;padding:0.5rem 1rem;">Guardar</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Manejar selección incremental de archivos y preview persistente
(function() {
    const input = document.getElementById('galeria-input');
    const preview = document.getElementById('galeria-preview');
    const principalInput = document.getElementById('galeria_principal');
    const form = input ? input.closest('form') : null;
    /** @type {File[]} */
    let selectedFiles = [];

    function rebuildInputFiles() {
        const dt = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        input.files = dt.files;
    }

    function rebuildPreview() {
        preview.innerHTML = '';
        selectedFiles.forEach((file, idx) => {
            const card = document.createElement('div');
            card.className = 'border rounded p-2 flex flex-col items-center gap-2';

            const topRow = document.createElement('div');
            topRow.className = 'w-full flex items-center justify-between';
            const label = document.createElement('label');
            label.className = 'text-xs text-gray-700';
            label.textContent = `Archivo ${idx + 1}`;
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.textContent = '✕';
            removeBtn.title = 'Eliminar de la selección';
            removeBtn.style = 'font-size:12px;color:#b91c1c;background:#fee2e2;border:1px solid #fecaca;border-radius:4px;padding:0 6px;';
            removeBtn.addEventListener('click', () => {
                // Si quitamos el marcado como principal, también lo limpiamos
                if (principalInput.value !== '' && Number(principalInput.value) === idx) {
                    principalInput.value = '';
                }
                selectedFiles.splice(idx, 1);
                rebuildInputFiles();
                rebuildPreview();
            });
            topRow.appendChild(label);
            topRow.appendChild(removeBtn);
            card.appendChild(topRow);

            // preview
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.className = 'w-24 h-24 object-cover rounded';
                img.src = URL.createObjectURL(file);
                card.appendChild(img);
            } else if (file.type === 'video/mp4') {
                const vid = document.createElement('video');
                vid.className = 'w-24 h-24 object-cover rounded';
                vid.src = URL.createObjectURL(file);
                vid.controls = true;
                card.appendChild(vid);
            }

            const radioWrap = document.createElement('div');
            radioWrap.className = 'flex items-center gap-1';
            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = 'galeria_principal_idx';
            radio.value = String(idx);
            radio.checked = principalInput.value !== '' && Number(principalInput.value) === idx;
            radio.addEventListener('change', () => {
                principalInput.value = String(idx);
            });
            const rLabel = document.createElement('span');
            rLabel.className = 'text-xs';
            rLabel.textContent = 'Marcar como principal';
            radioWrap.appendChild(radio);
            radioWrap.appendChild(rLabel);
            card.appendChild(radioWrap);

            preview.appendChild(card);
        });
    }

    input?.addEventListener('change', function(e) {
        const files = Array.from(e.target.files || []);
        if (!files.length) return;
        // Limpiar el valor del input primero para permitir volver a seleccionar los mismos archivos
        // y evitar que se borren los programados al hacer submit
        input.value = '';
        // Agregar los nuevos al arreglo persistente
        selectedFiles.push(...files);
        // Reconstruir files del input preservando anteriores
        rebuildInputFiles();
        // Reconstruir la vista previa
        rebuildPreview();
    });

    // Asegurar que antes de enviar el formulario, el input contenga todos los archivos acumulados
    form?.addEventListener('submit', function() {
        rebuildInputFiles();
        // Si no se eligió principal pero hay archivos, por defecto usar el primero (índice 0)
        if (principalInput && principalInput.value === '' && selectedFiles.length > 0) {
            principalInput.value = '0';
        }
    });
})();
</script>
@endpush
