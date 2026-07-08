import '../entities/class_session.dart';

abstract class ClassSessionRepository {
  Future<List<ClassSession>> getClassSessions(int classId);
}
