<?php

namespace App\Actions\Google;

use Google_Client as GoogleCliente;
use Google_Service_Tasks as GoogleServiceTasks;
use App\Repositories\User\UserInterfaceRepository;

class GetGoogleTaskListAction
{
    public function __construct(
        private readonly GoogleCliente $googleCliente,
        private readonly UserInterfaceRepository $userRepository
    ) {
        $this->googleCliente->setAuthConfig(config('google.client_secret'));
        $this->googleCliente->addScope(GoogleServiceTasks::TASKS);
    }

    public function execute(string $userAccessToken): array
    {
        $this->googleCliente->setAccessToken($userAccessToken);
        $googleService = new GoogleServiceTasks($this->googleCliente);

        return $googleService->tasklists->listTasklists()->getItems();
    }
}
