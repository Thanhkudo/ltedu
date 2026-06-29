<?php

namespace App\Repositories\Contracts;

interface ClassRepositoryInterface extends RepositoryInterface
{
    /** Lay cac lop dang active */
    public function getActive(array $relations = []);

    /** Lay lop theo giao vien */
    public function getByTeacher(int $teacherId, array $relations = []);
}
