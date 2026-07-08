import '../../domain/entities/user.dart';
import '../../domain/repositories/auth_repository.dart';
import '../datasources/auth_remote_datasource.dart';
import 'package:ltedu_student/core/storage/secure_storage.dart';

class AuthRepositoryImpl implements AuthRepository {
  AuthRepositoryImpl({
    required this.remoteDataSource,
    required this.secureStorage,
  });

  final AuthRemoteDataSource remoteDataSource;
  final SecureStorage secureStorage;

  @override
  Future<User> login(String studentCode) async {
    final result = await remoteDataSource.login(studentCode);
    final token = result['token'] as String;
    final user = result['user'] as User;

    await secureStorage.saveSession(
      token: token,
      studentName: user.fullName,
    );

    return user;
  }

  @override
  Future<void> logout() async {
    await remoteDataSource.logout();
    await secureStorage.clear();
  }
}
