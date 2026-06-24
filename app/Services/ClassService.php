<?php

namespace App\Services;

use App\Models\SchoolClass;
use App\Repositories\Contracts\ClassRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ClassService
{
    private ClassRepositoryInterface $classRepo;

    public function __construct(ClassRepositoryInterface $classRepo)
    {
        $this->classRepo = $classRepo;
    }

    public function listClasses(array $filters = [])
    {
        if (!empty($filters['teacher_id'])) {
            return $this->classRepo->getByTeacher($filters['teacher_id'], ['teacher']);
        }

        if (isset($filters['active_only']) && $filters['active_only']) {
            return $this->classRepo->getActive(['teacher']);
        }

        return $this->classRepo->paginate(15, ['*'], ['teacher']);
    }

    public function getClass(int $id): SchoolClass
    {
        return $this->classRepo->findById($id, ['*'], ['teacher', 'sessions', 'activeStudents']);
    }

    public function createClass(array $data): SchoolClass
    {
        if (empty($data['class_code'])) {
            $data['class_code'] = $this->generateClassCode();
        }

        return $this->classRepo->create($data);
    }

    public function updateClass(int $id, array $data): SchoolClass
    {
        $this->classRepo->update($id, $data);
        return $this->classRepo->findById($id);
    }

    public function deleteClass(int $id): bool
    {
        return $this->classRepo->delete($id);
    }

    /**
     * Thêm học viên vào lớp (enrollment).
     * @param int   $classId
     * @param array $studentIds  Mảng student_id cần thêm
     */
    public function enrollStudents(int $classId, array $studentIds): void
    {
        $class = $this->classRepo->findById($classId);

        // syncWithoutDetaching: không xoá học viên cũ khi thêm mới
        $class->students()->syncWithoutDetaching(
            collect($studentIds)->mapWithKeys(fn ($id) => [
                $id => ['enrolled_at' => now(), 'status' => 'active'],
            ])->all()
        );
    }

    /**
     * Cho học viên rời lớp (drop).
     */
    public function dropStudent(int $classId, int $studentId): void
    {
        $class = $this->classRepo->findById($classId);
        $class->students()->updateExistingPivot($studentId, ['status' => 'dropped']);
    }

    // ─── Private helpers ───────────────────────────────────────
    private function generateClassCode(): string
    {
        $latest = SchoolClass::orderBy('id', 'desc')->first();
        $nextNumber = $latest ? ($latest->id + 1) : 1;
        return 'LOP-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
