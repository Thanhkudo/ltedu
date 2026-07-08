import 'package:logger/logger.dart';

class AppLogger {
  AppLogger._();

  static final _logger = Logger(
    printer: PrettyPrinter(
      methodCount: 0,
      errorMethodCount: 5,
      lineLength: 120,
      colors: true,
      printEmojis: true,
    ),
  );

  static void log(dynamic message) => _logger.d(message);
  static void info(dynamic message) => _logger.i(message);
  static void warn(dynamic message) => _logger.w(message);
  static void error(dynamic message, [dynamic error, StackTrace? stack]) =>
      _logger.e(message, error: error, stackTrace: stack);
}
