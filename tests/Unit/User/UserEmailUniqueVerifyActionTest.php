<?php

namespace Tests\Unit\User;

use App\Actions\User\UserEmailUniqueVerifyAction;
use App\Models\User;
use App\Repositories\User\UserInterfaceRepository;
use Tests\TestCase;

;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class UserEmailUniqueVerifyActionTest extends TestCase
{
    private $repositoryMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = $this->createMock(UserInterfaceRepository::class);
    }

    public function test_expected_no_exception_when_email_is_unique(): void
    {
        $email = 'test@example.com';

        $this->repositoryMock->expects($this->once())
            ->method('findByEmail')
            ->with($email)
            ->willReturn(null);

        $action = new UserEmailUniqueVerifyAction($this->repositoryMock);

        $action->execute($email);
    }

    public function test_expected_conflict_http_exception_when_email_is_not_unique(): void
    {
        $email = 'test@example.com';
        $user = new User();
        $user->email = $email;

        $this->repositoryMock->expects($this->once())
            ->method('findByEmail')
            ->with($email)
            ->willReturn($user);

        $action = new UserEmailUniqueVerifyAction($this->repositoryMock);

        $this->expectException(ConflictHttpException::class);
        $this->expectExceptionMessage('Email is already being used');

        $action->execute($email);
    }
}
