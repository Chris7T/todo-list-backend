<?php

namespace Tests\Feature\TaskList;

use App\Models\TaskList;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskListListTest extends TestCase
{
    private const ROUTE = 'task-list.list';
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
        $response = $this->getJson(route(self::ROUTE), []);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                    'message' => 'Unauthenticated.',
            ]);
    }

    public function test_expected_list_empty_list_when_have_no_list_in_database()
    {
        $response = $this->withToken($this->token)->getJson(route(self::ROUTE, 0));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => []
            ]);
    }

    public function test_expected_list_of_list_when_has_data_in_the_database()
    {
        $list = TaskList::factory()->create(['user_id' => $this->user->id]);
        $response = $this->withToken($this->token)->getJson(route(self::ROUTE, $list->id));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => [
                    [
                        'name' => $list->name,
                        'user_id' => $list->user_id
                    ]
                ]
            ]);
    }
}
