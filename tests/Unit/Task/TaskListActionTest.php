<?php

namespace Tests\Unit\Task;

use App\Actions\Task\TaskListAction;
use App\Actions\TaskList\TaskListGetAction;
use App\Models\TaskList;
use App\Repositories\Task\TaskInterfaceRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class TaskListActionTest extends TestCase
{
    public function test_expected_not_found_when_tasl_list_not_belong_to_logged_user()
    {
        $userId = 1;
        $taskListId = 1;
        Auth::shouldReceive('id')->andReturn($userId);

        $taskInterfaceRepository = Mockery::mock(TaskInterfaceRepository::class);
        $taskListGetAction = Mockery::mock(TaskListGetAction::class);
        $taskListGetAction->shouldReceive('execute')->with($taskListId)->once()->andThrow(new NotFoundHttpException());

        $taskListAction = new TaskListAction($taskInterfaceRepository, $taskListGetAction);
        $this->expectException(NotFoundHttpException::class);

        $response = $taskListAction->execute($taskListId);

        $this->assertInstanceOf(Paginator::class, $response);
        $taskInterfaceRepository->shouldHaveReceived('getAll');
    }

    public function test_expected_list_all_tasks_relating_to_logged_user()
    {
        $userId = 1;
        $taskListId = 1;

        Auth::shouldReceive('id')->andReturn($userId);
        $taskListGetAction = Mockery::mock(TaskListGetAction::class);
        $taskListGetAction->shouldReceive('execute');
        $taskInterfaceRepository = Mockery::mock(TaskInterfaceRepository::class);
        $taskInterfaceRepository->shouldReceive('getAll')->with($taskListId)->once()->andReturn(new Paginator([], 0, 5));

        $taskListAction = new TaskListAction($taskInterfaceRepository, $taskListGetAction);
        $response = $taskListAction->execute($taskListId);

        $this->assertInstanceOf(Paginator::class, $response);
        $taskInterfaceRepository->shouldHaveReceived('getAll');
    }
}
