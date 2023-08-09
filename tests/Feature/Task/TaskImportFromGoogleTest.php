<?php

namespace Tests\Feature\Task;

use App\Actions\Task\TaskImportFromGoogleAction;
use App\Models\User;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskImportFromGoogleTest extends TestCase
{
    private const ROUTE = 'task.google.import';
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
        $taskImportFromGoogleActionMock = Mockery::mock(TaskImportFromGoogleAction::class);
        $taskImportFromGoogleActionMock->shouldReceive('execute')
            ->andThrow(new Exception('Error'));

        $this->app->instance(TaskImportFromGoogleAction::class, $taskImportFromGoogleActionMock);

        $response = $this->withToken($this->token)->getJson(route(self::ROUTE));

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'message' => config('messages.error.server'),
            ]);
    }

    public function test_expected_sucess_mensagem_when_import_successfully()
    {
        $taskImportFromGoogleActionMock = Mockery::mock(TaskImportFromGoogleAction::class);
        $taskImportFromGoogleActionMock->shouldReceive('execute');

        $this->app->instance(TaskImportFromGoogleAction::class, $taskImportFromGoogleActionMock);

        $response = $this->withToken($this->token)->getJson(route(self::ROUTE));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Task imported'
            ]);
    }
}
