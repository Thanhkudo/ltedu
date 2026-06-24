<?php

namespace App\Repositories\Contracts;

interface ExerciseRepositoryInterface extends RepositoryInterface
{
    /** Lọc bài tập theo type và/hoặc difficulty */
    public function filter(array $filters, int $perPage = 15);
}
