<?php

namespace App\Services;

use App\Models\Exercise;
use App\Repositories\Contracts\ExerciseRepositoryInterface;

class ExerciseService
{
    private ExerciseRepositoryInterface $exerciseRepo;

    public function __construct(ExerciseRepositoryInterface $exerciseRepo)
    {
        $this->exerciseRepo = $exerciseRepo;
    }

    public function listExercises(array $filters = [])
    {
        if (!empty($filters['search']) || !empty($filters['type']) || !empty($filters['difficulty'])) {
            return $this->exerciseRepo->filter($filters);
        }

        return $this->exerciseRepo->paginate(15, ['*'], ['creator']);
    }

    public function getExercise(int $id): Exercise
    {
        return $this->exerciseRepo->findById($id, ['*'], ['creator']);
    }

    public function createExercise(array $data, int $creatorId): Exercise
    {
        $data['created_by'] = $creatorId;
        return $this->exerciseRepo->create($data);
    }

    public function updateExercise(int $id, array $data): Exercise
    {
        $this->exerciseRepo->update($id, $data);
        return $this->exerciseRepo->findById($id);
    }

    public function deleteExercise(int $id): bool
    {
        return $this->exerciseRepo->delete($id);
    }
}
