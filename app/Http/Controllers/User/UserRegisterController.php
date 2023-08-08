<?php

namespace App\Http\Controllers\User;

use App\Actions\User\UserRegisterAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class UserRegisterController extends Controller
{
    public function __construct(
        private UserRegisterAction $userRegisterAction
    ) {
    }

    /**
     * @OA\Post(
     *     tags={"User"},
     *     path="/api/user/register",
     *     summary="Register a new user",
     *     description="Endpoint to register a new user.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", description="User's email"),
     *             @OA\Property(property="name", type="string", description="User's name", example="Name exemple"),
     *             @OA\Property(property="password", type="string", description="User's password", example="12345678"),
     *             @OA\Property(property="password_confirmation", type="string", description="Confirmation of user's password", example="12345678"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful registration",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", description="User's JWT token", example="eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0L2FwaS91c2V")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array",
     *                     @OA\Items(type="string", example="The name field is required.")
     *                 ),
     *                 @OA\Property(property="email", type="array",
     *                     @OA\Items(type="string", example="The email field is required.")
     *                 ),
     *                 @OA\Property(property="password", type="array",
     *                     @OA\Items(type="string", example="The password field is required."),
     *                     @OA\Items(type="string", example="The password field must be at least 8 characters.")
     *                 ),
     *                 @OA\Property(property="password_confirmation", type="array",
     *                     @OA\Items(type="string", example="The password field confirmation does not match.")
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Email is already being used",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email is already being used")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unable to generate token")
     *         )
     *     ),
     * )
     */
    public function __invoke(UserRegisterRequest $request): JsonResponse
    {
        try {
            $token = $this->userRegisterAction->execute($request->validated());
            return Response::json(['token' => $token], HttpResponse::HTTP_CREATED);
        } catch (ConflictHttpException $ex) {

            return Response::json(
                ['message' => $ex->getMessage()],
                $ex->getStatusCode()
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
