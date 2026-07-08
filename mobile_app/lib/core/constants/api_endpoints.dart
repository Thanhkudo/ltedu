class ApiEndpoints {
  ApiEndpoints._();

  // Auth
  static const String login = '/login';
  static const String logout = '/logout';

  // Classes
  static const String classes = '/classes';
  static String classSessions(int classId) => '/classes/$classId/sessions';

  // Assignments
  static String assignmentDetail(int id) => '/assignments/$id';
  static String checkAnswer(int assignmentId, int questionId) =>
      '/assignments/$assignmentId/questions/$questionId/check';
  static String submitAssignment(int assignmentId) =>
      '/assignments/$assignmentId/submit';
}
