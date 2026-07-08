import '../repositories/assignment_repository.dart';

class CheckAnswerUseCase {
  const CheckAnswerUseCase({required this.repository});

  final AssignmentRepository repository;

  Future<Map<String, dynamic>> call({
    required int assignmentId,
    required int questionId,
    required String answer,
  }) {
    return repository.checkAnswer(
      assignmentId: assignmentId,
      questionId: questionId,
      answer: answer,
    );
  }
}
