<?php

namespace App\Actions\Google;

use Google_Client as GoogleCliente;
use Google_Service_Tasks as GoogleServiceTasks;

class GetGoogleTaskAction
{
    public function __construct(
        private readonly GoogleCliente $googleCliente,
    ) {
        $this->googleCliente->setAuthConfig(config('google.client_secret'));
        $this->googleCliente->addScope(GoogleServiceTasks::TASKS);
    }

    public function execute(string $userAccessToken, string $tasklistId): array
    {
        $this->googleCliente->setAccessToken($userAccessToken);
        $googleService = new GoogleServiceTasks($this->googleCliente);

        return $googleService->tasks->listTasks($tasklistId)->getItems();
    }
}
