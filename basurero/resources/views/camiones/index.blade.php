<x-dinamico-layout>
        <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    🚛 Gestión de Camiones
                </h2>
                <p class="text-sm text-gray-600 mt-1">Administra la flota de camiones recolectores</p>
            </div>
            <div class="text-sm text-gray-600">{{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Mensajes -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
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
            
            <!-- Tarjetas de Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Camiones -->
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Camiones</p>
                            <p class="text-3xl font-bold mt-2">{{ $camiones->count() }}</p>
                        </div>
                        <div class="bg-blue-400 p-3 rounded-full">
                            <i class="fas fa-truck text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Activos -->
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Activos</p>
                            <p class="text-3xl font-bold mt-2">{{ $camiones->where('estado', 'activo')->count() }}</p>
                        </div>
                        <div class="bg-green-400 p-3 rounded-full">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Mantenimiento -->
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm font-medium">Mantenimiento</p>
                            <p class="text-3xl font-bold mt-2">{{ $camiones->where('estado', 'mantenimiento')->count() }}</p>
                        </div>
                        <div class="bg-orange-400 p-3 rounded-full">
                            <i class="fas fa-tools text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Con Rutas -->
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Con Rutas</p>
                            <p class="text-3xl font-bold mt-2">{{ $camiones->filter(fn($c) => $c->rutas->count() > 0)->count() }}</p>
                        </div>
                        <div class="bg-purple-400 p-3 rounded-full">
                            <i class="fas fa-route text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón y título -->
            <div class="mb-6 flex justify-between items-center">
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Flota de Camiones</h3>
                    <p class="text-sm text-gray-600">Gestiona toda la flota de camiones recolectores</p>
                </div>
                <a href="{{ route('camiones.create') }}" 
                   class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 
                          text-white font-medium py-2 px-4 rounded-lg transition-all duration-300 
                          flex items-center shadow-lg hover:shadow-xl">
                    <i class="fas fa-plus mr-2"></i>
                    Nuevo Camión
                </a>
            </div>

            <!-- Tabla de camiones -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Lista de Camiones</h3>
                            <p class="text-sm text-gray-600">{{ $camiones->count() }} camiones registrados</p>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    @if($camiones->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Información</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rutas Asignadas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conductores</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horarios</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($camiones as $camion)
                                <tr class="hover:bg-gray-50 transition-colors duration-200" id="camion-{{ $camion->id }}">
                                    <!-- Información del camión -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-truck text-blue-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $camion->placa }}</div>
                                                <div class="text-sm text-gray-500">{{ $camion->codigo }}</div>
                                                <div class="mt-1">
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                          {{ $camion->estado == 'activo' ? 'bg-green-100 text-green-800' : 
                                                             ($camion->estado == 'mantenimiento' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                        {{ ucfirst($camion->estado) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Rutas asignadas -->
                                    <td class="px-6 py-4">
                                        @if($camion->rutas->count() > 0)
                                            <div class="space-y-2">
                                                @foreach($camion->rutas as $ruta)
                                                    <div class="text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                                        <div class="flex justify-between items-center">
                                                            <span>
                                                                <i class="fas fa-route text-blue-500 mr-1"></i>
                                                                {{ $ruta->nombre }}
                                                            </span>
                                                            <span class="text-xs {{ $ruta->pivot->activa ? 'text-green-600' : 'text-red-600' }}">
                                                                {{ $ruta->pivot->activa ? 'Activa' : 'Inactiva' }}
                                                            </span>
                                                        </div>
                                                        @if($ruta->pivot->hora_inicio)
                                                            <div class="text-xs text-gray-500 mt-1">
                                                                <i class="far fa-clock mr-1"></i>
                                                                {{ $ruta->pivot->hora_inicio }} - {{ $ruta->pivot->hora_fin }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500 italic">Sin rutas asignadas</span>
                                        @endif
                                    </td>

                                    <!-- Conductores asignados -->
                                    <td class="px-6 py-4">
                                        @if($camion->conductorAsignado)
                                            <div class="text-sm text-gray-900 font-medium">
                                                <i class="fas fa-user text-purple-500 mr-1"></i>
                                                {{ $camion->conductorAsignado->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $camion->conductorAsignado->email }}
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500 italic">Sin conductor asignado</span>
                                        @endif
                                    </td>

                                    <!-- Horarios -->
                                    <td class="px-6 py-4">
                                        @if($camion->rutas->count() > 0)
                                            @php
                                                $detalleHorarios = $camion->rutas->map(function($ruta) use ($horariosPorPivot) {
                                                    $horarios = collect($horariosPorPivot[$ruta->pivot->id] ?? [])->map(function($h) {
                                                        return [
                                                            'dia' => $h->dia,
                                                            'hora_inicio' => $h->hora_inicio,
                                                            'hora_fin' => $h->hora_fin,
                                                        ];
                                                    })->values();

                                                    return [
                                                        'ruta' => $ruta->nombre,
                                                        'general' => [
                                                            'hora_inicio' => $ruta->pivot->hora_inicio,
                                                            'hora_fin' => $ruta->pivot->hora_fin,
                                                        ],
                                                        'por_dia' => $horarios,
                                                    ];
                                                })->values();
                                            @endphp
                                            <button type="button"
                                                    onclick='mostrarHorariosCamion(@json($detalleHorarios), @json($camion->placa))'
                                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors text-xs font-semibold">
                                                <i class="fas fa-clock mr-1"></i>
                                                Ver horarios por día
                                            </button>
                                        @else
                                            <span class="text-sm text-gray-500 italic">Sin horarios</span>
                                        @endif
                                    </td>

                                    <!-- Acciones -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('camiones.asignar_rutas', $camion) }}" 
                                               class="text-blue-600 hover:text-blue-900 inline-flex items-center px-2 py-1 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                                <i class="fas fa-route mr-1"></i> Rutas
                                            </a>
                                            <a href="{{ route('camiones.asignar_conductor', $camion) }}" 
                                            class="text-purple-600 hover:text-purple-900 inline-flex items-center px-2 py-1 bg-purple-50 rounded-lg hover:bg-purple-100">
                                                    <i class="fas fa-user mr-1"></i> Conductor
                                                </a>
                                            <a href="{{ route('camiones.edit', $camion) }}" 
                                               class="text-green-600 hover:text-green-900 inline-flex items-center px-2 py-1 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                                                <i class="fas fa-edit mr-1"></i> Editar
                                            </a>
                                            <button onclick="eliminarCamion({{ $camion->id }}, '{{ $camion->placa }}')" 
                                                    class="text-red-600 hover:text-red-900 inline-flex items-center px-2 py-1 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                                <i class="fas fa-trash mr-1"></i> Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center py-12">
                        <div class="text-gray-300 mb-4">
                            <i class="fas fa-truck text-5xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700">No hay camiones registrados</h3>
                        <p class="mt-1 text-sm text-gray-500">Comienza agregando tu primer camión.</p>
                        <a href="{{ route('camiones.create') }}" 
                           class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white 
                                  rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Agregar primer camión
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
    <div id="modal-eliminar" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Eliminar Camión</h3>
                <p class="text-sm text-gray-500 mb-4" id="mensaje-eliminar"></p>
                <div class="flex justify-center space-x-3">
                    <button onclick="cerrarModalEliminar()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancelar
                    </button>
                    <button id="btn-confirmar-eliminar" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal horarios por día -->
    <div id="modal-horarios-camion" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-0 w-11/12 md:w-2/3 lg:w-1/2 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <!-- Header del modal -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="bg-white/20 p-2 rounded-lg mr-3">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Horarios de Trabajo</h3>
                            <p id="modal-horarios-camion-placa" class="text-indigo-100 text-sm"></p>
                        </div>
                    </div>
                    <button type="button" onclick="cerrarModalHorariosCamion()" class="text-white/70 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Contenido del modal -->
            <div id="modal-horarios-camion-contenido" class="p-6 space-y-4 max-h-[60vh] overflow-y-auto"></div>
            
            <!-- Footer del modal -->
            <div class="border-t bg-gray-50 px-6 py-4">
                <button type="button" onclick="cerrarModalHorariosCamion()" class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg hover:from-gray-700 hover:to-gray-800 transition-all font-medium shadow-md hover:shadow-lg">
                    <i class="fas fa-check mr-2"></i>Entendido
                </button>
            </div>
        </div>
    </div>

    <script>
    let camionAEliminar = null;

    const diasNombres = {
        lunes: 'Lunes',
        martes: 'Martes',
        miercoles: 'Miércoles',
        jueves: 'Jueves',
        viernes: 'Viernes',
        sabado: 'Sábado',
        domingo: 'Domingo'
    };

    const diasIconos = {
        lunes: '🌙',
        martes: '🔥',
        miercoles: '💧',
        jueves: '⚡',
        viernes: '💚',
        sabado: '🌟',
        domingo: '☀️'
    };

    function formatearHora(hora) {
        if (!hora) return '--:--';
        const partes = hora.split(':');
        if (partes.length >= 2) {
            return `${partes[0]}:${partes[1]}`;
        }
        return hora;
    }

    function mostrarHorariosCamion(detalleHorarios, placa) {
        const modal = document.getElementById('modal-horarios-camion');
        const placaEl = document.getElementById('modal-horarios-camion-placa');
        const contenido = document.getElementById('modal-horarios-camion-contenido');

        placaEl.innerHTML = `<i class="fas fa-truck mr-1"></i> Camión: <strong>${placa}</strong>`;

        if (!Array.isArray(detalleHorarios) || detalleHorarios.length === 0) {
            contenido.innerHTML = `
                <div class="text-center py-8">
                    <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-times text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No hay horarios configurados</p>
                    <p class="text-gray-400 text-sm mt-1">Asigna rutas y horarios desde la sección de asignaciones</p>
                </div>
            `;
        } else {
            contenido.innerHTML = detalleHorarios.map((item, index) => {
                const horarioGeneral = item.general?.hora_inicio && item.general?.hora_fin
                    ? `${formatearHora(item.general.hora_inicio)} - ${formatearHora(item.general.hora_fin)}`
                    : null;

                let porDiaHtml = '';
                if (Array.isArray(item.por_dia) && item.por_dia.length > 0) {
                    porDiaHtml = `
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                <i class="fas fa-calendar-week mr-1"></i> Horarios por día
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                ${item.por_dia.map(h => {
                                    const dia = diasNombres[h.dia] || h.dia;
                                    const icono = diasIconos[h.dia] || '📅';
                                    return `
                                        <div class="flex items-center justify-between bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg px-3 py-2 border border-indigo-100">
                                            <span class="flex items-center text-sm font-medium text-gray-700">
                                                <span class="mr-2">${icono}</span>${dia}
                                            </span>
                                            <span class="text-sm font-semibold text-indigo-600">
                                                ${formatearHora(h.hora_inicio)} - ${formatearHora(h.hora_fin)}
                                            </span>
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                        </div>
                    `;
                } else {
                    porDiaHtml = `
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <div class="flex items-center text-gray-400 text-sm">
                                <i class="fas fa-info-circle mr-2"></i>
                                Usa el horario general para todos los días
                            </div>
                        </div>
                    `;
                }

                return `
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-4 py-3">
                            <div class="flex items-center">
                                <div class="bg-white/20 rounded-lg p-2 mr-3">
                                    <i class="fas fa-route text-white"></i>
                                </div>
                                <h4 class="font-bold text-white">${item.ruta}</h4>
                            </div>
                        </div>
                        <div class="p-4">
                            ${horarioGeneral ? `
                                <div class="flex items-center justify-between bg-green-50 rounded-lg px-4 py-3 border border-green-100">
                                    <div class="flex items-center">
                                        <div class="bg-green-500 rounded-full p-1.5 mr-3">
                                            <i class="fas fa-clock text-white text-xs"></i>
                                        </div>
                                        <span class="text-sm font-medium text-gray-600">Horario General</span>
                                    </div>
                                    <span class="text-lg font-bold text-green-600">${horarioGeneral}</span>
                                </div>
                            ` : `
                                <div class="flex items-center bg-amber-50 rounded-lg px-4 py-3 border border-amber-100">
                                    <i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i>
                                    <span class="text-sm text-amber-700">Sin horario general configurado</span>
                                </div>
                            `}
                            ${porDiaHtml}
                        </div>
                    </div>
                `;
            }).join('');
        }

        modal.classList.remove('hidden');
    }

    function cerrarModalHorariosCamion() {
        document.getElementById('modal-horarios-camion').classList.add('hidden');
    }

    function eliminarCamion(id, placa) {
        camionAEliminar = id;
        document.getElementById('mensaje-eliminar').textContent = 
            `¿Estás seguro de eliminar el camión ${placa}? Esta acción no se puede deshacer.`;
        document.getElementById('modal-eliminar').classList.remove('hidden');
    }

    function cerrarModalEliminar() {
        document.getElementById('modal-eliminar').classList.add('hidden');
        camionAEliminar = null;
    }

    document.getElementById('btn-confirmar-eliminar').addEventListener('click', function() {
        if (!camionAEliminar) return;
        
        fetch(`/camiones/${camionAEliminar}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`camion-${camionAEliminar}`).remove();
                showToast('success', '¡Camión eliminado!', data.message || 'El camión ha sido eliminado correctamente.');
                
                if (document.querySelectorAll('tbody tr').length === 0) {
                    location.reload();
                }
            } else {
                showToast('error', 'No se pudo eliminar', data.message || 'Ocurrió un error al intentar eliminar el camión.');
            }
            cerrarModalEliminar();
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Error de conexión', 'No se pudo conectar con el servidor. Intenta nuevamente.');
            cerrarModalEliminar();
        });
    });

    // Cerrar modal al hacer clic fuera
    document.getElementById('modal-eliminar').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModalEliminar();
        }
    });

    document.getElementById('modal-horarios-camion').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModalHorariosCamion();
        }
    });
    </script>

    <style>
        .bg-gradient-to-r {
            background-image: linear-gradient(to right, var(--tw-gradient-from), var(--tw-gradient-to));
        }
        
        .shadow-lg {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .hover\:scale-105:hover {
            transform: scale(1.05);
        }
        
        .transition-transform {
            transition-property: transform;
        }
        
        .duration-300 {
            transition-duration: 300ms;
        }
    </style>
</x-dinamico-layout>