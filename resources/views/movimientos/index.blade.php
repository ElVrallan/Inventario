<x-app-layout>
    <x-slot name="header">
    @push('styles')
    <style>
        th a {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: inherit;
        }
        th a:hover {
            text-decoration: underline;
        }
        .form-select {
            padding: 0.375rem 1.75rem 0.375rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background-color: white;
        }
    </style>
    @endpush
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
                            @if($filtroActivo)
                        <div class="mb-4 flex items-center justify-between bg-blue-50 p-3 rounded-lg">
                            <span class="text-blue-700">Mostrando: {{ $filtroActivo }}</span>
                            <a href="{{ route('movimientos.index') }}" class="text-blue-600 hover:text-blue-800">
                                × Limpiar filtro
                            </a>
                        </div>
                    @endif
                    <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'fecha', 'direction' => ($sort_field === 'fecha' && $sort_direction === 'desc') ? 'asc' : 'desc']) }}" 
                                           class="hover:text-gray-700">
                                            Fecha
                                            @if(isset($sort_field) && $sort_field === 'fecha')
                                                <span class="ml-1">{{ $sort_direction === 'desc' ? '↓' : '↑' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <span class="text-gray-700">Tipo</span>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'cantidad', 'direction' => ($sort_field === 'cantidad' && $sort_direction === 'desc') ? 'asc' : 'desc']) }}" class="hover:text-gray-700">
                                            Cantidad
                                            @if(isset($sort_field) && $sort_field === 'cantidad')
                                                <span class="ml-1">{{ $sort_direction === 'desc' ? '↓' : '↑' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proveedor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referencia</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($movimientos as $movimiento)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $movimiento->fecha->format('d/m/Y H:i:s') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('movimientos.index', ['tipo' => $movimiento->tipo]) }}" class="hover:no-underline">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $movimiento->tipo === 'entrada' ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                                    {{ ucfirst($movimiento->tipo) }}
                                                </span>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $movimiento->cantidad }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($movimiento->producto && $movimiento->producto->categoria)
                                                <a href="{{ route('movimientos.index', ['categoria_id' => $movimiento->producto->categoria->id]) }}" class="hover:text-blue-600">
                                                    {{ $movimiento->producto->categoria->nombre }}
                                                </a>
                                            @else
                                                <span class="text-gray-400">Sin categoría</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('movimientos.index', ['producto_id' => $movimiento->producto_id]) }}" class="hover:text-blue-600">
                                                {{-- Prefer stored nombre (audit), fallback to relationship --}}
                                                @if(!empty($movimiento->producto_nombre))
                                                    {{ $movimiento->producto_nombre }}
                                                @elseif($movimiento->producto)
                                                    {{ $movimiento->producto->nombre }}
                                                @else
                                                    <span class="text-red-500">Producto eliminado</span>
                                                @endif
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($movimiento->producto && $movimiento->producto->proveedor)
                                                <a href="{{ route('movimientos.index', ['proveedor_id' => $movimiento->producto->proveedor->id]) }}" class="hover:text-blue-600">
                                                    {{ $movimiento->producto->proveedor->nombre ?? $movimiento->producto->proveedor->razon_social ?? 'Proveedor' }}
                                                </a>
                                            @else
                                                <span class="text-gray-400">Sin proveedor</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('movimientos.index', ['user_id' => $movimiento->user_id]) }}" class="hover:text-blue-600">
                                                @if($movimiento->user)
                                                    {{ $movimiento->user->name }}
                                                @else
                                                    <span class="text-red-500">Usuario eliminado</span>
                                                @endif
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $movimiento->referencia_documento }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-700">Mostrar</span>
                            <select class="form-select text-sm" onchange="window.location.href=this.value">
                                @foreach([10, 25, 50, 100] as $size)
                                    <option value="{{ request()->fullUrlWithQuery(['per_page' => $size]) }}"
                                            {{ request()->input('per_page', 10) == $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-sm text-gray-700">resultados por página</span>
                        </div>

                        <div class="flex-grow">
                            {{ $movimientos->links() }}
                        </div>
                    </div>

                    @push('scripts')
                    <script>
                        // Traducción de la paginación
                        document.addEventListener('DOMContentLoaded', function() {
                            const text = document.querySelector('[role="status"]');
                            if (text) {
                                const content = text.textContent;
                                if (content.includes('Showing')) {
                                    const parts = content.match(/Showing (\d+) to (\d+) of (\d+) results/);
                                    if (parts) {
                                        text.textContent = `Mostrando ${parts[1]} a ${parts[2]} de ${parts[3]} resultados`;
                                    }
                                }
                            }
                        });
                    </script>
                    @endpush
                </div>
            </div>
        </div>
    </div>
</x-app-layout>