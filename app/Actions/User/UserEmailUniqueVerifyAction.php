<?php

namespace App\Actions\User;

use App\Repositories\User\UserInterfaceRepository;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class UserEmailUniqueVerifyAction
{
    public function __construct(
        private readonly UserInterfaceRepository $userInterfaceRepository
    ) {
    }

    public function execute(string $email): void
    {
        $user = $this->userInterfaceRepository->findByEmail($email);

        if (!is_null($user)) {
            throw new ConflictHttpException('Email is already being used');
        }
    }
}
