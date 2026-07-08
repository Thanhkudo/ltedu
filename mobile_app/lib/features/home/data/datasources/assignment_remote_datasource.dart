import '../../../../core/constants/api_endpoints.dart';
import '../../../../core/network/dio_client.dart';
import '../../domain/entities/assignment_detail.dart';

class AssignmentRemoteDataSource {
  AssignmentRemoteDataSource({required this.dioClient});

  final DioClient dioClient;

  Future<AssignmentDetail> getAssignmentDetail(int assignmentId) async {
    final response = await dioClient.get(ApiEndpoints.assignmentDetail(assignmentId));
    return AssignmentDetail.fromJson(response);
  }

  Future<Map<String, dynamic>> checkAnswer({
    required int assignmentId,
    required int questionId,
    required String answer,
  }) async {
    final response = await dioClient.post(
      ApiEndpoints.checkAnswer(assignmentId, questionId),
      data: {'answer': answer},
    );
    return response['data'] as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> submitAssignment({
    required int assignmentId,
    required Map<String, String> answers,
  }) async {
    final response = await dioClient.post(
      ApiEndpoints.submitAssignment(assignmentId),
      data: {'answers': answers},
    );
    return response['submission'] as Map<String, dynamic>;
  }
}
