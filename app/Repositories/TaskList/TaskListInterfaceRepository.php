<?php

namespace App\Repositories\TaskList;

use App\Models\TaskList;
use Illuminate\Pagination\Paginator;

interface TaskListInterfaceRepository
{
    public function getAll(int $userId): Paginator;

    public function create(string $name, int $userId): TaskList;

    public function getById(int $id): ?TaskList;

    public function update(int $id, string $name): bool;

    public function delete(int $id): bool;

    public function setTaskComplete(int $id): bool;
}
