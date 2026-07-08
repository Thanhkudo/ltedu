import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../data/datasources/class_sessions_remote_datasource.dart';
import '../../data/repositories/class_session_repository_impl.dart';
import '../../domain/entities/class_session.dart';
import '../../domain/repositories/class_session_repository.dart';
import '../../domain/usecases/get_class_sessions_usecase.dart';
import 'package:ltedu_student/core/network/dio_client.dart';
import 'package:ltedu_student/core/storage/secure_storage.dart';

final _secureStorageProvider = Provider<SecureStorage>((ref) => SecureStorage());

final _dioClientProvider = Provider<DioClient>((ref) {
  final storage = ref.watch(_secureStorageProvider);
  return DioClient(secureStorage: storage);
});

final _classSessionsRemoteDataSourceProvider = Provider<ClassSessionsRemoteDataSource>((ref) {
  return ClassSessionsRemoteDataSource(dioClient: ref.watch(_dioClientProvider));
});

final _classSessionRepositoryProvider = Provider<ClassSessionRepository>((ref) {
  return ClassSessionRepositoryImpl(remoteDataSource: ref.watch(_classSessionsRemoteDataSourceProvider));
});

final getClassSessionsUseCaseProvider = Provider<GetClassSessionsUseCase>((ref) {
  return GetClassSessionsUseCase(repository: ref.watch(_classSessionRepositoryProvider));
});

final classSessionsProvider = FutureProvider.family<List<ClassSession>, int>((ref, classId) {
  return ref.watch(getClassSessionsUseCaseProvider).call(classId);
});
