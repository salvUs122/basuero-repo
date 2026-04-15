import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'dart:async';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:geolocator/geolocator.dart';
import 'config.dart';
import 'gps_background_service.dart';
import 'time_utils.dart';

// ========== DRAWER PERSONALIZADO ==========
class RecorridoDrawer extends StatelessWidget {
  final Map<String, dynamic>? recorridoActivo;
  final VoidCallback onFinalizar;
  final VoidCallback onToggleGPS;
  final bool gpsActivo;
  final String? horaInicio;
  // Parámetros para descarga al botadero
  final bool enDescarga;
  final int numeroDescarga;
  final String? horaInicioDescarga;
  final VoidCallback onIniciarDescarga;
  final VoidCallback onFinalizarDescarga;

  const RecorridoDrawer({
    super.key,
    this.recorridoActivo,
    required this.onFinalizar,
    required this.onToggleGPS,
    required this.gpsActivo,
    this.horaInicio,
    // Nuevos parámetros requeridos
    required this.enDescarga,
    required this.numeroDescarga,
    this.horaInicioDescarga,
    required this.onIniciarDescarga,
    required this.onFinalizarDescarga,
  });

  @override
  Widget build(BuildContext context) {
    return Drawer(
      width: MediaQuery.of(context).size.width * 0.8,
      child: Container(
        color: Colors.white,
        child: SafeArea(
          child: Column(
            children: [
              // Header del drawer
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [Colors.blue, Color.fromARGB(255, 30, 95, 160)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(8),
                          decoration: BoxDecoration(
                            color: Colors.white.withOpacity(0.2),
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(
                            Icons.route,
                            color: Colors.white,
                            size: 24,
                          ),
                        ),
                        const SizedBox(width: 12),
                        const Expanded(
                          child: Text(
                            'RECORRIDO ACTIVO',
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              // Información del recorrido
              Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  children: [
                    _buildDrawerItem(
                      icon: Icons.route,
                      color: Colors.blue,
                      label: 'Ruta',
                      value: recorridoActivo?['ruta'] ?? 'Sin ruta',
                    ),
                    const Divider(),
                    _buildDrawerItem(
                      icon: Icons.local_shipping,
                      color: Colors.green,
                      label: 'Camión',
                      value: recorridoActivo?['camion'] ?? 'N/A',
                    ),
                    const Divider(),
                    _buildDrawerItem(
                      icon: Icons.access_time,
                      color: Colors.purple,
                      label: 'Inicio',
                      value: horaInicio ?? 'N/D',
                    ),
                  ],
                ),
              ),

              const Spacer(),

              // Indicador de descarga activa
              if (enDescarga)
                Container(
                  margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.orange.shade50,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: Colors.orange.shade200),
                  ),
                  child: Row(
                    children: [
                      Icon(Icons.local_shipping, color: Colors.orange.shade700),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'DESCARGA #$numeroDescarga EN CURSO',
                              style: TextStyle(
                                color: Colors.orange.shade900,
                                fontWeight: FontWeight.bold,
                                fontSize: 12,
                              ),
                            ),
                            if (horaInicioDescarga != null)
                              Text(
                                'Inicio: $horaInicioDescarga',
                                style: TextStyle(
                                  color: Colors.orange.shade700,
                                  fontSize: 11,
                                ),
                              ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),

              // Botones de acción
              Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  children: [
                    // Botón de descarga/continuar ruta
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        onPressed: enDescarga ? onFinalizarDescarga : onIniciarDescarga,
                        icon: Icon(enDescarga ? Icons.play_arrow : Icons.local_shipping),
                        label: Text(
                          enDescarga ? 'CONTINUAR RUTA' : 'DESCARGAR AL BOTADERO',
                        ),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: enDescarga ? Colors.green : Colors.orange,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 12),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        onPressed: onToggleGPS,
                        icon: Icon(gpsActivo ? Icons.stop : Icons.satellite),
                        label: Text(
                          gpsActivo
                              ? 'DETENER TRANSMISIÓN GPS'
                              : 'INICIAR TRANSMISIÓN GPS',
                        ),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: gpsActivo
                              ? Colors.red
                              : Colors.green,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 12),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        onPressed: enDescarga ? null : onFinalizar,
                        icon: const Icon(Icons.stop),
                        label: const Text('FINALIZAR RECORRIDO'),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.red,
                          foregroundColor: Colors.white,
                          disabledBackgroundColor: Colors.grey.shade300,
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildDrawerItem({
    required IconData icon,
    required Color color,
    required String label,
    required String value,
  }) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: color.withOpacity(0.1),
            shape: BoxShape.circle,
          ),
          child: Icon(icon, color: color, size: 20),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
              ),
              Text(
                value,
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
}

class DashboardConductor extends StatefulWidget {
  const DashboardConductor({super.key});

  @override
  State<DashboardConductor> createState() => _DashboardConductorState();
}

class _DashboardConductorState extends State<DashboardConductor> {
  // ========== ESTADOS ==========
  bool _isLoading = true;
  bool _tieneRecorridoActivo = false;
  String? _errorMessage;

  // ========== DATOS DEL RECORRIDO ACTIVO ==========
  Map<String, dynamic>? _recorridoActivo;
  List<dynamic> _camiones = [];
  List<dynamic> _rutas = [];
  int? _camionSeleccionadoId;
  int? _rutaSeleccionadaId;
  Map<String, dynamic>? _horarioSeleccionado;

  // ========== MAPA Y GPS ==========
  final GlobalKey<ScaffoldState> _scaffoldKey = GlobalKey<ScaffoldState>();
  final MapController _mapController = MapController();
  StreamSubscription<Position>? _gpsSubscription;
  Position? _currentPosition;
  List<LatLng> _trackPoints = [];
  List<LatLng> _routePoints = [];
  int _puntosEnviados = 0;
  bool _gpsActivo = false;
  String? _horaInicioRecorrido;
  bool _mostrandoRecordatorioFin = false;

  // ========== DESCARGA AL BOTADERO ==========
  bool _enDescarga = false;
  Map<String, dynamic>? _descargaActiva;
  int _numeroDescarga = 0;
  String? _horaInicioDescarga;
  Map<String, dynamic>? _puntoDescarga; // Coordenadas del botadero

  // ========== PARADAS DETECTADAS ==========
  List<Map<String, dynamic>> _paradas = [];
  Timer? _paradasTimer;

  @override
  void initState() {
    super.initState();
    _cargarDatosIniciales();
  }

  Future<void> _cargarDatosIniciales() async {
    setState(() => _isLoading = true);

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');
      final recorridoId = prefs.getInt('recorrido_id');

      if (token == null) {
        _irALogin();
        return;
      }

      await _cargarRecorridoActivo(token);

      if (!_tieneRecorridoActivo) {
        if (recorridoId != null) {
          await prefs.remove('recorrido_id');
        }
        await _cargarCamiones();
      }

      setState(() => _isLoading = false);
    } catch (e) {
      setState(() {
        _errorMessage = 'Error: $e';
        _isLoading = false;
      });
    }
  }

  Future<void> _cargarRecorridoActivo(String token) async {
    try {
      final prefs = await SharedPreferences.getInstance();

      final response = await http.get(
        Uri.parse('${Config.baseUrl}/conductor/recorrido/activo'),
        headers: {'Authorization': 'Bearer $token'},
      );

      if (response.statusCode != 200) {
        return;
      }

      final data = jsonDecode(response.body);
      final activo = data['data'];

      if (activo == null) {
        setState(() {
          _tieneRecorridoActivo = false;
          _recorridoActivo = null;
          _routePoints = [];
          _horaInicioRecorrido = null;
        });
        return;
      }

      final recorridoId = activo['id'] as int;
      await prefs.setInt('recorrido_id', recorridoId);

      setState(() {
        _tieneRecorridoActivo = true;
        _recorridoActivo = {
          'id': recorridoId,
          'camion': activo['camion'] ?? 'N/D',
          'ruta': activo['ruta'] ?? 'N/D',
        };
        _routePoints = _extraerPuntosRuta(activo['geometria']);
        _horaInicioRecorrido = _formatearHora(activo['fecha_inicio']);
        // Guardar punto de descarga (botadero)
        _puntoDescarga = activo['punto_descarga'];
        debugPrint('🗑️ Botadero recibido: $_puntoDescarga');
      });

      _centrarMapaEnRuta();

      if (_routePoints.isEmpty) {
        _mostrarSnackbar('No se pudo cargar la ruta del recorrido');
      }

      // Restaurar trayectoria GPS previa (por si el conductor recargó la página)
      _cargarPuntosExistentes(recorridoId);

      // Verificar si hay descarga activa
      await _verificarDescargaActiva(token);

      // Cargar paradas detectadas e iniciar actualización periódica
      _cargarParadas();
      _iniciarActualizacionParadas();

      // GPS NO se inicia automáticamente - el conductor debe activarlo manualmente
      // _iniciarGPS();  // REMOVIDO: El GPS debe iniciarse manualmente
    } catch (e) {
      debugPrint('Error cargando recorrido activo: $e');
    }
  }

  List<LatLng> _extraerPuntosRuta(dynamic geometria) {
    if (geometria == null) return [];

    if (geometria is String) {
      try {
        geometria = jsonDecode(geometria);
      } catch (_) {
        return [];
      }
    }

    if (geometria is! Map) return [];

    dynamic geometryNode = geometria;

    if (geometryNode['type'] == 'Feature') {
      geometryNode = geometryNode['geometry'];
    } else if (geometryNode['type'] == 'FeatureCollection' &&
        geometryNode['features'] is List &&
        (geometryNode['features'] as List).isNotEmpty) {
      final firstFeature = (geometryNode['features'] as List).first;
      if (firstFeature is Map) {
        geometryNode = firstFeature['geometry'];
      }
    }

    if (geometryNode is! Map) return [];

    final tipo = geometryNode['type'];
    final coordinates = geometryNode['coordinates'];

    if (tipo == 'LineString' && coordinates is List) {
      return coordinates
          .whereType<List>()
          .where((coord) => coord.length >= 2)
          .map(
            (coord) => LatLng(
              (coord[1] as num).toDouble(),
              (coord[0] as num).toDouble(),
            ),
          )
          .toList();
    }

    if (tipo == 'MultiLineString' && coordinates is List) {
      final puntos = <LatLng>[];
      for (final segmento in coordinates.whereType<List>()) {
        for (final coord in segmento.whereType<List>()) {
          if (coord.length >= 2) {
            puntos.add(
              LatLng(
                (coord[1] as num).toDouble(),
                (coord[0] as num).toDouble(),
              ),
            );
          }
        }
      }
      return puntos;
    }

    if (geometria['geometria'] is Map) {
      return _extraerPuntosRuta(geometria['geometria']);
    }

    return [];
  }

  void _centrarMapaEnRuta() {
    if (_routePoints.isEmpty) return;

    final lats = _routePoints.map((p) => p.latitude).toList();
    final lngs = _routePoints.map((p) => p.longitude).toList();

    final minLat = lats.reduce((a, b) => a < b ? a : b);
    final maxLat = lats.reduce((a, b) => a > b ? a : b);
    final minLng = lngs.reduce((a, b) => a < b ? a : b);
    final maxLng = lngs.reduce((a, b) => a > b ? a : b);

    final center = LatLng((minLat + maxLat) / 2, (minLng + maxLng) / 2);

    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (!mounted || _routePoints.isEmpty) return;
      _mapController.move(center, 13);
    });
  }

  String? _formatearHora(dynamic fecha) {
    return TimeUtils.formatHmsCochabamba(fecha);
  }

  // ========== MÉTODOS PARA DESCARGA AL BOTADERO ==========

  /// Verifica si hay descarga activa al cargar datos
  Future<void> _verificarDescargaActiva(String token) async {
    try {
      final response = await http.get(
        Uri.parse('${Config.baseUrl}/conductor/descarga/activa'),
        headers: {'Authorization': 'Bearer $token'},
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['data'] != null) {
          setState(() {
            _enDescarga = true;
            _descargaActiva = data['data'];
            _numeroDescarga = data['data']['numero_descarga'];
            _horaInicioDescarga = _formatearHora(data['data']['fecha_inicio']);
          });
        }
      }
    } catch (e) {
      debugPrint('Error verificando descarga activa: $e');
    }
  }

  /// Inicia una descarga al botadero
  Future<void> _iniciarDescarga() async {
    if (_currentPosition == null) {
      _mostrarSnackbar('Esperando ubicación GPS...');
      return;
    }

    // Verificar si hay punto de descarga configurado
    final tieneBotadero = _puntoDescarga != null;
    final nombreBotadero = _puntoDescarga?['nombre'] ?? 'Botadero';

    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: Colors.orange.shade100,
                shape: BoxShape.circle,
              ),
              child: Icon(Icons.local_shipping, color: Colors.orange.shade600, size: 20),
            ),
            const SizedBox(width: 12),
            const Text('Iniciar descarga'),
          ],
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Vas a registrar el inicio de descarga al botadero. '
              'El GPS seguirá capturando tu ubicación durante el viaje.'
            ),
            if (tieneBotadero) ...[
              const SizedBox(height: 12),
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.orange.shade50,
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(color: Colors.orange.shade200),
                ),
                child: Row(
                  children: [
                    Icon(Icons.place, color: Colors.orange.shade600, size: 20),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        'Destino: $nombreBotadero',
                        style: TextStyle(
                          fontWeight: FontWeight.w600,
                          color: Colors.orange.shade800,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('CANCELAR'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.orange,
              foregroundColor: Colors.white,
            ),
            child: const Text('INICIAR DESCARGA'),
          ),
        ],
      ),
    );

    if (confirm != true) return;

    setState(() => _isLoading = true);

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      final response = await http.post(
        Uri.parse('${Config.baseUrl}/conductor/descarga/iniciar'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: jsonEncode({
          'lat': _currentPosition!.latitude,
          'lng': _currentPosition!.longitude,
        }),
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        setState(() {
          _enDescarga = true;
          _descargaActiva = data['data'];
          _numeroDescarga = data['data']['numero_descarga'];
          _horaInicioDescarga = _formatearHora(data['data']['fecha_inicio']);
        });

        _mostrarSnackbar('Descarga #$_numeroDescarga iniciada');

        // Actualizar notificación del foreground service
        await updateGpsServiceNotification(
          ruta: _recorridoActivo?['ruta'],
          camion: _recorridoActivo?['camion'],
          horaInicio: _horaInicioRecorrido,
          enDescarga: true,
          numeroDescarga: _numeroDescarga,
        );

        // Centrar mapa para mostrar posición actual y botadero
        if (tieneBotadero) {
          _centrarMapaEnDescarga();
        }
      } else {
        _mostrarSnackbar(data['message'] ?? 'Error al iniciar descarga');
      }
    } catch (e) {
      _mostrarSnackbar('Error: $e');
    } finally {
      setState(() => _isLoading = false);
    }
  }

  /// Centra el mapa para mostrar la posición actual y el botadero
  void _centrarMapaEnDescarga() {
    if (_currentPosition == null || _puntoDescarga == null) return;

    final currentLat = _currentPosition!.latitude;
    final currentLng = _currentPosition!.longitude;
    final botaderoLat = (_puntoDescarga!['lat'] as num).toDouble();
    final botaderoLng = (_puntoDescarga!['lng'] as num).toDouble();

    // Calcular el centro entre los dos puntos
    final centerLat = (currentLat + botaderoLat) / 2;
    final centerLng = (currentLng + botaderoLng) / 2;

    // Calcular la distancia para determinar el zoom
    final distance = Geolocator.distanceBetween(
      currentLat, currentLng, botaderoLat, botaderoLng
    );

    // Determinar zoom basado en la distancia
    double zoom;
    if (distance < 500) {
      zoom = 16;
    } else if (distance < 1000) {
      zoom = 15;
    } else if (distance < 2000) {
      zoom = 14;
    } else if (distance < 5000) {
      zoom = 13;
    } else if (distance < 10000) {
      zoom = 12;
    } else {
      zoom = 11;
    }

    // Mover el mapa al centro con el zoom calculado
    _mapController.move(LatLng(centerLat, centerLng), zoom);
  }

  /// Centra el mapa en el botadero
  void _centrarEnBotadero() {
    if (_puntoDescarga == null) return;
    
    final botaderoLat = (_puntoDescarga!['lat'] as num).toDouble();
    final botaderoLng = (_puntoDescarga!['lng'] as num).toDouble();
    
    _mapController.move(LatLng(botaderoLat, botaderoLng), 16);
    _mostrarSnackbar('📍 Botadero: ${_puntoDescarga!['nombre'] ?? 'Botadero'}');
  }

  /// Muestra información del botadero
  void _mostrarInfoBotadero() {
    if (_puntoDescarga == null) return;
    
    final nombreBotadero = _puntoDescarga!['nombre'] ?? 'Botadero';
    final lat = (_puntoDescarga!['lat'] as num).toDouble();
    final lng = (_puntoDescarga!['lng'] as num).toDouble();
    
    // Calcular distancia si tenemos posición actual
    String distanciaTexto = '';
    if (_currentPosition != null) {
      final distancia = Geolocator.distanceBetween(
        _currentPosition!.latitude,
        _currentPosition!.longitude,
        lat,
        lng,
      );
      if (distancia < 1000) {
        distanciaTexto = '${distancia.round()} m';
      } else {
        distanciaTexto = '${(distancia / 1000).toStringAsFixed(1)} km';
      }
    }
    
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: Colors.orange.shade100,
                shape: BoxShape.circle,
              ),
              child: Icon(Icons.delete_rounded, color: Colors.orange.shade600, size: 24),
            ),
            const SizedBox(width: 12),
            const Expanded(child: Text('Botadero')),
          ],
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              nombreBotadero,
              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 8),
            Text(
              'Lat: ${lat.toStringAsFixed(6)}\nLng: ${lng.toStringAsFixed(6)}',
              style: TextStyle(fontSize: 13, color: Colors.grey.shade600),
            ),
            if (distanciaTexto.isNotEmpty) ...[
              const SizedBox(height: 12),
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: Colors.blue.shade50,
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Row(
                  children: [
                    Icon(Icons.directions_car, color: Colors.blue.shade700, size: 20),
                    const SizedBox(width: 8),
                    Text(
                      'Distancia: $distanciaTexto',
                      style: TextStyle(
                        fontWeight: FontWeight.w600,
                        color: Colors.blue.shade700,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('CERRAR'),
          ),
          if (!_enDescarga)
            ElevatedButton.icon(
              onPressed: () {
                Navigator.pop(context);
                _iniciarDescarga();
              },
              icon: const Icon(Icons.local_shipping, size: 18),
              label: const Text('IR AL BOTADERO'),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.orange,
                foregroundColor: Colors.white,
              ),
            ),
        ],
      ),
    );
  }

  /// Finaliza la descarga al botadero
  Future<void> _finalizarDescarga() async {
    if (_currentPosition == null) {
      _mostrarSnackbar('Esperando ubicación GPS...');
      return;
    }

    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: Colors.green.shade100,
                shape: BoxShape.circle,
              ),
              child: Icon(Icons.check_circle, color: Colors.green.shade600, size: 20),
            ),
            const SizedBox(width: 12),
            const Text('Finalizar descarga'),
          ],
        ),
        content: Text(
          'Finalizarás la descarga #$_numeroDescarga. '
          'Continuarás con el recorrido normal de la ruta.'
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('CANCELAR'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.green,
              foregroundColor: Colors.white,
            ),
            child: const Text('CONTINUAR RUTA'),
          ),
        ],
      ),
    );

    if (confirm != true) return;

    setState(() => _isLoading = true);

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      final response = await http.post(
        Uri.parse('${Config.baseUrl}/conductor/descarga/finalizar'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: jsonEncode({
          'lat': _currentPosition!.latitude,
          'lng': _currentPosition!.longitude,
        }),
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        final duracion = data['data']['duracion_minutos'] ?? 0;

        setState(() {
          _enDescarga = false;
          _descargaActiva = null;
          _horaInicioDescarga = null;
        });

        _mostrarSnackbar('Descarga #$_numeroDescarga finalizada (${duracion}min)');

        // Restaurar notificación normal
        await updateGpsServiceNotification(
          ruta: _recorridoActivo?['ruta'],
          camion: _recorridoActivo?['camion'],
          horaInicio: _horaInicioRecorrido,
          enDescarga: false,
        );
      } else {
        _mostrarSnackbar(data['message'] ?? 'Error al finalizar descarga');
      }
    } catch (e) {
      _mostrarSnackbar('Error: $e');
    } finally {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _cargarCamiones() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      final response = await http.get(
        Uri.parse('${Config.baseUrl}/conductor/camiones'),
        headers: {'Authorization': 'Bearer $token'},
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        setState(() {
          _camiones = data['data'] ?? [];
        });
      }
    } catch (e) {
      debugPrint('Error cargando camiones: $e');
    }
  }

  Future<void> _cargarRutasPorCamion(int camionId) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      final response = await http.get(
        Uri.parse('${Config.baseUrl}/conductor/rutas-hoy?camion_id=$camionId'),
        headers: {'Authorization': 'Bearer $token'},
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        setState(() {
          _rutas = data['data'] ?? [];
        });
      }
    } catch (e) {
      debugPrint('Error cargando rutas: $e');
    }
  }

  Future<void> _cargarGeometriaRuta(int rutaId) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      final response = await http.get(
        Uri.parse('${Config.baseUrl}/rutas/$rutaId'),
        headers: {'Authorization': 'Bearer $token'},
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final puntosRuta = _extraerPuntosRuta(data['geometria']);

        setState(() {
          _routePoints = puntosRuta;
        });

        _centrarMapaEnRuta();
      }
    } catch (e) {
      debugPrint('Error cargando geometría: $e');
    }
  }

  Future<void> _iniciarRecorrido() async {
    if (_camionSeleccionadoId == null || _rutaSeleccionadaId == null) {
      _mostrarSnackbar('Selecciona camión y ruta');
      return;
    }

    setState(() => _isLoading = true);

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      final response = await http.post(
        Uri.parse('${Config.baseUrl}/conductor/recorrido/iniciar'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: jsonEncode({
          'camion_id': _camionSeleccionadoId,
          'ruta_id': _rutaSeleccionadaId,
        }),
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        await prefs.setInt('recorrido_id', data['data']['recorrido_id']);

        final rutaSeleccionada = _rutas.firstWhere(
          (r) => r['id'] == _rutaSeleccionadaId,
          orElse: () => null,
        );

        final puntosRutaSeleccionada = rutaSeleccionada != null
            ? _extraerPuntosRuta(rutaSeleccionada['geometria'])
            : <LatLng>[];

        if (puntosRutaSeleccionada.isNotEmpty) {
          setState(() {
            _routePoints = puntosRutaSeleccionada;
          });
          _centrarMapaEnRuta();
        } else {
          await _cargarGeometriaRuta(_rutaSeleccionadaId!);
        }

        // Obtener punto_descarga de la ruta seleccionada
        final rutaSelec = _rutas.firstWhere(
          (r) => r['id'] == _rutaSeleccionadaId,
          orElse: () => {},
        );
        
        setState(() {
          _tieneRecorridoActivo = true;
          _recorridoActivo = {
            'id': data['data']['recorrido_id'],
            'camion': _camiones.firstWhere(
              (c) => c['id'] == _camionSeleccionadoId,
            )['placa'],
            'ruta': rutaSelec['nombre'] ?? 'Ruta',
            'horario': _horarioSeleccionado,
          };
          _horaInicioRecorrido = _formatearHora(
            TimeUtils.nowIsoForCochabamba(),
          );
          // Establecer punto de descarga (botadero) desde la ruta
          _puntoDescarga = rutaSelec['punto_descarga'];
        });

        // GPS NO se inicia automáticamente - el conductor debe activarlo manualmente
        // _iniciarGPS();
        _mostrarSnackbar('✅ Recorrido iniciado. Activa el GPS cuando estés listo.');
      } else {
        _mostrarSnackbar(data['message'] ?? 'Error al iniciar');
      }
    } catch (e) {
      _mostrarSnackbar('Error: $e');
    } finally {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _finalizarRecorrido() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: Colors.red.shade100,
                shape: BoxShape.circle,
              ),
              child: Icon(Icons.flag_rounded, color: Colors.red.shade600, size: 20),
            ),
            const SizedBox(width: 12),
            const Text('Finalizar recorrido'),
          ],
        ),
        content: const Text('¿Estás seguro de que deseas finalizar el recorrido actual?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('CANCELAR'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red,
              foregroundColor: Colors.white,
            ),
            child: const Text('FINALIZAR'),
          ),
        ],
      ),
    );

    if (confirm != true) return;

    setState(() => _isLoading = true);

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');
      final recorridoId = prefs.getInt('recorrido_id');

      debugPrint('\u2705 Finalizando recorrido $recorridoId...');
      debugPrint('\ud83d\udccd URL: ${Config.baseUrl}/conductor/recorrido/finalizar');

      if (token == null) {
        _mostrarSnackbar('Error: No hay sesión activa');
        setState(() => _isLoading = false);
        return;
      }

      final response = await http.post(
        Uri.parse('${Config.baseUrl}/conductor/recorrido/finalizar'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({'recorrido_id': recorridoId}),
      ).timeout(const Duration(seconds: 15));

      debugPrint('\ud83d\udccd Respuesta: ${response.statusCode} - ${response.body}');

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        await prefs.remove('recorrido_id');
        await _detenerGPS();
        _paradasTimer?.cancel();
        
        if (mounted) {
          setState(() {
            _tieneRecorridoActivo = false;
            _recorridoActivo = null;
            _trackPoints.clear();
            _routePoints.clear();
            _puntosEnviados = 0;
            _horaInicioRecorrido = null;
            _camionSeleccionadoId = null;
            _rutaSeleccionadaId = null;
            _currentPosition = null;
            _paradas.clear();
            _puntoDescarga = null;
            _enDescarga = false;
            _descargaActiva = null;
            _numeroDescarga = 0;
            _horaInicioDescarga = null;
          });
        }
        
        await _cargarCamiones();
        _mostrarSnackbar('\u2705 Recorrido finalizado correctamente');
      } else {
        final mensaje = data['message'] ?? 'Error desconocido del servidor';
        _mostrarSnackbar('\u274c $mensaje');
      }
    } on http.ClientException catch (e) {
      debugPrint('\u274c Error de conexión: $e');
      _mostrarSnackbar('Error de conexión. Verifica tu red.');
    } on FormatException catch (e) {
      debugPrint('\u274c Error al procesar respuesta: $e');
      _mostrarSnackbar('Error en respuesta del servidor');
    } catch (e) {
      debugPrint('\u274c Error general: $e');
      _mostrarSnackbar('Error: $e');
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Future<void> _iniciarGPS() async {
    debugPrint('═══════════════════════════════════════════');
    debugPrint('🛰️ INICIANDO GPS...');
    debugPrint('📡 URL del servidor: ${Config.baseUrl}');
    
    final prefs = await SharedPreferences.getInstance();
    final recorridoId = prefs.getInt('recorrido_id');
    final token = prefs.getString('token');
    debugPrint('🔑 Token: ${token != null ? "presente" : "❌ FALTA"}');
    debugPrint('🚗 Recorrido ID: ${recorridoId ?? "❌ FALTA"}');
    debugPrint('═══════════════════════════════════════════');

    if (token == null || recorridoId == null) {
      _mostrarSnackbar('Error: Falta token o recorrido activo');
      return;
    }

    final tienePermiso = await _determinarPosicion();
    if (!tienePermiso) {
      debugPrint('❌ Sin permisos de ubicación');
      return;
    }

    await _gpsSubscription?.cancel();

    if (mounted) {
      setState(() => _gpsActivo = true);
    }

    // Arrancar foreground service (notificación persistente + keep-alive)
    try {
      await startGpsService(
        ruta: _recorridoActivo?['ruta'],
        camion: _recorridoActivo?['camion'],
        horaInicio: _horaInicioRecorrido,
      );
      debugPrint('✅ Foreground service iniciado');
    } catch (e) {
      debugPrint('⚠️ Foreground service no disponible (normal en web): $e');
    }

    // ═══════════════════════════════════════════
    // OBTENER POSICIÓN INICIAL (con fallback)
    // ═══════════════════════════════════════════
    bool obtuvoPosicionInicial = false;
    
    try {
      debugPrint('📍 Obteniendo posición inicial (precisión media)...');
      
      // Usar precisión MEDIA primero para ser más rápido en web
      final posicionInicial = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.medium,
        timeLimit: const Duration(seconds: 30),
      );
      
      debugPrint('✅ Posición inicial: ${posicionInicial.latitude}, ${posicionInicial.longitude}');
      obtuvoPosicionInicial = true;
      
      if (mounted) {
        setState(() {
          _currentPosition = posicionInicial;
          _trackPoints.add(LatLng(posicionInicial.latitude, posicionInicial.longitude));
        });
        
        // Centrar mapa en la posición actual
        _mapController.move(
          LatLng(posicionInicial.latitude, posicionInicial.longitude), 16,
        );
        
      }
      
      // Enviar primera posición al servidor
      await _enviarPosicion(posicionInicial);
      
    } catch (e) {
      debugPrint('⚠️ No se pudo obtener posición inicial: $e');
      if (mounted) {
        _mostrarSnackbar('⚠️ Esperando señal GPS... El stream seguirá intentando.');
      }
      // NO detenemos el GPS, seguimos con el stream
    }

    // ═══════════════════════════════════════════
    // STREAM GPS POR DISTANCIA: envía cada 10 metros
    // Esto seguirá funcionando aunque falle la posición inicial
    // ═══════════════════════════════════════════
    debugPrint('📡 Iniciando stream de ubicación...');
    
    _gpsSubscription = Geolocator.getPositionStream(
      locationSettings: const LocationSettings(
        accuracy: LocationAccuracy.high,
        distanceFilter: 10,
      ),
    ).listen(
      (Position position) {
        if (!mounted || !_gpsActivo) return;

        debugPrint('📍 Nueva posición: ${position.latitude}, ${position.longitude}');

        final esNuevaPosicion = _currentPosition == null ||
            _currentPosition!.latitude != position.latitude ||
            _currentPosition!.longitude != position.longitude;

        setState(() {
          _currentPosition = position;
          if (esNuevaPosicion) {
            _trackPoints.add(LatLng(position.latitude, position.longitude));
          }
        });
        
        // Centrar mapa en nueva posición
        _mapController.move(
          LatLng(position.latitude, position.longitude), 16,
        );

        if (!obtuvoPosicionInicial) {
          obtuvoPosicionInicial = true;
        }

        _enviarPosicion(position);

        // Verificar si llegó al final de la ruta
        _verificarFinDeRuta(position);
      },
      onError: (error) {
        debugPrint('❌ Error en stream GPS: $error');
        if (mounted) {
          _mostrarSnackbar('⚠️ Error GPS: Verifica permisos de ubicación');
        }
      },
    );
  }

  Future<void> _detenerGPS() async {
    await _gpsSubscription?.cancel();
    _gpsSubscription = null;

    // Detener el foreground service (quita la notificación)
    await stopGpsService();

    if (mounted) {
      setState(() => _gpsActivo = false);
    }
  }

  Future<void> _enviarPosicion(Position position) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');
      final recorridoId = prefs.getInt('recorrido_id');

      // Validar que tengamos los datos necesarios
      if (token == null || recorridoId == null) {
        debugPrint('❌ GPS: No hay token ($token) o recorrido_id ($recorridoId)');
        return;
      }

      debugPrint('📍 Enviando GPS: lat=${position.latitude}, lng=${position.longitude}');
      debugPrint('📍 URL: ${Config.baseUrl}/conductor/gps');
      debugPrint('📍 Recorrido ID: $recorridoId');

      final response = await http.post(
        Uri.parse('${Config.baseUrl}/conductor/gps'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'recorrido_id': recorridoId,
          'lat': position.latitude,
          'lng': position.longitude,
          'precision_m': position.accuracy,
          'velocidad_mps': position.speed,
          'fecha_gps': TimeUtils.nowIsoForCochabamba(),
        }),
      ).timeout(const Duration(seconds: 10));

      debugPrint('📍 Respuesta GPS: ${response.statusCode} - ${response.body}');

      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = jsonDecode(response.body);
        // Solo incrementar si el punto realmente se guardó (no omitido)
        if (data['message'] == 'Punto guardado') {
          if (mounted) setState(() => _puntosEnviados++);
          debugPrint('✅ GPS guardado. Total puntos: $_puntosEnviados');
        } else {
          debugPrint('⏭️ Punto omitido (distancia < 10m)');
        }
      } else {
        debugPrint('❌ Error del servidor: ${response.statusCode} - ${response.body}');
        // Mostrar error al usuario si es un error crítico
        if (mounted && response.statusCode == 409) {
          _mostrarSnackbar('No hay recorrido activo en el servidor');
        }
      }
    } catch (e) {
      debugPrint('❌ Error de red enviando posición: $e');
      // No mostrar snackbar por cada error para no molestar
    }
  }

  /// Verifica si el conductor llegó cerca del final de la ruta (bandera)
  /// y muestra un recordatorio para finalizar el recorrido.
  void _verificarFinDeRuta(Position position) {
    if (_routePoints.isEmpty || _mostrandoRecordatorioFin) return;

    final finRuta = _routePoints.last;
    const distanciaUmbral = 80.0; // metros

    final distancia = Geolocator.distanceBetween(
      position.latitude,
      position.longitude,
      finRuta.latitude,
      finRuta.longitude,
    );

    if (distancia <= distanciaUmbral) {
      _mostrandoRecordatorioFin = true;
      showDialog(
        context: context,
        barrierDismissible: false,
        builder: (context) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          title: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: Colors.green.shade100,
                  shape: BoxShape.circle,
                ),
                child: Icon(Icons.flag_rounded, color: Colors.green.shade700, size: 24),
              ),
              const SizedBox(width: 12),
              const Expanded(child: Text('¡Llegaste al final!')),
            ],
          ),
          content: const Text(
            'Estás cerca del punto final de la ruta. '
            '¿Deseas finalizar el recorrido?',
          ),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.pop(context);
                // Permitir que vuelva a mostrarse si se aleja y vuelve
                Future.delayed(const Duration(minutes: 2), () {
                  if (mounted) _mostrandoRecordatorioFin = false;
                });
              },
              child: const Text('CONTINUAR'),
            ),
            ElevatedButton.icon(
              onPressed: () {
                Navigator.pop(context);
                _finalizarRecorrido();
              },
              icon: const Icon(Icons.stop, size: 18),
              label: const Text('FINALIZAR'),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.red,
                foregroundColor: Colors.white,
              ),
            ),
          ],
        ),
      );
    }
  }

  /// Carga los puntos GPS ya guardados en el servidor para restaurar
  /// la trayectoria visual cuando el conductor recargó la página.
  Future<void> _cargarPuntosExistentes(int recorridoId) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');
      if (token == null) return;

      final response = await http.get(
        Uri.parse('${Config.baseUrl}/conductor/recorrido/puntos'),
        headers: {'Authorization': 'Bearer $token'},
      ).timeout(const Duration(seconds: 15));

      if (response.statusCode != 200) return;

      final data = jsonDecode(response.body);
      if (data['success'] != true) return;

      final List<dynamic> puntos = data['data'] ?? [];
      if (puntos.isEmpty) return;

      final List<LatLng> restored = puntos
          .map((p) => LatLng((p['lat'] as num).toDouble(), (p['lng'] as num).toDouble()))
          .toList();

      if (!mounted) return;
      setState(() {
        _trackPoints = restored;
        _puntosEnviados = restored.length;
      });

      // Centrar mapa en el último punto conocido
      if (restored.isNotEmpty) {
        WidgetsBinding.instance.addPostFrameCallback((_) {
          if (!mounted) return;
          _mapController.move(restored.last, 16);
        });
      }
    } catch (e) {
      debugPrint('Error restaurando puntos GPS: \$e');
    }
  }

  // ========== PARADAS DETECTADAS ==========

  /// Inicia la actualización periódica de paradas cada 30 segundos
  void _iniciarActualizacionParadas() {
    _paradasTimer?.cancel();
    _paradasTimer = Timer.periodic(const Duration(seconds: 30), (_) {
      _cargarParadas();
    });
  }

  /// Carga las paradas detectadas del servidor
  Future<void> _cargarParadas() async {
    if (!_tieneRecorridoActivo) return;

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');
      if (token == null) return;

      final response = await http.get(
        Uri.parse('${Config.baseUrl}/conductor/recorrido/paradas'),
        headers: {'Authorization': 'Bearer $token'},
      ).timeout(const Duration(seconds: 15));

      if (response.statusCode != 200) return;

      final data = jsonDecode(response.body);
      if (data['success'] != true) return;

      final List<dynamic> paradasData = data['data'] ?? [];

      if (!mounted) return;
      setState(() {
        _paradas = paradasData.map((p) => Map<String, dynamic>.from(p)).toList();
      });

      debugPrint('⏱️ Paradas cargadas: ${_paradas.length}');
    } catch (e) {
      debugPrint('Error cargando paradas: $e');
    }
  }

  /// Muestra información de una parada específica
  void _mostrarInfoParada(Map<String, dynamic> parada, int index) {
    final lat = (parada['lat'] as num).toDouble();
    final lng = (parada['lng'] as num).toDouble();
    final duracion = parada['duracion'] ?? 'N/A';
    final inicio = parada['inicio'] ?? 'N/A';
    final fin = parada['fin'] ?? 'N/A';

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: Colors.amber.shade100,
                shape: BoxShape.circle,
              ),
              child: Icon(Icons.timer, color: Colors.amber.shade700, size: 24),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                '⏱️ Parada #${index + 1}',
                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
              ),
            ),
          ],
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Duración destacada
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 12),
              decoration: BoxDecoration(
                color: Colors.amber.shade50,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: Colors.amber.shade200),
              ),
              child: Column(
                children: [
                  const Text('Tiempo detenido', style: TextStyle(color: Colors.black54, fontSize: 13)),
                  const SizedBox(height: 4),
                  Text(
                    duracion,
                    style: TextStyle(
                      fontSize: 28,
                      fontWeight: FontWeight.bold,
                      color: Colors.amber.shade800,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),
            // Horarios
            _infoRow(Icons.play_circle_outline, 'Inicio', inicio, Colors.green),
            const SizedBox(height: 8),
            _infoRow(Icons.stop_circle_outlined, 'Fin', fin, Colors.red),
            const SizedBox(height: 12),
            const Divider(),
            const SizedBox(height: 8),
            // Coordenadas
            Text(
              '📍 Lat: ${lat.toStringAsFixed(6)}',
              style: const TextStyle(fontSize: 13, color: Colors.black54),
            ),
            Text(
              '📍 Lng: ${lng.toStringAsFixed(6)}',
              style: const TextStyle(fontSize: 13, color: Colors.black54),
            ),
          ],
        ),
        actions: [
          TextButton.icon(
            onPressed: () {
              Navigator.pop(context);
              _mapController.move(LatLng(lat, lng), 18);
            },
            icon: const Icon(Icons.center_focus_strong),
            label: const Text('Centrar'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Cerrar'),
          ),
        ],
      ),
    );
  }

  Widget _infoRow(IconData icon, String label, String value, Color color) {
    return Row(
      children: [
        Icon(icon, size: 20, color: color),
        const SizedBox(width: 8),
        Text('$label: ', style: const TextStyle(color: Colors.black54)),
        Text(value, style: const TextStyle(fontWeight: FontWeight.w600)),
      ],
    );
  }

  Future<bool> _determinarPosicion() async {
    debugPrint('🔐 Verificando permisos de ubicación...');
    
    bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      debugPrint('❌ Servicio GPS deshabilitado');
      _mostrarSnackbar('⚠️ Activa el GPS en tu dispositivo');
      return false;
    }
    debugPrint('✅ Servicio GPS habilitado');

    LocationPermission permission = await Geolocator.checkPermission();
    debugPrint('📋 Permiso actual: $permission');
    
    if (permission == LocationPermission.denied) {
      debugPrint('🔄 Solicitando permiso de ubicación...');
      permission = await Geolocator.requestPermission();
      debugPrint('📋 Nuevo permiso: $permission');
      
      if (permission == LocationPermission.denied) {
        debugPrint('❌ Permiso denegado por el usuario');
        await _mostrarDialogoPermisos();
        return false;
      }
    }

    if (permission == LocationPermission.deniedForever) {
      debugPrint('❌ Permiso denegado permanentemente');
      await _mostrarDialogoPermisos();
      return false;
    }

    debugPrint('✅ Permisos de ubicación OK');
    return true;
  }

  Future<void> _mostrarDialogoPermisos() async {
    await showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: Colors.orange.shade100,
                shape: BoxShape.circle,
              ),
              child: Icon(Icons.location_off, color: Colors.orange.shade700, size: 24),
            ),
            const SizedBox(width: 12),
            const Expanded(child: Text('Ubicación Requerida')),
          ],
        ),
        content: const Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Para usar el GPS necesitas permitir el acceso a tu ubicación.'),
            SizedBox(height: 16),
            Text('📱 En el navegador:', style: TextStyle(fontWeight: FontWeight.bold)),
            SizedBox(height: 8),
            Text('1. Haz clic en el ícono de candado 🔒 junto a la URL'),
            Text('2. Busca "Ubicación" y cámbialo a "Permitir"'),
            Text('3. Recarga la página (F5)'),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('ENTENDIDO'),
          ),
          ElevatedButton.icon(
            onPressed: () async {
              Navigator.pop(context);
              // Intentar abrir configuración (funciona en móvil)
              await Geolocator.openAppSettings();
            },
            icon: const Icon(Icons.settings, size: 18),
            label: const Text('ABRIR CONFIG'),
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF1e40af),
              foregroundColor: Colors.white,
            ),
          ),
        ],
      ),
    );
  }

  void _irALogin() {
    Navigator.pushReplacementNamed(context, '/login');
  }

  void _mostrarSnackbar(String mensaje) {
    ScaffoldMessenger.of(
      context,
    ).showSnackBar(SnackBar(content: Text(mensaje)));
  }

  Future<void> _cerrarSesion() async {
    final prefs = await SharedPreferences.getInstance();
    await _detenerGPS();
    await prefs.remove('token');
    await prefs.remove('recorrido_id');
    _irALogin();
  }

  @override
  void dispose() {
    _gpsSubscription?.cancel();
    _paradasTimer?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      key: _scaffoldKey,
      appBar: AppBar(
        title: const Text(
          'GeoFlota · Conductor',
          style: TextStyle(
            fontWeight: FontWeight.w800,
            fontSize: 17,
            letterSpacing: .4,
          ),
        ),
        flexibleSpace: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [Color(0xFF0f2557), Color(0xFF1e40af), Color(0xFF1d4ed8)],
            ),
          ),
        ),
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        elevation: 4,
        shadowColor: const Color(0xFF1e40af).withOpacity(.5),
        leading: _tieneRecorridoActivo
            ? IconButton(
                icon: const Icon(Icons.menu_rounded),
                onPressed: () => _scaffoldKey.currentState?.openDrawer(),
              )
            : null,
        actions: [
          if (_gpsActivo)
            Container(
              margin: const EdgeInsets.only(right: 4),
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(
                color: Colors.green.withOpacity(.25),
                borderRadius: BorderRadius.circular(999),
                border: Border.all(color: Colors.greenAccent.withOpacity(.6)),
              ),
              child: const Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(Icons.satellite_alt, size: 14, color: Colors.greenAccent),
                  SizedBox(width: 4),
                  Text('GPS', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: Colors.greenAccent)),
                ],
              ),
            ),
          IconButton(
            icon: const Icon(Icons.logout_rounded),
            tooltip: 'Cerrar sesión',
            onPressed: _cerrarSesion,
          ),
        ],
      ),
      drawer: _tieneRecorridoActivo
          ? RecorridoDrawer(
              recorridoActivo: _recorridoActivo,
              onFinalizar: _finalizarRecorrido,
              onToggleGPS: () {
                if (_gpsActivo) {
                  _detenerGPS();
                } else {
                  _iniciarGPS();
                }
              },
              gpsActivo: _gpsActivo,
              horaInicio: _horaInicioRecorrido,
              // Parámetros de descarga
              enDescarga: _enDescarga,
              numeroDescarga: _numeroDescarga,
              horaInicioDescarga: _horaInicioDescarga,
              onIniciarDescarga: _iniciarDescarga,
              onFinalizarDescarga: _finalizarDescarga,
            )
          : null,
      body: _isLoading
          ? Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: const Color(0xFF1e40af).withOpacity(.1),
                      shape: BoxShape.circle,
                    ),
                    child: const CircularProgressIndicator(
                      color: Color(0xFF1e40af),
                      strokeWidth: 3,
                    ),
                  ),
                  const SizedBox(height: 16),
                  const Text('Cargando datos...',
                    style: TextStyle(color: Color(0xFF1e40af), fontWeight: FontWeight.w600)),
                ],
              ),
            )
          : _errorMessage != null
          ? Center(
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(18),
                      decoration: BoxDecoration(
                        color: Colors.red.shade50,
                        shape: BoxShape.circle,
                      ),
                      child: Icon(Icons.error_outline, color: Colors.red.shade400, size: 42),
                    ),
                    const SizedBox(height: 16),
                    Text(_errorMessage!,
                      textAlign: TextAlign.center,
                      style: TextStyle(color: Colors.grey.shade700)),
                    const SizedBox(height: 20),
                    ElevatedButton.icon(
                      onPressed: _cargarDatosIniciales,
                      icon: const Icon(Icons.refresh_rounded),
                      label: const Text('Reintentar'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF1e40af),
                        foregroundColor: Colors.white,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                    ),
                  ],
                ),
              ),
            )
          : _tieneRecorridoActivo
          ? _buildRecorridoActivo()
          : _buildSinRecorrido(),
    );
  }

  Widget _buildSinRecorrido() {
    return Container(
      color: const Color(0xFFF8FAFC),
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [

            // ── Banner ──
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(22),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFF0f2557), Color(0xFF1e40af)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: const Color(0xFF1e40af).withOpacity(.35),
                    blurRadius: 20,
                    offset: const Offset(0, 8),
                  ),
                ],
              ),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(.15),
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(Icons.add_road_rounded, color: Colors.white, size: 30),
                  ),
                  const SizedBox(width: 16),
                  const Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Sin recorrido activo',
                          style: TextStyle(color: Colors.white70, fontSize: 12, fontWeight: FontWeight.w500)),
                        SizedBox(height: 2),
                        Text('Inicia un nuevo recorrido',
                          style: TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w900)),
                      ],
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 22),

            // ── Formulario ──
            Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(color: Colors.black.withOpacity(.06), blurRadius: 16, offset: const Offset(0, 6)),
                ],
              ),
              padding: const EdgeInsets.all(22),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Configurar Recorrido',
                    style: TextStyle(fontSize: 17, fontWeight: FontWeight.w800, color: Color(0xFF1e3a5f))),
                  const SizedBox(height: 4),
                  Text('Selecciona camión y ruta asignada',
                    style: TextStyle(fontSize: 13, color: Colors.grey.shade500)),
                  const SizedBox(height: 22),

                  // Camión
                  Text('Camión', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: Colors.grey.shade700)),
                  const SizedBox(height: 8),
                  DropdownButtonFormField<int>(
                    value: _camionSeleccionadoId,
                    items: _camiones.map<DropdownMenuItem<int>>((camion) {
                      return DropdownMenuItem<int>(
                        value: camion['id'] as int,
                        child: Text('${camion['placa']} · ${camion['codigo']}'),
                      );
                    }).toList(),
                    onChanged: (value) {
                      setState(() {
                        _camionSeleccionadoId = value;
                        _rutas.clear();
                        _rutaSeleccionadaId = null;
                      });
                      if (value != null) _cargarRutasPorCamion(value);
                    },
                    decoration: InputDecoration(
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(14)),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(14),
                        borderSide: BorderSide(color: Colors.grey.shade200),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(14),
                        borderSide: const BorderSide(color: Color(0xFF1e40af), width: 1.5),
                      ),
                      filled: true,
                      fillColor: Colors.grey.shade50,
                      prefixIcon: const Icon(Icons.local_shipping_rounded, color: Color(0xFF1e40af)),
                    ),
                    hint: const Text('-- Selecciona un camión --'),
                  ),

                  const SizedBox(height: 18),

                  // Ruta
                  Text('Ruta asignada', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: Colors.grey.shade700)),
                  const SizedBox(height: 8),
                  if (_rutas.isEmpty && _camionSeleccionadoId != null)
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.amber.shade50,
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: Colors.amber.shade200),
                      ),
                      child: Row(
                        children: [
                          Icon(Icons.schedule_rounded, color: Colors.amber.shade700, size: 24),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text('Sin rutas disponibles',
                                  style: TextStyle(fontWeight: FontWeight.bold, color: Colors.amber.shade800)),
                                const SizedBox(height: 4),
                                Text('No hay rutas programadas para este horario.',
                                  style: TextStyle(fontSize: 12, color: Colors.amber.shade700)),
                              ],
                            ),
                          ),
                        ],
                      ),
                    )
                  else
                    DropdownButtonFormField<int>(
                      value: _rutaSeleccionadaId,
                      items: _rutas.map<DropdownMenuItem<int>>((ruta) {
                        final horario = ruta['horario'];
                        final horaInicio = horario?['inicio'] ?? '--:--';
                        final horaFin = horario?['fin'] ?? '--:--';
                        return DropdownMenuItem<int>(
                          value: ruta['id'] as int,
                          child: Text('${ruta['nombre']} ($horaInicio - $horaFin)'),
                        );
                      }).toList(),
                      onChanged: (value) {
                        setState(() {
                          _rutaSeleccionadaId = value;
                          if (value != null) {
                            _horarioSeleccionado = _rutas.firstWhere((r) => r['id'] == value)['horario'];
                          }
                        });
                      },
                      decoration: InputDecoration(
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(14)),
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(14),
                          borderSide: BorderSide(color: Colors.grey.shade200),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(14),
                          borderSide: const BorderSide(color: Color(0xFF1e40af), width: 1.5),
                        ),
                        filled: true,
                        fillColor: Colors.grey.shade50,
                        prefixIcon: const Icon(Icons.route_rounded, color: Color(0xFF1e40af)),
                      ),
                      hint: const Text('-- Primero selecciona un camión --'),
                    ),

                  if (_horarioSeleccionado != null) ...[  
                    const SizedBox(height: 16),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                      decoration: BoxDecoration(
                        color: const Color(0xFFeff6ff),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: const Color(0xFFbfdbfe)),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.schedule_rounded, color: Color(0xFF1e40af), size: 20),
                          const SizedBox(width: 10),
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text('Horario de hoy',
                                style: TextStyle(fontSize: 11, color: Color(0xFF1e40af), fontWeight: FontWeight.w600)),
                              Text(
                                '${_horarioSeleccionado!['inicio']} – ${_horarioSeleccionado!['fin']}',
                                style: const TextStyle(fontSize: 15, color: Color(0xFF1e40af), fontWeight: FontWeight.w800),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ],

                  const SizedBox(height: 26),

                  SizedBox(
                    width: double.infinity,
                    height: 52,
                    child: ElevatedButton.icon(
                      onPressed: _iniciarRecorrido,
                      icon: const Icon(Icons.play_arrow_rounded, size: 22),
                      label: const Text('INICIAR RECORRIDO',
                        style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800, letterSpacing: .6)),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF1e40af),
                        foregroundColor: Colors.white,
                        elevation: 5,
                        shadowColor: const Color(0xFF1e40af).withOpacity(.45),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                      ),
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 20),

            // ── Instrucciones ──
            Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(color: Colors.black.withOpacity(.05), blurRadius: 14, offset: const Offset(0, 5)),
                ],
              ),
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          color: const Color(0xFFeff6ff),
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: const Icon(Icons.info_outline_rounded, color: Color(0xFF1e40af), size: 18),
                      ),
                      const SizedBox(width: 10),
                      const Text('Instrucciones',
                        style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: Color(0xFF1e3a5f))),
                    ],
                  ),
                  const SizedBox(height: 18),
                  _buildInstruccion('1', 'Selecciona camión y ruta', 'Elige el vehículo y la ruta asignada'),
                  const SizedBox(height: 12),
                  _buildInstruccion('2', 'Inicia el recorrido', 'Comienza el seguimiento GPS automático'),
                  const SizedBox(height: 12),
                  _buildInstruccion('3', 'Activa el GPS', 'Tu ubicación se enviará cada 10 segundos'),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInstruccion(String numero, String titulo, String descripcion) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          width: 26,
          height: 26,
          decoration: const BoxDecoration(
            color: Color(0xFF1e40af),
            shape: BoxShape.circle,
          ),
          child: Center(
            child: Text(
              numero,
              style: const TextStyle(
                color: Colors.white,
                fontWeight: FontWeight.w800,
                fontSize: 13,
              ),
            ),
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(titulo,
                style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: Color(0xFF1e3a5f))),
              Text(descripcion,
                style: TextStyle(fontSize: 12, color: Colors.grey.shade500)),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildRecorridoActivo() {
    return Stack(
      children: [
        FlutterMap(
          mapController: _mapController,
          options: const MapOptions(
            initialCenter: LatLng(-17.3934, -66.1571),
            initialZoom: 13,
          ),
          children: [
            TileLayer(
              urlTemplate: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
              userAgentPackageName: 'com.example.conductor_app',
            ),
            if (_routePoints.isNotEmpty)
              PolylineLayer(
                polylines: [
                  Polyline(points: _routePoints, color: const Color(0xFF3b82f6), strokeWidth: 5),
                ],
              ),
            if (_trackPoints.isNotEmpty)
              PolylineLayer(
                polylines: [
                  Polyline(points: _trackPoints, color: const Color(0xFF10b981), strokeWidth: 4,
                    isDotted: true),
                ],
              ),
            // Línea punteada hacia el botadero cuando está en descarga
            if (_enDescarga && _puntoDescarga != null && _currentPosition != null)
              PolylineLayer(
                polylines: [
                  Polyline(
                    points: [
                      LatLng(_currentPosition!.latitude, _currentPosition!.longitude),
                      LatLng(
                        (_puntoDescarga!['lat'] as num).toDouble(),
                        (_puntoDescarga!['lng'] as num).toDouble(),
                      ),
                    ],
                    color: Colors.orange,
                    strokeWidth: 3,
                    isDotted: true,
                  ),
                ],
              ),
            if (_routePoints.isNotEmpty)
              MarkerLayer(
                markers: [
                  Marker(
                    point: _routePoints.first,
                    width: 40, height: 40,
                    child: Container(
                      decoration: BoxDecoration(
                        color: Colors.green,
                        shape: BoxShape.circle,
                        border: Border.all(color: Colors.white, width: 2.5),
                        boxShadow: [BoxShadow(color: Colors.green.withOpacity(.4), blurRadius: 8)],
                      ),
                      child: const Icon(Icons.play_arrow_rounded, color: Colors.white, size: 20),
                    ),
                  ),
                  Marker(
                    point: _routePoints.last,
                    width: 40, height: 40,
                    child: Container(
                      decoration: BoxDecoration(
                        color: Colors.red,
                        shape: BoxShape.circle,
                        border: Border.all(color: Colors.white, width: 2.5),
                        boxShadow: [BoxShadow(color: Colors.red.withOpacity(.4), blurRadius: 8)],
                      ),
                      child: const Icon(Icons.flag_rounded, color: Colors.white, size: 18),
                    ),
                  ),
                ],
              ),
            // Marcadores de paradas detectadas (amarillo/ámbar)
            if (_paradas.isNotEmpty)
              MarkerLayer(
                markers: _paradas.asMap().entries.map((entry) {
                  final index = entry.key;
                  final parada = entry.value;
                  final lat = (parada['lat'] as num).toDouble();
                  final lng = (parada['lng'] as num).toDouble();
                  final duracion = parada['duracion'] ?? '';
                  
                  return Marker(
                    point: LatLng(lat, lng),
                    width: 50, height: 50,
                    child: GestureDetector(
                      onTap: () => _mostrarInfoParada(parada, index),
                      child: Stack(
                        alignment: Alignment.center,
                        children: [
                          Container(
                            width: 36,
                            height: 36,
                            decoration: BoxDecoration(
                              color: Colors.amber.shade600,
                              shape: BoxShape.circle,
                              border: Border.all(color: Colors.white, width: 2.5),
                              boxShadow: [
                                BoxShadow(
                                  color: Colors.amber.withOpacity(.5),
                                  blurRadius: 8,
                                  spreadRadius: 1,
                                ),
                              ],
                            ),
                            child: const Icon(Icons.timer, color: Colors.white, size: 18),
                          ),
                          // Tooltip con duración
                          Positioned(
                            bottom: 0,
                            child: Container(
                              padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 1),
                              decoration: BoxDecoration(
                                color: Colors.amber.shade800,
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: Text(
                                duracion,
                                style: const TextStyle(
                                  color: Colors.white,
                                  fontSize: 9,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  );
                }).toList(),
              ),
            if (_currentPosition != null)
              MarkerLayer(
                markers: [
                  Marker(
                    point: LatLng(_currentPosition!.latitude, _currentPosition!.longitude),
                    width: 44, height: 44,
                    child: Container(
                      decoration: BoxDecoration(
                        color: const Color(0xFF1e40af),
                        shape: BoxShape.circle,
                        border: Border.all(color: Colors.white, width: 3),
                        boxShadow: [
                          BoxShadow(color: const Color(0xFF1e40af).withOpacity(.5), blurRadius: 12, spreadRadius: 2),
                        ],
                      ),
                      child: const Icon(Icons.directions_bus_rounded, color: Colors.white, size: 20),
                    ),
                  ),
                ],
              ),
            // Marcador del botadero (naranja)
            if (_puntoDescarga != null)
              MarkerLayer(
                markers: [
                  Marker(
                    point: LatLng(
                      (_puntoDescarga!['lat'] as num).toDouble(),
                      (_puntoDescarga!['lng'] as num).toDouble(),
                    ),
                    width: 48, height: 48,
                    child: GestureDetector(
                      onTap: _mostrarInfoBotadero,
                      child: Container(
                        decoration: BoxDecoration(
                          color: _enDescarga ? Colors.orange : Colors.orange.shade400,
                          shape: BoxShape.circle,
                          border: Border.all(color: Colors.white, width: 3),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.orange.withOpacity(_enDescarga ? .6 : .3),
                              blurRadius: _enDescarga ? 16 : 8,
                              spreadRadius: _enDescarga ? 3 : 1,
                            ),
                          ],
                        ),
                        child: Icon(
                          Icons.delete_rounded,
                          color: Colors.white,
                          size: _enDescarga ? 24 : 20,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
          ],
        ),

        // ── Banner de GPS apagado ──
        if (!_gpsActivo)
          Positioned(
            top: 0, left: 0, right: 0,
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              decoration: BoxDecoration(
                color: Colors.amber.shade600,
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.2),
                    blurRadius: 4,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: SafeArea(
                bottom: false,
                child: Row(
                  children: [
                    Icon(Icons.gps_off, color: Colors.white, size: 22),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        mainAxisSize: MainAxisSize.min,
                        children: const [
                          Text('GPS Apagado',
                            style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14)),
                          Text('Abre el menú lateral para activar la transmisión',
                            style: TextStyle(color: Colors.white70, fontSize: 12)),
                        ],
                      ),
                    ),
                    ElevatedButton.icon(
                      onPressed: () => _scaffoldKey.currentState?.openDrawer(),
                      icon: const Icon(Icons.menu, size: 18),
                      label: const Text('Activar'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.white,
                        foregroundColor: Colors.amber.shade700,
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                        textStyle: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),

        // ── Botón flotante para ver/ir al botadero ──
        if (_puntoDescarga != null && !_enDescarga)
          Positioned(
            top: _gpsActivo ? 16 : 90, right: 16,
            child: Column(
              children: [
                FloatingActionButton.small(
                  heroTag: 'btn_botadero',
                  backgroundColor: Colors.orange,
                  onPressed: _centrarEnBotadero,
                  tooltip: 'Ver Botadero',
                  child: const Icon(Icons.delete_rounded, color: Colors.white, size: 20),
                ),
                const SizedBox(height: 8),
                FloatingActionButton.small(
                  heroTag: 'btn_ruta',
                  backgroundColor: Colors.indigo,
                  onPressed: _centrarMapaEnRuta,
                  tooltip: 'Centrar en la ruta',
                  child: const Icon(Icons.route, color: Colors.white, size: 20),
                ),
                const SizedBox(height: 8),
                FloatingActionButton.small(
                  heroTag: 'btn_centrar',
                  backgroundColor: Colors.blue.shade700,
                  onPressed: () {
                    if (_currentPosition != null) {
                      _mapController.move(
                        LatLng(_currentPosition!.latitude, _currentPosition!.longitude),
                        16,
                      );
                    }
                  },
                  tooltip: 'Centrar en mi posición',
                  child: const Icon(Icons.my_location, color: Colors.white, size: 20),
                ),
              ],
            ),
          ),

        // ── Botón de descarga al botadero en la parte inferior ──
        if (_puntoDescarga != null && !_enDescarga)
          Positioned(
            bottom: 100, left: 16, right: 16,
            child: ElevatedButton.icon(
              onPressed: _iniciarDescarga,
              icon: const Icon(Icons.local_shipping, size: 20),
              label: Text(
                'IR AL BOTADERO · ${_puntoDescarga!['nombre'] ?? 'Botadero'}',
                style: const TextStyle(fontWeight: FontWeight.bold),
              ),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.orange,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 20),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                elevation: 6,
                shadowColor: Colors.orange.withOpacity(0.5),
              ),
            ),
          ),

        // ── Botón para continuar ruta (cuando está en descarga) ──
        if (_enDescarga)
          Positioned(
            bottom: 100, left: 16, right: 16,
            child: ElevatedButton.icon(
              onPressed: _finalizarDescarga,
              icon: const Icon(Icons.play_arrow, size: 20),
              label: Text(
                'DESCARGA #$_numeroDescarga EN CURSO · CONTINUAR RUTA',
                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12),
              ),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.green,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 16),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                elevation: 6,
                shadowColor: Colors.green.withOpacity(0.5),
              ),
            ),
          ),

        // ── Overlay info GPS ──
        Positioned(
          bottom: 20, left: 16, right: 16,
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(color: Colors.black.withOpacity(.15), blurRadius: 16, offset: const Offset(0, 6)),
              ],
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                Expanded(
                  child: _buildMapStat(Icons.route,
                      _recorridoActivo?['ruta'] ?? 'N/D', 'Ruta',
                      color: const Color(0xFF1e40af)),
                ),
                _buildMapDivider(),
                Expanded(
                  child: _buildMapStat(Icons.local_shipping,
                      _recorridoActivo?['camion'] ?? 'N/D', 'Camión',
                      color: Colors.green),
                ),
                _buildMapDivider(),
                Expanded(
                  child: _buildMapStat(Icons.access_time_rounded,
                      _horaInicioRecorrido ?? '--:--', 'Inicio',
                      color: Colors.purple),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildMapStat(IconData icon, String value, String label, {required Color color}) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, color: color, size: 20),
        const SizedBox(height: 3),
        Text(value,
          textAlign: TextAlign.center,
          overflow: TextOverflow.ellipsis,
          maxLines: 1,
          style: TextStyle(fontWeight: FontWeight.w800, fontSize: 12, color: color)),
        Text(label, style: TextStyle(fontSize: 10, color: Colors.grey.shade500)),
      ],
    );
  }

  Widget _buildMapDivider() {
    return Container(width: 1, height: 36, color: Colors.grey.shade200);
  }
}
