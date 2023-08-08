<?php

namespace App\Actions\TaskList;

use App\Models\TaskList;
use App\Repositories\TaskList\TaskListInterfaceRepository;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskListGetAction
{
    public function __construct(
        private readonly TaskListInterfaceRepository $taskListInterfaceRepository,
    ) {
    }

    public function execute(int $id): TaskList
    {
        $userId = Auth::id();
        $list = $this->taskListInterfaceRepository->getById($id);
        if(is_null($list) || $list->user_id != $userId) {
            throw new NotFoundHttpException('Task list not found.');
        }

        return $list;
    }
}
