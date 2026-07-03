import 'dart:convert';

import 'package:http/http.dart' as http;

import 'api_exception.dart';
import 'app_config.dart';
import 'token_store.dart';

class ApiClient {
  ApiClient({http.Client? httpClient, TokenStore? tokenStore})
      : _httpClient = httpClient ?? http.Client(),
        _tokenStore = tokenStore ?? TokenStore();

  final http.Client _httpClient;
  final TokenStore _tokenStore;

  Future<Map<String, dynamic>> login({
    required String studentCode,
    String deviceName = 'flutter',
  }) async {
    final data = await post('/login', body: {
      'student_code': studentCode,
      'device_name': deviceName,
    }, authenticated: false);

    final token = data['access_token']?.toString();
    final student = data['student'] as Map<String, dynamic>? ?? {};
    final name = student['full_name']?.toString() ?? '';

    if (token == null || token.isEmpty) {
      throw const ApiException('Server did not return an access token.');
    }

    await _tokenStore.saveSession(token: token, studentName: name);
    return data;
  }

  Future<void> logout() async {
    try {
      await post('/logout');
    } finally {
      await _tokenStore.clear();
    }
  }

  Future<Map<String, dynamic>> get(String path) {
    return _send('GET', path);
  }

  Future<Map<String, dynamic>> post(
    String path, {
    Map<String, dynamic>? body,
    bool authenticated = true,
  }) {
    return _send('POST', path, body: body, authenticated: authenticated);
  }

  Future<Map<String, dynamic>> _send(
    String method,
    String path, {
    Map<String, dynamic>? body,
    bool authenticated = true,
  }) async {
    final uri = Uri.parse('${AppConfig.apiBaseUrl}$path');
    final headers = <String, String>{
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    };

    if (authenticated) {
      final token = await _tokenStore.readToken();
      if (token == null || token.isEmpty) {
        throw const ApiException('Please sign in again.', statusCode: 401);
      }
      headers['Authorization'] = 'Bearer $token';
    }

    final response = method == 'GET'
        ? await _httpClient.get(uri, headers: headers)
        : await _httpClient.post(
            uri,
            headers: headers,
            body: jsonEncode(body ?? {}),
          );

    final decoded = response.body.isEmpty
        ? <String, dynamic>{}
        : jsonDecode(response.body) as Map<String, dynamic>;

    if (response.statusCode < 200 || response.statusCode >= 300) {
      throw ApiException(
        decoded['message']?.toString() ?? 'Request failed.',
        statusCode: response.statusCode,
      );
    }

    return decoded;
  }
}
