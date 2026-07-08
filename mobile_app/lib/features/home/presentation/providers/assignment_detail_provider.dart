import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../data/datasources/assignment_remote_datasource.dart';
import '../../data/repositories/assignment_repository_impl.dart';
import '../../domain/entities/assignment_detail.dart';
import '../../domain/repositories/assignment_repository.dart';
import '../../domain/usecases/check_answer_usecase.dart';
import '../../domain/usecases/get_assignment_detail_usecase.dart';
import '../../domain/usecases/submit_assignment_usecase.dart';
import 'package:ltedu_student/core/network/dio_client.dart';
import 'package:ltedu_student/core/storage/secure_storage.dart';

final _secureStorageProvider = Provider<SecureStorage>((ref) => SecureStorage());

final _dioClientProvider = Provider<DioClient>((ref) {
  final storage = ref.watch(_secureStorageProvider);
  return DioClient(secureStorage: storage);
});

final _assignmentRemoteDataSourceProvider = Provider<AssignmentRemoteDataSource>((ref) {
  return AssignmentRemoteDataSource(dioClient: ref.watch(_dioClientProvider));
});

final _assignmentRepositoryProvider = Provider<AssignmentRepository>((ref) {
  return AssignmentRepositoryImpl(remoteDataSource: ref.watch(_assignmentRemoteDataSourceProvider));
});

final getAssignmentDetailUseCaseProvider = Provider<GetAssignmentDetailUseCase>((ref) {
  return GetAssignmentDetailUseCase(repository: ref.watch(_assignmentRepositoryProvider));
});

final checkAnswerUseCaseProvider = Provider<CheckAnswerUseCase>((ref) {
  return CheckAnswerUseCase(repository: ref.watch(_assignmentRepositoryProvider));
});

final submitAssignmentUseCaseProvider = Provider<SubmitAssignmentUseCase>((ref) {
  return SubmitAssignmentUseCase(repository: ref.watch(_assignmentRepositoryProvider));
});

final assignmentDetailProvider = FutureProvider.family<AssignmentDetail, int>((ref, assignmentId) {
  return ref.watch(getAssignmentDetailUseCaseProvider).call(assignmentId);
});
