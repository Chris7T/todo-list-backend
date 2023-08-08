<?php

namespace App\Actions\TaskList;

use App\Repositories\TaskList\TaskListInterfaceRepository;

class TaskListUpdateAction
{
    public function __construct(
        private readonly TaskListInterfaceRepository $taskListInterfaceRepository,
        private readonly TaskListGetAction $taskListGetAction,
    ) {
    }

    public function execute(int $id, string $name): void
    {
        $this->taskListGetAction->execute($id);
        $this->taskListInterfaceRepository->update($id, $name);
    }
}
