<x-dinamico-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    📋 Historial de Recorridos
                </h2>
                <p class="text-sm text-gray-600 mt-1">Seguimiento y monitoreo de rutas realizadas</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm bg-blue-100 text-blue-800 px-3 py-1.5 rounded-lg font-medium">
                    Total: {{ $recorridos->total() }} recorridos
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Tabla de Recorridos - SIN ESTADO -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ruta / Camión</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conductor</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inicio / Fin</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duración</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Puntos</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recorridos as $recorrido)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-mono text-gray-600">#{{ $recorrido->id }}</span>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <div class="flex items-center">
                                            <i class="fas fa-route text-blue-500 text-xs mr-1.5"></i>
                                            <span class="text-sm font-medium text-gray-900">
                                                {{ $recorrido->ruta?->nombre ?? 'Sin ruta' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center mt-1">
                                            <i class="fas fa-truck text-green-600 text-xs mr-1.5"></i>
                                            <span class="text-xs text-gray-600">
                                                {{ $recorrido->camion?->placa ?? 'N/A' }}
                                                <span class="text-gray-400">({{ $recorrido->camion?->codigo ?? '' }})</span>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 bg-purple-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-purple-600 text-xs"></i>
                                        </div>
                                        <div class="ml-2">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $recorrido->conductor?->name ?? 'N/A' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $recorrido->conductor?->email ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <div class="flex items-center text-xs">
                                            <i class="far fa-play-circle text-green-500 mr-1"></i>
                                            <span class="text-gray-700">{{ $recorrido->fecha_inicio->format('d/m/Y H:i') }}</span>
                                        </div>
                                        @if($recorrido->fecha_fin)
                                        <div class="flex items-center text-xs mt-1">
                                            <i class="far fa-stop-circle text-red-500 mr-1"></i>
                                            <span class="text-gray-700">{{ $recorrido->fecha_fin->format('d/m/Y H:i') }}</span>
                                        </div>
                                        @else
                                        <div class="flex items-center text-xs mt-1">
                                            <span class="text-green-600 font-medium">🟢 En curso</span>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($recorrido->fecha_inicio && $recorrido->fecha_fin)
                                        @php
                                            $duracion = $recorrido->fecha_inicio->diff($recorrido->fecha_fin);
                                            $horas = $duracion->h + ($duracion->days * 24);
                                            $minutos = $duracion->i;
                                        @endphp
                                        <span class="text-sm text-gray-900 font-mono">
                                            {{ $horas > 0 ? $horas . 'h ' : '' }}{{ $minutos }}min
                                        </span>
                                        <span class="text-xs text-gray-500 block">
                                            {{ round($duracion->totalHours, 1) }} horas
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500 italic">En curso</span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4">
                                    @php
                                        $puntosCount = $recorrido->puntos()->count();
                                    @endphp
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ $puntosCount }} pts
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('recorridos.show.historial', $recorrido) }}" 
                                       class="inline-flex items-center px-3 py-2 bg-blue-50 hover:bg-blue-100 
                                              text-blue-700 text-sm font-medium rounded-lg transition-colors">
                                        <i class="fas fa-map-marked-alt mr-1.5"></i>
                                        Ver detalles
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="text-gray-300 mb-3">
                                            <i class="fas fa-route text-5xl"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-700">No hay recorridos registrados</h3>
                                        <p class="text-sm text-gray-500 mt-1">Los recorridos aparecerán cuando los conductores inicien rutas.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $recorridos->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-dinamico-layout>