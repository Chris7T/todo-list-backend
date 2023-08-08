<?php

namespace App\Http\Controllers\TaskList;

use App\Actions\TaskList\TaskListListAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskListResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class TaskListListController extends Controller
{
    public function __construct(
        private TaskListListAction $taskListListAction
    ) {
    }

    /**
     * @OA\Get(
     *     tags={"Task List"},
     *     path="/api/task-list/list",
     *     summary="Get a list of task list",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="name", type="string", example="task_name"),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                 )
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated."),
     *         ),
     *     )
     * )
     */
    public function __invoke(): AnonymousResourceCollection|JsonResponse
    {
        return TaskListResource::collection($this->taskListListAction->execute());
        try {
        } catch (\Exception $ex) {
            Log::critical('Controller: ' . self::class, ['exception' => $ex->getMessage()]);

            return Response::json(
                ['message' => config('messages.error.server')],
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
