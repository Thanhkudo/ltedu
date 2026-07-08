class Assignment {
  const Assignment({
    required this.id,
    required this.title,
    required this.maxScore,
    required this.hasQuestions,
    this.description,
    this.dueDate,
    this.latestSubmission,
  });

  final int id;
  final String title;
  final String? description;
  final String? dueDate;
  final double maxScore;
  final bool hasQuestions;
  final SubmissionSummary? latestSubmission;

  factory Assignment.fromJson(Map<String, dynamic> json) {
    return Assignment(
      id: json['id'] as int,
      title: json['title']?.toString() ?? 'Homework',
      description: json['description']?.toString(),
      dueDate: json['due_date']?.toString(),
      maxScore: (json['max_score'] as num? ?? 0).toDouble(),
      hasQuestions: json['has_questions'] == true,
      latestSubmission: json['latest_submission'] is Map<String, dynamic>
          ? SubmissionSummary.fromJson(
              json['latest_submission'] as Map<String, dynamic>,
            )
          : null,
    );
  }
}

class SubmissionSummary {
  const SubmissionSummary({
    required this.id,
    this.score,
    this.status,
    this.submittedAt,
    this.result,
  });

  final int id;
  final double? score;
  final String? status;
  final String? submittedAt;
  final Map<String, dynamic>? result;

  factory SubmissionSummary.fromJson(Map<String, dynamic> json) {
    return SubmissionSummary(
      id: json['id'] as int,
      score: (json['score'] as num?)?.toDouble(),
      status: json['status']?.toString(),
      submittedAt: json['submitted_at']?.toString(),
      result: json['result'] is Map<String, dynamic>
          ? json['result'] as Map<String, dynamic>
          : null,
    );
  }
}
