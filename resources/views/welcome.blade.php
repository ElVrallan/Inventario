@push('styles')
<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
@endpush

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body class="welcome-container">

    <div class="welcome-card">
        <h1>Inventario</h1>

        <a href="{{ route('login') }}" class="bg-blue-500">Ingresar</a>
        <a href="{{ route('register') }}" class="bg-green-500">Registrar</a>
    </div>

</body>
</html>
