<?php

namespace App\Http\Controllers\Google;

use App\Actions\Google\GoogleCallBackAction;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class GoogleCallBackController extends Controller
{
    public function __construct(
        private readonly GoogleCallBackAction $googleCallBackAction
    ) {
    }

    /**
     * @OA\Get(
     *      path="/api/oauth2-callback",
     *      operationId="getGoogleOauthCallback",
     *      tags={"Authentication"},
     *      summary="Google authentication callback",
     *      description="This API is the callback for Google authentication.",
     *      @OA\Response(
     *          response=200,
     *          description="Google token saved successfully.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Google token saved successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Authorization code not provided.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Authorization code not provided")
     *          )
     *      ),
     *      @OA\Response(
     *          response=503,
     *          description="Error fetching access token.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Error fetching access token")
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
            $this->googleCallBackAction->execute();
    
            return Response::json(
                ['message' => 'Google token saved successfully'],
                HttpResponse::HTTP_OK,
            );
        } catch (UnauthorizedHttpException $ex) {
            return Response::json(
                ['message' => $ex->getMessage()],
                $ex->getStatusCode(),
            );
        } catch (ServiceUnavailableHttpException $ex) {
            return Response::json(
                ['message' => $ex->getMessage()],
                $ex->getStatusCode(),
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
