<?php

namespace Tests\Feature\TaskList;

use App\Actions\TaskList\TaskListUpdateAction;
use App\Models\TaskList;
use App\Models\User;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskListUpdateTest extends TestCase
{
    private const ROUTE = 'task-list.update';
    private $token;
    private $user;
    private $list;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
        $this->list = TaskList::factory()->create(['user_id' => $this->user->id]);
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

    public function test_expected_unprocessable_entity_exception_when_name_is_null()
    {
        $response = $this->withToken($this->token)->putJson(route(self::ROUTE, $this->list->id), ['name' => null]);

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

        $listUpdateActionMock = Mockery::mock(TaskListUpdateAction::class);
        $listUpdateActionMock->shouldReceive('execute')
            ->andThrow(new Exception(config('messages.error.server')));

        $this->app->instance(TaskListUpdateAction::class, $listUpdateActionMock);

        $response = $this->withToken($this->token)->putJson(route(self::ROUTE, $this->list->id), $request);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'message' => config('messages.error.server'),
            ]);
    }

    public function test_expected_sucess_mensagem_when_update_successfully()
    {
        $name = $this->faker->word();
        $request = [
            'name' => $name,
        ];

        $response = $this->withToken($this->token)->putJson(route(self::ROUTE, $this->list->id), $request);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'TaskList updated'
            ]);

        $this->assertDatabaseHas('task_lists', [
            'name' => $name,
            'user_id' => $this->user->id
        ]);
    }
}
