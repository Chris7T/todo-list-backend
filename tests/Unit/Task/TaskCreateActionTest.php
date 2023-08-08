<?php

namespace Tests\Unit\Task;

use App\Actions\Task\TaskCreateAction;
use App\Actions\TaskList\TaskListGetAction;
use App\Models\Task;
use App\Models\TaskList;
use App\Repositories\Task\TaskInterfaceRepository;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class TaskCreateActionTest extends TestCase
{
    private TaskCreateAction $action;
    private TaskInterfaceRepository $repositoryMock;
    private TaskListGetAction $taskListGetActionMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = Mockery::mock(TaskInterfaceRepository::class);
        $this->taskListGetActionMock = Mockery::mock(TaskListGetAction::class);
        $this->action = new TaskCreateAction($this->repositoryMock, $this->taskListGetActionMock);
    }

    public function test_expected_data_when_task_list_dont_belong_to_logged_user()
    {
        $userId = 1;
        $taskListId = 1;
        $title = 'Some Title';
        $description = 'Some Description';
        $dateTime = now()->format('Y-m-d H:i:s');

        $task = new Task();
        $task->title = $title;
        $task->user_id = $userId;
        $task->description = $description;
        $task->dateTime = $dateTime;
        $task->task_list_id = $taskListId;

        Auth::shouldReceive('id')->andReturn($userId);

        $this->taskListGetActionMock->shouldReceive('execute')
            ->once()
            ->andThrow(new NotFoundHttpException());

        $this->expectException(NotFoundHttpException::class);

        $this->action->execute($title, $description, $dateTime, $taskListId);
    }

    public function test_expected_data_when_task_list_belong_to_logged_user()
    {
        $userId = 1;
        $taskListId = 1;
        $title = 'Some Title';
        $description = 'Some Description';
        $dateTime = now()->format('Y-m-d H:i:s');

        $task = new Task();
        $task->title = $title;
        $task->user_id = $userId;
        $task->description = $description;
        $task->dateTime = $dateTime;
        $task->task_list_id = $taskListId;

        Auth::shouldReceive('id')->andReturn($userId);

        $this->taskListGetActionMock->shouldReceive('execute')
            ->once()
            ->andReturn(new TaskList());

        $this->repositoryMock->shouldReceive('create')
            ->once()
            ->andReturn($task);

        $return = $this->action->execute($title, $description, $dateTime, $taskListId);

        $this->assertEquals($return, $task);
    }
}
