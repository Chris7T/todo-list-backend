<?php

namespace Tests\Feature\Google;

use App\Actions\Google\GoogleAuthAction;
use App\Models\User;
use Exception;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class GoogleAuthTest extends TestCase
{
    private const ROUTE = 'google.auth';
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
        $googleAuthActionMock = Mockery::mock(GoogleAuthAction::class);
        $googleAuthActionMock->shouldReceive('execute')
            ->andThrow(new Exception('Error'));

        $this->app->instance(GoogleAuthAction::class, $googleAuthActionMock);

        $response = $this->withToken($this->token)->getJson(route(self::ROUTE));

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'message' => config('messages.error.server'),
            ]);
    }

    public function test_expected_token_when_login_to_google_successfully()
    {
        $googleAuthActionMock = Mockery::mock(GoogleAuthAction::class);
        $googleAuthActionMock->shouldReceive('execute')
            ->andReturn('token');

        $this->app->instance(GoogleAuthAction::class, $googleAuthActionMock);

        $response = $this->withToken($this->token)->getJson(route(self::ROUTE));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'auth_url' => 'token'
            ]);
    }
}
