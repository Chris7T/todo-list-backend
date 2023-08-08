<?php

namespace Tests\Unit\User;

use App\Actions\TaskList\TaskListListAction;
use App\Repositories\TaskList\TaskListInterfaceRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskListListActionTest extends TestCase
{
    public function expected_not_found_exception_when_task_list_is_null()
    {
        $this->expectException(NotFoundHttpException::class);

        $userId = 1;
        $list = new Paginator(5, 0, 1);

        Auth::shouldReceive('id')->andReturn($userId);

        $taskListInterfaceRepositoryMock = Mockery::mock(TaskListInterfaceRepository::class);

        $taskListInterfaceRepositoryMock->shouldReceive('getAll')
            ->with($userId)
            ->once()
            ->andReturn($list);

        $action = new TaskListListAction($taskListInterfaceRepositoryMock);
        $return = $action->execute();

        $this->assertEquals($list, $return);
    }
}
