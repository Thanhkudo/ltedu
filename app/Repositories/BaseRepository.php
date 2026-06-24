<?php

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Base Repository - triển khai CRUD chung dùng cho tất cả domain.
 * Các Repository cụ thể extend class này và override nếu cần.
 */
abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $columns = ['*'], array $relations = [])
    {
        return $this->model->select($columns)->with($relations)->get();
    }

    public function findById(int $id, array $columns = ['*'], array $relations = [])
    {
        return $this->model->select($columns)->with($relations)->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->findById($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->findById($id)->delete();
    }

    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = [])
    {
        return $this->model->select($columns)->with($relations)->latest()->paginate($perPage);
    }
}
