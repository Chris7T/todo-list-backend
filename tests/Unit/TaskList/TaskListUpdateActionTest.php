<?php

namespace Tests\Unit\User;

use App\Actions\TaskList\TaskListDeleteAction;
use App\Actions\TaskList\TaskListGetAction;
use App\Actions\TaskList\TaskListUpdateAction;
use App\Repositories\TaskList\TaskListInterfaceRepository;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskListUpdateActionTest extends TestCase
{
    public function test_expected_task_model_when_create_data()
    {
        $this->expectNotToPerformAssertions();
        $id = 1;
        $name = 'name';

        $taskListInterfaceRepositoryMock = Mockery::mock(TaskListInterfaceRepository::class);
        $taskListGetActionMock = Mockery::mock(TaskListGetAction::class);

        $taskListInterfaceRepositoryMock->shouldReceive('update')
            ->with($id, $name)
            ->once();
        $taskListGetActionMock->shouldReceive('execute')
            ->with($id)
            ->once();
        $action = new TaskListUpdateAction($taskListInterfaceRepositoryMock, $taskListGetActionMock);
        $action->execute($id, $name);
    }

    public function test_expected_not_found_exception_when_task_list_is_not_found()
    {
        $id = 1;
        $name = 'name';

        $taskListInterfaceRepositoryMock = Mockery::mock(TaskListInterfaceRepository::class);
        $taskListGetActionMock = Mockery::mock(TaskListGetAction::class);

        $taskListGetActionMock->shouldReceive('execute')
            ->with($id)
            ->once()
            ->andThrow(new NotFoundHttpException());

        $this->expectException(NotFoundHttpException::class);

        $action = new TaskListDeleteAction($taskListInterfaceRepositoryMock, $taskListGetActionMock);
        $action->execute($id, $name);
    }
}
