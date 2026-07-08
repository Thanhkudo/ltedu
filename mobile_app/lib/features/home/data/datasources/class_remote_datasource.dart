import 'package:ltedu_student/core/constants/api_endpoints.dart';
import 'package:ltedu_student/core/network/dio_client.dart';
import '../../domain/entities/class_entity.dart';

class ClassRemoteDataSource {
  ClassRemoteDataSource({required this.dioClient});

  final DioClient dioClient;

  Future<List<ClassEntity>> getClasses() async {
    final response = await dioClient.get(ApiEndpoints.classes);
    return (response['data'] as List? ?? [])
        .whereType<Map<String, dynamic>>()
        .map(ClassEntity.fromJson)
        .toList();
  }
}
