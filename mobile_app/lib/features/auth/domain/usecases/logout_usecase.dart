import '../repositories/auth_repository.dart';

class LogoutUseCase {
  LogoutUseCase({required this.repository});

  final AuthRepository repository;

  Future<void> call() {
    return repository.logout();
  }
}
