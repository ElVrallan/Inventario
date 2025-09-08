@extends('layouts.app')

@section('content')
<form id="rolesForm" action="{{ route('admin.usuarios.cambiarRol') }}" method="POST">
    @csrf
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow-md">
            <thead>
                <tr>
                    <th
                        class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Nombre</th>
                    <th
                        class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Email</th>
                    <th
                        class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Rol</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                    <td class="px-6 py-4 border-b border-gray-200">{{ $usuario->name }}</td>
                    <td class="px-6 py-4 border-b border-gray-200">{{ $usuario->email }}</td>
                    <td class="px-6 py-4 border-b border-gray-200">
                        <select name="roles[{{ $usuario->id }}]"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300 bg-white">
                            <option value="pendiente" {{ $usuario->rol == 'pendiente' ? 'selected' : '' }}>Pendiente
                            </option>
                            <option value="vendedor" {{ $usuario->rol == 'vendedor' ? 'selected' : '' }}>Vendedor
                            </option>
                            <option value="admin" {{ $usuario->rol == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Botón flotante abajo a la derecha -->
    <button type="button" id="guardarCambiosBtn" style="
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 999;
            background: #3490dc;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 15px 30px;
            font-size: 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: background 0.2s;
        " onmouseover="this.style.background='#2779bd'" onmouseout="this.style.background='#3490dc'">
        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-floppy-fill"
            viewBox="0 0 16 16">
            <path
                d="M0 1.5A1.5 1.5 0 0 1 1.5 0H3v5.5A1.5 1.5 0 0 0 4.5 7h7A1.5 1.5 0 0 0 13 5.5V0h.086a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5H14v-5.5A1.5 1.5 0 0 0 12.5 9h-9A1.5 1.5 0 0 0 2 10.5V16h-.5A1.5 1.5 0 0 1 0 14.5z" />
            <path
                d="M3 16h10v-5.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5zm9-16H4v5.5a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5zM9 1h2v4H9z" />
        </svg> </button>
</form>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
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
                document.getElementById('rolesForm').submit();
            }
        });
    });

    @if(session('success'))
    Swal.fire({
        title: '¡Éxito!',
        text: '{{ session('
        success ') }}',
        icon: 'success',
        confirmButtonText: 'OK'
    });
    @endif
});
</script>
@endsection