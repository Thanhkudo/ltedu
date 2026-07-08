import 'package:flutter_riverpod/flutter_riverpod.dart';

import 'package:ltedu_student/features/auth/data/datasources/auth_remote_datasource.dart';
import 'package:ltedu_student/features/auth/data/repositories/auth_repository_impl.dart';
import 'package:ltedu_student/features/auth/domain/entities/user.dart';
import 'package:ltedu_student/features/auth/domain/repositories/auth_repository.dart';
import 'package:ltedu_student/features/auth/domain/usecases/login_usecase.dart';
import 'package:ltedu_student/features/auth/domain/usecases/logout_usecase.dart';
import 'package:ltedu_student/core/network/dio_client.dart';
import 'package:ltedu_student/core/storage/secure_storage.dart';

final _secureStorageProvider = Provider<SecureStorage>((ref) => SecureStorage());

final _dioClientProvider = Provider<DioClient>((ref) {
  final storage = ref.watch(_secureStorageProvider);
  return DioClient(secureStorage: storage);
});

final _authRemoteDataSourceProvider = Provider<AuthRemoteDataSource>((ref) {
  return AuthRemoteDataSource(dioClient: ref.watch(_dioClientProvider));
});

final authRepositoryProvider = Provider<AuthRepository>((ref) {
  return AuthRepositoryImpl(
    remoteDataSource: ref.watch(_authRemoteDataSourceProvider),
    secureStorage: ref.watch(_secureStorageProvider),
  );
});

final loginUseCaseProvider = Provider<LoginUseCase>((ref) {
  return LoginUseCase(repository: ref.watch(authRepositoryProvider));
});

final logoutUseCaseProvider = Provider<LogoutUseCase>((ref) {
  return LogoutUseCase(repository: ref.watch(authRepositoryProvider));
});

final authStateProvider = StateNotifierProvider<AuthStateNotifier, User?>(
  (ref) => AuthStateNotifier(ref),
);

class AuthStateNotifier extends StateNotifier<User?> {
  AuthStateNotifier(this.ref) : super(null);

  final Ref ref;

  Future<void> login(String studentCode) async {
    final user = await ref.watch(loginUseCaseProvider).call(studentCode);
    state = user;
  }

  Future<void> logout() async {
    await ref.watch(logoutUseCaseProvider).call();
    state = null;
  }
}
