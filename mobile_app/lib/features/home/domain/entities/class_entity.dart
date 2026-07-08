class ClassEntity {
  const ClassEntity({
    required this.id,
    required this.classCode,
    required this.name,
    this.teacher,
    this.status,
  });

  final int id;
  final String classCode;
  final String name;
  final String? teacher;
  final String? status;

  factory ClassEntity.fromJson(Map<String, dynamic> json) {
    return ClassEntity(
      id: json['id'] as int,
      classCode: json['class_code']?.toString() ?? '',
      name: json['name']?.toString() ?? '',
      teacher: json['teacher']?.toString(),
      status: json['status']?.toString(),
    );
  }
}
