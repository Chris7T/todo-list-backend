<?php

namespace Tests\Unit\Task;

use App\Actions\Task\TaskDeleteAction;
use App\Actions\Task\TaskGetAction;
use App\Repositories\Task\TaskInterfaceRepository;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class TaskDeleteActionTest extends TestCase
{
    public function test_can_delete_task()
    {
        $taskId = 1;

        $taskGetAction = Mockery::mock(TaskGetAction::class);
        $taskGetAction->shouldReceive('execute')->with($taskId)->once();

        $taskInterfaceRepository = Mockery::mock(TaskInterfaceRepository::class);
        $taskInterfaceRepository->shouldReceive('delete')->with($taskId)->once();

        $taskDeleteAction = new TaskDeleteAction($taskInterfaceRepository, $taskGetAction);
        $taskDeleteAction->execute($taskId);

        $taskInterfaceRepository->shouldHaveReceived('delete');
        $taskGetAction->shouldHaveReceived('execute');
    }

    public function test_expected_not_found_when_there_not_is_task()
    {
        $taskId = 1;

        $taskGetAction = Mockery::mock(TaskGetAction::class);
        $taskGetAction->shouldReceive('execute')->with($taskId)->once()->andThrow(new NotFoundHttpException());

        $taskInterfaceRepository = Mockery::mock(TaskInterfaceRepository::class);

        $taskDeleteAction = new TaskDeleteAction($taskInterfaceRepository, $taskGetAction);

        $this->expectException(NotFoundHttpException::class);

        $taskDeleteAction->execute($taskId);
    }
}
