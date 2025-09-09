<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Perfil de {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12 space-y-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @include('profile.partials.update-profile-information-form')
        @include('profile.partials.update-password-form')
        @include('profile.partials.delete-user-form')
    </div>

    {{-- SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Update profile
        const updateProfileForm = document.getElementById("update-profile-form");
        if (updateProfileForm) {
            updateProfileForm.addEventListener("submit", function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "¿Actualizar perfil?",
                    text: "Se guardarán los cambios en tu información.",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Sí, actualizar",
                    cancelButtonText: "Cancelar"
                }).then(result => {
                    if (result.isConfirmed) this.submit();
                });
            });
        }

        // Update password
        const updatePasswordForm = document.getElementById("update-password-form");
        if (updatePasswordForm) {
            updatePasswordForm.addEventListener("submit", function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "¿Cambiar contraseña?",
                    text: "Estás a punto de modificar tu contraseña.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sí, cambiar",
                    cancelButtonText: "Cancelar"
                }).then(result => {
                    if (result.isConfirmed) this.submit();
                });
            });
        }

        // Delete account
        const deleteUserForm = document.getElementById("delete-user-form");
        if (deleteUserForm) {
            deleteUserForm.addEventListener("submit", function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "¿Eliminar cuenta?",
                    text: "Esta acción no se puede deshacer.",
                    icon: "error",
                    showCancelButton: true,
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                }).then(result => {
                    if (result.isConfirmed) this.submit();
                });
            });
        }

        // Mensajes de éxito después de redirigir
        @if (session('status') === 'profile-updated')
            Swal.fire("¡Perfil actualizado!", "Tu información se guardó correctamente.", "success");
        @elseif (session('status') === 'password-updated')
            Swal.fire("¡Contraseña actualizada!", "Tu contraseña se cambió correctamente.", "success");
        @endif
    </script>
</x-app-layout>
