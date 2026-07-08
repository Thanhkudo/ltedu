import '../../domain/entities/user.dart';

class UserModel extends User {
  const UserModel({
    required super.id,
    required super.studentCode,
    required super.fullName,
    super.email,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] as int,
      studentCode: json['student_code']?.toString() ?? '',
      fullName: json['full_name']?.toString() ?? '',
      email: json['email']?.toString(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'student_code': studentCode,
      'full_name': fullName,
      'email': email,
    };
  }
}
