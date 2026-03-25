<x-dinamico-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('recorridos.index') }}" 
                   class="text-gray-600 hover:text-gray-900 transition-colors">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        🗺️ Recorrido #{{ $recorrido->id }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-0.5">
                        {{ $recorrido->fecha_inicio->format('d/m/Y H:i') }} - 
                        {{ $recorrido->conductor?->name ?? 'Sin conductor' }}
                    </p>
                </div>
            </div>
            
            <!-- Badge de estado -->
            @php
                $estado = $recorrido->estado ?? 'finalizado';
                $estados = [
                    'activo' => ['bg-green-100', 'text-green-800', '🟢 En curso'],
                    'finalizado' => ['bg-gray-100', 'text-gray-800', '⏹️ Finalizado'],
                    'incidencia' => ['bg-red-100', 'text-red-800', '⚠️ Incidencia']
                ];
                [$bg, $text, $label] = $estados[$estado] ?? $estados['finalizado'];
            @endphp
            <span class="px-4 py-2 rounded-lg text-sm font-medium {{ $bg }} {{ $text }}">
                {{ $label }}
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Grid de 2 columnas: Mapa + Información -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Columna izquierda: Mapa (ocupa 2/3) -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-map-marked-alt text-blue-600"></i>
                                    <h3 class="font-semibold text-gray-800">Visualización de ruta</h3>
                                </div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <!-- Leyenda de colores -->
                                    <div class="hidden sm:flex items-center gap-2 text-xs text-gray-500 border border-gray-200 rounded-lg px-3 py-1.5 bg-gray-50">
                                        <span class="flex items-center gap-1">
                                            <span class="w-3 h-3 rounded-full bg-green-500 inline-block flex-shrink-0"></span>
                                            En ruta
                                        </span>
                                        <span class="text-gray-300">|</span>
                                        <span class="flex items-center gap-1">
                                            <span class="w-3 h-3 rounded-full bg-red-500 inline-block flex-shrink-0"></span>
                                            Fuera de ruta
                                        </span>
                                        <span class="text-gray-300">|</span>
                                        <span class="flex items-center gap-1">
                                            <span class="inline-block w-5 h-1 bg-blue-500 rounded"></span>
                                            Ruta planificada
                                        </span>
                                        @if($recorrido->descargas && $recorrido->descargas->count() > 0)
                                        <span class="text-gray-300">|</span>
                                        <span class="flex items-center gap-1">
                                            <span class="inline-block w-5 h-0.5 rounded" style="border-bottom: 2px dashed #f97316;"></span>
                                            Descarga
                                        </span>
                                        @endif
                                    </div>
                                    <button id="btn_fullscreen" class="text-sm bg-gray-100 hover:bg-gray-200 px-2 py-1.5 rounded-lg transition-colors" title="Pantalla completa">
                                        <i class="fas fa-expand"></i>
                                    </button>
                                    <button id="btn_centrar" class="text-sm bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition-colors">
                                        <i class="fas fa-crosshairs mr-1"></i> Centrar
                                    </button>
                                    <span id="estado_gps" class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                        Cargando...
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div id="map" class="w-full h-[600px] lg:h-[650px]"></div>
                    </div>
                </div>

                <!-- Columna derecha: Panel de información y estadísticas -->
                <div class="lg:col-span-1 space-y-6">
                    
                    <!-- Tarjeta de información general -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4">
                            <h3 class="text-white font-semibold flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                Información del recorrido
                            </h3>
                        </div>
                        <div class="p-5 space-y-4">
                            <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                                <span class="text-sm text-gray-600">ID Recorrido</span>
                                <span class="text-sm font-mono font-semibold text-gray-900">#{{ $recorrido->id }}</span>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                    <i class="fas fa-route text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500">Ruta asignada</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $recorrido->ruta?->nombre ?? 'Sin ruta' }}</p>
                                    @if($recorrido->ruta)
                                    <p class="text-xs text-gray-600 mt-1">
                                        <span class="bg-blue-50 px-2 py-0.5 rounded-full">
                                            Tolerancia: {{ $recorrido->ruta->tolerancia_metros }}m
                                        </span>
                                    </p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="bg-green-100 p-2 rounded-lg mr-3">
                                    <i class="fas fa-truck text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Camión</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $recorrido->camion?->placa ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-600">{{ $recorrido->camion?->codigo ?? '' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                    <i class="fas fa-user text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Conductor</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $recorrido->conductor?->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-600">{{ $recorrido->conductor?->email ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de estadísticas del recorrido -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-800 to-gray-900 px-5 py-4">
                            <h3 class="text-white font-semibold flex items-center">
                                <i class="fas fa-chart-line mr-2"></i>
                                Estadísticas
                            </h3>
                        </div>
                        <div class="p-5">
                            <div id="stats-container" class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Puntos GPS</span>
                                    <span id="stats_puntos" class="text-lg font-bold text-gray-900">0</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Distancia recorrida</span>
                                    <span id="stats_distancia" class="text-lg font-bold text-gray-900">0 km</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Duración</span>
                                    <span id="stats_duracion" class="text-lg font-bold text-gray-900">
                                        @if($recorrido->fecha_inicio && $recorrido->fecha_fin)
                                            @php
                                                $duracion = $recorrido->fecha_inicio->diff($recorrido->fecha_fin);
                                                $horas = $duracion->h + ($duracion->days * 24);
                                            @endphp
                                            {{ $horas }}h {{ $duracion->i }}min
                                        @else
                                            En curso
                                        @endif
                                    </span>
                                </div>
                                <div class="pt-3 mt-3 border-t border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Fuera de ruta</span>
                                        <span id="stats_fuera_ruta" class="text-lg font-bold text-red-600">0</span>
                                    </div>
                                    <div class="flex justify-between items-center mt-2">
                                        <span class="text-sm text-gray-600">Tolerancia</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $recorrido->ruta?->tolerancia_metros ?? 50 }} m</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de Descargas al Botadero -->
                    @if($recorrido->descargas && $recorrido->descargas->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-600 to-orange-700 px-5 py-4">
                            <h3 class="text-white font-semibold flex items-center">
                                <i class="fas fa-truck-loading mr-2"></i>
                                Descargas al Botadero ({{ $recorrido->descargas->count() }})
                            </h3>
                        </div>
                        <div class="p-5 space-y-3">
                            @foreach($recorrido->descargas as $descarga)
                            <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="text-sm font-semibold text-gray-900">
                                            Descarga #{{ $descarga->numero_descarga }}
                                        </span>
                                        <div class="text-xs text-gray-600 mt-1">
                                            <i class="far fa-clock mr-1"></i>
                                            {{ $descarga->fecha_inicio->format('H:i:s') }}
                                            @if($descarga->fecha_fin)
                                            - {{ $descarga->fecha_fin->format('H:i:s') }}
                                            @endif
                                        </div>
                                        @if($descarga->duracion_minutos)
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            Duración: {{ $descarga->duracion_formateada }}
                                            | {{ $descarga->puntos_durante_descarga }} pts
                                            @if($descarga->distancia_metros)
                                            | {{ number_format($descarga->distancia_metros / 1000, 2) }} km
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                    <div class="flex gap-2">
                                        @if($descarga->estado === 'finalizada')
                                        <button onclick="mostrarDescarga({{ $descarga->id }})"
                                                class="text-xs bg-orange-100 hover:bg-orange-200 text-orange-700 px-3 py-1.5 rounded-lg transition-colors">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            Ver en mapa
                                        </button>
                                        @else
                                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">
                                            En curso
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Tarjeta de exportación -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-5">
                            <div class="flex flex-col gap-3">
                               
                                <a href="#" onclick="exportarCSV({{ $recorrido->id }})"
                                   class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-4 rounded-lg transition-colors flex items-center justify-center">
                                    <i class="fas fa-file-csv mr-2"></i>
                                    Exportar CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-polylinedecorator@1.6.0/leaflet.polylineDecorator.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Configuración inicial
        const MAP_CONFIG = {
            zoomControl: true,
            attributionControl: true
        };

        const map = L.map('map', MAP_CONFIG).setView([-17.7833, -63.1821], 13);
        
        // Capa base - Google Maps sin desplazamiento
        L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            attribution: 'Google Maps'
        }).addTo(map);

        // Variables globales
        let rutaPlanificadaLayer = null;
        let recorridoRealLayer = null;
        let puntosFueraRutaLayer = L.layerGroup().addTo(map);
        let inicioMarker = null;
        let finMarker = null;
        let posicionActualMarker = null;
        
        // Tolerancia
        const TOLERANCIA = {{ (int)($recorrido->ruta?->tolerancia_metros ?? 50) }};
        
        // Cargar ruta planificada (AZUL) - VERSIÓN ORIGINAL QUE FUNCIONABA
        @if($recorrido->ruta && $recorrido->ruta->geometria_geojson)
        try {
            const geojson = JSON.parse('{!! addslashes($recorrido->ruta->geometria_geojson) !!}');

            // ── Aplanar coordenadas independientemente del tipo de geometría ──
            // Soporta: LineString, MultiLineString, Feature, FeatureCollection
            function extraerCoordenadas(gj) {
                if (!gj) return [];
                if (gj.type === 'FeatureCollection') {
                    return gj.features.reduce((acc, f) => acc.concat(extraerCoordenadas(f)), []);
                }
                if (gj.type === 'Feature') return extraerCoordenadas(gj.geometry);
                if (gj.type === 'LineString') return gj.coordinates;
                if (gj.type === 'MultiLineString') {
                    return gj.coordinates.reduce((acc, seg) => acc.concat(seg), []);
                }
                return [];
            }

            const coords = extraerCoordenadas(geojson);

            if (coords.length > 0) {
                // Convertir [lng, lat] → L.latLng(lat, lng)
                const rutaLatLngs = coords.map(c => L.latLng(c[1], c[0]));

                // Dibujar como un polyline sólido y continuo (sin dashArray)
                rutaPlanificadaLayer = L.polyline(rutaLatLngs, {
                    color: '#3b82f6',
                    weight: 5,
                    opacity: 0.85,
                    lineJoin: 'round',
                    lineCap: 'round'
                }).addTo(map);

                // Decorar con flechas de dirección
                try {
                    L.polylineDecorator(rutaLatLngs, {
                        patterns: [{
                            offset: '10%',
                            repeat: '80px',
                            symbol: L.Symbol.arrowHead({
                                pixelSize: 12,
                                polygon: false,
                                pathOptions: { color: '#1d4ed8', weight: 2.5, opacity: 0.9 }
                            })
                        }]
                    }).addTo(map);
                } catch(ed) { /* polylineDecorator opcional */ }

                // Marcador de INICIO (verde)
                inicioMarker = L.marker(rutaLatLngs[0], {
                    icon: L.divIcon({
                        html: '<div style="background:#10b981;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid white;box-shadow:0 4px 8px rgba(0,0,0,0.25);"><i class="fas fa-play" style="color:white;font-size:14px;"></i></div>',
                        iconSize: [36, 36],
                        iconAnchor: [18, 18]
                    })
                }).addTo(map).bindPopup('🏁 <b>Inicio de ruta</b>');

                // Marcador de FIN (rojo)
                finMarker = L.marker(rutaLatLngs[rutaLatLngs.length - 1], {
                    icon: L.divIcon({
                        html: '<div style="background:#ef4444;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid white;box-shadow:0 4px 8px rgba(0,0,0,0.25);"><i class="fas fa-flag-checkered" style="color:white;font-size:14px;"></i></div>',
                        iconSize: [36, 36],
                        iconAnchor: [18, 18]
                    })
                }).addTo(map).bindPopup('🏁 <b>Fin de ruta</b>');
            }

        } catch(e) {
            console.error('Error cargando GeoJSON:', e);
        }
        @endif

        // Funciones de utilidad
        function calcularDistancia(latlngs) {
            if (!latlngs || latlngs.length < 2) return 0;
            let total = 0;
            for (let i = 0; i < latlngs.length - 1; i++) {
                total += latlngs[i].distanceTo(latlngs[i+1]);
            }
            return (total / 1000).toFixed(2);
        }

        function distanciaAPolilinea(p, polyline) {
            if (!polyline || polyline.length < 2) return Infinity;
            let minDist = Infinity;
            for (let i = 0; i < polyline.length - 1; i++) {
                const d = distanceToSegment(p, polyline[i], polyline[i+1]);
                if (d < minDist) minDist = d;
            }
            return minDist;
        }

        function distanceToSegment(p, a, b) {
            const R = 6371000;
            function toCartesian(ll, ref) {
                const x = (ll.lng - ref.lng) * Math.cos(ref.lat * Math.PI/180) * (Math.PI/180) * R;
                const y = (ll.lat - ref.lat) * (Math.PI/180) * R;
                return {x, y};
            }
            const P = toCartesian(p, a);
            const A = toCartesian(a, a);
            const B = toCartesian(b, a);
            
            const AB = {x: B.x - A.x, y: B.y - A.y};
            const AP = {x: P.x - A.x, y: P.y - A.y};
            
            const t = (AP.x * AB.x + AP.y * AB.y) / (AB.x * AB.x + AB.y * AB.y);
            const clampedT = Math.max(0, Math.min(1, t));
            
            const C = {
                x: A.x + clampedT * AB.x,
                y: A.y + clampedT * AB.y
            };
            
            const dx = P.x - C.x;
            const dy = P.y - C.y;
            return Math.sqrt(dx*dx + dy*dy);
        }

        // Cargar puntos del recorrido
        async function cargarPuntos() {
            try {
                const response = await fetch("{{ route('recorridos.puntos', $recorrido) }}");
                const puntos = await response.json();

                if (!puntos || puntos.length === 0) {
                    document.getElementById('estado_gps').innerHTML = '📡 Sin puntos GPS';
                    return;
                }

                const latlngs = puntos.map(p => L.latLng(parseFloat(p.lat), parseFloat(p.lng)));

                // Limpiar capas anteriores (segmentos + circulos)
                puntosFueraRutaLayer.clearLayers();

                const distanciaKm = calcularDistancia(latlngs);
                document.getElementById('stats_distancia').textContent = `${distanciaKm} km`;
                document.getElementById('stats_puntos').textContent = latlngs.length;

                // Determinar estado de cada punto (fuera de ruta = true)
                const rutaLatLngs = rutaPlanificadaLayer ? rutaPlanificadaLayer.getLatLngs() : [];
                const estadoPuntos = latlngs.map(punto => {
                    if (rutaLatLngs.length === 0) return false; // sin ruta definida → todos en ruta
                    return distanciaAPolilinea(punto, rutaLatLngs) > TOLERANCIA;
                });

                let fueraRuta = 0;

                // ── 1. Dibujar segmentos coloreados entre puntos consecutivos ──
                for (let i = 1; i < latlngs.length; i++) {
                    const esFuera = estadoPuntos[i];
                    L.polyline([latlngs[i - 1], latlngs[i]], {
                        color: esFuera ? '#ef4444' : '#22c55e',
                        weight: 3,
                        opacity: 0.85,
                        lineCap: 'round',
                        lineJoin: 'round'
                    }).addTo(puntosFueraRutaLayer);
                }

                // ── 2. Dibujar circulo en cada punto (verde / rojo) ──
                puntos.forEach((p, i) => {
                    const punto = latlngs[i];
                    const esFuera = estadoPuntos[i];
                    if (esFuera) fueraRuta++;

                    const colorBorde  = esFuera ? '#b91c1c' : '#15803d';
                    const colorRelleno = esFuera ? '#ef4444' : '#22c55e';
                    const etiqueta    = esFuera ? '⚠️ Fuera de ruta' : '✅ En ruta';

                    const hora = p.fecha_gps
                        ? `<br><span style="color:#6b7280;">🕐 ${new Date(p.fecha_gps).toLocaleTimeString()}</span>`
                        : '';

                    L.circleMarker(punto, {
                        radius: 5,
                        color: colorBorde,
                        weight: 1.5,
                        fillColor: colorRelleno,
                        fillOpacity: 0.85
                    }).addTo(puntosFueraRutaLayer)
                      .bindPopup(`
                        <div style="font-size:12px;line-height:1.6">
                            <b style="color:${colorRelleno};">${etiqueta}</b><br>
                            Lat: ${parseFloat(p.lat).toFixed(6)}<br>
                            Lng: ${parseFloat(p.lng).toFixed(6)}${hora}
                        </div>
                      `);
                });

                // ── 3. Marcador del camión en la última posición ──
                if (latlngs.length > 0) {
                    const ultimo = latlngs[latlngs.length - 1];
                    if (!posicionActualMarker) {
                        posicionActualMarker = L.marker(ultimo, {
                            icon: L.divIcon({
                                html: '<div style="background:#8b5cf6;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid white;box-shadow:0 4px 12px rgba(0,0,0,0.3);"><i class="fas fa-truck" style="color:white;font-size:16px;"></i></div>',
                                iconSize: [40, 40],
                                iconAnchor: [20, 20]
                            })
                        }).addTo(map).bindPopup('📍 <b>Última ubicación del camión</b>');
                    } else {
                        posicionActualMarker.setLatLng(ultimo);
                    }
                }

                document.getElementById('stats_fuera_ruta').textContent = fueraRuta;
                document.getElementById('estado_gps').innerHTML =
                    `🟢 ${latlngs.length} pts | ${fueraRuta} fuera de ruta | ${distanciaKm} km`;

                // Ajustar vista al cargar por primera vez
                if (!window.vistaInicializada) {
                    if (latlngs.length > 0) {
                        const bounds = L.latLngBounds(latlngs);
                        if (rutaPlanificadaLayer) bounds.extend(rutaPlanificadaLayer.getBounds());
                        map.fitBounds(bounds, { padding: [50, 50] });
                    } else if (rutaPlanificadaLayer) {
                        map.fitBounds(rutaPlanificadaLayer.getBounds(), { padding: [50, 50] });
                    }
                    window.vistaInicializada = true;
                }

            } catch(error) {
                console.error('Error:', error);
                document.getElementById('estado_gps').innerHTML = '❌ Error cargando puntos';
            }
        }

        // Botón centrar — usa los límites de todos los layers visibles
        document.getElementById('btn_centrar').addEventListener('click', () => {
            const bounds = L.latLngBounds();
            if (rutaPlanificadaLayer) bounds.extend(rutaPlanificadaLayer.getBounds());
            puntosFueraRutaLayer.eachLayer(layer => {
                if (layer.getLatLng) bounds.extend(layer.getLatLng());
                else if (layer.getLatLngs) layer.getLatLngs().forEach(ll => bounds.extend(ll));
            });
            if (bounds.isValid()) map.fitBounds(bounds, { padding: [50, 50] });
        });

        // Inicializar
        cargarPuntos();

        @if($recorrido->estado === 'activo')
        setInterval(cargarPuntos, 10000);
        @endif

        // ========== VISUALIZACIÓN DE DESCARGAS ==========
        window.descargaLayers = {};
        window.descargaActiva = null;
        window.mapRef = map;
    });

    // Mostrar/ocultar descarga en el mapa
    async function mostrarDescarga(descargaId) {
        const map = window.mapRef;

        // Si ya está mostrada, ocultar
        if (window.descargaActiva === descargaId) {
            ocultarDescarga(descargaId);
            return;
        }

        // Ocultar descarga anterior si existe
        if (window.descargaActiva !== null) {
            ocultarDescarga(window.descargaActiva);
        }

        window.descargaActiva = descargaId;

        // Verificar si ya tenemos los puntos en cache
        if (window.descargaLayers[descargaId]) {
            window.descargaLayers[descargaId].addTo(map);
            map.fitBounds(window.descargaLayers[descargaId].getBounds(), { padding: [50, 50] });
            return;
        }

        try {
            const response = await fetch(`{{ url('/recorridos') }}/{{ $recorrido->id }}/descargas/${descargaId}/puntos`);
            const puntos = await response.json();

            if (!puntos || puntos.length === 0) {
                alert('No hay puntos GPS para esta descarga');
                return;
            }

            // Crear layer group para la descarga
            const layerGroup = L.layerGroup();

            // Convertir puntos a LatLng
            const latlngs = puntos.map(p => L.latLng(parseFloat(p.lat), parseFloat(p.lng)));

            // Dibujar línea naranja punteada
            const polyline = L.polyline(latlngs, {
                color: '#f97316',
                weight: 4,
                opacity: 0.9,
                dashArray: '10, 10',
                lineCap: 'round',
            }).addTo(layerGroup);

            // Marcador de inicio de descarga (naranja)
            L.marker(latlngs[0], {
                icon: L.divIcon({
                    html: '<div style="background:#ea580c;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid white;box-shadow:0 4px 8px rgba(0,0,0,0.25);"><i class="fas fa-arrow-down" style="color:white;font-size:12px;"></i></div>',
                    iconSize: [32, 32],
                    iconAnchor: [16, 16]
                })
            }).addTo(layerGroup).bindPopup('🚛 <b>Inicio descarga</b>');

            // Marcador de fin de descarga (verde)
            L.marker(latlngs[latlngs.length - 1], {
                icon: L.divIcon({
                    html: '<div style="background:#16a34a;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid white;box-shadow:0 4px 8px rgba(0,0,0,0.25);"><i class="fas fa-arrow-up" style="color:white;font-size:12px;"></i></div>',
                    iconSize: [32, 32],
                    iconAnchor: [16, 16]
                })
            }).addTo(layerGroup).bindPopup('✅ <b>Fin descarga</b>');

            // Guardar en cache y agregar al mapa
            window.descargaLayers[descargaId] = layerGroup;
            layerGroup.addTo(map);

            // Ajustar vista
            map.fitBounds(polyline.getBounds(), { padding: [50, 50] });

        } catch (error) {
            console.error('Error cargando puntos de descarga:', error);
            alert('Error al cargar los puntos de la descarga');
        }
    }

    function ocultarDescarga(descargaId) {
        const map = window.mapRef;
        if (window.descargaLayers[descargaId] && map.hasLayer(window.descargaLayers[descargaId])) {
            map.removeLayer(window.descargaLayers[descargaId]);
        }
        window.descargaActiva = null;
    }

    // Funciones de exportación
    function exportarCSV(id) {
        window.location.href = `/recorridos/${id}/exportar/csv`;
    }

    // Función para pantalla completa
    function toggleFullscreen() {
        const mapContainer = document.getElementById('map');
        
        if (!document.fullscreenElement) {
            if (mapContainer.requestFullscreen) {
                mapContainer.requestFullscreen();
            } else if (mapContainer.webkitRequestFullscreen) {
                mapContainer.webkitRequestFullscreen();
            } else if (mapContainer.msRequestFullscreen) {
                mapContainer.msRequestFullscreen();
            }
            document.getElementById('btn_fullscreen').innerHTML = '<i class="fas fa-compress"></i>';
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
            document.getElementById('btn_fullscreen').innerHTML = '<i class="fas fa-expand"></i>';
        }
    }

    document.getElementById('btn_fullscreen').addEventListener('click', toggleFullscreen);

    document.addEventListener('fullscreenchange', () => {
        const btn = document.getElementById('btn_fullscreen');
        if (btn) {
            btn.innerHTML = document.fullscreenElement ? '<i class="fas fa-compress"></i>' : '<i class="fas fa-expand"></i>';
        }
    });
    </script>

    <style>
        .leaflet-popup-content {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            margin: 12px 16px;
        }
        
        .custom-div-icon {
            background: none;
            border: none;
        }
        
        .leaflet-control-zoom {
            border: none !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
        }
        
        .leaflet-control-zoom a {
            background-color: white !important;
            color: #374151 !important;
            border-radius: 8px !important;
            margin: 2px !important;
            width: 36px !important;
            height: 36px !important;
            line-height: 36px !important;
            font-size: 18px !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
        }
        
        .leaflet-control-zoom a:hover {
            background-color: #f3f4f6 !important;
        }
    </style>
</x-dinamico-layout>