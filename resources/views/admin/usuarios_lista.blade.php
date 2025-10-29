@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/usuarios_lista.css') }}">
@endpush

@section('content')
<form id="rolesForm" action="{{ route('admin.usuarios.cambiarRol') }}" method="POST">
    @csrf
    <div class="overflow-x-auto">
        <table class="usuarios-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->name }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>
                        <select name="roles[{{ $usuario->id }}]">
                            <option value="pendiente" {{ $usuario->rol == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="vendedor" {{ $usuario->rol == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                            <option value="admin" {{ $usuario->rol == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Botón flotante -->
    <button type="button" id="guardarCambiosBtn">
        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-floppy-fill" viewBox="0 0 16 16">
            <path d="M0 1.5A1.5 1.5 0 0 1 1.5 0H3v5.5A1.5 1.5 0 0 0 4.5 7h7A1.5 1.5 0 0 0 13 5.5V0h.086a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5H14v-5.5A1.5 1.5 0 0 0 12.5 9h-9A1.5 1.5 0 0 0 2 10.5V16h-.5A1.5 1.5 0 0 1 0 14.5z"/>
            <path d="M3 16h10v-5.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5zm9-16H4v5.5a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5zM9 1h2v4H9z"/>
        </svg>
    </button>
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables para detectar cambios
    let initialValues = {};
    let hasUnsavedChanges = false;
    let isSubmitting = false;
    
    // Guardar valores iniciales de los selects
    const selects = document.querySelectorAll('select[name^="roles"]');
    selects.forEach(function(select) {
        initialValues[select.name] = select.value;
        
        // Agregar event listener para detectar cambios
        select.addEventListener('change', function() {
            checkForChanges();
        });
    });
    
    // Función para verificar si hay cambios
    function checkForChanges() {
        hasUnsavedChanges = false;
        selects.forEach(function(select) {
            if (initialValues[select.name] !== select.value) {
                hasUnsavedChanges = true;
            }
        });
        
        // Mostrar/ocultar botón de guardar según si hay cambios
        const btnGuardar = document.getElementById('guardarCambiosBtn');
        if (hasUnsavedChanges) {
            btnGuardar.style.display = 'block';
        } else {
            btnGuardar.style.display = 'none';
        }
    }
    
    // Ocultar botón inicialmente
    checkForChanges();
    
    // Evento para el botón guardar
    document.getElementById('guardarCambiosBtn').addEventListener('click', function() {
        Swal.fire({
            title: '¿Guardar cambios?',
            text: '¿Estás seguro de que quieres actualizar los roles seleccionados?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                isSubmitting = true;
                document.getElementById('rolesForm').submit();
            }
        });
    });
    
    // Detectar intento de abandonar la página
    window.addEventListener('beforeunload', function(event) {
        if (hasUnsavedChanges && !isSubmitting) {
            event.preventDefault();
            event.returnValue = '¿Estás seguro de que quieres salir? Tienes cambios sin guardar.';
            return event.returnValue;
        }
    });
    
    // Interceptar navegación con enlaces
    document.addEventListener('click', function(event) {
        const link = event.target.closest('a');
        if (link && hasUnsavedChanges && !isSubmitting) {
            event.preventDefault();
            
            Swal.fire({
                title: 'Cambios sin guardar',
                text: 'Tienes cambios sin guardar. ¿Qué deseas hacer?',
                icon: 'warning',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'Guardar y continuar',
                denyButtonText: 'Descartar cambios',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Guardar cambios y luego navegar
                    isSubmitting = true;
                    const form = document.getElementById('rolesForm');
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'redirect_to';
                    hiddenInput.value = link.href;
                    form.appendChild(hiddenInput);
                    form.submit();
                } else if (result.isDenied) {
                    // Descartar cambios y navegar
                    hasUnsavedChanges = false;
                    window.location.href = link.href;
                }
                // Si cancela, no hace nada
            });
        }
    });

    @if(session('success'))
    Swal.fire({
        title: '¡Éxito!',
        text: '{{ session('success') }}',
        icon: 'success',
        confirmButtonText: 'OK'
    });
    @endif
});
</script>
@endsection
