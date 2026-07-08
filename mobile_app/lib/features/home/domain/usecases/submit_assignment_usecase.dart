import '../repositories/assignment_repository.dart';

class SubmitAssignmentUseCase {
  const SubmitAssignmentUseCase({required this.repository});

  final AssignmentRepository repository;

  Future<Map<String, dynamic>> call({
    required int assignmentId,
    required Map<String, String> answers,
  }) {
    return repository.submitAssignment(
      assignmentId: assignmentId,
      answers: answers,
    );
  }
}
