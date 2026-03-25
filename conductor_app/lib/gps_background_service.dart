import 'dart:async';
import 'dart:isolate';
import 'package:flutter/foundation.dart' show kIsWeb, debugPrint;
import 'package:flutter_foreground_task/flutter_foreground_task.dart';

// ═══════════════════════════════════════════════════════
//  Inicializar la configuración del Foreground Task
//  SOLO mantiene la notificación persistente y evita
//  que Android mate el proceso. La lógica GPS real
//  corre en el isolate principal (dashboard).
//  
//  NOTA: En web, este servicio no hace nada ya que
//  flutter_foreground_task no es compatible con web.
// ═══════════════════════════════════════════════════════
void initForegroundTask() {
  // No inicializar en web
  if (kIsWeb) {
    debugPrint('⚠️ Foreground Task no disponible en web');
    return;
  }
  
  FlutterForegroundTask.init(
    androidNotificationOptions: AndroidNotificationOptions(
      channelId: 'geoflota_gps',
      channelName: 'GeoFlota GPS',
      channelDescription: 'Seguimiento GPS del recorrido activo',
      channelImportance: NotificationChannelImportance.LOW,
      priority: NotificationPriority.LOW,
      isSticky: true,
      iconData: const NotificationIconData(
        resType: ResourceType.mipmap,
        resPrefix: ResourcePrefix.ic,
        name: 'launcher',
      ),
    ),
    iosNotificationOptions: const IOSNotificationOptions(
      showNotification: true,
      playSound: false,
    ),
    foregroundTaskOptions: const ForegroundTaskOptions(
      autoRunOnBoot: false,
      autoRunOnMyPackageReplaced: false,
      allowWakeLock: true,
      allowWifiLock: false,
      interval: 600000,
    ),
  );
}

Future<void> startGpsService({
  String? ruta,
  String? camion,
  String? horaInicio,
}) async {
  // En web no hay foreground service
  if (kIsWeb) {
    debugPrint('⚠️ startGpsService: Saltando en web');
    return;
  }
  
  try {
    if (await FlutterForegroundTask.isRunningService) return;
    
    final titulo = 'GeoFlota · ${camion ?? ""}';
    final partes = <String>[];
    if (ruta != null) partes.add('Ruta: $ruta');
    if (horaInicio != null) partes.add('Inicio: $horaInicio');
    final texto = partes.isNotEmpty ? partes.join(' | ') : 'Compartiendo ubicación';
    
    await FlutterForegroundTask.startService(
      notificationTitle: titulo,
      notificationText: texto,
      callback: startCallback,
    );
  } catch (e) {
    debugPrint('⚠️ Error iniciando foreground service: $e');
  }
}

Future<void> stopGpsService() async {
  // En web no hay foreground service
  if (kIsWeb) {
    debugPrint('⚠️ stopGpsService: Saltando en web');
    return;
  }

  try {
    await FlutterForegroundTask.stopService();
  } catch (e) {
    debugPrint('⚠️ Error deteniendo foreground service: $e');
  }
}

/// Actualiza la notificación del servicio foreground
Future<void> updateGpsServiceNotification({
  String? ruta,
  String? camion,
  String? horaInicio,
  bool enDescarga = false,
  int? numeroDescarga,
}) async {
  // En web no hay foreground service
  if (kIsWeb) {
    debugPrint('⚠️ updateGpsServiceNotification: Saltando en web');
    return;
  }

  try {
    if (!await FlutterForegroundTask.isRunningService) return;

    String titulo;
    String texto;

    if (enDescarga && numeroDescarga != null) {
      titulo = 'GeoFlota · Descarga #$numeroDescarga';
      texto = 'Camión: ${camion ?? "N/D"} | Descargando en botadero';
    } else {
      titulo = 'GeoFlota · ${camion ?? ""}';
      final partes = <String>[];
      if (ruta != null) partes.add('Ruta: $ruta');
      if (horaInicio != null) partes.add('Inicio: $horaInicio');
      texto = partes.isNotEmpty ? partes.join(' | ') : 'Compartiendo ubicación';
    }

    await FlutterForegroundTask.updateService(
      notificationTitle: titulo,
      notificationText: texto,
    );
  } catch (e) {
    debugPrint('⚠️ Error actualizando notificación: $e');
  }
}

@pragma('vm:entry-point')
void startCallback() {
  FlutterForegroundTask.setTaskHandler(_KeepAliveHandler());
}

/// Handler mínimo: solo mantiene vivo el servicio.
class _KeepAliveHandler extends TaskHandler {
  @override
  void onStart(DateTime timestamp, SendPort? sendPort) {}

  @override
  void onRepeatEvent(DateTime timestamp, SendPort? sendPort) {}

  @override
  void onDestroy(DateTime timestamp, SendPort? sendPort) {}
}
