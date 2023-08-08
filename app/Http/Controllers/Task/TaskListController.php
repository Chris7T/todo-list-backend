<?php

namespace App\Http\Controllers\Task;

use App\Actions\Task\TaskListAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class TaskListController extends Controller
{
    public function __construct(
        private TaskListAction $taskListAction
    ) {
    }

    /**
     * @OA\Get(
     *      path="/api/task/list",
     *      operationId="listTasks",
     *      tags={"Tasks"},
     *      summary="List tasks",
     *      description="This API returns a paginated list of tasks.",
     *      @OA\Response(
     *          response=200,
     *          description="Tasks fetched successfully.",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="title", type="string", example="Task title"),
     *                      @OA\Property(property="description", type="string", example="Task description"),
     *                      @OA\Property(property="completed", type="boolean", example=false),
     *                      @OA\Property(property="user_id", type="integer", example=1)
     *                  )
     *              )
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
    public function __invoke(int $taskListId): AnonymousResourceCollection|JsonResponse
    {
        return TaskResource::collection($this->taskListAction->execute($taskListId));
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
