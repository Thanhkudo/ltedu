import 'env.dart';

class AppConfig {
  AppConfig._();

  static String get apiBaseUrl => Env.apiBaseUrl;
  static Duration get httpTimeout => Env.httpTimeout;
}
