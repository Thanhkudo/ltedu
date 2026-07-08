import '../../domain/entities/class_session.dart';
import '../../domain/repositories/class_session_repository.dart';
import '../datasources/class_sessions_remote_datasource.dart';

class ClassSessionRepositoryImpl implements ClassSessionRepository {
  ClassSessionRepositoryImpl({required this.remoteDataSource});

  final ClassSessionsRemoteDataSource remoteDataSource;

  @override
  Future<List<ClassSession>> getClassSessions(int classId) {
    return remoteDataSource.getClassSessions(classId);
  }
}
