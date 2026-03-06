<div class="space-y-6">
    <!-- Información básica -->
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-blue-50 p-4 rounded-xl">
            <div class="text-sm text-blue-600 font-medium">Placa</div>
            <div class="text-xl font-bold text-gray-800 mt-1">{{ $camion->placa }}</div>
        </div>
        <div class="bg-green-50 p-4 rounded-xl">
            <div class="text-sm text-green-600 font-medium">Código</div>
            <div class="text-xl font-bold text-gray-800 mt-1">{{ $camion->codigo }}</div>
        </div>
    </div>

    <!-- Estado -->
    <div class="bg-gray-50 p-4 rounded-xl">
        <div class="text-sm text-gray-600 font-medium">Estado</div>
        <div class="mt-2">
            <span class="px-3 py-1 text-sm rounded-full 
                  {{ $camion->estado == 'activo' ? 'bg-green-100 text-green-800' : 
                     ($camion->estado == 'mantenimiento' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                <i class="fas fa-circle mr-1 text-xs"></i>
                {{ ucfirst($camion->estado) }}
            </span>
        </div>
    </div>

    <!-- Rutas asignadas -->
    <div class="bg-gray-50 p-4 rounded-xl">
        <div class="text-sm text-gray-600 font-medium mb-3">Rutas y Horarios Asignados</div>
        
        @if($camion->rutas->count() > 0)
            <div class="space-y-3">
                @foreach($camion->rutas as $ruta)
                    <div class="bg-white p-4 rounded-lg border">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-medium text-gray-800 flex items-center">
                                    <i class="fas fa-route text-blue-500 mr-2"></i>
                                    {{ $ruta->nombre }}
                                </div>
                                <div class="text-sm text-gray-600 mt-1">
                                    Estado: 
                                    <span class="{{ $ruta->pivot->activa ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $ruta->pivot->activa ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </div>
                            </div>
                            <span class="text-xs px-2 py-1 rounded 
                                  {{ $ruta->estado == 'activa' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $ruta->estado }}
                            </span>
                        </div>
                        
                        @if($ruta->pivot->hora_inicio)
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <div class="text-xs text-gray-500">Horario</div>
                                        <div class="text-sm font-medium text-gray-700">
                                            <i class="far fa-clock mr-1"></i>
                                            {{ $ruta->pivot->hora_inicio }} - {{ $ruta->pivot->hora_fin }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500">Días</div>
                                        <div class="text-sm font-medium text-gray-700">
                                            @php
                                                $dias = json_decode($ruta->pivot->dias_semana ?? '[]', true);
                                                $diasNombres = [
                                                    'lunes' => 'L', 'martes' => 'M', 'miercoles' => 'X',
                                                    'jueves' => 'J', 'viernes' => 'V', 'sabado' => 'S', 
                                                    'domingo' => 'D'
                                                ];
                                            @endphp
                                            @if(count($dias) > 0)
                                                @foreach($dias as $dia)
                                                    <span class="inline-block w-6 h-6 text-center bg-blue-100 
                                                          text-blue-800 rounded text-xs leading-6 mr-1">
                                                        {{ $diasNombres[$dia] ?? substr($dia, 0, 1) }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-gray-400 text-xs">Sin días específicos</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-route text-gray-300 text-3xl mb-2"></i>
                <p class="text-gray-500">No hay rutas asignadas</p>
            </div>
        @endif
    </div>

    <!-- Información adicional -->
    <div class="bg-gray-50 p-4 rounded-xl">
        <div class="text-sm text-gray-600 font-medium mb-2">Información del Sistema</div>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <div class="text-gray-500">Creado</div>
                <div class="font-medium">{{ $camion->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div>
                <div class="text-gray-500">Actualizado</div>
                <div class="font-medium">{{ $camion->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>
</div>