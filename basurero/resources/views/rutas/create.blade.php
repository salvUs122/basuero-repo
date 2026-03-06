<x-dinamico-layout>
<x-slot name="header">
    <div class="flex items-center">
        <a href="{{ route('rutas.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🗺️ Crear Nueva Ruta
        </h2>
    </div>
</x-slot>

<style>
    .gradient-primary { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
    .card-shadow { box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
    .map-container { border-radius: 12px; overflow: hidden; border: 2px solid #e5e7eb; }
</style>

<div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Mensajes de error -->
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulario -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl card-shadow overflow-hidden">
                        <div class="gradient-primary p-6 text-white">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-blue-100 text-sm font-medium">INFORMACIÓN DE LA RUTA</p>
                                    <p class="text-2xl font-bold mt-1">Datos principales</p>
                                </div>
                                <div class="bg-blue-400 bg-opacity-30 p-3 rounded-full">
                                    <i class="fas fa-info-circle text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <form method="POST" action="{{ route('rutas.store') }}" id="form-ruta">
                                @csrf

                                <div class="space-y-6">
                                    <!-- Nombre -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-signature mr-1 text-blue-500"></i>
                                            Nombre de la Ruta *
                                        </label>
                                        <input type="text" name="nombre" required
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                               placeholder="Ej: Ruta Centro - Zona Norte"
                                               value="{{ old('nombre') }}">
                                        <p class="mt-1 text-sm text-gray-500">Nombre descriptivo para identificar la ruta</p>
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
                                                       {{ old('estado', 'activa') == 'activa' ? 'checked' : '' }}
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
                                                       {{ old('estado') == 'inactiva' ? 'checked' : '' }}
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
                                                   value="{{ old('tolerancia_metros', 50) }}"
                                                   min="5" max="500" required
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors pr-12">
                                            <span class="absolute right-3 top-3 text-gray-500">m</span>
                                        </div>

                                        <div class="mt-2">
                                            <div class="flex justify-between text-xs text-gray-500">
                                                <span>Mínima precisión</span>
                                                <span>Máxima desviación</span>
                                            </div>
                                            <input type="range" min="5" max="500" value="{{ old('tolerancia_metros', 50) }}"
                                                   class="w-full mt-1" id="tolerancia-slider">
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">Distancia máxima permitida fuera de la ruta</p>
                                    </div>

                                    <!-- Geometría (oculto) -->
                                    <input type="hidden" name="geometria_geojson" id="geometria_geojson" required>

                                    <!-- Botones -->
                                    <div class="pt-4 border-t border-gray-200">
                                        <div class="flex space-x-3">
                                            <a href="{{ route('rutas.index') }}"
                                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium text-center">
                                                <i class="fas fa-times mr-2"></i> Cancelar
                                            </a>
                                            <button type="submit"
                                                    class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800
                                                           text-white font-medium py-3 px-4 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl"
                                                    id="btn-guardar">
                                                <i class="fas fa-save mr-2"></i> Guardar Ruta
                                            </button>
                                        </div>

                                        <p class="text-xs text-gray-500 mt-3">
                                            💡 Tip: antes de guardar la ruta, dibuja y presiona “Guardar dibujo” para validar.
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Instrucciones -->
                    <div class="mt-6 bg-white rounded-xl card-shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-graduation-cap text-blue-600 mr-2"></i>
                            Instrucciones
                        </h3>

                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="bg-blue-100 p-2 rounded-full mr-3">
                                    <span class="text-blue-600 text-sm font-bold">1</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Dibuja la ruta en el mapa</p>
                                    <p class="text-sm text-gray-600">Usa “Dibujar Línea” y traza el recorrido</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="bg-blue-100 p-2 rounded-full mr-3">
                                    <span class="text-blue-600 text-sm font-bold">2</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Guarda el dibujo</p>
                                    <p class="text-sm text-gray-600">Presiona “Guardar dibujo” para validar y cargar el GeoJSON</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="bg-blue-100 p-2 rounded-full mr-3">
                                    <span class="text-blue-600 text-sm font-bold">3</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Guarda la ruta</p>
                                    <p class="text-sm text-gray-600">Completa los datos y presiona “Guardar Ruta”</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mapa -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl card-shadow p-6 h-full">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-map-marked-alt text-blue-600 mr-2"></i>
                                Dibuja la Ruta en el Mapa
                            </h3>
                            <div class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                                <i class="fas fa-mouse-pointer mr-1"></i>
                                <span id="estado-herramienta">Selecciona herramienta de dibujo</span>
                            </div>
                        </div>

                        <!-- Estado del dibujo -->
                        <div id="estado-dibujo" class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-yellow-800 font-medium">Aún no has dibujado la ruta</p>
                                    <p class="text-yellow-700 text-sm mt-1">
                                        Usa “Dibujar Línea” para trazar el recorrido y luego “Guardar dibujo”.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Mapa -->
                        <div id="map" class="map-container" style="height: 500px;"></div>

                        <!-- Controles del mapa -->
                        <div class="mt-4 flex flex-wrap gap-2">
                            <button id="btn-dibujar-linea"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-draw-polygon mr-2"></i> Dibujar Línea
                            </button>

                            <button id="btn-guardar-dibujo"
                                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                                <i class="fas fa-check mr-2"></i> Guardar dibujo
                            </button>

                            <button id="btn-limpiar"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                                    title="Borra el dibujo actual">
                                <i class="fas fa-trash mr-2"></i> Limpiar dibujo
                            </button>

                            <button id="btn-centrar"
                                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                <i class="fas fa-crosshairs mr-2"></i> Centrar Colcapirhua
                            </button>
                        </div>

                        <p class="text-xs text-gray-500 mt-3">
                            Nota: “Guardar dibujo” solo guarda el trazo en el <b>input oculto GeoJSON</b>. El guardado final en base de datos se hace con “Guardar Ruta”.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS ya incluido en el layout -->

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ====== COLCAPIRHUA, COCHABAMBA ======
        const COCHA_CENTER = [-17.3924, -66.2479];
        const COCHA_ZOOM   = 15;

        // Elementos DOM
        const inputGeojson = document.getElementById('geometria_geojson');
        const estadoDibujo = document.getElementById('estado-dibujo');
        const estadoHerramienta = document.getElementById('estado-herramienta');
        const btnGuardar = document.getElementById('btn-guardar');
        const formRuta = document.getElementById('form-ruta');

        const btnDibujarLinea = document.getElementById('btn-dibujar-linea');
        const btnGuardarDibujo = document.getElementById('btn-guardar-dibujo');
        const btnLimpiar = document.getElementById('btn-limpiar');
        const btnCentrar = document.getElementById('btn-centrar');

        const toleranciaSlider = document.getElementById('tolerancia-slider');
        const toleranciaInput = document.querySelector('[name="tolerancia_metros"]');

        // Variables del mapa
        let map;
        let drawnItems;
        let drawControl;
        let drawLineMode = false;

        function initMap() {
            // Crear mapa centrado en Cochabamba
            map = L.map('map').setView(COCHA_CENTER, COCHA_ZOOM);

            // Capa base
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // Grupo para elementos dibujados
            drawnItems = new L.FeatureGroup();
            map.addLayer(drawnItems);

            // Control de dibujo (solo polyline)
            drawControl = new L.Control.Draw({
                position: 'topright',
                draw: {
                    polygon: false,
                    rectangle: false,
                    circle: false,
                    circlemarker: false,
                    marker: false,
                    polyline: {
                        shapeOptions: {
                            color: '#3b82f6',
                            weight: 5,
                            opacity: 0.8
                        },
                        showLength: true,
                        metric: true
                    }
                },
                edit: {
                    featureGroup: drawnItems,
                    edit: false,
                    remove: false
                }
            });

            // Nota: NO agregamos el drawControl al mapa porque tú usas tus propios botones
            // map.addControl(drawControl);

            // Eventos de dibujo
            map.on(L.Draw.Event.CREATED, function (event) {
                // Solo 1 línea
                drawnItems.clearLayers();
                drawnItems.addLayer(event.layer);

                // Cuando se crea, todavía NO guardamos automáticamente el geojson:
                // el usuario debe presionar "Guardar dibujo".
                estadoHerramienta.textContent = 'Ruta dibujada (presiona “Guardar dibujo”)';
                drawLineMode = false;
            });

            // Asegura render
            setTimeout(() => map.invalidateSize(), 100);
        }

        // Calcular distancia entre dos puntos (Haversine)
        function calcularDistancia(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a =
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        function buildGeoJsonFromDrawn() {
            const data = drawnItems.toGeoJSON();
            const lineas = data.features.filter(f => f.geometry && f.geometry.type === 'LineString');
            if (lineas.length !== 1) return null;
            return lineas[0]; // feature
        }

        function pintarEstadoOk(feature) {
            const coords = feature.geometry.coordinates; // [ [lng,lat], ... ]
            let distanciaTotal = 0;

            for (let i = 1; i < coords.length; i++) {
                const [lng1, lat1] = coords[i-1];
                const [lng2, lat2] = coords[i];
                distanciaTotal += calcularDistancia(lat1, lng1, lat2, lng2);
            }

            estadoDibujo.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-green-800 font-medium">Dibujo guardado ✅</p>
                        <p class="text-green-700 text-sm mt-1">
                            Puntos: ${coords.length} | Distancia aproximada: ${distanciaTotal.toFixed(2)} km
                        </p>
                    </div>
                </div>
            `;
            estadoDibujo.className = 'mb-4 p-4 bg-green-50 border border-green-200 rounded-lg';
        }

        function pintarEstadoFalta() {
            inputGeojson.value = '';
            estadoDibujo.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-yellow-800 font-medium">Aún no has guardado el dibujo</p>
                        <p class="text-yellow-700 text-sm mt-1">
                            Dibuja una línea y presiona “Guardar dibujo”.
                        </p>
                    </div>
                </div>
            `;
            estadoDibujo.className = 'mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg';
        }

        // ====== BOTONES ======
        btnDibujarLinea.addEventListener('click', function() {
            // Activa el modo dibujo (polyline)
            if (!drawLineMode) {
                new L.Draw.Polyline(map, drawControl.options.draw.polyline).enable();
                drawLineMode = true;
                estadoHerramienta.textContent = 'Modo dibujo activo - Haz clic para empezar';
            }
        });

        btnGuardarDibujo.addEventListener('click', function() {
            const feature = buildGeoJsonFromDrawn();
            if (!feature) {
                alert('Primero dibuja EXACTAMENTE 1 línea (ruta) y luego presiona Guardar dibujo.');
                pintarEstadoFalta();
                return;
            }

            // Guardamos SOLO la geometría (LineString) como venías haciendo
            inputGeojson.value = JSON.stringify(feature.geometry);
            pintarEstadoOk(feature);
            estadoHerramienta.textContent = 'Dibujo guardado en el formulario ✅';
        });

        btnLimpiar.addEventListener('click', function() {
            if (!confirm('¿Eliminar el dibujo actual del mapa?')) return;
            drawnItems.clearLayers();
            pintarEstadoFalta();
            estadoHerramienta.textContent = 'Dibujo eliminado';
        });

        btnCentrar.addEventListener('click', function() {
            map.setView(COCHA_CENTER, COCHA_ZOOM);
            estadoHerramienta.textContent = 'Mapa centrado en Cochabamba';
        });

        // Sincronizar slider con input
        toleranciaSlider.addEventListener('input', function() {
            toleranciaInput.value = this.value;
        });

        toleranciaInput.addEventListener('input', function() {
            toleranciaSlider.value = this.value;
        });

        // Validar formulario
        formRuta.addEventListener('submit', function (e) {
            if (!inputGeojson.value) {
                e.preventDefault();
                alert('Debes dibujar una ruta y presionar “Guardar dibujo” antes de guardar la ruta.');
                estadoDibujo.scrollIntoView({ behavior: 'smooth' });
                return false;
            }

            btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Guardando...';
            btnGuardar.disabled = true;
        });

        // Inicializar
        initMap();
        pintarEstadoFalta();
    });
    </script>
@endpush
</x-dinamico-layout>
