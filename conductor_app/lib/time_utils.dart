class TimeUtils {
  static const Duration _cochabambaOffset = Duration(hours: -4);

  static DateTime _toCochabamba(DateTime dateTime) {
    return dateTime.toUtc().add(_cochabambaOffset);
  }

  static DateTime? parseToCochabamba(dynamic value) {
    if (value == null) return null;

    final raw = value.toString().trim();
    if (raw.isEmpty) return null;

    try {
      final parsed = DateTime.parse(raw);
      final hasZone = raw.endsWith('Z') || RegExp(r'[+-]\d{2}:\d{2}$').hasMatch(raw);

      // Si el backend no envía zona horaria, se asume que ya está en hora de Cochabamba.
      return hasZone ? _toCochabamba(parsed) : parsed;
    } catch (_) {
      return null;
    }
  }

  static String? formatHmsCochabamba(dynamic value) {
    final dt = parseToCochabamba(value);
    if (dt == null) return null;

    final hour = dt.hour.toString().padLeft(2, '0');
    final minute = dt.minute.toString().padLeft(2, '0');
    final second = dt.second.toString().padLeft(2, '0');
    return '$hour:$minute:$second';
  }

  static String nowIsoForCochabamba() {
    final dt = _toCochabamba(DateTime.now());
    final yyyy = dt.year.toString().padLeft(4, '0');
    final mm = dt.month.toString().padLeft(2, '0');
    final dd = dt.day.toString().padLeft(2, '0');
    final hh = dt.hour.toString().padLeft(2, '0');
    final mi = dt.minute.toString().padLeft(2, '0');
    final ss = dt.second.toString().padLeft(2, '0');
    return '$yyyy-$mm-${dd}T$hh:$mi:$ss-04:00';
  }
}
