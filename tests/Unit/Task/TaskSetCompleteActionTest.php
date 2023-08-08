<?php

namespace Tests\Unit\Actions\Task;

use App\Actions\Task\TaskSetCompleteAction;
use App\Actions\Task\TaskGetAction;
use App\Repositories\Task\TaskInterfaceRepository;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class TaskSetCompleteActionTest extends TestCase
{
    public function test_set_task_as_complete()
    {
        $taskId = 1;

        $taskGetAction = Mockery::mock(TaskGetAction::class);
        $taskGetAction->shouldReceive('execute')->with($taskId)->once();

        $taskInterfaceRepository = Mockery::mock(TaskInterfaceRepository::class);
        $taskInterfaceRepository->shouldReceive('setTaskComplete')->with($taskId)->once();

        $taskSetCompleteAction = new TaskSetCompleteAction($taskInterfaceRepository, $taskGetAction);
        $taskSetCompleteAction->execute($taskId);

        $taskInterfaceRepository->shouldHaveReceived('setTaskComplete');
        $taskGetAction->shouldHaveReceived('execute');
    }

    public function test_expected_not_found_when_there_not_is_task()
    {
        $taskId = 1;

        $taskGetAction = Mockery::mock(TaskGetAction::class);
        $taskGetAction->shouldReceive('execute')->with($taskId)->once()->andThrow(new NotFoundHttpException());

        $taskInterfaceRepository = Mockery::mock(TaskInterfaceRepository::class);

        $taskSetCompleteAction = new TaskSetCompleteAction($taskInterfaceRepository, $taskGetAction);

        $this->expectException(NotFoundHttpException::class);

        $taskSetCompleteAction->execute($taskId);
    }
}
