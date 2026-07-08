import '../entities/user.dart';
import '../repositories/auth_repository.dart';

class LoginUseCase {
  LoginUseCase({required this.repository});

  final AuthRepository repository;

  Future<User> call(String studentCode) {
    return repository.login(studentCode);
  }
}
