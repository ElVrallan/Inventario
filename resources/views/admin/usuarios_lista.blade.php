<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($usuarios as $usuario)
            <tr>
                <td>{{ $usuario->name }}</td>
                <td>{{ $usuario->email }}</td>
                <td>{{ $usuario->rol }}</td>
                <td>
                    <form action="{{ route('admin.usuarios.cambiarRol', $usuario->id) }}" method="POST">
                        @csrf
                        <select name="rol">
                            <option value="pendiente" {{ $usuario->rol == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="vendedor" {{ $usuario->rol == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                            <option value="admin" {{ $usuario->rol == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        <button type="submit">Actualizar</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
