<?php

namespace App\Actions\Task;

use App\Actions\TaskList\TaskListGetAction;
use App\Models\Task;
use App\Repositories\Task\TaskInterfaceRepository;

class TaskCreateAction
{
    public function __construct(
        private readonly TaskInterfaceRepository $taskInterfaceRepository,
        private readonly TaskListGetAction $taskListGetAction
    ) {
    }

    public function execute(string $title, string $description, string $dateTime, int $taskListId): Task
    {
        $this->taskListGetAction->execute($taskListId);

        return $this->taskInterfaceRepository->create($title, $description, $dateTime, $taskListId);
    }
}
