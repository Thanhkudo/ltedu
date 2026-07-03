import 'package:shared_preferences/shared_preferences.dart';

class TokenStore {
  static const _tokenKey = 'ltedu_access_token';
  static const _studentNameKey = 'ltedu_student_name';

  Future<String?> readToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_tokenKey);
  }

  Future<String?> readStudentName() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_studentNameKey);
  }

  Future<void> saveSession({
    required String token,
    required String studentName,
  }) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_tokenKey, token);
    await prefs.setString(_studentNameKey, studentName);
  }

  Future<void> clear() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_tokenKey);
    await prefs.remove(_studentNameKey);
  }
}
