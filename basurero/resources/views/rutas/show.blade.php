<x-dinamico-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('rutas.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🗺️ Detalles de Ruta: {{ $ruta->nombre }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Columna izquierda: Información -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Tarjeta de información -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-blue-100 text-sm font-medium">INFORMACIÓN DE LA RUTA</p>
                                    <p class="text-2xl font-bold mt-1">{{ $ruta->nombre }}</p>
                                </div>
                                <div class="bg-blue-500 bg-opacity-30 p-3 rounded-full">
                                    <i class="fas fa-route text-2xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Estado -->
                                <div class="flex items-center">
                                    <div class="bg-{{ $ruta->estado == 'activa' ? 'green' : 'red' }}-100 p-2 rounded-lg mr-3">
                                        <i class="fas fa-toggle-on text-{{ $ruta->estado == 'activa' ? 'green' : 'red' }}-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Estado</p>
                                        <p class="font-medium">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                  {{ $ruta->estado == 'activa' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($ruta->estado) }}
                                            </span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Tolerancia -->
                                <div class="flex items-center">
                                    <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                        <i class="fas fa-ruler text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Tolerancia</p>
                                        <p class="font-medium">{{ $ruta->tolerancia_metros }} metros</p>
                                    </div>
                                </div>

                                <!-- Camiones asignados -->
                                <div class="flex items-center">
                                    <div class="bg-orange-100 p-2 rounded-lg mr-3">
                                        <i class="fas fa-truck text-orange-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Camiones asignados</p>
                                        <p class="font-medium">{{ $ruta->camiones_count }}</p>
                                    </div>
                                </div>

                                <!-- Fecha de creación -->
                                <div class="flex items-center">
                                    <div class="bg-gray-100 p-2 rounded-lg mr-3">
                                        <i class="fas fa-calendar text-gray-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Creada</p>
                                        <p class="font-medium">{{ $ruta->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>

                                <!-- Lista de camiones -->
                                @if($ruta->camiones->count() > 0)
                                <div class="border-t pt-4">
                                    <p class="text-sm font-medium text-gray-700 mb-3">Camiones en esta ruta:</p>
                                    <div class="space-y-2">
                                        @foreach($ruta->camiones as $camion)
                                            <div class="bg-gray-50 p-2 rounded-lg flex justify-between items-center">
                                                <div>
                                                    <span class="font-medium">{{ $camion->placa }}</span>
                                                    <span class="text-xs text-gray-500 ml-2">{{ $camion->codigo }}</span>
                                                </div>
                                                <span class="text-xs {{ $camion->pivot->activa ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $camion->pivot->activa ? 'Activa' : 'Inactiva' }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <!-- Botones de acción -->
                                <div class="flex space-x-3 pt-4 border-t">
                                    <a href="{{ route('rutas.edit', $ruta->id) }}" 
                                       class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 
                                              text-white font-medium py-2 px-4 rounded-lg transition-all duration-300 text-center">
                                        <i class="fas fa-edit mr-2"></i> Editar
                                    </a>
                                    <button onclick="eliminarRuta({{ $ruta->id }}, '{{ $ruta->nombre }}')" 
                                            class="flex-1 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 
                                                   text-white font-medium py-2 px-4 rounded-lg transition-all duration-300">
                                        <i class="fas fa-trash mr-2"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha: Mapa -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-map-marked-alt text-blue-600 mr-2"></i>
                            Visualización de la Ruta
                        </h3>
                        
                        <div id="map" style="height: 500px;" class="rounded-lg border-2 border-gray-300"></div>
                        
                        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                                <div>
                                    <p class="text-sm text-blue-700">
                                        <strong>📍 Ruta completa:</strong> Esta es la trayectoria planificada para la ruta "{{ $ruta->nombre }}".
                                    </p>
                                    <p class="text-xs text-blue-600 mt-1">
                                        Longitud: {{ $ruta->geometria_geojson ? 'Calculando...' : 'No disponible' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para eliminar (igual que en index) -->
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

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar mapa
        const map = L.map('map').setView([-17.3934, -66.1571], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        // Cargar geometría de la ruta
        const geojson = @json($ruta->geometria_geojson);
        
        if (geojson) {
            try {
                const geoData = JSON.parse(geojson);
                
                // Dibujar la ruta
                const rutaLayer = L.geoJSON(geoData, {
                    style: {
                        color: '#3b82f6',
                        weight: 5,
                        opacity: 0.8
                    }
                }).addTo(map);
                
                // Ajustar vista al tamaño de la ruta
                map.fitBounds(rutaLayer.getBounds(), { padding: [50, 50] });
                
                // Calcular y mostrar longitud
                const coords = geoData.coordinates;
                let distancia = 0;
                
                for (let i = 1; i < coords.length; i++) {
                    const [lng1, lat1] = coords[i-1];
                    const [lng2, lat2] = coords[i];
                    distancia += calcularDistancia(lat1, lng1, lat2, lng2);
                }
                
                // Actualizar información de longitud
                document.querySelector('.text-xs.text-blue-600').innerHTML = 
                    `Longitud aproximada: ${distancia.toFixed(2)} km`;
                
                // Agregar marcadores de inicio y fin
                if (coords.length > 0) {
                    const inicio = [coords[0][1], coords[0][0]];
                    const fin = [coords[coords.length-1][1], coords[coords.length-1][0]];
                    
                    L.marker(inicio, {
                        icon: L.divIcon({
                            html: '<div style="background: #10b981; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border: 2px solid white;"><i class="fas fa-play text-xs"></i></div>',
                            iconSize: [30, 30]
                        })
                    }).addTo(map).bindPopup('<b>Inicio de ruta</b>');
                    
                    L.marker(fin, {
                        icon: L.divIcon({
                            html: '<div style="background: #ef4444; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border: 2px solid white;"><i class="fas fa-flag-checkered text-xs"></i></div>',
                            iconSize: [30, 30]
                        })
                    }).addTo(map).bindPopup('<b>Fin de ruta</b>');
                }
                
            } catch (e) {
                console.error('Error cargando geometría:', e);
            }
        }

        // Función para calcular distancia (Haversine)
        function calcularDistancia(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radio de la Tierra en km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = 
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }
    });

    // Función para eliminar (igual que en index)
    let rutaAEliminar = null;

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
                showToast('success', '¡Ruta eliminada!', 'La ruta ha sido eliminada. Redirigiendo...');
                setTimeout(() => {
                    window.location.href = "{{ route('rutas.index') }}";
                }, 1500);
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

    // Cerrar modal al hacer clic fuera
    document.getElementById('modal-eliminar').addEventListener('click', function(e) {
        if (e.target === this) cerrarModalEliminar();
    });
    </script>
</x-dinamico-layout>