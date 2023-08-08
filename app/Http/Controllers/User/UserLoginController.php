<?php

namespace App\Http\Controllers\User;

use App\Actions\User\UserLoginAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserLoginController extends Controller
{
    public function __construct(
        private UserLoginAction $userLoginAction
    ) {
    }

    /**
     * @OA\Post(
     *     tags={"User"},
     *     path="/api/user/login",
     *     summary="User Login",
     *     @OA\RequestBody(
     *         description="Endpoint to authenticate a user. Returns an authentication token on success.",
     *         required=true,
     *         @OA\JsonContent(
     *            required={"email","password"},
     *            @OA\Property(property="email", type="string", format="email", description="User's email"),
     *            @OA\Property(property="password", type="string", description="User's password", example="12345678"),
     *         ),
     *     ),
     *      @OA\Response(
     *         response=200,
     *         description="Successful authentication",
     *         @OA\JsonContent(
     *            @OA\Property(property="token", type="string", description="JWT Authentication Token", example="eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0L2FwaS91c2V"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array",
     *                     @OA\Items(type="string", example="The email field is required."),
     *                     @OA\Items(type="string", example="The email field must be a valid email address.")
     *                 ),
     *             @OA\Property(property="password", type="array",
     *                     @OA\Items(type="string", example="The password field is required."),
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *            @OA\Property(property="message", type="string", example="Invalid credentials."),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *            @OA\Property(property="message",type="string", example="The given data was invalid."),
     *         ),
     *     )
     * )
     */
    public function __invoke(UserLoginRequest $request): JsonResponse
    {
        try {
            $token = $this->userLoginAction->execute($request->input('email'), $request->input('password'));
    
            return Response::json(['token' => $token], HttpResponse::HTTP_OK);
        } catch (UnauthorizedHttpException $ex) {
            return Response::json(
                ['message' => $ex->getMessage()],
                $ex->getStatusCode(),
            );
        } catch (\Exception $ex) {
            Log::critical('Controller: ' . self::class, ['exception' => $ex->getMessage()]);

            return Response::json(
                ['message' => config('messages.error.server')],
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
