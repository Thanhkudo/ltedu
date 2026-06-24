<?php

namespace App\Repositories;

use App\Models\SchoolClass;
use App\Repositories\Contracts\ClassRepositoryInterface;

class ClassRepository extends BaseRepository implements ClassRepositoryInterface
{
    public function __construct(SchoolClass $model)
    {
        parent::__construct($model);
    }

    public function getActive(array $relations = [])
    {
        return $this->model->where('status', 'active')->with($relations)->latest()->get();
    }

    public function getByTeacher(int $teacherId, array $relations = [])
    {
        return $this->model
            ->where('teacher_id', $teacherId)
            ->with($relations)
            ->latest()
            ->get();
    }
}
