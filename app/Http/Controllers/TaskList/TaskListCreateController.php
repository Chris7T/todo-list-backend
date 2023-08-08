<?php

namespace App\Http\Controllers\TaskList;

use App\Actions\TaskList\TaskListCreateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaskListRequest;
use App\Http\Resources\TaskListResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class TaskListCreateController extends Controller
{
    public function __construct(
        private TaskListCreateAction $taskListCreateAction
    ) {
    }

    /**
     * @OA\Get(
     *     tags={"Task List"},
     *     path="/api/task-list/register",
     *     summary="Register a task list",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="The name of the task",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="name", type="string", example="task_name"),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated."),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array",
     *                     @OA\Items(type="string", example="The name field is required."),
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     */
    public function __invoke(TaskListRequest $request): JsonResponse|JsonResource
    {
        try {
            return TaskListResource::make(
                $this->taskListCreateAction->execute(
                    $request->input('name'),
                )
            )
                ->response()
                ->setStatusCode(HttpResponse::HTTP_CREATED);
        } catch (\Exception $ex) {
            Log::critical('Controller: ' . self::class, ['exception' => $ex->getMessage()]);

            return Response::json(
                ['message' => config('messages.error.server')],
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
