class Config {
  // ═══════════════════════════════════════════════════════════════════════
  //  URL del servidor de producción
  // ═══════════════════════════════════════════════════════════════════════
  static const String _baseUrl = 'https://carros-basureros.colcapirhua.gob.bo/api';

  static String get baseUrl => _baseUrl;

  static const int timeout = 30;
}