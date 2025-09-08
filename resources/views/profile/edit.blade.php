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
</x-app-layout>
