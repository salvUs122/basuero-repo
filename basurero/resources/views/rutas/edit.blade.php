<x-dinamico-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('rutas.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Ruta: {{ $ruta->nombre }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-red-800 font-medium">Por favor corrige los siguientes errores:</p>
                            <ul class="mt-2 list-disc list-inside text-sm text-red-600">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Editar Informacion de la Ruta</h3>
                    <p class="text-sm text-gray-600">Modifica los datos de la ruta</p>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('rutas.update', $ruta->id) }}" id="form-ruta">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Columna izquierda: Datos -->
                            <div class="lg:col-span-1 space-y-6">
                                <!-- Nombre -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-signature mr-1 text-blue-500"></i>
                                        Nombre de la Ruta *
                                    </label>
                                    <input type="text" name="nombre" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           value="{{ old('nombre', $ruta->nombre) }}">
                                </div>

                                <!-- Estado -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        <i class="fas fa-toggle-on mr-1 text-blue-500"></i>
                                        Estado de la Ruta *
                                    </label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="relative flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                            <input type="radio" name="estado" value="activa"
                                                   {{ old('estado', $ruta->estado) == 'activa' ? 'checked' : '' }}
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                            <div class="ml-3">
                                                <span class="block text-sm font-medium text-gray-900">Activa</span>
                                                <span class="block text-sm text-gray-500">En uso</span>
                                            </div>
                                            <div class="absolute top-4 right-4">
                                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                            </div>
                                        </label>

                                        <label class="relative flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                            <input type="radio" name="estado" value="inactiva"
                                                   {{ old('estado', $ruta->estado) == 'inactiva' ? 'checked' : '' }}
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                            <div class="ml-3">
                                                <span class="block text-sm font-medium text-gray-900">Inactiva</span>
                                                <span class="block text-sm text-gray-500">No disponible</span>
                                            </div>
                                            <div class="absolute top-4 right-4">
                                                <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Tolerancia -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-ruler mr-1 text-blue-500"></i>
                                        Tolerancia (metros) *
                                    </label>
                                    <div class="relative">
                                        <input type="number" name="tolerancia_metros"
                                               value="{{ old('tolerancia_metros', $ruta->tolerancia_metros) }}"
                                               min="5" max="500" required
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-12">
                                        <span class="absolute right-3 top-3 text-gray-500">m</span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">Distancia maxima permitida fuera de la ruta</p>
                                </div>

                                <!-- Botones -->
                                <div class="flex space-x-3 pt-4">
                                    <a href="{{ route('rutas.index') }}"
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-center">
                                        Cancelar
                                    </a>
                                    <button type="submit"
                                            class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 font-medium shadow-lg hover:shadow-xl">
                                        <i class="fas fa-save mr-2"></i> Actualizar
                                    </button>
                                </div>
                            </div>

                            <!-- Columna derecha: Mapa -->
                            <div class="lg:col-span-2">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex justify-between items-center mb-4">
                                        <h4 class="font-medium text-gray-700">
                                            <i class="fas fa-map-marked-alt text-blue-500 mr-2"></i>
                                            Ruta en el mapa
                                        </h4>
                                        <span id="estado-dibujo" class="text-sm text-green-600">
                                            Ruta cargada
                                        </span>
                                    </div>

                                    <div id="map" style="height: 500px;" class="rounded-lg border-2 border-gray-300"></div>

                                    <input type="hidden" name="geometria_geojson" id="geometria_geojson"
                                           value="{{ old('geometria_geojson', $ruta->geometria_geojson) }}" required>

                                    <div class="mt-4 flex gap-2">
                                        <button type="button" id="btn-dibujar"
                                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-pencil-alt mr-2"></i> Redibujar
                                        </button>
                                        <button type="button" id="btn-limpiar"
                                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                            <i class="fas fa-trash mr-2"></i> Limpiar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Coordenadas de Cochabamba
        const map = L.map('map').setView([-17.3934, -66.1571], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        const drawControl = new L.Control.Draw({
            edit: { featureGroup: drawnItems },
            draw: {
                polygon: false,
                rectangle: false,
                circle: false,
                circlemarker: false,
                marker: false,
                polyline: {
                    shapeOptions: { color: '#3b82f6', weight: 5 }
                }
            }
        });
        map.addControl(drawControl);

        const inputGeojson = document.getElementById('geometria_geojson');
        const estadoDibujo = document.getElementById('estado-dibujo');

        // Cargar geometria existente
        if (inputGeojson.value) {
            try {
                const geoData = JSON.parse(inputGeojson.value);
                const layer = L.geoJSON(geoData).getLayers()[0];
                drawnItems.addLayer(layer);
                map.fitBounds(layer.getBounds());
            } catch (e) {
                console.error('Error cargando geometria:', e);
            }
        }

        function actualizarEstado() {
            const data = drawnItems.toGeoJSON();
            const lineas = data.features.filter(f => f.geometry?.type === 'LineString');

            if (lineas.length === 1) {
                inputGeojson.value = JSON.stringify(lineas[0].geometry);
                estadoDibujo.innerHTML = 'Ruta actualizada';
                estadoDibujo.className = 'text-sm text-green-600';
            } else {
                inputGeojson.value = '';
                estadoDibujo.innerHTML = 'Dibuja la ruta nuevamente';
                estadoDibujo.className = 'text-sm text-red-600';
            }
        }

        map.on(L.Draw.Event.CREATED, function(event) {
            drawnItems.clearLayers();
            drawnItems.addLayer(event.layer);
            actualizarEstado();
        });

        map.on(L.Draw.Event.EDITED, actualizarEstado);
        map.on(L.Draw.Event.DELETED, actualizarEstado);

        document.getElementById('btn-dibujar').addEventListener('click', function() {
            drawnItems.clearLayers();
            new L.Draw.Polyline(map, drawControl.options.draw.polyline).enable();
        });

        document.getElementById('btn-limpiar').addEventListener('click', function() {
            drawnItems.clearLayers();
            actualizarEstado();
        });

        document.getElementById('form-ruta').addEventListener('submit', function(e) {
            if (!inputGeojson.value) {
                e.preventDefault();
                showToast('warning', 'Falta el dibujo', 'Debes dibujar una ruta en el mapa antes de guardar.');
            }
        });
    });
    </script>
</x-dinamico-layout>
