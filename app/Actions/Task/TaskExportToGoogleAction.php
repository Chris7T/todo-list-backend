<?php

namespace App\Actions\Task;

use App\Actions\Google\RegisterGoogleTaskAction;
use App\Actions\Google\RegisterGoogleTaskListAction;
use App\Actions\TaskList\TaskListListAction;
use App\Repositories\User\UserInterfaceRepository;
use Illuminate\Support\Facades\Auth;

class TaskExportToGoogleAction
{
    public function __construct(
        private readonly TaskListAction $taskListAction,
        private readonly TaskListListAction $taskListListAction,
        private readonly UserInterfaceRepository $userRepository,
        private readonly RegisterGoogleTaskAction $registerGoogleTaskAction,
        private readonly RegisterGoogleTaskListAction $registerGoogleTaskListAction
    ) {
    }

    public function execute(): void
    {
        $userId = Auth::id();
        $user = $this->userRepository->findById($userId);
        $userAccessToken = $user->google_token;
        $taskLists = $this->taskListListAction->execute();
        foreach ($taskLists as $taskList) {
            $tasks = $this->taskListAction->execute($taskList->getKey());
            $createdTaskListId = $this->registerGoogleTaskAction->execute($userAccessToken, $taskList->name);
            foreach ($tasks as $task) {
                $this->registerGoogleTaskListAction->execute(
                        $userAccessToken,
                        $task->title,
                        $task->description,
                        $task->date_time,
                        $createdTaskListId
                    );
            }
        }
    }
}
