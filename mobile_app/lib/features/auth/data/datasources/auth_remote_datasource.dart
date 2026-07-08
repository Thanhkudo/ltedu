import 'package:ltedu_student/core/constants/api_endpoints.dart';
import 'package:ltedu_student/core/network/dio_client.dart';
import '../models/user_model.dart';

class AuthRemoteDataSource {
  AuthRemoteDataSource({required this.dioClient});

  final DioClient dioClient;

  Future<Map<String, dynamic>> login(String studentCode) async {
    final response = await dioClient.post(
      ApiEndpoints.login,
      data: {
        'student_code': studentCode,
        'device_name': 'flutter',
      },
    );

    final accessToken = response['access_token']?.toString();
    final student = response['student'] as Map<String, dynamic>?;

    if (accessToken == null || student == null) {
      throw Exception('Invalid login response');
    }

    return {
      'token': accessToken,
      'user': UserModel.fromJson(student),
    };
  }

  Future<void> logout() async {
    await dioClient.post(ApiEndpoints.logout);
  }
}
