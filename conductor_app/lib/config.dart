class Config {
  // ═══════════════════════════════════════════════════════════════════════
  //  URL del servidor - CAMBIAR SEGÚN ENTORNO
  // ═══════════════════════════════════════════════════════════════════════

  // LOCAL (para desarrollo)
  static const String _baseUrl = 'http://127.0.0.1:8000/api';

  // PRODUCCIÓN (descomentar para producción)
  // static const String _baseUrl = 'https://carros-basureros.colcapirhua.gob.bo/api';

  static String get baseUrl => _baseUrl;

  static const int timeout = 30;
}