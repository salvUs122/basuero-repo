<x-dinamico-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('camiones.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                👤 Asignar Conductor a Camión: {{ $camion->placa }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Información del camión -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-l-4 border-blue-500 p-6 rounded-r-xl mb-8">
                <div class="flex items-center">
                    <div class="bg-blue-600 p-3 rounded-lg mr-4">
                        <i class="fas fa-truck text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">{{ $camion->placa }}</h3>
                        <div class="flex items-center space-x-4 mt-1">
                            <span class="text-gray-600">
                                <i class="fas fa-hashtag mr-1"></i>{{ $camion->codigo }}
                            </span>
                            <span class="px-2 py-1 text-xs rounded-full 
                                  {{ $camion->estado == 'activo' ? 'bg-green-100 text-green-800' : 
                                     ($camion->estado == 'mantenimiento' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($camion->estado) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Asignar Conductor</h3>
                    <p class="text-sm text-gray-600">Selecciona el conductor que manejará este camión</p>
                </div>

                <form method="POST" action="{{ route('camiones.asignar_conductor.update', $camion) }}" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Selector de conductor -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user mr-1 text-blue-500"></i>
                                Seleccionar Conductor
                            </label>
                            <select name="conductor_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Sin conductor asignado --</option>
                                @foreach($conductores as $conductor)
                                    <option value="{{ $conductor->id }}" 
                                        {{ $camion->conductor_id == $conductor->id ? 'selected' : '' }}>
                                        {{ $conductor->name }} - {{ $conductor->email }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                El conductor solo podrá ver este camión y sus rutas asignadas.
                            </p>
                        </div>

                        <!-- Información adicional -->
                        @if($camion->conductor_id)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                                <div>
                                    <p class="text-sm text-green-800 font-medium">
                                        Conductor actualmente asignado
                                    </p>
                                    @php
                                        $conductorActual = $conductores->firstWhere('id', $camion->conductor_id);
                                    @endphp
                                    @if($conductorActual)
                                        <p class="text-sm text-green-700">
                                            {{ $conductorActual->name }} ({{ $conductorActual->email }})
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Botones -->
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <a href="{{ route('camiones.index') }}" 
                               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 font-medium shadow-lg hover:shadow-xl">
                                <i class="fas fa-save mr-2"></i>
                                Asignar Conductor
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Rutas del camión -->
            @if($camion->rutas->count() > 0)
            <div class="mt-6 bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Rutas Asignadas a este Camión</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($camion->rutas as $ruta)
                            <div class="border rounded-lg p-4 hover:bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $ruta->nombre }}</h4>
                                        <div class="flex items-center space-x-4 mt-2 text-sm">
                                            <span class="text-gray-600">
                                                <i class="far fa-clock mr-1"></i>
                                                {{ $ruta->pivot->hora_inicio }} - {{ $ruta->pivot->hora_fin }}
                                            </span>
                                            <span class="text-gray-600">
                                                <i class="fas fa-calendar mr-1"></i>
                                                Días: 
                                                @php
                                                    $dias = json_decode($ruta->pivot->dias_semana ?? '[]', true);
                                                    $diasNombres = ['lunes'=>'L','martes'=>'M','miercoles'=>'X','jueves'=>'J','viernes'=>'V','sabado'=>'S','domingo'=>'D'];
                                                @endphp
                                                @foreach($dias as $dia)
                                                    <span class="inline-block w-5 h-5 text-center bg-blue-100 text-blue-800 rounded text-xs leading-5">
                                                        {{ $diasNombres[$dia] ?? substr($dia,0,1) }}
                                                    </span>
                                                @endforeach
                                            </span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $ruta->pivot->activa ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $ruta->pivot->activa ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-dinamico-layout>