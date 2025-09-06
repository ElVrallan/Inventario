<!-- resources/views/pendiente.blade.php -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cuenta Pendiente</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded shadow-md w-96 text-center">
        <h1 class="text-2xl font-bold mb-4">Cuenta Pendiente</h1>
        <p class="mb-6 text-gray-700">
            Tu cuenta fue creada exitosamente, pero necesita que un administrador autorice tu acceso al inventario.
        </p>
        <p class="text-gray-500 text-sm">
            Por favor, espera a que un administrador revise tu cuenta.
        </p>
        <a href="{{ route('logout') }}" 
           class="mt-6 inline-block bg-red-500 text-white px-6 py-2 rounded"
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
