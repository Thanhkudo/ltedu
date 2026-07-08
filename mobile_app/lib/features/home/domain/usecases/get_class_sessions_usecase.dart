import '../entities/class_session.dart';
import '../repositories/class_session_repository.dart';

class GetClassSessionsUseCase {
  GetClassSessionsUseCase({required this.repository});

  final ClassSessionRepository repository;

  Future<List<ClassSession>> call(int classId) {
    return repository.getClassSessions(classId);
  }
}
