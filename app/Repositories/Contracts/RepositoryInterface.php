<?php

namespace App\Repositories\Contracts;

/**
 * Base interface chung cho tat ca Repositories.
 * Dinh nghia cac thao tac CRUD co ban.
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
