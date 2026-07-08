import 'package:dio/dio.dart';

import '../config/env.dart';
import '../storage/secure_storage.dart';
import '../utils/logger.dart';
import 'network_exception.dart';

class DioClient {
  DioClient({
    required SecureStorage secureStorage,
    void Function()? onUnauthorized,
  })  : _secureStorage = secureStorage,
        _onUnauthorized = onUnauthorized {
    _dio = Dio(
      BaseOptions(
        baseUrl: Env.apiBaseUrl,
        connectTimeout: Env.httpTimeout,
        receiveTimeout: Env.httpTimeout,
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
      ),
    );

    _dio.interceptors.addAll([
      _authInterceptor(),
      _errorInterceptor(),
      LogInterceptor(
        requestBody: true,
        responseBody: true,
        logPrint: (o) => AppLogger.log('[DIO] $o'),
      ),
    ]);
  }

  late final Dio _dio;
  final SecureStorage _secureStorage;
  final void Function()? _onUnauthorized;

  Interceptor _authInterceptor() {
    return InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await _secureStorage.readToken();
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        handler.next(options);
      },
      onError: (error, handler) async {
        if (error.response?.statusCode == 401) {
          await _secureStorage.clear();
          _onUnauthorized?.call();
        }
        handler.next(error);
      },
    );
  }

  Interceptor _errorInterceptor() {
    return InterceptorsWrapper(
      onError: (error, handler) {
        final exception = _mapDioError(error);
        handler.reject(
          DioException(
            requestOptions: error.requestOptions,
            error: exception,
            response: error.response,
            type: error.type,
          ),
        );
      },
    );
  }

  Exception _mapDioError(DioException error) {
    switch (error.type) {
      case DioExceptionType.connectionTimeout:
      case DioExceptionType.sendTimeout:
      case DioExceptionType.receiveTimeout:
        return const NetworkTimeoutException();
      case DioExceptionType.connectionError:
        return const NetworkConnectionException();
      case DioExceptionType.badResponse:
        final statusCode = error.response?.statusCode;
        final message = _extractMessage(error.response);
        if (statusCode == 401) {
          return UnauthorizedException(message);
        }
        return ApiErrorException(message, statusCode: statusCode);
      case DioExceptionType.cancel:
        return const NetworkException('Request was cancelled');
      default:
        return NetworkUnknownException(error.message);
    }
  }

  String _extractMessage(Response? response) {
    if (response?.data is Map) {
      return (response!.data as Map)['message']?.toString() ?? 'Request failed';
    }
    return 'Request failed';
  }

  Future<Map<String, dynamic>> get(
    String path, {
    Map<String, dynamic>? queryParameters,
  }) async {
    final response = await _dio.get(path, queryParameters: queryParameters);
    return response.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> post(
    String path, {
    Map<String, dynamic>? data,
  }) async {
    final response = await _dio.post(path, data: data);
    return response.data as Map<String, dynamic>;
  }
}
