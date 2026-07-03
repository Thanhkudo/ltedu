import 'assignment.dart';

class ClassSession {
  const ClassSession({
    required this.id,
    required this.sessionNumber,
    required this.title,
    required this.status,
    required this.assignments,
    this.sessionDate,
  });

  final int id;
  final int sessionNumber;
  final String title;
  final String status;
  final String? sessionDate;
  final List<Assignment> assignments;

  bool get isCancelled => status == 'cancelled';

  factory ClassSession.fromJson(Map<String, dynamic> json) {
    return ClassSession(
      id: json['id'] as int,
      sessionNumber: json['session_number'] as int? ?? 0,
      title: json['title']?.toString() ?? '',
      status: json['status']?.toString() ?? 'scheduled',
      sessionDate: json['session_date']?.toString(),
      assignments: (json['assignments'] as List? ?? [])
          .whereType<Map<String, dynamic>>()
          .map(Assignment.fromJson)
          .toList(),
    );
  }
}
