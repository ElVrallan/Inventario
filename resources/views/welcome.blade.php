<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Inventario</title>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded shadow-md w-96 text-center">
        <h1 class="text-2xl font-bold mb-6">Inventario</h1>

        <a href="{{ route('login') }}" class="bg-blue-500 text-white px-6 py-2 rounded block mb-4">Login</a>
        <a href="{{ route('register') }}" class="bg-green-500 text-white px-6 py-2 rounded block">Registrar</a>
    </div>

</body>
</html>
