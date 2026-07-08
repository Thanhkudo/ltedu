import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class SecureStorage {
  SecureStorage()
      : _storage = const FlutterSecureStorage(
          aOptions: AndroidOptions(encryptedSharedPreferences: true),
        );

  final FlutterSecureStorage _storage;

  static const _tokenKey = 'ltedu_access_token';
  static const _studentNameKey = 'ltedu_student_name';

  Future<String?> readToken() => _storage.read(key: _tokenKey);

  Future<String?> readStudentName() => _storage.read(key: _studentNameKey);

  Future<void> saveSession({
    required String token,
    required String studentName,
  }) async {
    await _storage.write(key: _tokenKey, value: token);
    await _storage.write(key: _studentNameKey, value: studentName);
  }

  Future<void> clear() async {
    await _storage.delete(key: _tokenKey);
    await _storage.delete(key: _studentNameKey);
  }
}
