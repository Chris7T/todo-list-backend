<?php

namespace Tests\Unit\Task;

use App\Actions\TaskList\TaskListGetAction;
use App\Models\TaskList;
use App\Repositories\TaskList\TaskListInterfaceRepository;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskGetActionTest extends TestCase
{
    protected $taskListInterfaceRepository;
    protected $taskListGetAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskListInterfaceRepository = Mockery::mock(TaskListInterfaceRepository::class);
        $this->taskListGetAction = new TaskListGetAction($this->taskListInterfaceRepository);
    }

    public function test_expected_exception_when_getById_returns_null()
    {
        $id = 1;
        Auth::shouldReceive('id')->andReturn($id);
        $this->taskListInterfaceRepository->shouldReceive('getById')->with($id)->andReturnNull();

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Task list not found.');

        $this->taskListGetAction->execute($id);
    }

    public function test_expected_exception_when_userId_mismatch()
    {
        $id = 1;
        $loggedInUserId = 1;
        $otherUserId = 2;

        $taskList = new TaskList();
        $taskList->user_id = $otherUserId;

        Auth::shouldReceive('id')->andReturn($loggedInUserId);
        $this->taskListInterfaceRepository->shouldReceive('getById')->with($id)->andReturn($taskList);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Task list not found.');

        $this->taskListGetAction->execute($id);
    }

    public function test_expected_taskList_when_userId_match()
    {
        $id = 1;
        $userId = 1;

        $taskList = new TaskList();
        $taskList->user_id = $userId;

        Auth::shouldReceive('id')->andReturn($userId);
        $this->taskListInterfaceRepository->shouldReceive('getById')->with($id)->andReturn($taskList);

        $result = $this->taskListGetAction->execute($id);

        $this->assertEquals($taskList, $result);
    }
}
