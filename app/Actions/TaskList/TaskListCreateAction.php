<?php

namespace App\Actions\TaskList;

use App\Models\TaskList;
use App\Repositories\TaskList\TaskListInterfaceRepository;
use Illuminate\Support\Facades\Auth;

class TaskListCreateAction
{
    public function __construct(
        private readonly TaskListInterfaceRepository $taskListInterfaceRepository,
    ) {
    }

    public function execute(string $name): TaskList
    {
        $userId = Auth::id();

        return $this->taskListInterfaceRepository->create($name, $userId);
    }
}
