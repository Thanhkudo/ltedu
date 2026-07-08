import '../entities/assignment_detail.dart';

abstract class AssignmentRepository {
  Future<AssignmentDetail> getAssignmentDetail(int assignmentId);

  Future<Map<String, dynamic>> checkAnswer({
    required int assignmentId,
    required int questionId,
    required String answer,
  });

  Future<Map<String, dynamic>> submitAssignment({
    required int assignmentId,
    required Map<String, String> answers,
  });
}
