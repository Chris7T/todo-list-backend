<?php

namespace Tests\Feature\Google;

use App\Actions\Google\GoogleCallBackAction;
use App\Models\User;
use Exception;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class GoogleCallBackTest extends TestCase
{
    private const ROUTE = 'google.callback';
    private $token;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
    }

    public function test_expected_server_error_when_throw_exeption()
    {
        $googleCallBackActionMock = Mockery::mock(GoogleCallBackAction::class);
        $googleCallBackActionMock->shouldReceive('execute')
            ->andThrow(new Exception('Error'));

        $this->app->instance(GoogleCallBackAction::class, $googleCallBackActionMock);

        $response = $this->withToken($this->token)->getJson(route(self::ROUTE));

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'message' => config('messages.error.server'),
            ]);
    }

    public function test_expected_token_when_google_token_saved_successfully()
    {
        $googleCallBackActionMock = Mockery::mock(GoogleCallBackAction::class);
        $googleCallBackActionMock->shouldReceive('execute');

        $this->app->instance(GoogleCallBackAction::class, $googleCallBackActionMock);

        $response = $this->withToken($this->token)->getJson(route(self::ROUTE));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Google token saved successfully'
            ]);
    }
}
