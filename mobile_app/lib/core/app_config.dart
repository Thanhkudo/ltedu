class AppConfig {
  // Android emulator: http://10.0.2.2/linh_trang/public/api/mobile
  // Physical device: use your LAN/domain URL, e.g. https://ltedu.pro/api/mobile
  static const String apiBaseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://10.0.2.2/linh_trang/public/api/mobile',
  );
}
