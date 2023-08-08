<?php

namespace Tests\Feature\Task;

use App\Actions\Task\TaskDeleteAction;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskDeleteTest extends TestCase
{
    private const ROUTE = 'task.delete';
    private $token;
    private $user;
    private $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
        $taskList = TaskList::factory()->create(['user_id' => $this->user->id]);
        $this->task = Task::factory()->create(['task_list_id' => $taskList->id]);
    }

    public function test_expected_true_when_route_exists()
    {
        $this->assertTrue(Route::has(self::ROUTE));
    }

    public function test_expected_unauthenticated_when_there_is_no_token()
    {
        $response = $this->deleteJson(route(self::ROUTE, $this->task->id), []);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                    'message' => 'Unauthenticated.',
            ]);
    }

    public function test_expected_not_found_when_task_not_exist()
    {
        $response = $this->withToken($this->token)->deleteJson(route(self::ROUTE, 0));

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Task not found.',
            ]);
    }

    public function test_expected_not_found_when_task_not_belong_to_the_logged_user()
    {
        $task = Task::factory()->create();

        $response = $this->withToken($this->token)->deleteJson(route(self::ROUTE, $task->id));

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Task not found.',
            ]);
    }

    public function test_expected_server_error_when_throw_any_exception()
    {
        $taskDeleteActionMock = Mockery::mock(TaskDeleteAction::class);
        $taskDeleteActionMock->shouldReceive('execute')
            ->andThrow(new Exception('Error'));

        $this->app->instance(TaskDeleteAction::class, $taskDeleteActionMock);

        $response = $this->withToken($this->token)->deleteJson(route(self::ROUTE, $this->task->id));

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'message' => config('messages.error.server'),
            ]);
    }

    public function test_expected_not_found_when_task_list_not_belong_to_the_logged_user()
    {
        $task = Task::factory()->create();
        $response = $this->withToken($this->token)->deleteJson(route(self::ROUTE, $task->id));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_expected_return_no_content_when_deleted_successfully()
    {
        $response = $this->withToken($this->token)->deleteJson(route(self::ROUTE, $this->task->id));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('tasks', [
            'title' => $this->task->title,
            'description' => $this->task->description,
            'deleted_at' => null
        ]);
    }
}
