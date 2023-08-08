<?php

namespace Tests\Feature\Task;

use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskListTest extends TestCase
{
    private const ROUTE = 'task.list';
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
        $response = $this->getJson(route(self::ROUTE, 0), []);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                    'message' => 'Unauthenticated.',
            ]);
    }

    public function test_expected_not_found_when_task_list_not_belong_to_the_logged_user()
    {
        $taskList = TaskList::factory()->create();
        $response = $this->withToken($this->token)->getJson(route(self::ROUTE, $taskList->id));

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Task list not found.'
            ]);
    }

    public function test_expected_task_empty_list_when_have_no_task_in_database()
    {
        $taskList = TaskList::factory()->create(['user_id' => $this->user->id]);
        $response = $this->withToken($this->token)->getJson(route(self::ROUTE, $taskList->id));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => []
            ]);
    }

    public function test_expected_list_of_task_when_has_data_in_the_database()
    {
        $taskList = TaskList::factory()->create(['user_id' => $this->user->id]);
        $task = Task::factory()->create(['task_list_id' => $taskList->id]);
        $response = $this->withToken($this->token)->getJson(route(self::ROUTE, $taskList->id));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => [
                    [
                        'title' => $task->title,
                        'description' => $task->description,
                        'task_list_id' => $taskList->id
                    ]
                ]
            ]);
    }
}
