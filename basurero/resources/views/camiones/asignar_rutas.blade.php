<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Rutas y Horarios</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        .gradient-blue { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
        .gradient-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .card-shadow { box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
        .hover-lift:hover { transform: translateY(-3px); transition: transform 0.3s ease; }
        .dia-activo { background-color: #3b82f6; color: white; border-color: #3b82f6; }
        .dia-inactivo { background-color: #f3f4f6; color: #4b5563; border-color: #d1d5db; }
        .dia-inactivo:hover { background-color: #e5e7eb; }
        
        /* Estilos para el modal */
        .modal {
            transition: opacity 0.25s ease;
        }
        body.modal-active {
            overflow-x: hidden;
            overflow-y: visible !important;
        }
        
        /* Estilos para Toast Notifications */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .toast {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            transform: translateX(120%);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            max-width: 400px;
            min-width: 300px;
        }
        
        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        
        .toast.hiding {
            transform: translateX(120%);
            opacity: 0;
        }
        
        .toast-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .toast-error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        
        .toast-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }
        
        .toast-info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }
        
        .toast-icon {
            font-size: 1.5rem;
            margin-right: 12px;
            flex-shrink: 0;
        }
        
        .toast-content {
            flex: 1;
        }
        
        .toast-title {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 2px;
        }
        
        .toast-message {
            font-size: 0.85rem;
            opacity: 0.9;
        }
        
        .toast-close {
            background: none;
            border: none;
            color: white;
            opacity: 0.7;
            cursor: pointer;
            padding: 4px;
            margin-left: 8px;
            transition: opacity 0.2s;
        }
        
        .toast-close:hover {
            opacity: 1;
        }
        
        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 0 0 12px 12px;
            animation: toast-progress 4s linear forwards;
        }
        
        @keyframes toast-progress {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Contenedor de Toast Notifications -->
    <div id="toast-container" class="toast-container"></div>
    
    <!-- Script de Toast Notifications -->
    <script>
        function showToast(type, title, message, duration = 4000) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            const icons = {
                success: 'fas fa-check-circle',
                error: 'fas fa-exclamation-circle',
                warning: 'fas fa-exclamation-triangle',
                info: 'fas fa-info-circle'
            };
            
            toast.innerHTML = `
                <i class="${icons[type]} toast-icon"></i>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.classList.add('hiding'); setTimeout(() => this.parentElement.remove(), 400);">
                    <i class="fas fa-times"></i>
                </button>
                <div class="toast-progress"></div>
            `;
            
            container.appendChild(toast);
            
            // Trigger animation
            setTimeout(() => toast.classList.add('show'), 10);
            
            // Auto remove
            setTimeout(() => {
                toast.classList.add('hiding');
                setTimeout(() => toast.remove(), 400);
            }, duration);
        }
    </script>

    <!-- Barra de navegación -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('camiones.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-truck text-blue-600 text-xl mr-2"></i>
                        <span class="font-bold text-gray-800">Asignar Rutas y Horarios</span>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Mensajes -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg animate-pulse">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-green-800 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-red-800 font-medium">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Información del camión -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-l-4 border-blue-500 p-6 rounded-r-xl mb-8">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center">
                        <div class="bg-blue-600 p-3 rounded-lg mr-4">
                            <i class="fas fa-truck text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">{{ $camion->placa }}</h3>
                            <div class="flex items-center flex-wrap gap-3 mt-1">
                                <span class="text-gray-600">
                                    <i class="fas fa-hashtag mr-1"></i>{{ $camion->codigo }}
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full 
                                      {{ $camion->estado == 'activo' ? 'bg-green-100 text-green-800' : 
                                         ($camion->estado == 'mantenimiento' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    <i class="fas fa-circle mr-1 text-xs"></i>
                                    {{ ucfirst($camion->estado) }}
                                </span>
                                {{-- Conductor asignado --}}
                                @if($camion->conductorAsignado)
                                    <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                        <i class="fas fa-user mr-1"></i>{{ $camion->conductorAsignado->name }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-500">
                                        <i class="fas fa-user-slash mr-1"></i>Sin conductor
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Botón Ver Horario Resumen --}}
                    <button type="button" onclick="abrirModalResumenHorario()"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow transition-all duration-200">
                        <i class="fas fa-clock mr-2"></i>
                        Ver Horario del Camión
                    </button>
                </div>
            </div>

            <!-- ===== MODAL RESUMEN HORARIO DEL CAMIÓN ===== -->
            <div id="modal-resumen-horario" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-2/5 shadow-2xl rounded-xl bg-white">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">
                            <i class="fas fa-clock text-indigo-600 mr-2"></i>
                            Horario del Camión — {{ $camion->placa }}
                        </h3>
                        <button onclick="cerrarModalResumenHorario()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    @if($camion->conductorAsignado)
                    <div class="mb-3 p-2 bg-purple-50 rounded-lg text-sm text-purple-800">
                        <i class="fas fa-user mr-1"></i>
                        <strong>Conductor:</strong> {{ $camion->conductorAsignado->name }}
                        <span class="text-xs text-purple-600 ml-2">{{ $camion->conductorAsignado->email }}</span>
                    </div>
                    @endif

                    <div id="resumen-horario-contenido" class="space-y-3 max-h-96 overflow-y-auto">
                        @forelse($asignadasConHorario as $rutaId => $asignacion)
                            @php
                                $rObj = \App\Models\Ruta::find($rutaId);
                            @endphp
                            <div class="border border-gray-200 rounded-lg p-3">
                                <p class="text-sm font-semibold text-gray-800">
                                    <i class="fas fa-route text-blue-500 mr-1"></i>
                                    {{ $rObj?->nombre ?? 'Ruta #'.$rutaId }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    General: {{ $asignacion['hora_inicio'] }} – {{ $asignacion['hora_fin'] }}
                                </p>
                                @if(!empty($asignacion['horarios_por_dia']))
                                    <ul class="mt-2 space-y-1">
                                        @foreach($asignacion['horarios_por_dia'] as $dia => $h)
                                            @if($h['activo'])
                                            <li class="text-xs text-gray-700">
                                                <span class="font-medium capitalize">{{ $dia }}:</span>
                                                {{ $h['hora_inicio'] }} – {{ $h['hora_fin'] }}
                                            </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-xs text-gray-400 mt-1 italic">Sin horarios por día configurados</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">No hay rutas asignadas con horario.</p>
                        @endforelse
                    </div>

                    <div class="mt-4 text-right">
                        <button onclick="cerrarModalResumenHorario()"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
            <!-- ================================================- -->

            <!-- Formulario de asignación -->
            <div class="bg-white shadow-lg rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Configurar Rutas y Horarios</h3>
                            <p class="text-sm text-gray-600">Selecciona las rutas y configura horarios personalizados</p>
                        </div>
                        <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">
                            <i class="fas fa-sync-alt mr-1"></i> Los cambios se aplican al guardar
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('camiones.guardar_rutas', $camion) }}" id="form-asignacion">
                    @csrf

                    <div class="p-6">
                        @if($rutas->count() > 0)
                            <div class="space-y-6">
                                @foreach($rutas as $ruta)
                                    @php
                                        $asignada = $asignadasConHorario[$ruta->id] ?? null;
                                        $activa = $asignada['activa'] ?? false;
                                        $horaInicio = $asignada['hora_inicio'] ?? '08:00';
                                        $horaFin = $asignada['hora_fin'] ?? '17:00';
                                        $diasSemana = $asignada['dias_semana'] ?? [];
                                        $pivotId = $asignada['pivot_id'] ?? null;
                                    @endphp

                                    <div class="border border-gray-200 rounded-lg p-5 hover:border-blue-300 transition-colors {{ $activa ? 'bg-white' : 'bg-gray-50' }}" id="ruta-card-{{ $ruta->id }}">
                                        <input type="hidden" name="rutas[{{ $ruta->id }}][pivot_id]" value="{{ $pivotId }}" class="pivot-id-field" data-ruta-id="{{ $ruta->id }}">
                                        
                                        <div class="flex items-start justify-between">
                                            <!-- Checkbox y nombre de ruta -->
                                            <div class="flex items-start space-x-3 flex-1">
                                                <div class="flex items-center h-5 mt-1">
                                                    <input type="checkbox" 
                                                           name="rutas[{{ $ruta->id }}][activa]" 
                                                           value="1"
                                                           id="ruta_{{ $ruta->id }}"
                                                           {{ $activa ? 'checked' : '' }}
                                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded toggle-ruta"
                                                           data-ruta-id="{{ $ruta->id }}">
                                                </div>
                                                <div class="flex-1">
                                                    <label for="ruta_{{ $ruta->id }}" 
                                                           class="font-medium {{ $activa ? 'text-gray-900' : 'text-gray-500' }} text-lg cursor-pointer">
                                                        {{ $ruta->nombre }}
                                                    </label>
                                                    <div class="flex items-center space-x-3 mt-1">
                                                        <span class="text-sm {{ $activa ? 'text-gray-600' : 'text-gray-400' }}">
                                                            <i class="fas fa-road mr-1"></i>Tolerancia: {{ $ruta->tolerancia_metros }}m
                                                        </span>
                                                        <span class="px-2 py-1 text-xs rounded-full 
                                                              {{ $ruta->estado == 'activa' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                            {{ $ruta->estado }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Badge de estado -->
                                            @if($activa)
                                                <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                                    <i class="fas fa-check-circle mr-1"></i>Asignada
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Panel de horarios (se muestra solo si está activa) -->
                                        <div id="horario_{{ $ruta->id }}" 
                                             class="mt-4 pt-4 border-t border-gray-200 {{ $activa ? '' : 'hidden' }}">
                                            
                                            <h4 class="text-sm font-medium text-gray-700 mb-3">
                                                <i class="far fa-clock mr-1 text-blue-500"></i>
                                                Horario General
                                            </h4>

                                            <!-- Horas generales -->
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                                        Hora de inicio
                                                    </label>
                                                    <input type="time" 
                                                           name="rutas[{{ $ruta->id }}][hora_inicio]"
                                                           value="{{ $horaInicio }}"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                                        Hora de fin
                                                    </label>
                                                    <input type="time" 
                                                           name="rutas[{{ $ruta->id }}][hora_fin]"
                                                           value="{{ $horaFin }}"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                            </div>

                                            <!-- Días de la semana -->
                                            <div class="mb-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Días de trabajo
                                                </label>
                                                <div class="flex flex-wrap gap-2">
                                                    @php
                                                        $dias = [
                                                            'lunes' => 'Lunes',
                                                            'martes' => 'Martes', 
                                                            'miercoles' => 'Miércoles',
                                                            'jueves' => 'Jueves',
                                                            'viernes' => 'Viernes',
                                                            'sabado' => 'Sábado',
                                                            'domingo' => 'Domingo'
                                                        ];
                                                    @endphp
                                                    
                                                    @foreach($dias as $key => $dia)
                                                        @php
                                                            $isChecked = in_array($key, $diasSemana);
                                                        @endphp
                                                        <label class="flex items-center px-3 py-2 border rounded-lg cursor-pointer transition-all duration-200
                                                              {{ $isChecked ? 'bg-blue-600 border-blue-600 text-white shadow-md' : 'bg-gray-100 border-gray-300 text-gray-700 hover:bg-gray-200' }}"
                                                               id="dia-label-{{ $ruta->id }}-{{ $key }}">
                                                            <input type="checkbox" 
                                                                   name="rutas[{{ $ruta->id }}][dias_semana][]"
                                                                   value="{{ $key }}"
                                                                   {{ $isChecked ? 'checked' : '' }}
                                                                   class="hidden dia-checkbox"
                                                                   data-ruta="{{ $ruta->id }}"
                                                                   data-dia="{{ $key }}">
                                                            <span class="text-sm font-medium">{{ $dia }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                <p class="text-xs text-gray-500 mt-2">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Selecciona los días en que esta ruta estará activa
                                                </p>
                                            </div>

                                            <!-- Botón para configurar horarios por día -->
                                            <div class="mt-4">
                                                @if($pivotId)
                                                <button type="button" 
                                                        onclick="abrirModalHorarios({{ $ruta->id }}, '{{ $ruta->nombre }}', '{{ $horaInicio }}', '{{ $horaFin }}', '{{ $pivotId }}')"
                                                        class="w-full bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 
                                                               text-white font-medium py-2 px-4 rounded-lg transition-all duration-300 
                                                               flex items-center justify-center shadow-md hover:shadow-lg">
                                                    <i class="fas fa-calendar-alt mr-2"></i>
                                                    Configurar Horarios Específicos por Día
                                                </button>
                                                <p class="text-xs text-gray-500 mt-2 text-center">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Define horarios diferentes para cada día de la semana
                                                </p>
                                                @else
                                                <div class="w-full bg-gradient-to-r from-gray-200 to-gray-300 
                                                            text-gray-600 font-medium py-2 px-4 rounded-lg 
                                                            flex items-center justify-center cursor-not-allowed opacity-75">
                                                    <i class="fas fa-calendar-alt mr-2"></i>
                                                    Configurar Horarios Específicos por Día
                                                </div>
                                                <div class="mt-3 p-3 bg-amber-50 rounded-lg border border-amber-200">
                                                    <div class="flex items-start">
                                                        <i class="fas fa-lightbulb text-amber-500 mt-0.5 mr-2"></i>
                                                        <div>
                                                            <p class="text-xs text-amber-700 font-medium">¿Cómo configurar horarios por día?</p>
                                                            <ol class="text-xs text-amber-600 mt-1 list-decimal list-inside space-y-1">
                                                                <li>Primero configura el <strong>Horario General</strong> arriba</li>
                                                                <li>Selecciona los <strong>Días de trabajo</strong></li>
                                                                <li>Haz clic en <strong>"Actualizar Asignaciones"</strong> (botón verde abajo)</li>
                                                                <li>¡Listo! Podrás personalizar horarios por día</li>
                                                            </ol>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>

                                            <!-- Previsualización del horario -->
                                            <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-100">
                                                <div class="flex items-start">
                                                    <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                                                    <div>
                                                        <span class="text-xs text-blue-700 font-medium">Horario actual:</span>
                                                        <span id="preview_{{ $ruta->id }}" class="text-xs text-blue-600 font-semibold block mt-1">
                                                            {{ $horaInicio }} - {{ $horaFin }} 
                                                            @if(count($diasSemana) > 0)
                                                                ({{ implode(', ', array_map(function($d) use ($dias) { 
                                                                    return substr($dias[$d] ?? $d, 0, 3); 
                                                                }, $diasSemana)) }})
                                                            @else
                                                                <span class="text-yellow-600">(Sin días seleccionados)</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="bg-yellow-50 rounded-lg p-6">
                                    <i class="fas fa-route text-yellow-400 text-4xl mb-3"></i>
                                    <h3 class="text-lg font-medium text-gray-700">No hay rutas activas disponibles</h3>
                                    <p class="text-sm text-gray-500 mt-1">Crea rutas activas primero para poder asignarlas a camiones</p>
                                    <a href="{{ route('rutas.create') }}" 
                                       class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white 
                                              rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>
                                        Crear nueva ruta
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Botones de acción -->
                    @if($rutas->count() > 0)
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-600">
                                <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                                Los cambios se aplicarán al hacer clic en "Actualizar Asignaciones"
                            </div>
                            <div class="flex space-x-3">
                                <a href="{{ route('camiones.index') }}" 
                                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 
                                          hover:bg-gray-50 transition-colors font-medium">
                                    Cancelar
                                </a>
                                <button type="submit" 
                                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 
                                               text-white rounded-lg hover:from-blue-700 hover:to-blue-800 
                                               transition-all duration-300 font-medium shadow-lg hover:shadow-xl
                                               flex items-center">
                                    <i class="fas fa-sync-alt mr-2"></i>
                                    ACTUALIZAR ASIGNACIONES
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </form>
            </div>

            <!-- Resumen de asignaciones actuales -->
            @if($rutas->count() > 0)
            <div class="mt-6 bg-white rounded-lg shadow p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                    <i class="fas fa-list-ul mr-2 text-blue-500"></i>
                    Resumen de asignaciones:
                </h4>
                <div id="resumen-asignaciones" class="text-sm text-gray-600">
                    Cargando...
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- ========== MODAL PARA CONFIGURAR HORARIOS POR DÍA ========== -->
    <div id="modal-horarios" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 modal">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-2xl rounded-xl bg-white">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
                    Configurar Horarios por Día
                </h3>
                <button onclick="cerrarModalHorarios()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <div class="mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-800 mb-2" id="modal-ruta-nombre"></h4>
                    <p class="text-sm text-gray-600">
                        Configura horarios específicos para cada día de la semana. 
                        Si no configuras un horario específico, se usará el horario general.
                    </p>
                </div>
            </div>
            
            <div id="modal-horarios-contenido" class="mb-6">
                <!-- Contenido cargado dinámicamente -->
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-blue-500 text-3xl"></i>
                    <p class="text-gray-600 mt-2">Cargando...</p>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 border-t pt-4">
                <button onclick="cerrarModalHorarios()" 
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Cancelar
                </button>
                <button id="btn-guardar-horarios" 
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 font-medium shadow-md hover:shadow-lg flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Guardar Horarios por Día
                </button>
            </div>
        </div>
    </div>
    <!-- ======================================================== -->

    <script>
        // ========== VARIABLES GLOBALES ==========
        let rutaIdActual = null;
        let horaInicioGeneral = '08:00';
        let horaFinGeneral = '17:00';
        
        const diasMap = {
            'lunes': 'Lunes',
            'martes': 'Martes',
            'miercoles': 'Miércoles',
            'jueves': 'Jueves',
            'viernes': 'Viernes',
            'sabado': 'Sábado',
            'domingo': 'Domingo'
        };

        const diasAbreviados = {
            'lunes': 'Lun',
            'martes': 'Mar',
            'miercoles': 'Mié',
            'jueves': 'Jue',
            'viernes': 'Vie',
            'sabado': 'Sáb',
            'domingo': 'Dom'
        };

        const horariosPorDiaIniciales = @json(
            collect($asignadasConHorario)->mapWithKeys(function ($asignacion, $rutaId) {
                return [$rutaId => $asignacion['horarios_por_dia'] ?? []];
            })
        );

        // ========== FUNCIONES GLOBALES ==========
        window.actualizarPreview = function(rutaId) {
            const horaInicio = document.querySelector(`[name="rutas[${rutaId}][hora_inicio]"]`);
            const horaFin = document.querySelector(`[name="rutas[${rutaId}][hora_fin]"]`);
            const preview = document.getElementById(`preview_${rutaId}`);
            
            if (horaInicio && horaFin && preview) {
                const inicio = horaInicio.value;
                const fin = horaFin.value;
                
                const diasSeleccionados = [];
                document.querySelectorAll(`[name="rutas[${rutaId}][dias_semana][]"]:checked`).forEach(checkbox => {
                    diasSeleccionados.push(diasAbreviados[checkbox.value] || checkbox.value);
                });
                
                if (diasSeleccionados.length > 0) {
                    preview.innerHTML = `${inicio} - ${fin} (${diasSeleccionados.join(', ')})`;
                    preview.classList.remove('text-yellow-600');
                    preview.classList.add('text-blue-600');
                } else {
                    preview.innerHTML = `${inicio} - ${fin} <span class="text-yellow-600">(Sin días seleccionados)</span>`;
                }
            }
        };

        window.actualizarResumen = function() {
            const resumenDiv = document.getElementById('resumen-asignaciones');
            if (!resumenDiv) return;
            
            let asignadas = 0;
            let totalDias = 0;
            let rutasConHorario = 0;
            
            document.querySelectorAll('.toggle-ruta').forEach(checkbox => {
                if (checkbox.checked) {
                    const rutaId = checkbox.dataset.rutaId;
                    const diasCount = document.querySelectorAll(`[name="rutas[${rutaId}][dias_semana][]"]:checked`).length;
                    
                    asignadas++;
                    totalDias += diasCount;
                    if (diasCount > 0) rutasConHorario++;
                }
            });
            
            let html = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <span class="text-xs text-blue-600 font-semibold">RUTAS ASIGNADAS</span>
                        <p class="text-2xl font-bold text-blue-700">${asignadas}</p>
                    </div>
                    <div class="bg-green-50 p-3 rounded-lg">
                        <span class="text-xs text-green-600 font-semibold">CON HORARIO</span>
                        <p class="text-2xl font-bold text-green-700">${rutasConHorario}</p>
                    </div>
                    <div class="bg-purple-50 p-3 rounded-lg">
                        <span class="text-xs text-purple-600 font-semibold">DÍAS ASIGNADOS</span>
                        <p class="text-2xl font-bold text-purple-700">${totalDias}</p>
                    </div>
                </div>
            `;
            
            resumenDiv.innerHTML = html;
        };

        window.abrirModalHorarios = function(rutaId, rutaNombre, horaInicio, horaFin, pivotId) {
            rutaIdActual = rutaId;
            horaInicioGeneral = horaInicio;
            horaFinGeneral = horaFin;
            
            // Guardar datos en el modal
            const modal = document.getElementById('modal-horarios');
            modal.setAttribute('data-pivot-id', pivotId);
            modal.setAttribute('data-ruta-id', rutaId);
            
            document.getElementById('modal-ruta-nombre').innerHTML = `
                <i class="fas fa-route mr-2 text-blue-600"></i>
                Ruta: ${rutaNombre}
                <span class="ml-2 text-sm font-normal text-gray-600">(ID: ${pivotId})</span>
            `;
            
            window.generarTablaHorarios(rutaId);
            
            modal.classList.remove('hidden');
            document.body.classList.add('modal-active');
        };

        window.cerrarModalHorarios = function() {
            document.getElementById('modal-horarios').classList.add('hidden');
            document.body.classList.remove('modal-active');
            rutaIdActual = null;
        };

        window.abrirModalResumenHorario = function() {
            document.getElementById('modal-resumen-horario').classList.remove('hidden');
        };

        window.cerrarModalResumenHorario = function() {
            document.getElementById('modal-resumen-horario').classList.add('hidden');
        };

        window.generarTablaHorarios = function(rutaId) {
            const diasSeleccionados = [];
            document.querySelectorAll(`[name="rutas[${rutaId}][dias_semana][]"]:checked`).forEach(checkbox => {
                diasSeleccionados.push(checkbox.value);
            });

            const horariosExistentes = horariosPorDiaIniciales[rutaId] || {};
            
            let html = `
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Día</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora Inicio</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora Fin</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
            `;
            
            Object.entries(diasMap).forEach(([key, nombre]) => {
                const horarioExistente = horariosExistentes[key] || null;
                const activo = horarioExistente ? Boolean(horarioExistente.activo) : diasSeleccionados.includes(key);
                const horaInicio = horarioExistente?.hora_inicio || horaInicioGeneral;
                const horaFin = horarioExistente?.hora_fin || horaFinGeneral;

                html += `<tr class="hover:bg-gray-50">`;
                html += `<td class="px-4 py-3 text-sm font-medium text-gray-900">${nombre}</td>`;
                html += `<td class="px-4 py-3">
                            <input type="checkbox" 
                                   id="dia_${rutaId}_${key}" 
                                   class="dia-horario-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                                   ${activo ? 'checked' : ''}
                                   data-dia="${key}">
                         </td>`;
                html += `<td class="px-4 py-3">
                            <input type="time" 
                                   id="inicio_${rutaId}_${key}" 
                            value="${horaInicio}"
                                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-32 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   ${!activo ? 'disabled' : ''}>
                         </td>`;
                html += `<td class="px-4 py-3">
                            <input type="time" 
                                   id="fin_${rutaId}_${key}" 
                            value="${horaFin}"
                                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-32 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   ${!activo ? 'disabled' : ''}>
                         </td>`;
                html += `</tr>`;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                        <div>
                            <p class="text-xs text-blue-700">
                                <strong>Nota:</strong> Los horarios configurados aquí tendrán prioridad sobre el horario general.
                            </p>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('modal-horarios-contenido').innerHTML = html;
            
            // Agregar eventos a los checkboxes del modal
            document.querySelectorAll('.dia-horario-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const row = this.closest('tr');
                    const inicioInput = row.querySelector('input[type="time"]:first-of-type');
                    const finInput = row.querySelector('input[type="time"]:last-of-type');
                    
                    inicioInput.disabled = !this.checked;
                    finInput.disabled = !this.checked;
                    
                    if (this.checked) {
                        inicioInput.value = horaInicioGeneral;
                        finInput.value = horaFinGeneral;
                    }
                });
            });
        };

        // ========== DOCUMENT READY ==========
        document.addEventListener('DOMContentLoaded', function() {
            
            // Inicializar checkboxes de días
            function inicializarCheckboxes() {
                document.querySelectorAll('.dia-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const rutaId = this.dataset.ruta;
                        const dia = this.dataset.dia;
                        const label = document.getElementById(`dia-label-${rutaId}-${dia}`);
                        
                        if (this.checked) {
                            label.classList.remove('bg-gray-100', 'border-gray-300', 'text-gray-700', 'hover:bg-gray-200');
                            label.classList.add('bg-blue-600', 'border-blue-600', 'text-white', 'shadow-md');
                        } else {
                            label.classList.remove('bg-blue-600', 'border-blue-600', 'text-white', 'shadow-md');
                            label.classList.add('bg-gray-100', 'border-gray-300', 'text-gray-700', 'hover:bg-gray-200');
                        }
                        
                        window.actualizarPreview(rutaId);
                        window.actualizarResumen();
                    });
                });
            }

            // Toggle para mostrar/ocultar horarios
            function inicializarToggles() {
                document.querySelectorAll('.toggle-ruta').forEach(checkbox => {
                    const rutaId = checkbox.dataset.rutaId;
                    const horarioDiv = document.getElementById(`horario_${rutaId}`);
                    const cardDiv = document.getElementById(`ruta-card-${rutaId}`);
                    
                    checkbox.addEventListener('change', function() {
                        const label = document.querySelector(`label[for="ruta_${rutaId}"]`);
                        const statusBadge = this.closest('.flex')?.querySelector('.bg-green-100');
                        
                        if (this.checked) {
                            horarioDiv?.classList.remove('hidden');
                            cardDiv?.classList.remove('bg-gray-50');
                            cardDiv?.classList.add('bg-white');
                            if (label) label.classList.remove('text-gray-500');
                            if (label) label.classList.add('text-gray-900');
                            if (statusBadge) statusBadge.classList.remove('hidden');
                        } else {
                            horarioDiv?.classList.add('hidden');
                            cardDiv?.classList.remove('bg-white');
                            cardDiv?.classList.add('bg-gray-50');
                            if (label) label.classList.remove('text-gray-900');
                            if (label) label.classList.add('text-gray-500');
                            if (statusBadge) statusBadge.classList.add('hidden');
                        }
                        
                        window.actualizarResumen();
                    });
                });
            }

            // Escuchar cambios en horas
            document.querySelectorAll('input[type="time"]').forEach(input => {
                input.addEventListener('change', function() {
                    const match = this.name.match(/rutas\[(\d+)\]/);
                    if (match) {
                        window.actualizarPreview(match[1]);
                    }
                });
            });

            // Inicializar todo
            inicializarCheckboxes();
            inicializarToggles();
            window.actualizarResumen();
            
            // Inicializar previsualizaciones
            @foreach($rutas as $ruta)
                @if(isset($asignadasConHorario[$ruta->id]))
                    window.actualizarPreview({{ $ruta->id }});
                @endif
            @endforeach
            
            // Actualizar resumen con cambios
            document.querySelectorAll('.toggle-ruta, .dia-checkbox, input[type="time"]').forEach(el => {
                el.addEventListener('change', function() {
                    setTimeout(window.actualizarResumen, 50);
                });
            });
        });

        // ========== GUARDAR HORARIOS ==========
        document.addEventListener('DOMContentLoaded', function() {
            const btnGuardar = document.getElementById('btn-guardar-horarios');
            if (btnGuardar) {
                btnGuardar.addEventListener('click', function() {
                    const modal = document.getElementById('modal-horarios');
                    const rutaId = modal.getAttribute('data-ruta-id');
                    const pivotId = modal.getAttribute('data-pivot-id');
                    
                    if (!rutaId || !pivotId || pivotId === 'null' || pivotId === '') {
                        showToast('warning', '¡Primero guarda la ruta!', 'Para configurar horarios específicos por día, primero debes guardar la ruta con el botón "Actualizar Asignaciones".', 5000);
                        return;
                    }
                    
                    // Mostrar loading
                    const btn = this;
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Guardando...';
                    btn.disabled = true;
                    
                    // Recolectar horarios
                    const horarios = {};
                    
                    Object.keys(diasMap).forEach(key => {
                        const checkbox = document.getElementById(`dia_${rutaId}_${key}`);
                        if (checkbox) {
                            if (checkbox.checked) {
                                const inicioInput = document.getElementById(`inicio_${rutaId}_${key}`);
                                const finInput = document.getElementById(`fin_${rutaId}_${key}`);
                                
                                horarios[key] = {
                                    activo: true,
                                    hora_inicio: inicioInput ? inicioInput.value : horaInicioGeneral,
                                    hora_fin: finInput ? finInput.value : horaFinGeneral
                                };
                                
                                // Actualizar checkbox principal
                                const checkboxPrincipal = document.querySelector(`[name="rutas[${rutaId}][dias_semana][]"][value="${key}"]`);
                                if (checkboxPrincipal) {
                                    checkboxPrincipal.checked = true;
                                    const event = new Event('change', { bubbles: true });
                                    checkboxPrincipal.dispatchEvent(event);
                                }
                            } else {
                                horarios[key] = { activo: false };
                                
                                // Desmarcar checkbox principal
                                const checkboxPrincipal = document.querySelector(`[name="rutas[${rutaId}][dias_semana][]"][value="${key}"]`);
                                if (checkboxPrincipal) {
                                    checkboxPrincipal.checked = false;
                                    const event = new Event('change', { bubbles: true });
                                    checkboxPrincipal.dispatchEvent(event);
                                }
                            }
                        }
                    });
                    
                    // Enviar a la base de datos
                    fetch(`/camiones/{{ $camion->id }}/guardar-horarios-dia`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            pivot_id: pivotId,
                            horarios: horarios
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Actualizar horarios en memoria para que el modal los muestre la próxima vez
                            horariosPorDiaIniciales[rutaId] = horariosPorDiaIniciales[rutaId] || {};
                            Object.keys(horarios).forEach(dia => {
                                if (horarios[dia].activo) {
                                    horariosPorDiaIniciales[rutaId][dia] = {
                                        dia: dia,
                                        hora_inicio: horarios[dia].hora_inicio,
                                        hora_fin: horarios[dia].hora_fin,
                                        activo: true
                                    };
                                } else {
                                    delete horariosPorDiaIniciales[rutaId][dia];
                                }
                            });

                            window.actualizarPreview(rutaId);
                            window.actualizarResumen();
                            window.cerrarModalHorarios();
                            showToast('success', '¡Horarios guardados!', 'Los horarios específicos por día se han configurado correctamente.');
                        } else {
                            showToast('error', 'Error al guardar', data.message || 'No se pudieron guardar los horarios. Intenta nuevamente.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('error', 'Error de conexión', 'No se pudo conectar con el servidor. Verifica tu conexión e intenta nuevamente.');
                    })
                    .finally(() => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    });
                });
            }
        });

        // ========== CERRAR MODALES AL HACER CLICK FUERA ==========
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modal-horarios');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        window.cerrarModalHorarios();
                    }
                });
            }
            const modalResumen = document.getElementById('modal-resumen-horario');
            if (modalResumen) {
                modalResumen.addEventListener('click', function(e) {
                    if (e.target === this) {
                        window.cerrarModalResumenHorario();
                    }
                });
            }
        });
    </script>

    <style>
        /* Animaciones */
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        /* Transiciones suaves */
        .transition-all {
            transition: all 0.3s ease;
        }
        
        /* Estilo para el botón de actualizar */
        button[type="submit"]:hover {
            transform: translateY(-2px);
        }
        
        /* Mejora visual para las tarjetas */
        .bg-gradient-to-r {
            background-image: linear-gradient(to right, var(--tw-gradient-from), var(--tw-gradient-to));
        }
        
        /* Estilos para el modal */
        .modal {
            transition: opacity 0.25s ease;
        }
        
        #modal-horarios {
            backdrop-filter: blur(4px);
        }
        
        /* Estilos para la tabla de horarios */
        table {
            border-collapse: collapse;
            width: 100%;
        }
        
        th {
            background-color: #f9fafb;
            font-weight: 600;
        }
        
        tr:hover {
            background-color: #f9fafb;
        }
        
        /* Estilo para inputs deshabilitados */
        input:disabled {
            background-color: #f3f4f6;
            opacity: 0.7;
            cursor: not-allowed;
        }
    </style>
</body>
</html>