<?php

namespace Tests\Feature\TaskList;

use App\Actions\TaskList\TaskListDeleteAction;
use App\Models\TaskList;
use App\Models\User;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskListDeleteTest extends TestCase
{
    private const ROUTE = 'task-list.delete';
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
        $response = $this->deleteJson(route(self::ROUTE, $this->list->id), []);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                    'message' => 'Unauthenticated.',
            ]);
    }

    public function test_expected_not_found_when_list_not_exist()
    {
        $response = $this->withToken($this->token)->deleteJson(route(self::ROUTE, 0));

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Task list not found.',
            ]);
    }

    public function test_expected_not_found_when_list_not_belong_to_the_logged_user()
    {
        $list = TaskList::factory()->create();

        $response = $this->withToken($this->token)->deleteJson(route(self::ROUTE, $list->id));

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Task list not found.',
            ]);
    }

    public function test_expected_server_error_when_throw_any_exception()
    {
        $listDeleteActionMock = Mockery::mock(TaskListDeleteAction::class);
        $listDeleteActionMock->shouldReceive('execute')
            ->andThrow(new Exception('Error'));

        $this->app->instance(TaskListDeleteAction::class, $listDeleteActionMock);

        $response = $this->withToken($this->token)->deleteJson(route(self::ROUTE, $this->list->id));

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'message' => config('messages.error.server'),
            ]);
    }

    public function test_expected_return_no_content_when_deleted_successfully()
    {
        $response = $this->withToken($this->token)->deleteJson(route(self::ROUTE, $this->list->id));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('task_lists', [
            'name' => $this->list->name,
            'deleted_at' => null
        ]);
    }
}
