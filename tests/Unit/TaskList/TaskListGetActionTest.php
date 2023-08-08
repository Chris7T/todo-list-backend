<?php

namespace Tests\Unit\User;

use App\Actions\TaskList\TaskListGetAction;
use App\Models\TaskList;
use App\Repositories\TaskList\TaskListInterfaceRepository;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskListGetActionTest extends TestCase
{
    public function test_expected_not_found_exception_when_task_list_is_null()
    {
        $this->expectException(NotFoundHttpException::class);

        $userId = 1;
        $id = 1;

        Auth::shouldReceive('id')->andReturn($userId);

        $taskListInterfaceRepositoryMock = Mockery::mock(TaskListInterfaceRepository::class);

        $taskListInterfaceRepositoryMock->shouldReceive('getById')
            ->with($id)
            ->once()
            ->andReturn(null);

        $action = new TaskListGetAction($taskListInterfaceRepositoryMock);
        $action->execute($id);
    }

    public function test_expected_not_found_exception_when_task_list_is_not_belong_logged_user()
    {
        $this->expectException(NotFoundHttpException::class);

        $anotherUser = 2;
        $userId = 1;
        $id = 1;
        $taskList = new TaskList();
        $taskList->user_id = $userId;

        Auth::shouldReceive('id')->andReturn($anotherUser);

        $taskListInterfaceRepositoryMock = Mockery::mock(TaskListInterfaceRepository::class);

        $taskListInterfaceRepositoryMock->shouldReceive('getById')
            ->with($id)
            ->once()
            ->andReturn($taskList);

        $action = new TaskListGetAction($taskListInterfaceRepositoryMock);
        $action->execute($id);
    }

    public function test_expected_task_list()
    {
        $userId = 1;
        $id = 1;
        $taskList = new TaskList();
        $taskList->user_id = $userId;
        $taskList->id = $id;

        Auth::shouldReceive('id')->andReturn($userId);

        $taskListInterfaceRepositoryMock = Mockery::mock(TaskListInterfaceRepository::class);

        $taskListInterfaceRepositoryMock->shouldReceive('getById')
            ->with($id)
            ->once()
            ->andReturn($taskList);

        $action = new TaskListGetAction($taskListInterfaceRepositoryMock);
        $return = $action->execute($id);

        $this->assertEquals($taskList, $return);
    }

}
