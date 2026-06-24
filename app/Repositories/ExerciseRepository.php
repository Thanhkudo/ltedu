<?php

namespace App\Repositories;

use App\Models\Exercise;
use App\Repositories\Contracts\ExerciseRepositoryInterface;

class ExerciseRepository extends BaseRepository implements ExerciseRepositoryInterface
{
    public function __construct(Exercise $model)
    {
        parent::__construct($model);
    }

    public function filter(array $filters, int $perPage = 15)
    {
        $query = $this->model->newQuery();

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }

        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        return $query->with('creator')->latest()->paginate($perPage);
    }
}
