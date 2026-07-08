import '../entities/user.dart';

abstract class AuthRepository {
  Future<User> login(String studentCode);
  Future<void> logout();
}
