class StudentModel {
  const StudentModel({
    required this.id,
    required this.fullName,
  });

  final int id;
  final String fullName;

  factory StudentModel.fromJson(Map<String, dynamic> json) {
    return StudentModel(
      id: json['id'] as int,
      fullName: json['full_name']?.toString() ?? '',
    );
  }
}
