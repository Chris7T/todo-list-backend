<?php

namespace App\Actions\Task;

use App\Actions\Google\GetGoogleTaskAction;
use App\Actions\Google\GetGoogleTaskListAction;
use App\Actions\TaskList\TaskListCreateAction;
use App\Repositories\Task\TaskInterfaceRepository;
use App\Repositories\User\UserInterfaceRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TaskImportFromGoogleAction
{
    public function __construct(
        private readonly TaskInterfaceRepository $taskRepository,
        private readonly GetGoogleTaskListAction $getGoogleTaskListAction,
        private readonly UserInterfaceRepository $userRepository,
        private readonly GetGoogleTaskAction $getGoogleTaskAction,
        private readonly TaskListCreateAction $taskListCreateAction,
        private readonly TaskCreateAction $taskCreateAction
    ) {
    }

    public function execute(): void
    {
        $userId = Auth::id();
        $user = $this->userRepository->findById($userId);
        $userAccessToken = $user->google_token;
        $taskLists = $this->getGoogleTaskListAction->execute($userAccessToken);
        $tasks = [];

        foreach ($taskLists as $taskList) {
            $taskId = $this->taskListCreateAction->execute($taskList->title)->getKey();
            $tasks = $this->getGoogleTaskAction->execute($userAccessToken, $taskList->id);
            $googleApi = $taskList->id;
            foreach ($tasks as $task) {
                $descricao = $task->notes;
                $dateTime = Carbon::parse($task->due)->format('Y-m-d H:i:s');
                $this->taskCreateAction->execute($task->title, $descricao, $dateTime, $taskId, $googleApi);
            }
        }
    }
}
