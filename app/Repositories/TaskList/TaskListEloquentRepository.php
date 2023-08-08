<?php

namespace App\Repositories\TaskList;

use App\Models\TaskList;
use Illuminate\Pagination\Paginator;

class TaskListEloquentRepository implements TaskListInterfaceRepository
{
    public function __construct(
        private readonly TaskList $model
    ) {
    }

    public function getAll(int $userId): Paginator
    {
        return $this->model->where('user_id', $userId)->simplePaginate(7);
    }

    public function create(string $name, int $userId): TaskList
    {
        return $this->model->create(
            [
                'name' => $name,
                'user_id' => $userId
            ]
        );
    }

    public function getById(int $id): ?TaskList
    {
        return $this->model->find($id);
    }

    public function update(int $id, string $name): bool
    {
        $task = $this->getById($id);
        return $task->update(
            [
                'name' => $name,
            ]
        );
    }

    public function delete(int $id): bool
    {
        return $this->model->where('id', $id)->delete();
    }

    public function setTaskComplete(int $id): bool
    {
        $task = $this->getById($id);
        return $task->update(['completed' => true]);
    }
}
