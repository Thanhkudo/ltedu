<?php

namespace App\Services;

use App\Models\Student;
use App\Repositories\Contracts\StudentRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentService
{
    private StudentRepositoryInterface $studentRepo;

    public function __construct(StudentRepositoryInterface $studentRepo)
    {
        $this->studentRepo = $studentRepo;
    }

    public function listStudents(array $filters = [])
    {
        if (!empty($filters['search'])) {
            return $this->studentRepo->search($filters['search']);
        }

        return $this->studentRepo->paginate(15, ['*'], ['user']);
    }

    public function getStudent(int $id): Student
    {
        return $this->studentRepo->findById($id, ['*'], ['user', 'classes']);
    }

    public function createStudent(array $data): Student
    {
        // Tu sinh ma hoc vien neu khong truyen vao
        if (empty($data['student_code'])) {
            $data['student_code'] = $this->generateStudentCode();
        }

        return $this->studentRepo->create($data);
    }

    public function updateStudent(int $id, array $data): Student
    {
        $this->studentRepo->update($id, $data);
        return $this->studentRepo->findById($id);
    }

    public function deleteStudent(int $id): bool
    {
        return $this->studentRepo->delete($id);
    }

    public function getStudentsByClass(int $classId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->studentRepo->getByClass($classId, ['user']);
    }

    //  Private helpers 
    private function generateStudentCode(): string
    {
        $latest = Student::orderBy('id', 'desc')->first();
        $nextNumber = $latest ? (intval(substr($latest->student_code, 2)) + 1) : 1;
        return 'HV' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT); // HV0001, HV0002...
    }
}
