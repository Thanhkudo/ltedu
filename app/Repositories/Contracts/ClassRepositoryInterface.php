<?php

namespace App\Repositories\Contracts;

interface ClassRepositoryInterface extends RepositoryInterface
{
    /** Lấy các lớp đang active */
    public function getActive(array $relations = []);

    /** Lấy lớp theo giáo viên */
    public function getByTeacher(int $teacherId, array $relations = []);
}
