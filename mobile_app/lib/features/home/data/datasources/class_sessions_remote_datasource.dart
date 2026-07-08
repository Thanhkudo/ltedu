import '../../domain/entities/class_session.dart';
import '../../../../core/constants/api_endpoints.dart';
import '../../../../core/network/dio_client.dart';

class ClassSessionsRemoteDataSource {
  ClassSessionsRemoteDataSource({required this.dioClient});

  final DioClient dioClient;

  Future<List<ClassSession>> getClassSessions(int classId) async {
    final response = await dioClient.get(ApiEndpoints.classSessions(classId));
    return (response['data'] as List? ?? [])
        .whereType<Map<String, dynamic>>()
        .map(ClassSession.fromJson)
        .toList();
  }
}
