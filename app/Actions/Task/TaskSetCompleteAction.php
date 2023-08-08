<?php

namespace App\Actions\Task;

use App\Repositories\Task\TaskInterfaceRepository;

class TaskSetCompleteAction
{
    public function __construct(
        private readonly TaskInterfaceRepository $taskInterfaceRepository,
        private readonly TaskGetAction $taskGetAction,
    ) {
    }

    public function execute(int $id): void
    {
        $this->taskGetAction->execute($id);
        $this->taskInterfaceRepository->setTaskComplete($id);
    }
}
