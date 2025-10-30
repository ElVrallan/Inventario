<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nuevo Movimiento de Inventario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('movimientos.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="fecha" :value="__('Fecha')" />
                            <x-text-input id="fecha" type="date" name="fecha" :value="old('fecha', date('Y-m-d'))" required class="block mt-1 w-full" />
                            <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="tipo" :value="__('Tipo de Movimiento')" />
                            <select id="tipo" name="tipo" required class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="entrada">Entrada</option>
                                <option value="salida">Salida</option>
                            </select>
                            <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="cantidad" :value="__('Cantidad')" />
                            <x-text-input id="cantidad" type="number" name="cantidad" :value="old('cantidad')" required class="block mt-1 w-full" min="1" />
                            <x-input-error :messages="$errors->get('cantidad')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="producto_id" :value="__('Producto')" />
                            <select id="producto_id" name="producto_id" required class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->id }}">{{ $producto->nombre }} (Stock actual: {{ $producto->stock }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('producto_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="referencia_documento" :value="__('Referencia del Documento')" />
                            <x-text-input id="referencia_documento" type="text" name="referencia_documento" :value="old('referencia_documento')" class="block mt-1 w-full" />
                            <x-input-error :messages="$errors->get('referencia_documento')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Registrar Movimiento') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>