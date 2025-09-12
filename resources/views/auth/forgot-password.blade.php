<div class="flex items-center justify-start mt-4">
    <a href="{{ url('/') }}" 
       class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
        ← Inicio
    </a>
</div>

<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('¿Olvidaste tu contraseña? No hay problema. Solo tienes que indicarnos tu dirección de correo electrónico y te enviaremos un enlace para restablecer la contraseña que te permitirá elegir una nueva.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Enviar correo a mi Email') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
