<x-dinamico-layout> 
   <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Gestion de Rutas
                </h2>
                <p class="text-sm text-gray-600 mt-1">Administra todas las rutas del sistema</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="abrirModalBotadero()"
                   class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700
                          text-white font-medium py-2 px-4 rounded-lg transition-all duration-300
                          flex items-center shadow-lg hover:shadow-xl">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Marcar Botadero
                </button>
                <a href="{{ route('rutas.create') }}"
                   class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800
                          text-white font-medium py-2 px-4 rounded-lg transition-all duration-300
                          flex items-center shadow-lg hover:shadow-xl">
                    <i class="fas fa-plus mr-2"></i>
                    Nueva Ruta
                </a>
            </div>
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

            <!-- Tarjeta del Botadero Global -->
            <div id="tarjeta-botadero" class="mb-6">
                @if($botadero['lat'])
                <div class="bg-white rounded-xl shadow-lg border-l-4 border-orange-500 p-5">
                    <div class="flex justify-between items-start">
                        <div class="flex items-start space-x-4">
                            <div class="bg-orange-100 p-3 rounded-full flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-orange-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                    <i class="fas fa-check-circle text-green-500 text-sm"></i>
                                    Botadero Configurado
                                </h3>
                                <p class="text-gray-700 font-semibold mt-1">{{ $botadero['nombre'] }}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-location-dot mr-1"></i>
                                    Lat: {{ number_format($botadero['lat'], 6) }}, Lng: {{ number_format($botadero['lng'], 6) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="verBotaderoEnMapa()"
                                    class="px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-lg transition-colors flex items-center gap-1">
                                <i class="fas fa-eye"></i>
                                <span class="hidden sm:inline">Ver</span>
                            </button>
                            <button onclick="abrirModalBotadero()"
                                    class="px-3 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm rounded-lg transition-colors flex items-center gap-1">
                                <i class="fas fa-edit"></i>
                                <span class="hidden sm:inline">Editar</span>
                            </button>
                            <button onclick="eliminarBotadero()"
                                    class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition-colors flex items-center gap-1">
                                <i class="fas fa-trash"></i>
                                <span class="hidden sm:inline">Eliminar</span>
                            </button>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-yellow-50 rounded-xl shadow-lg border-l-4 border-yellow-500 p-5">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-4">
                            <div class="bg-yellow-100 p-3 rounded-full flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-yellow-800">Sin Botadero Configurado</h3>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Los conductores no tendrán punto de descarga disponible.
                                </p>
                            </div>
                        </div>
                        <button onclick="abrirModalBotadero()"
                                class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm rounded-lg transition-colors flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Agregar Botadero
                        </button>
                    </div>
                </div>
                @endif
            </div>

            <!-- Tarjetas de estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Rutas -->
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Rutas</p>
                            <p class="text-3xl font-bold mt-2">{{ $rutas->count() }}</p>
                        </div>
                        <div class="bg-blue-400 p-3 rounded-full">
                            <i class="fas fa-route text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Rutas Activas -->
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Rutas Activas</p>
                            <p class="text-3xl font-bold mt-2">{{ $rutas->where('estado', 'activa')->count() }}</p>
                        </div>
                        <div class="bg-green-400 p-3 rounded-full">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Con Camiones -->
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Con Camiones</p>
                            <p class="text-3xl font-bold mt-2">{{ $rutas->filter(fn($r) => $r->camiones->count() > 0)->count() }}</p>
                        </div>
                        <div class="bg-purple-400 p-3 rounded-full">
                            <i class="fas fa-truck text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de rutas -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Rutas Registradas</h3>
                            <p class="text-sm text-gray-600">{{ $rutas->count() }} rutas en el sistema</p>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    @if($rutas->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Información</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Camiones Asignados</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Configuración</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($rutas as $ruta)
                                <tr class="hover:bg-gray-50 transition-colors duration-200" id="ruta-{{ $ruta->id }}">
                                    <!-- Información de la ruta -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-route text-blue-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $ruta->nombre }}</div>
                                                <div class="mt-1">
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                          {{ $ruta->estado == 'activa' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ ucfirst($ruta->estado) }}
                                                    </span>
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    Creada: {{ $ruta->created_at->format('d/m/Y') }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Camiones asignados -->
                                    <td class="px-6 py-4">
                                        @if($ruta->camiones->count() > 0)
                                            <div class="space-y-2">
                                                @foreach($ruta->camiones->take(3) as $camion)
                                                    <div class="text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                                        <div class="flex justify-between items-center">
                                                            <span>
                                                                <i class="fas fa-truck text-blue-500 mr-1"></i>
                                                                {{ $camion->placa }}
                                                            </span>
                                                            <span class="text-xs {{ $camion->pivot->activa ? 'text-green-600' : 'text-red-600' }}">
                                                                {{ $camion->pivot->activa ? 'Activa' : 'Inactiva' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @if($ruta->camiones->count() > 3)
                                                    <div class="text-xs text-gray-500">
                                                        +{{ $ruta->camiones->count() - 3 }} más
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500 italic">Sin camiones asignados</span>
                                        @endif
                                    </td>

                                    <!-- Configuración -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <div class="flex items-center">
                                                <i class="fas fa-ruler text-gray-400 mr-2"></i>
                                                Tolerancia: {{ $ruta->tolerancia_metros }}m
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $ruta->geometria_geojson ? 'Ruta definida' : 'Sin geometría' }}
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Acciones -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button onclick="verRuta({{ $ruta->id }})" 
                                                    class="text-blue-600 hover:text-blue-900 inline-flex items-center px-2 py-1 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                                <i class="fas fa-eye mr-1"></i> Ver
                                            </button>
                                            <a href="{{ route('rutas.edit', $ruta->id) }}" 
                                               class="text-green-600 hover:text-green-900 inline-flex items-center px-2 py-1 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                                                <i class="fas fa-edit mr-1"></i> Editar
                                            </a>
                                            <button onclick="eliminarRuta({{ $ruta->id }}, '{{ $ruta->nombre }}')" 
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
                            <i class="fas fa-route text-5xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700">No hay rutas registradas</h3>
                        <p class="mt-1 text-sm text-gray-500">Comienza creando tu primera ruta.</p>
                        <a href="{{ route('rutas.create') }}" 
                           class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white 
                                  rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Crear primera ruta
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver ruta -->
    <div id="modal-ver" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-route mr-2 text-blue-600"></i>
                    Detalles de la Ruta
                </h3>
                <button onclick="cerrarModalVer()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="modal-contenido" class="space-y-3">
                <!-- Contenido cargado por JS -->
            </div>
            <div class="mt-4 flex justify-end">
                <button onclick="cerrarModalVer()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal para eliminar -->
    <div id="modal-eliminar" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Eliminar Ruta</h3>
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

    <!-- Modal para marcar botadero -->
    <div id="modal-botadero" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border max-w-2xl shadow-lg rounded-xl bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-map-marker-alt mr-2 text-orange-600"></i>
                    Ubicación del Botadero Global
                </h3>
                <button onclick="cerrarModalBotadero()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <p class="text-sm text-gray-600 mb-4">
                Configure la ubicación única del botadero que será utilizada por todos los conductores. Solo puede haber un botadero global.
            </p>

            <!-- Estado actual -->
            <div id="estado-botadero" class="mb-4 p-4 rounded-lg {{ $botadero['lat'] ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' }}">
                @if($botadero['lat'])
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-green-800 font-medium">
                                <i class="fas fa-check-circle mr-1"></i>
                                Botadero Configurado
                            </p>
                            <p class="text-sm text-green-700 mt-1">
                                <strong>{{ $botadero['nombre'] }}</strong>
                            </p>
                            <p class="text-xs text-green-600 mt-1">
                                <i class="fas fa-location-dot mr-1"></i>
                                {{ number_format($botadero['lat'], 6) }}, {{ number_format($botadero['lng'], 6) }}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="editarBotadero()" class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded-lg transition-colors">
                                <i class="fas fa-edit mr-1"></i>Editar
                            </button>
                            <button onclick="eliminarBotadero()" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs rounded-lg transition-colors">
                                <i class="fas fa-trash mr-1"></i>Eliminar
                            </button>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        No hay botadero configurado. Haz clic en el mapa para marcarlo.
                    </p>
                @endif
            </div>

            <!-- Nombre del botadero -->
            <div class="mb-4" id="form-botadero-nombre">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre del Botadero
                </label>
                <input type="text" id="botadero-nombre"
                       value="{{ $botadero['nombre'] ?? 'Botadero Municipal' }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                       placeholder="Ej: Botadero Municipal K'ara K'ara">
            </div>

            <!-- Mapa -->
            <div id="map-botadero" class="h-80 rounded-lg border-2 border-gray-300 mb-4"></div>

            <!-- Coordenadas seleccionadas -->
            <div id="coords-botadero" class="text-sm text-gray-600 mb-4 hidden">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <i class="fas fa-map-pin mr-1 text-blue-600"></i>
                    <span class="font-medium">Nuevas coordenadas:</span> <span id="coords-texto" class="font-mono"></span>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3" id="botones-edicion">
                <button onclick="cerrarModalBotadero()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancelar
                </button>
                <button onclick="guardarBotadero()" id="btn-guardar-botadero"
                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ $botadero['lat'] ? '' : 'disabled' }}>
                    <i class="fas fa-save mr-2"></i>
                    Guardar Ubicación
                </button>
            </div>
            <!-- Botón solo cerrar (para modo vista) -->
            <div class="flex justify-end space-x-3 hidden" id="botones-vista">
                <button onclick="cerrarModalBotadero()"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal confirmar eliminar botadero -->
    <div id="modal-eliminar-botadero" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Eliminar Botadero</h3>
                <p class="text-sm text-gray-500 mb-4">¿Estás seguro de que deseas eliminar la ubicación del botadero? Los conductores no tendrán punto de descarga configurado.</p>
                <div class="flex justify-center space-x-3">
                    <button onclick="cerrarModalEliminarBotadero()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancelar
                    </button>
                    <button onclick="confirmarEliminarBotadero()"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
    let rutaAEliminar = null;
    let mapBotadero = null;
    let marcadorBotadero = null;
    let botaderoLat = {{ $botadero['lat'] ?: 'null' }};
    let botaderoLng = {{ $botadero['lng'] ?: 'null' }};
    let modoVista = false; // true = solo ver, false = editar

    function verRuta(id) {
        window.location.href = `/rutas/${id}`;
    }

    function cerrarModalVer() {
        document.getElementById('modal-ver').classList.add('hidden');
    }

    function eliminarRuta(id, nombre) {
        rutaAEliminar = id;
        document.getElementById('mensaje-eliminar').textContent =
            `¿Estás seguro de eliminar la ruta "${nombre}"? Esta acción no se puede deshacer.`;
        document.getElementById('modal-eliminar').classList.remove('hidden');
    }

    function cerrarModalEliminar() {
        document.getElementById('modal-eliminar').classList.add('hidden');
        rutaAEliminar = null;
    }

    // ===== FUNCIONES DEL BOTADERO =====
    
    // Ver botadero en mapa (solo lectura)
    function verBotaderoEnMapa() {
        modoVista = true;
        document.getElementById('form-botadero-nombre').classList.add('hidden');
        document.getElementById('botones-edicion').classList.add('hidden');
        document.getElementById('botones-vista').classList.remove('hidden');
        document.getElementById('coords-botadero').classList.add('hidden');
        document.querySelector('#modal-botadero h3').innerHTML = '<i class="fas fa-eye mr-2 text-blue-600"></i>Ver Botadero';
        
        abrirModalBotaderoInterno(false);
    }
    
    // Abrir modal para editar/agregar
    function abrirModalBotadero() {
        modoVista = false;
        document.getElementById('form-botadero-nombre').classList.remove('hidden');
        document.getElementById('botones-edicion').classList.remove('hidden');
        document.getElementById('botones-vista').classList.add('hidden');
        document.querySelector('#modal-botadero h3').innerHTML = '<i class="fas fa-map-marker-alt mr-2 text-orange-600"></i>Ubicación del Botadero Global';
        
        abrirModalBotaderoInterno(true);
    }
    
    function abrirModalBotaderoInterno(permitirEdicion) {
        document.getElementById('modal-botadero').classList.remove('hidden');

        // Inicializar mapa si no existe
        setTimeout(() => {
            if (!mapBotadero) {
                // Centro en Cochabamba por defecto
                const centroLat = botaderoLat || -17.3934;
                const centroLng = botaderoLng || -66.1571;
                const zoom = botaderoLat ? 15 : 13;

                mapBotadero = L.map('map-botadero').setView([centroLat, centroLng], zoom);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(mapBotadero);

                // Si ya hay botadero, mostrar marcador
                if (botaderoLat && botaderoLng) {
                    marcadorBotadero = L.marker([botaderoLat, botaderoLng], {
                        icon: L.divIcon({
                            className: 'custom-marker',
                            html: '<div style="background: #ea580c; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"><i class="fas fa-trash" style="color: white; font-size: 14px;"></i></div>',
                            iconSize: [30, 30],
                            iconAnchor: [15, 15]
                        })
                    }).addTo(mapBotadero);

                    if (permitirEdicion) {
                        document.getElementById('coords-botadero').classList.remove('hidden');
                        document.getElementById('coords-texto').textContent = `${botaderoLat.toFixed(6)}, ${botaderoLng.toFixed(6)}`;
                    }
                }

                // Click en el mapa para marcar botadero (solo si se permite edición)
                mapBotadero.on('click', function(e) {
                    if (modoVista) return; // No permitir clicks en modo vista
                    
                    botaderoLat = e.latlng.lat;
                    botaderoLng = e.latlng.lng;

                    // Quitar marcador anterior
                    if (marcadorBotadero) {
                        mapBotadero.removeLayer(marcadorBotadero);
                    }

                    // Agregar nuevo marcador
                    marcadorBotadero = L.marker([botaderoLat, botaderoLng], {
                        icon: L.divIcon({
                            className: 'custom-marker',
                            html: '<div style="background: #ea580c; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"><i class="fas fa-trash" style="color: white; font-size: 14px;"></i></div>',
                            iconSize: [30, 30],
                            iconAnchor: [15, 15]
                        })
                    }).addTo(mapBotadero);

                    // Mostrar coordenadas
                    document.getElementById('coords-botadero').classList.remove('hidden');
                    document.getElementById('coords-texto').textContent = `${botaderoLat.toFixed(6)}, ${botaderoLng.toFixed(6)}`;

                    // Habilitar boton guardar
                    document.getElementById('btn-guardar-botadero').disabled = false;
                });
            } else {
                mapBotadero.invalidateSize();
            }
        }, 100);
    }

    function cerrarModalBotadero() {
        document.getElementById('modal-botadero').classList.add('hidden');
        // Limpiar cambios si no se guardaron
        if (botaderoLat && botaderoLng) {
            document.getElementById('btn-guardar-botadero').disabled = false;
        }
    }

    function editarBotadero() {
        abrirModalBotadero();
    }

    function eliminarBotadero() {
        document.getElementById('modal-eliminar-botadero').classList.remove('hidden');
    }

    function cerrarModalEliminarBotadero() {
        document.getElementById('modal-eliminar-botadero').classList.add('hidden');
    }

    function confirmarEliminarBotadero() {
        const btnEliminar = document.querySelector('#modal-eliminar-botadero button:last-child');
        btnEliminar.disabled = true;
        btnEliminar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Eliminando...';

        fetch('/botadero', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', '¡Botadero eliminado!', data.message || 'La ubicación del botadero ha sido eliminada.');
                cerrarModalEliminarBotadero();
                location.reload();
            } else {
                showToast('error', 'No se pudo eliminar', data.message || 'Ocurrió un error al eliminar el botadero.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Error de conexión', 'No se pudo conectar con el servidor.');
        })
        .finally(() => {
            btnEliminar.disabled = false;
            btnEliminar.innerHTML = 'Eliminar';
        });
    }

    function guardarBotadero() {
        if (!botaderoLat || !botaderoLng) {
            showToast('warning', 'Ubicación requerida', 'Por favor, marca una ubicación en el mapa haciendo clic sobre él.');
            return;
        }

        const nombre = document.getElementById('botadero-nombre').value.trim();
        if (!nombre) {
            showToast('warning', 'Nombre requerido', 'Por favor, ingresa un nombre para identificar el botadero.');
            return;
        }

        const btnGuardar = document.getElementById('btn-guardar-botadero');
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Guardando...';

        fetch('/botadero', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                lat: botaderoLat,
                lng: botaderoLng,
                nombre: nombre
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const estadoDiv = document.getElementById('estado-botadero');
                estadoDiv.className = 'mb-4 p-3 rounded-lg bg-green-50 border border-green-200';
                estadoDiv.innerHTML = `
                    <p class="text-sm text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>
                        Botadero configurado: <strong>${nombre}</strong>
                    </p>
                `;

                showToast('success', '¡Botadero guardado!', data.message || 'La ubicación del botadero se guardó correctamente.');
                cerrarModalBotadero();
            } else {
                showToast('error', 'Error al guardar', data.message || 'No se pudo guardar la ubicación.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Error de conexión', 'No se pudo conectar con el servidor.');
        })
        .finally(() => {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = '<i class="fas fa-save mr-2"></i> Guardar Ubicacion';
        });
    }

    document.getElementById('btn-confirmar-eliminar').addEventListener('click', function() {
        if (!rutaAEliminar) return;

        fetch(`/rutas/${rutaAEliminar}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`ruta-${rutaAEliminar}`).remove();
                showToast('success', '¡Ruta eliminada!', data.message || 'La ruta ha sido eliminada correctamente.');
                if (document.querySelectorAll('tbody tr').length === 0) {
                    location.reload();
                }
            } else {
                showToast('error', 'No se pudo eliminar', data.message || 'Ocurrió un error al eliminar la ruta.');
            }
            cerrarModalEliminar();
        })
        .catch(error => {
            showToast('error', 'Error de conexión', 'No se pudo conectar con el servidor.');
            cerrarModalEliminar();
        });
    });

    // Cerrar modales al hacer clic fuera
    document.getElementById('modal-ver').addEventListener('click', function(e) {
        if (e.target === this) cerrarModalVer();
    });

    document.getElementById('modal-eliminar').addEventListener('click', function(e) {
        if (e.target === this) cerrarModalEliminar();
    });

    document.getElementById('modal-botadero').addEventListener('click', function(e) {
        if (e.target === this) cerrarModalBotadero();
    });

    document.getElementById('modal-eliminar-botadero').addEventListener('click', function(e) {
        if (e.target === this) cerrarModalEliminarBotadero();
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