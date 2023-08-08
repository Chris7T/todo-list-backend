<?php

namespace App\Actions\Task;

use App\Models\Task;
use App\Repositories\Task\TaskInterfaceRepository;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskGetAction
{
    public function __construct(
        private readonly TaskInterfaceRepository $taskInterfaceRepository,
    ) {
    }

    public function execute(int $id): Task
    {
        $userId = Auth::id();
        $task = $this->taskInterfaceRepository->getById($id, $userId);
        if(is_null($task)) {
            throw new NotFoundHttpException('Task not found.');
        }

        return $task;
    }
}
