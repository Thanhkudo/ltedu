import '../../domain/entities/assignment_detail.dart';
import '../../domain/repositories/assignment_repository.dart';
import '../datasources/assignment_remote_datasource.dart';

class AssignmentRepositoryImpl implements AssignmentRepository {
  AssignmentRepositoryImpl({required this.remoteDataSource});

  final AssignmentRemoteDataSource remoteDataSource;

  @override
  Future<AssignmentDetail> getAssignmentDetail(int assignmentId) {
    return remoteDataSource.getAssignmentDetail(assignmentId);
  }

  @override
  Future<Map<String, dynamic>> checkAnswer({
    required int assignmentId,
    required int questionId,
    required String answer,
  }) {
    return remoteDataSource.checkAnswer(
      assignmentId: assignmentId,
      questionId: questionId,
      answer: answer,
    );
  }

  @override
  Future<Map<String, dynamic>> submitAssignment({
    required int assignmentId,
    required Map<String, String> answers,
  }) {
    return remoteDataSource.submitAssignment(
      assignmentId: assignmentId,
      answers: answers,
    );
  }
}
