<?php

namespace App\Actions\Google;

use Google_Client as GoogleCliente;
use Google_Service_Tasks as GoogleServiceTasks;

class GoogleAuthAction
{
    public function __construct(
        private readonly GoogleCliente $googleCliente
    ) {
    }

    public function execute(): string
    {
        $this->googleCliente->setAuthConfig(config('google.client_secret'));
        $this->googleCliente->setRedirectUri(config('google.redirect_url'));
        $this->googleCliente->addScope(GoogleServiceTasks::TASKS);
        $this->googleCliente->setAccessType('offline');
        $this->googleCliente->setPrompt('select_account consent');

        return $this->googleCliente->createAuthUrl();
    }
}
