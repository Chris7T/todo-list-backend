<?php

namespace App\Repositories\User;

use App\Models\User;

class UserEloquentRepository implements UserInterfaceRepository
{
    public function __construct(
        private readonly User $model,
    ) {
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->firstWhere('email', $email);
    }

    public function setGoogleToken(int $id, string $token): void
    {
        $this->model->where('id', $id)->update(['google_token' => $token]);
    }

    public function findById(int $id): ?User
    {
        return $this->model->find($id);
    }
}
