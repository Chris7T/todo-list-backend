<?php

namespace Tests\Feature\Task;

use App\Actions\Task\TaskUpdateAction;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskUpdateTest extends TestCase
{
    private const ROUTE = 'task.update';
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
        $response = $this->putJson(route(self::ROUTE, 0), []);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                    'message' => 'Unauthenticated.',
            ]);
    }

    public function test_expected_unprocessable_entity_exception_when_title_is_null()
    {
        $response = $this->withToken($this->token)->putJson(route(self::ROUTE, $this->task->id), ['title' => null]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['title'])
            ->assertJson([
                'errors' => [
                    'title' => ['The title field is required.'],
                ],
            ]);
    }

    public function test_expected_unprocessable_entity_exception_when_email_is_null()
    {
        $response = $this->withToken($this->token)->putJson(route(self::ROUTE, $this->task->id), ['description' => null]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['description'])
            ->assertJson([
                'errors' => [
                    'description' => ['The description field is required.'],
                ],
            ]);
    }

    public function test_expected_server_error_when_throw_any_exception()
    {
        $title = $this->faker->word();
        $description = $this->faker->word();
        $dateTime = now()->format('Y-m-d H:i:s');
        $request = [
            'title' => $title,
            'description' => $description,
            'date_time' => $dateTime,
        ];

        $taskUpdateActionMock = Mockery::mock(TaskUpdateAction::class);
        $taskUpdateActionMock->shouldReceive('execute')
            ->andThrow(new Exception(config('messages.error.server')));

        $this->app->instance(TaskUpdateAction::class, $taskUpdateActionMock);

        $response = $this->withToken($this->token)->putJson(route(self::ROUTE, $this->task->id), $request);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'message' => config('messages.error.server'),
            ]);
    }

    public function test_expected_sucess_mensagem_when_update__successfully()
    {
        $title = $this->faker->word();
        $description = $this->faker->word();
        $dateTime = now()->format('Y-m-d H:i:s');
        $request = [
            'title' => $title,
            'description' => $description,
            'date_time' => $dateTime,
        ];

        $response = $this->withToken($this->token)->putJson(route(self::ROUTE, $this->task->id), $request);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Task updated'
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => $title,
            'description' => $description,
        ]);
    }
}
