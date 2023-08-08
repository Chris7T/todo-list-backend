<?php

namespace Tests\Unit\User;

use App\Actions\TaskList\TaskListCreateAction;
use App\Models\TaskList;
use App\Repositories\TaskList\TaskListInterfaceRepository;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

;

class TaskListCreateActionTest extends TestCase
{
    public function test_expected_task_model_when_create_data()
    {
        $name = 'test';
        $userId = 1;
        $task = new TaskList();
        $task->name = $name;
        $task->user_id = $userId;
        Auth::shouldReceive('id')->andReturn($userId);
        $repositoryMock = Mockery::mock(TaskListInterfaceRepository::class);
        $repositoryMock->shouldReceive('create')
            ->with($name, $userId)
            ->once()
            ->andReturn($task);
        $action = new TaskListCreateAction($repositoryMock);
        $return = $action->execute($name);

        $this->assertEquals($return, $task);
    }
}
