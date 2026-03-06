<x-dinamico-layout> 
   <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    🗺️ Gestión de Rutas
                </h2>
                <p class="text-sm text-gray-600 mt-1">Administra todas las rutas del sistema</p>
            </div>
            <a href="{{ route('rutas.create') }}" 
               class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 
                      text-white font-medium py-2 px-4 rounded-lg transition-all duration-300 
                      flex items-center shadow-lg hover:shadow-xl">
                <i class="fas fa-plus mr-2"></i>
                Nueva Ruta
            </a>
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

    <script>
    let rutaAEliminar = null;

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
                alert(data.message);
                if (document.querySelectorAll('tbody tr').length === 0) {
                    location.reload();
                }
            } else {
                alert(data.message);
            }
            cerrarModalEliminar();
        })
        .catch(error => {
            alert('Error al eliminar la ruta');
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