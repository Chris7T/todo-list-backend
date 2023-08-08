<?php

namespace App\Actions\Task;

use App\Actions\TaskList\TaskListGetAction;
use App\Repositories\Task\TaskInterfaceRepository;
use Illuminate\Pagination\Paginator;

class TaskListAction
{
    public function __construct(
        private readonly TaskInterfaceRepository $taskInterfaceRepository,
        private readonly TaskListGetAction $taskListGetAction
    ) {
    }

    public function execute(int $taskListId): Paginator
    {
        $this->taskListGetAction->execute($taskListId);

        return $this->taskInterfaceRepository->getAll($taskListId);
    }
}
