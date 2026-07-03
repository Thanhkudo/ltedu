class Question {
  const Question({
    required this.id,
    required this.text,
    required this.questionType,
    required this.typeLabel,
    required this.options,
    this.group,
    this.audioUrl,
    this.interactionData,
  });

  final int id;
  final String text;
  final String questionType;
  final String typeLabel;
  final List<QuestionOption> options;
  final QuestionGroup? group;
  final String? audioUrl;
  final Map<String, dynamic>? interactionData;

  factory Question.fromJson(Map<String, dynamic> json) {
    return Question(
      id: json['id'] as int,
      text: json['question_text']?.toString() ?? '',
      questionType: json['question_type']?.toString() ?? 'input',
      typeLabel: json['type_label']?.toString() ?? '',
      audioUrl: json['audio_url']?.toString(),
      group: json['group'] is Map<String, dynamic>
          ? QuestionGroup.fromJson(json['group'] as Map<String, dynamic>)
          : null,
      interactionData: json['interaction_data'] is Map<String, dynamic>
          ? json['interaction_data'] as Map<String, dynamic>
          : null,
      options: (json['options'] as List? ?? [])
          .whereType<Map<String, dynamic>>()
          .map(QuestionOption.fromJson)
          .toList(),
    );
  }
}

class QuestionOption {
  const QuestionOption({required this.id, required this.text});

  final int id;
  final String text;

  factory QuestionOption.fromJson(Map<String, dynamic> json) {
    return QuestionOption(
      id: json['id'] as int,
      text: json['option_text']?.toString() ?? '',
    );
  }
}

class QuestionGroup {
  const QuestionGroup({
    required this.title,
    this.passage,
    this.audioUrl,
  });

  final String title;
  final String? passage;
  final String? audioUrl;

  factory QuestionGroup.fromJson(Map<String, dynamic> json) {
    return QuestionGroup(
      title: json['title']?.toString() ?? '',
      passage: json['passage']?.toString(),
      audioUrl: json['audio_url']?.toString(),
    );
  }
}
