import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../data/datasources/class_remote_datasource.dart';
import '../../data/repositories/class_repository_impl.dart';
import '../../domain/entities/class_entity.dart';
import '../../domain/repositories/class_repository.dart';
import '../../domain/usecases/get_classes_usecase.dart';
import 'package:ltedu_student/core/network/dio_client.dart';
import 'package:ltedu_student/core/storage/secure_storage.dart';

final _secureStorageProvider = Provider<SecureStorage>((ref) => SecureStorage());

final _dioClientProvider = Provider<DioClient>((ref) {
  final storage = ref.watch(_secureStorageProvider);
  return DioClient(secureStorage: storage);
});

final _classRemoteDataSourceProvider = Provider<ClassRemoteDataSource>((ref) {
  return ClassRemoteDataSource(dioClient: ref.watch(_dioClientProvider));
});

final classRepositoryProvider = Provider<ClassRepository>((ref) {
  return ClassRepositoryImpl(remoteDataSource: ref.watch(_classRemoteDataSourceProvider));
});

final getClassesUseCaseProvider = Provider<GetClassesUseCase>((ref) {
  return GetClassesUseCase(ref.watch(classRepositoryProvider));
});

final classesProvider = FutureProvider<List<ClassEntity>>((ref) {
  return ref.watch(getClassesUseCaseProvider).call();
});
