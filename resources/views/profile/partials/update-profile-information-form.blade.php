<div class="bg-white p-6 rounded shadow">
    <h3 class="text-lg font-semibold mb-4">Actualizar Información de Perfil</h3>

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PATCH')

        <div class="mb-4">
            <label for="name" class="block font-medium text-gray-700">Nombre</label>
            <input type="text" name="name" id="name" value="{{ $user->name }}" class="mt-1 block w-full border rounded p-2">
        </div>

        <div class="mb-4">
            <label for="email" class="block font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="{{ $user->email }}" class="mt-1 block w-full border rounded p-2">
        </div>

        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded shadow-lg z-50 relative">
            Guardar cambios
        </button>
    </form>
</div>
