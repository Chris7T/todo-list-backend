<?php

namespace App\Actions\Google;

use Google_Client as GoogleCliente;
use Google_Service_Tasks as GoogleServiceTasks;
use Google_Service_Tasks_Task as GoogleServiceTasksTask;
use DateTime;
use DateTimeZone;

class RegisterGoogleTaskListAction
{
    public function __construct(
        private readonly GoogleCliente $googleCliente,
    ) {
        $this->googleCliente->setAuthConfig(config('google.client_secret'));
        $this->googleCliente->addScope(GoogleServiceTasks::TASKS);
    }

    public function execute(string $userAccessToken, string $title, string $description, string $dateTime, string $createdTaskListId): void
    {
        $this->googleCliente->setAccessToken($userAccessToken);
        $service = new GoogleServiceTasks($this->googleCliente);
        $dueDateTime = new DateTime($dateTime);
        $dueDateTime->setTimezone(new DateTimeZone('UTC'));
        $googleTask = new GoogleServiceTasksTask();
        $googleTask->setTitle($title);
        $googleTask->setNotes($description);
        $googleTask->setDue($dueDateTime->format(DateTime::RFC3339));
        $service->tasks->insert($createdTaskListId, $googleTask);
    }
}
