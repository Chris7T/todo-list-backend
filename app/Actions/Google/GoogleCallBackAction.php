<?php

namespace App\Actions\Google;

use App\Repositories\User\UserInterfaceRepository;
use Google_Client as GoogleCliente;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class GoogleCallBackAction
{
    public function __construct(
        private readonly GoogleCliente $googleCliente,
        private readonly UserInterfaceRepository $userRepository
    ) {
    }

    public function execute(): void
    {
        $googleToken = $this->getClientWithAccessToken();

        $userId = Auth::id();
        $this->userRepository->setGoogleToken($userId, $googleToken);
    }

    private function getClientWithAccessToken(): string
    {
        if (!request('code')) {
            throw new UnauthorizedHttpException('Authorization code not provided');
        }
        $this->googleCliente->setAuthConfig(config('google.client_secret'));
        $this->googleCliente->setRedirectUri(config('google.redirect_url'));
        $this->googleCliente->fetchAccessTokenWithAuthCode(request('code'));
        if ($this->googleCliente->getAccessToken()) {
            return $this->googleCliente->getAccessToken()['access_token'];
        }

        throw new ServiceUnavailableHttpException('Error fetching access token');
    }
}
