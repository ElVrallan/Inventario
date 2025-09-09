<div class="bg-white p-6 rounded shadow mt-6">
    <h3 class="text-lg font-semibold mb-4 text-red-600">Eliminar Cuenta</h3>

    <form method="POST" action="{{ route('profile.destroy') }}" id="delete-user-form">
        @csrf
        @method('DELETE')

        <p class="mb-4 text-gray-700">
            Esta acción es irreversible. Tu cuenta será eliminada permanentemente.
        </p>

        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
            Eliminar Cuenta
        </button>
    </form>
</div>
