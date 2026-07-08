import 'assignment.dart';
import 'question.dart';

class AssignmentDetail {
  const AssignmentDetail({
    required this.assignment,
    required this.questions,
  });

  final Assignment assignment;
  final List<Question> questions;

  factory AssignmentDetail.fromJson(Map<String, dynamic> json) {
    return AssignmentDetail(
      assignment: Assignment.fromJson(json['assignment'] as Map<String, dynamic>),
      questions: (json['questions'] as List? ?? [])
          .whereType<Map<String, dynamic>>()
          .map(Question.fromJson)
          .toList(),
    );
  }
}
