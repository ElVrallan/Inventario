@push('styles')
<link rel="stylesheet" href="{{ asset('css/pendiente.css') }}">
@endpush

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cuenta Pendiente</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div class="pendiente-container">
        <h1>Cuenta Pendiente</h1>
        <p>
            Tu cuenta fue creada exitosamente, pero necesita que un administrador autorice tu acceso al inventario.
        </p>
        <p class="text-sm">
            Por favor, espera a que un administrador revise tu cuenta.
        </p>
        <a href="{{ route('logout') }}" 
           class="logout-button"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
           Cerrar Sesión
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</body>
</html>

@if(session('pendiente'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: 'warning',
        title: 'Cuenta pendiente',
        text: '{{ session('pendiente') }}',
        confirmButtonText: 'Aceptar'
    });
</script>
@endif
