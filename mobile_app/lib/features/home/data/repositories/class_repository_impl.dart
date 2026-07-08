import '../../domain/entities/class_entity.dart';
import '../../domain/repositories/class_repository.dart';
import '../datasources/class_remote_datasource.dart';

class ClassRepositoryImpl implements ClassRepository {
  ClassRepositoryImpl({required this.remoteDataSource});

  final ClassRemoteDataSource remoteDataSource;

  @override
  Future<List<ClassEntity>> getClasses() {
    return remoteDataSource.getClasses();
  }
}
