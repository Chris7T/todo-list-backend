<?php

namespace Tests\Feature\Task;

use App\Actions\Task\TaskExportToGoogleAction;
use App\Models\User;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskExportFromGoogleTest extends TestCase
{
    private const ROUTE = 'task.google.export';
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
        $response = $this->getJson(route(self::ROUTE));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                    'message' => 'Unauthenticated.',
            ]);
    }

    public function test_expected_server_error_when_throw_any_exception()
    {
        $TaskExportToGoogleActionMock = Mockery::mock(TaskExportToGoogleAction::class);
        $TaskExportToGoogleActionMock->shouldReceive('execute')
            ->andThrow(new Exception(config('messages.error.server')));

        $this->app->instance(TaskExportToGoogleAction::class, $TaskExportToGoogleActionMock);

        $response = $this->withToken($this->token)->getJson(route(self::ROUTE));

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'message' => config('messages.error.server'),
            ]);
    }

    public function test_expected_return_of_created_data_when_export_successfully()
    {
        $TaskExportToGoogleActionMock = Mockery::mock(TaskExportToGoogleAction::class);
        $TaskExportToGoogleActionMock->shouldReceive('execute');

        $this->app->instance(TaskExportToGoogleAction::class, $TaskExportToGoogleActionMock);

        $response = $this->withToken($this->token)->getJson(route(self::ROUTE));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Task exported'
            ]);
    }
}
