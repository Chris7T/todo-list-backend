<?php

namespace Tests\Feature\Task;

use App\Actions\Task\TaskGetAction;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskSetCompleteTest extends TestCase
{
    private const ROUTE = 'task.complete';
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
        $response = $this->patchJson(route(self::ROUTE, $this->task->id), []);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                    'message' => 'Unauthenticated.',
            ]);
    }

    public function test_expected_not_found_when_task_not_exist()
    {
        $response = $this->withToken($this->token)->patchJson(route(self::ROUTE, 0));

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Task not found.',
            ]);
    }

    public function test_expected_not_found_when_task_not_belong_to_the_logged_user()
    {
        $task = Task::factory()->create();

        $response = $this->withToken($this->token)->patchJson(route(self::ROUTE, $task->id));

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Task not found.',
            ]);
    }

    public function test_expected_server_error_when_throw_any_exception()
    {
        $taskGetActionMock = Mockery::mock(TaskGetAction::class);
        $taskGetActionMock->shouldReceive('execute')
            ->andThrow(new Exception('Error'));

        $this->app->instance(TaskGetAction::class, $taskGetActionMock);

        $response = $this->withToken($this->token)->patchJson(route(self::ROUTE, $this->task->id));

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'message' => config('messages.error.server'),
            ]);
    }

    public function test_expected_sucess_mensagem_when_set_complete()
    {
        $response = $this->withToken($this->token)->patchJson(route(self::ROUTE, $this->task->id));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Task completed'
            ]);
    }
}
