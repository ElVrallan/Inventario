<div class="bg-white p-6 rounded shadow mt-6">
    <h3 class="text-lg font-semibold mb-4">Cambiar Contraseña</h3>

    <form method="POST" action="{{ route('profile.update-password') }}">
        @csrf
        @method('PATCH')

        <div class="mb-4">
            <label for="current_password" class="block font-medium text-gray-700">Contraseña Actual</label>
            <input type="password" name="current_password" id="current_password" class="mt-1 block w-full border rounded p-2">
        </div>

        <div class="mb-4">
            <label for="password" class="block font-medium text-gray-700">Nueva Contraseña</label>
            <input type="password" name="password" id="password" class="mt-1 block w-full border rounded p-2">
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block font-medium text-gray-700">Confirmar Nueva Contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full border rounded p-2">
        </div>

        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded shadow-lg z-50 relative">
            Cambiar Contraseña
        </button>

    </form>
</div>
