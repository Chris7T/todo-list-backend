<?php

namespace App\Http\Controllers\Google;

use App\Actions\Google\GoogleAuthAction;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class GoogleAuthController extends Controller
{
    public function __construct(
        private readonly GoogleAuthAction $googleAuthAction
    ) {
    }

    /**
     * @OA\Get(
     *      path="/api/google/auth",
     *      operationId="getUserGoogleAuth",
     *      tags={"Authentication"},
     *      summary="Authenticate user with Google",
     *      description="This API is used to authenticate a user with Google.",
     *      @OA\Response(
     *          response=200,
     *          description="Authentication was successful.",
     *          @OA\JsonContent(
     *              @OA\Property(property="auth_url", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="An unknown error occurred.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The request could not be completed due to an unknown error. Please contact support!")
     *          )
     *      )
     * )
     */
    public function __invoke(): JsonResponse
    {
        try {
            $authUrl = $this->googleAuthAction->execute();
    
            return Response::json(
                ['auth_url' => $authUrl],
                HttpResponse::HTTP_OK,
            );
        } catch (Exception $ex) {
            Log::critical('Controller: ' . self::class, ['exception' => $ex->getMessage()]);

            return Response::json(
                ['message' => config('messages.error.server')],
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
