<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Movimientos de Inventario') }}
            </h2>
            <a href="{{ route('movimientos.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Nuevo Movimiento
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referencia</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($movimientos as $movimiento)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $movimiento->fecha->format('d/m/Y H:i:s') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $movimiento->tipo === 'entrada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($movimiento->tipo) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $movimiento->cantidad }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{-- Prefer stored nombre (audit), fallback to relationship --}}
                                            @if(!empty($movimiento->producto_nombre))
                                                {{ $movimiento->producto_nombre }}
                                            @elseif($movimiento->producto)
                                                {{ $movimiento->producto->nombre }}
                                            @else
                                                <span class="text-red-500">Producto eliminado</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($movimiento->user)
                                                {{ $movimiento->user->name }}
                                            @else
                                                <span class="text-red-500">Usuario eliminado</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $movimiento->referencia_documento }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $movimientos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>