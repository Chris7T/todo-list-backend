<?php

namespace App\Actions\TaskList;

use App\Repositories\TaskList\TaskListInterfaceRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

class TaskListListAction
{
    public function __construct(
        private readonly TaskListInterfaceRepository $taskListInterfaceRepository,
    ) {
    }

    public function execute(): Paginator
    {
        $userId = Auth::id();

        return $this->taskListInterfaceRepository->getAll($userId);
    }
}
