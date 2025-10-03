@push('styles')
<link rel="stylesheet" href="{{ asset('css/navigation.css') }}">
@endpush

<nav x-data="{ open: false }" class="glass-navbar border-b border-gray-200 shadow-lg">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-6">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="navbar-logo flex items-center gap-2">
                        <x-application-logo class="block h-9 w-auto fill-current text-primary-600" />
                        <span class="font-extrabold text-lg tracking-tight text-primary-700 hidden sm:inline">Inventario</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                @if (
                    url()->previous() !== url()->current() &&
                    !request()->routeIs('dashboard')
                )
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex nav-links">
                        <x-nav-link :href="url()->previous()" class="nav-back-link">
                            ← Quick back
                        </x-nav-link>
                    </div>
                @endif

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex nav-links">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="nav-link-dashboard" style="color:#fff !important">
                        <i class="bi bi-house-door-fill mr-1"></i> {{ __('Tablero principal') }}
                    </x-nav-link>
                </div>

                @if(Auth::user() && Auth::user()->rol === 'admin')
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex nav-links">
                        <x-nav-link :href="route('admin.usuarios.lista')" style="color:#fff !important">
                             INVENTARIO
                        </x-nav-link>
                    </div>
                @endif
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-semibold rounded-xl text-primary-700 bg-white/30 hover:bg-white/50 shadow-sm backdrop-blur-md transition ease-in-out duration-150">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-person-circle text-xl text-primary-600"></i>
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="fill-current h-4 w-4 nav-icon ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Mi perfil') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Cerrar Sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-primary-400 hover:text-primary-600 hover:bg-primary-50 focus:outline-none focus:bg-primary-100 focus:text-primary-700 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div x-show="open" x-transition x-cloak class="glass-navbar-mobile sm:hidden" style="position:relative;z-index:9999;">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="nav-link-dashboard" style="color:#fff !important">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>
        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-primary-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-primary-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
