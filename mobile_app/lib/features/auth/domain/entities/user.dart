class User {
  const User({
    required this.id,
    required this.studentCode,
    required this.fullName,
    this.email,
  });

  final int id;
  final String studentCode;
  final String fullName;
  final String? email;
}
