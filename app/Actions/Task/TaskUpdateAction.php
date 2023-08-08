<?php

namespace App\Actions\Task;

use App\Repositories\Task\TaskInterfaceRepository;

class TaskUpdateAction
{
    public function __construct(
        private readonly TaskInterfaceRepository $taskInterfaceRepository,
        private readonly TaskGetAction $taskGetAction,
    ) {
    }

    public function execute(int $id, string $title, string $description, string $dateTime): void
    {
        $this->taskGetAction->execute($id);
        $this->taskInterfaceRepository->update($id, $title, $description, $dateTime);
    }
}
