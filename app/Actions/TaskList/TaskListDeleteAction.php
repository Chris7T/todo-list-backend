<?php

namespace App\Actions\TaskList;

use App\Repositories\TaskList\TaskListInterfaceRepository;

class TaskListDeleteAction
{
    public function __construct(
        private readonly TaskListInterfaceRepository $taskListInterfaceRepository,
        private readonly TaskListGetAction $taskListGetAction,
    ) {
    }

    public function execute(int $id): void
    {
        $this->taskListGetAction->execute($id);
        $this->taskListInterfaceRepository->delete($id);
    }
}
