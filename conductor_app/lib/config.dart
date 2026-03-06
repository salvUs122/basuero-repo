import 'dart:io' show Platform;
import 'package:flutter/foundation.dart' show kIsWeb;

class Config {
  // ═══════════════════════════════════════════════════════════════════════
  //  CONFIGURACIÓN DE URL DEL SERVIDOR LARAVEL
  // ═══════════════════════════════════════════════════════════════════════
  //
  //  🔴 IMPORTANTE: Cambia _serverIP a la IP de tu computadora en la red
  //     Para encontrar tu IP:
  //     - Windows: ejecuta "ipconfig" en CMD y busca "IPv4 Address"
  //     - Mac/Linux: ejecuta "ifconfig" o "ip addr"
  //
  //  📱 Si corres en dispositivo Android real conectado al mismo WiFi,
  //     usa la IP de tu computadora (ej: 192.168.1.XX)
  //
  //  🖥️ Si corres en Chrome (web) local, usa 127.0.0.1
  //
  //  🤖 Si corres en emulador Android, usa 10.0.2.2
  //
  // ═══════════════════════════════════════════════════════════════════════
  
  // 🔧 CAMBIA ESTA IP según tu red local:
  static const String _serverIP = '192.168.100.119'; // ← IP de tu computadora
  static const int _serverPort = 8000;

  static String get baseUrl {
    // Si es web (Chrome), usar localhost
    if (kIsWeb) {
      return 'http://127.0.0.1:$_serverPort/api';
    }
    
    // Si es Android emulador, usar IP especial
    // Para dispositivo real, usar _serverIP
    try {
      if (Platform.isAndroid) {
        // En debug mode con emulador, puedes usar 10.0.2.2
        // En dispositivo real, debes usar la IP real del servidor
        return 'http://$_serverIP:$_serverPort/api';
      }
      if (Platform.isIOS) {
        // iOS simulador usa localhost, dispositivo real usa IP servidor
        return 'http://$_serverIP:$_serverPort/api';
      }
    } catch (e) {
      // Platform no disponible en web
    }
    
    return 'http://127.0.0.1:$_serverPort/api';
  }
  
  static const int timeout = 30;
}