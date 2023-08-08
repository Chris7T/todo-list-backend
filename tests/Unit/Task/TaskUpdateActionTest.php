<?php

namespace Tests\Unit\Actions\Task;

use App\Actions\Task\TaskUpdateAction;
use App\Actions\Task\TaskGetAction;
use App\Repositories\Task\TaskInterfaceRepository;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class TaskUpdateActionTest extends TestCase
{
    public function test_task_update()
    {
        $taskId = 1;
        $title = 'Updated Task Title';
        $description = 'Updated Task Description';
        $dateTime = now()->format('Y-m-d H:i:s');

        $taskGetAction = Mockery::mock(TaskGetAction::class);
        $taskGetAction->shouldReceive('execute')->with($taskId)->once();

        $taskInterfaceRepository = Mockery::mock(TaskInterfaceRepository::class);
        $taskInterfaceRepository->shouldReceive('update')->with($taskId, $title, $description, $dateTime)->once();

        $taskUpdateAction = new TaskUpdateAction($taskInterfaceRepository, $taskGetAction);
        $taskUpdateAction->execute($taskId, $title, $description, $dateTime);

        $taskInterfaceRepository->shouldHaveReceived('update');
        $taskGetAction->shouldHaveReceived('execute');
    }

    public function test_expected_not_found_when_there_not_is_task()
    {
        $taskId = 1;
        $title = 'Updated Task Title';
        $description = 'Updated Task Description';
        $dateTime = now()->format('Y-m-d H:i:s');

        $taskGetAction = Mockery::mock(TaskGetAction::class);
        $taskGetAction->shouldReceive('execute')->with($taskId)->once()->andThrow(new NotFoundHttpException());

        $taskInterfaceRepository = Mockery::mock(TaskInterfaceRepository::class);

        $taskUpdateAction = new TaskUpdateAction($taskInterfaceRepository, $taskGetAction);

        $this->expectException(NotFoundHttpException::class);

        $taskUpdateAction->execute($taskId, $title, $description, $dateTime);
    }
}
