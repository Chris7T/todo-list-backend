<?php

namespace Tests\Feature\User;

use App\Actions\User\UserRegisterAction;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserRegisterTest extends TestCase
{
    private const ROUTE = 'user.register';

    public function test_expected_true_when_route_exists()
    {
        $this->assertTrue(Route::has(self::ROUTE));
    }

    public function test_expected_unprocessable_entity_exception_when_name_is_null()
    {
        $response = $this->postJson(route(self::ROUTE, ['name' => null]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name'])
            ->assertJson([
                'errors' => [
                    'name' => ['The name field is required.'],
                ],
            ]);
    }

    public function test_expected_unprocessable_entity_exception_when_email_is_null()
    {
        $response = $this->postJson(route(self::ROUTE, ['email' => null]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email'])
            ->assertJson([
                'errors' => [
                    'email' => ['The email field is required.'],
                ],
            ]);
    }

    public function test_expected_unprocessable_entity_exception_when_password_is_null()
    {
        $response = $this->postJson(route(self::ROUTE, ['password' => null]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['password'])
            ->assertJson([
                'errors' => [
                    'password' => ['The password field is required.'],
                ],
            ]);
    }

    public function test_expected_unprocessable_entity_exception_when_password_is_less_than_8_characteres()
    {
        $response = $this->postJson(route(self::ROUTE, ['password' => '123', 'password_confirmation' => '123']));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['password'])
            ->assertJson([
                'errors' => [
                    'password' => ['The password field must be at least 8 characters.'],
                ],
            ]);
    }

    public function test_expected_unprocessable_entity_exception_when_password_confirmation_is_null()
    {
        $request = [
            'email' => $this->faker->email(),
            'name' => $this->faker->name(),
            'password' =>  $this->faker->password(8)
        ];
        $response = $this->postJson(route(self::ROUTE, $request));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['password'])
            ->assertJson([
                'errors' => [
                    'password' => ['The password field confirmation does not match.'],
                ],
            ]);
    }

    public function test_expected_unprocessable_entity_exception_when_email_is_used_twice()
    {
        $email = User::factory()->create()->email;
        $name = $this->faker->name();
        $password =  $this->faker->password(8);
        $request = [
            'email' => $email,
            'name' => $name,
            'password' =>  $password,
            'password_confirmation' =>  $password,
        ];

        $response = $this->postJson(route(self::ROUTE, $request));

        $response->assertStatus(Response::HTTP_CONFLICT)
            ->assertJson([
                'message' => 'Email is already being used',
            ]);
    }

    public function test_expected_server_error_when_jwt_auth_throw_exception()
    {
        $email = $this->faker->unique()->email();
        $name = $this->faker->name();
        $password =  $this->faker->password(8);
        $request = [
            'email' => $email,
            'name' => $name,
            'password' =>  $password,
            'password_confirmation' =>  $password,
        ];

        $userRegisterActionMock = Mockery::mock(UserRegisterAction::class);
        $userRegisterActionMock->shouldReceive('execute')
            ->andThrow(new JWTException('Unable to generate token'));

        $this->app->instance(UserRegisterAction::class, $userRegisterActionMock);

        $response = $this->postJson(route(self::ROUTE, $request));

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'message' => config('messages.error.server'),
            ]);
    }

    public function test_token_expires_in_two_hours()
    {
        $email = $this->faker->unique()->email();
        $name = $this->faker->name();
        $password =  $this->faker->password(8);
        $request = [
            'email' => $email,
            'name' => $name,
            'password' =>  $password,
            'password_confirmation' =>  $password,
        ];

        $response = $this->postJson(route(self::ROUTE, $request));

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'token',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name' => $name
        ]);

        $token = $response->json('token');
        $payload = JWTAuth::setToken($token)->getPayload();

        $this->assertNotNull($payload->get('exp'));

        $expirationTime = Carbon::createFromTimestamp($payload->get('exp'));
        $issuedAtTime = Carbon::createFromTimestamp($payload->get('iat'));

        $this->assertEquals(120, $expirationTime->diffInMinutes($issuedAtTime));
    }

    public function test_expected_user_token_when_registered_successfully()
    {
        $email = $this->faker->unique()->email();
        $name = $this->faker->name();
        $password =  $this->faker->password(8);
        $request = [
            'email' => $email,
            'name' => $name,
            'password' =>  $password,
            'password_confirmation' =>  $password,
        ];

        $response = $this->postJson(route(self::ROUTE, $request));

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'token',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name' => $name
        ]);
    }
}
