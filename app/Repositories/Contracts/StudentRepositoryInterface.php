<?php

namespace App\Repositories\Contracts;

interface StudentRepositoryInterface extends RepositoryInterface
{
    /** Tim kiem hoc vien theo ten, email hoac ma */
    public function search(string $keyword, int $perPage = 15);

    /** Lay danh sach hoc vien cua mot lop */
    public function getByClass(int $classId, array $relations = []);

    /** Tim theo ma hoc vien */
    public function findByCode(string $code);
}
