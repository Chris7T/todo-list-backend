<?php

namespace App\Repositories\Task;

use App\Models\Task;
use Illuminate\Pagination\Paginator;

class TaskEloquentRepository implements TaskInterfaceRepository
{
    public function __construct(
        private readonly Task $model
    ) {
    }

    public function getAll(int $taskListId): Paginator
    {
        return $this->model->where('task_list_id', $taskListId)->simplePaginate(7);
    }

    public function create(string $title, string $description, string $dateTime, int $taskListId): Task
    {
        return $this->model->create(
            [
                'title' => $title,
                'description' => $description,
                'task_list_id' => $taskListId,
                'date_time' => $dateTime,
                'task_list_id' => $taskListId,
            ]
        );
    }

    public function getById(int $id, int $userId): ?Task
    {
        return $this->model
                    ->join('task_lists', 'tasks.task_list_id', '=', 'task_lists.id')
                    ->where('tasks.id', $id)
                    ->where('task_lists.user_id', $userId)
                    ->first();
    }

    public function update(int $id, string $title, string $description, string $dateTime): bool
    {
        return $this->model->find($id)->update(
            [
                'title' => $title,
                'description' => $description,
                'date_time' => $dateTime
            ]
        );
    }

    public function delete(int $id): bool
    {
        return $this->model->where('id', $id)->delete();
    }

    public function setTaskComplete(int $id): bool
    {
        return $this->model
            ->where('tasks.id', $id)
            ->update(['completed' => true]);
    }
}
