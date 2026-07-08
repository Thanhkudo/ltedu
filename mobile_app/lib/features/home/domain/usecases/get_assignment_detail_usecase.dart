import '../entities/assignment_detail.dart';
import '../repositories/assignment_repository.dart';

class GetAssignmentDetailUseCase {
  GetAssignmentDetailUseCase({required this.repository});

  final AssignmentRepository repository;

  Future<AssignmentDetail> call(int assignmentId) {
    return repository.getAssignmentDetail(assignmentId);
  }
}
