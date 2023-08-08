<?php

namespace App\Repositories\User;

use App\Models\User;

interface UserInterfaceRepository
{
    public function create(array $data): User;

    public function findByEmail(string $email): ?User;

    public function setGoogleToken(int $id, string $token): void;

    public function findById(int $id): ?User;
}
