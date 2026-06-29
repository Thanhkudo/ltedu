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
        return DB::transaction(function () use ($id) {
            $class = $this->classRepo->findById($id);

            $sessionIds = DB::table('sessions')->where('class_id', $id)->pluck('id');
            $assignmentIds = DB::table('assignments')->whereIn('session_id', $sessionIds)->pluck('id');

            if ($assignmentIds->isNotEmpty()) {
                DB::table('assignment_submissions')->whereIn('assignment_id', $assignmentIds)->delete();
                DB::table('assignments')->whereIn('id', $assignmentIds)->delete();
            }

            if ($sessionIds->isNotEmpty()) {
                DB::table('sessions')->whereIn('id', $sessionIds)->delete();
            }

            DB::table('class_student')->where('class_id', $id)->delete();

            return (bool) $class->delete();
        });
    }

    /**
     * Them hoc vien vao lop (enrollment).
     * @param int   $classId
     * @param array $studentIds  Mang student_id can them
     */
    public function enrollStudents(int $classId, array $studentIds): void
    {
        $class = $this->classRepo->findById($classId);

        // syncWithoutDetaching: khong xoa hoc vien cu khi them moi
        $class->students()->syncWithoutDetaching(
            collect($studentIds)->mapWithKeys(fn ($id) => [
                $id => ['enrolled_at' => now(), 'status' => 'active'],
            ])->all()
        );
    }

    /**
     * Cho hoc vien roi lop (drop).
     */
    public function dropStudent(int $classId, int $studentId): void
    {
        $class = $this->classRepo->findById($classId);
        $class->students()->updateExistingPivot($studentId, ['status' => 'dropped']);
    }

    //  Private helpers 
    private function generateClassCode(): string
    {
        $latest = SchoolClass::orderBy('id', 'desc')->first();
        $nextNumber = $latest ? ($latest->id + 1) : 1;
        return 'LOP-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

}
