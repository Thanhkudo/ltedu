<?php

namespace App\Repositories\Contracts;

interface ExerciseRepositoryInterface extends RepositoryInterface
{
    /** Loc bai tap theo type va/hoac difficulty */
    public function filter(array $filters, int $perPage = 15);
}
