class Env {
  /// Base URL for the API, configurable via --dart-define.
  /// Defaults to Android emulator localhost.
  static const String apiBaseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://10.0.2.2/linh_trang/public/api/mobile',
  );

  /// HTTP request timeout.
  static const Duration httpTimeout = Duration(seconds: 30);

  /// App version (from pubspec).
  static const String appVersion = '0.1.0';
}
