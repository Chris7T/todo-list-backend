<?php

namespace Tests\Feature\TaskList;

use App\Actions\TaskList\TaskListCreateAction;
use App\Models\User;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskListCreateTest extends TestCase
{
    private const ROUTE = 'task-list.register';
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

    public function test_expected_unprocessable_entity_exception_when_name_is_null()
    {
        $response = $this->withToken($this->token)->postJson(route(self::ROUTE, ['name' => null]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name'])
            ->assertJson([
                'errors' => [
                    'name' => ['The name field is required.'],
                ],
            ]);
    }

    public function test_expected_server_error_when_throw_any_exception()
    {
        $name = $this->faker->word();
        $request = [
            'name' => $name,
        ];

        $tasklistCreateActionActionMock = Mockery::mock(TaskListCreateAction::class);
        $tasklistCreateActionActionMock->shouldReceive('execute')
            ->andThrow(new Exception('Error'));

        $this->app->instance(TaskListCreateAction::class, $tasklistCreateActionActionMock);

        $response = $this->withToken($this->token)->postJson(route(self::ROUTE, $request));

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'message' => config('messages.error.server'),
            ]);
    }

    public function test_expected_return_of_created_data()
    {
        $name = $this->faker->word();
        $request = [
            'name' => $name,
        ];

        $response = $this->withToken($this->token)->postJson(route(self::ROUTE, $request));

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'data' => [
                    'name' => $name,
                    'user_id' => $this->user->id
                ]
            ]);

        $this->assertDatabaseHas('task_lists', [
            'name' => $name,
            'user_id' => $this->user->id
        ]);
    }
}
