<?php

namespace App\Repositories;

use App\Models\Student;
use App\Repositories\Contracts\StudentRepositoryInterface;

class StudentRepository extends BaseRepository implements StudentRepositoryInterface
{
    public function __construct(Student $model)
    {
        parent::__construct($model);
    }

    public function search(string $keyword, int $perPage = 15)
    {
        return $this->model
            ->where('full_name', 'like', "%{$keyword}%")
            ->orWhere('email', 'like', "%{$keyword}%")
            ->orWhere('student_code', 'like', "%{$keyword}%")
            ->latest()
            ->paginate($perPage);
    }

    public function getByClass(int $classId, array $relations = [])
    {
        return $this->model
            ->with($relations)
            ->whereHas('classes', fn ($q) => $q->where('classes.id', $classId))
            ->get();
    }

    public function findByCode(string $code)
    {
        return $this->model->where('student_code', $code)->firstOrFail();
    }
}
