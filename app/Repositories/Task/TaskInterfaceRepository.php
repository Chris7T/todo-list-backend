<?php

namespace App\Repositories\Task;

use App\Models\Task;
use Illuminate\Pagination\Paginator;

interface TaskInterfaceRepository
{
    public function getAll(int $taskListId): Paginator;

    public function create(string $title, string $description, string $dateTime, int $taskListId): Task;

    public function getById(int $id, int $userId): ?Task;

    public function update(int $id, string $title, string $description, string $dateTime): bool;

    public function delete(int $id): bool;

    public function setTaskComplete(int $id): bool;
}
