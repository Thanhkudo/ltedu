<?php

namespace App\Repositories\Contracts;

/**
 * Base interface chung cho tất cả Repositories.
 * Định nghĩa các thao tác CRUD cơ bản.
 */
interface RepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []);
    public function findById(int $id, array $columns = ['*'], array $relations = []);
    public function create(array $data);
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []);
}
