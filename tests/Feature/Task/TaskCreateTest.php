<?php

namespace Tests\Feature\Task;

use App\Actions\Task\TaskCreateAction;
use App\Models\TaskList;
use App\Models\User;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskCreateTest extends TestCase
{
    private const ROUTE = 'task.register';
    private $token;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
    }

    public function test_expected_true_when_route_exists()
    {
        $this->assertTrue(Route::has(self::ROUTE));
    }

    public function test_expected_unauthenticated_when_there_is_no_token()
    {
        $response = $this->postJson(route(self::ROUTE, []));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                    'message' => 'Unauthenticated.',
            ]);
    }

    public function test_expected_unprocessable_entity_exception_when_title_is_null()
    {
        $response = $this->withToken($this->token)->postJson(route(self::ROUTE, ['title' => null]));

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
        $response = $this->withToken($this->token)->postJson(route(self::ROUTE, ['description' => null]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['description'])
            ->assertJson([
                'errors' => [
                    'description' => ['The description field is required.'],
                ],
            ]);
    }

    public function test_expected_not_found_when_task_list_id_is_invalid()
    {
        $title = $this->faker->word();
        $description = $this->faker->word();
        $dateTime = now()->format('Y-m-d H:i:s');
        $request = [
            'title' => $title,
            'description' => $description,
            'date_time' => $dateTime,
            'task_list_id' => 0
        ];

        $response = $this->withToken($this->token)->postJson(route(self::ROUTE, $request));

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Task list not found.'
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
            'task_list_id' => 0
        ];

        $taskCreateActionActionMock = Mockery::mock(TaskCreateAction::class);
        $taskCreateActionActionMock->shouldReceive('execute')
            ->andThrow(new Exception('Error'));

        $this->app->instance(TaskCreateAction::class, $taskCreateActionActionMock);

        $response = $this->withToken($this->token)->postJson(route(self::ROUTE, $request));

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'message' => config('messages.error.server'),
            ]);
    }

    public function test_expected_return_of_created_data()
    {
        $title = $this->faker->word();
        $description = $this->faker->word();
        $dateTime = now()->format('Y-m-d H:i:s');
        $taskListId = TaskList::factory()->create(['user_id' => $this->user->getKey()])->getKey();
        $request = [
            'title' => $title,
            'description' => $description,
            'date_time' => $dateTime,
            'task_list_id' => $taskListId
        ];

        $response = $this->withToken($this->token)->postJson(route(self::ROUTE, $request));

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'data' => [
                    'title' => $title,
                    'description' => $description,
                    'task_list_id' => $taskListId
                ]
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => $title,
            'description' => $description,
            'task_list_id' => $taskListId
        ]);
    }
}
