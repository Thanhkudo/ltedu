class Student {
  const Student({
    required this.id,
    required this.studentCode,
    required this.fullName,
    this.email,
  });

  final int id;
  final String studentCode;
  final String fullName;
  final String? email;

  factory Student.fromJson(Map<String, dynamic> json) {
    return Student(
      id: json['id'] as int,
      studentCode: json['student_code']?.toString() ?? '',
      fullName: json['full_name']?.toString() ?? '',
      email: json['email']?.toString(),
    );
  }
}
