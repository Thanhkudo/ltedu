<?php

namespace App\Repositories\Contracts;

interface StudentRepositoryInterface extends RepositoryInterface
{
    /** Tìm kiếm học viên theo tên, email hoặc mã */
    public function search(string $keyword, int $perPage = 15);

    /** Lấy danh sách học viên của một lớp */
    public function getByClass(int $classId, array $relations = []);

    /** Tìm theo mã học viên */
    public function findByCode(string $code);
}
