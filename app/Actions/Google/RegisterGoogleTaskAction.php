<?php

namespace App\Actions\Google;

use Google_Client as GoogleCliente;
use Google_Service_Tasks as GoogleServiceTasks;
use Google_Service_Tasks_TaskList as GoogleServiceTasksTaskList;

class RegisterGoogleTaskAction
{
    public function __construct(
        private readonly GoogleCliente $googleCliente,
    ) {
        $this->googleCliente->setAuthConfig(config('google.client_secret'));
        $this->googleCliente->addScope(GoogleServiceTasks::TASKS);
    }

    public function execute(string $userAccessToken, string $taskName): string
    {
        $this->googleCliente->setAccessToken($userAccessToken);
        $service = new GoogleServiceTasks($this->googleCliente);
        $newTaskList = new GoogleServiceTasksTaskList();
        $newTaskList->setTitle($taskName);
        $createdTaskList = $service->tasklists->insert($newTaskList);

        return $createdTaskList->getId();
    }
}
