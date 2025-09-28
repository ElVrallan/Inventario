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
    document.addEventListener('DOMContentLoaded', function() {
        // Buscar formularios por id o con selectores de respaldo (action)
        const updateProfileForm = document.getElementById("update-profile-form")
            || document.querySelector('form[action*="profile.update"]')
            || document.querySelector('form[action*="user/profile"]');

        if (updateProfileForm) {
            updateProfileForm.addEventListener("submit", function(e) {
                e.preventDefault();
                const form = this;
                Swal.fire({
                    title: "¿Actualizar perfil?",
                    text: "Se guardarán los cambios en tu información.",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Sí, actualizar",
                    cancelButtonText: "Cancelar"
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            });
        }

        const updatePasswordForm = document.getElementById("update-password-form")
            || document.querySelector('form[action*="password.update"]')
            || document.querySelector('form[action*="user/password"]');

        if (updatePasswordForm) {
            updatePasswordForm.addEventListener("submit", function(e) {
                e.preventDefault();
                const form = this;
                Swal.fire({
                    title: "¿Cambiar contraseña?",
                    text: "Estás a punto de modificar tu contraseña.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sí, cambiar",
                    cancelButtonText: "Cancelar"
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            }); // <-- CORRECCIÓN: cerrar addEventListener correctamente
        } // <-- CORRECCIÓN: cerrar el if de updatePasswordForm

        const deleteUserForm = document.getElementById("delete-user-form")
            || document.querySelector('form[action*="profile.destroy"]')
            || document.querySelector('form[action*="user/delete"]');

        if (deleteUserForm) {
            deleteUserForm.addEventListener("submit", function(e) {
                e.preventDefault();
                const form = this;
                Swal.fire({
                    title: "¿Eliminar cuenta?",
                    text: "Esta acción no se puede deshacer.",
                    icon: "error",
                    showCancelButton: true,
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            });
        }

        // Mensajes de éxito después de redirigir (se muestran una vez cargado el DOM)
        @if (session('status') === 'profile-updated')
            Swal.fire("¡Perfil actualizado!", "Tu información se guardó correctamente.", "success");
        @elseif (session('status') === 'password-updated')
            Swal.fire("¡Contraseña actualizada!", "Tu contraseña se cambió correctamente.", "success");
        @endif

        // --- Mostrar errores de validación de contraseña en SweetAlert ---
        (function() {
            // Traer todos los mensajes de error desde el servidor
            const validationErrors = @json($errors->getMessages() ?? []);

            // Campos relacionados con el cambio de contraseña
            const pwFields = ['current_password', 'password', 'password_confirmation'];
            const pwMessages = [];

            pwFields.forEach(function(field) {
                if (validationErrors[field] && validationErrors[field].length) {
                    validationErrors[field].forEach(function(msg) {
                        pwMessages.push(msg);
                    });
                }
            });

            // Traduce frases comunes en inglés a español
            function translateMessage(msg) {
                if (!msg) return msg;
                let s = msg;

                // Traducciones directas / variantes comunes
                s = s.replace(/The provided password does not match our records\./i, 'La contraseña actual no coincide con nuestros registros.');
                s = s.replace(/The current password is incorrect\./i, 'La contraseña actual es incorrecta.');
                s = s.replace(/The password is incorrect\./i, 'La contraseña es incorrecta.');
                s = s.replace(/The password confirmation does not match\./i, 'La confirmación de la contraseña no coincide.');
                s = s.replace(/The password field confirmation does not match\./i, 'La confirmación de la contraseña no coincide.');
                s = s.replace(/The password must be at least (\d+) characters\./i, 'La contraseña debe tener al menos $1 caracteres.');
                s = s.replace(/The password field must be at least (\d+) characters\./i, 'La contraseña debe tener al menos $1 caracteres.');

                // Frases más genéricas
                s = s.replace(/confirmation does not match/i, 'La confirmación no coincide.');
                s = s.replace(/must be at least (\d+) characters/i, 'debe tener al menos $1 caracteres');

                return s;
            }

            if (pwMessages.length) {
                // Formatear en HTML (cada mensaje en su línea) y traducirlos
                const html = pwMessages
                    .map(m => `<div style="text-align:left;margin-bottom:6px;">${translateMessage(m)}</div>`)
                    .join('');
                Swal.fire({
                    icon: 'error',
                    title: 'Error al cambiar la contraseña',
                    html: html,
                    confirmButtonText: 'Cerrar'
                });
            }
        })();
        // --- fin errores de contraseña ---
    });
    </script>
</x-app-layout>
