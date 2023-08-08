<?php

namespace Tests\Unit\User;

use App\Actions\User\UserLoginAction;
use App\Models\User;
use App\Repositories\User\UserInterfaceRepository;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserLoginActionTest extends TestCase
{
    private $repositoryMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = $this->createMock(UserInterfaceRepository::class);
    }

    public function test_expected_jwt_token_when_valid_credentials_are_provided(): void
    {
        $email = 'test@example.com';
        $password = 'password123';

        $user = new User();
        $user->email = $email;
        $user->password = Hash::make($password);
        $this->repositoryMock->expects($this->once())
            ->method('findByEmail')
            ->with($email)
            ->willReturn($user);

        JWTAuth::shouldReceive('fromUser')
            ->once()
            ->with($user)
            ->andReturn('jwt_token');

        $action = new UserLoginAction($this->repositoryMock);

        $jwtToken = $action->execute($email, $password);

        $this->assertSame('jwt_token', $jwtToken);
    }

    public function test_expected_unauthorized_http_exception_when_invalid_credentials_are_provided(): void
    {
        $email = 'test@example.com';
        $password = 'invalid_password';

        $user = new User();
        $user->email = $email;
        $user->password = Hash::make('valid_password');

        $this->repositoryMock->expects($this->once())
            ->method('findByEmail')
            ->with($email)
            ->willReturn($user);

        $action = new UserLoginAction($this->repositoryMock);

        $this->expectException(UnauthorizedHttpException::class);
        $this->expectExceptionMessage('Invalid credentials');

        $action->execute($email, $password);
    }
}
