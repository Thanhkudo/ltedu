import '../entities/class_entity.dart';

abstract class ClassRepository {
  Future<List<ClassEntity>> getClasses();
}