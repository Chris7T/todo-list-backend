<?php

namespace App\Http\Controllers\TaskList;

use App\Actions\TaskList\TaskListUpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaskListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskListUpdateController extends Controller
{
    public function __construct(
        private TaskListUpdateAction $taskTaskListUpdateAction
    ) {
    }

    /**
     * @OA\Put(
     *     tags={"Task List"},
     *     path="/api/task-list/update/{id}",
     *     summary="Update a task list",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the task to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="task_name"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="TaskList updated"),
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
     *     )
     * )
     */
    public function __invoke(int $id, TaskListRequest $request): JsonResponse
    {
        $this->taskTaskListUpdateAction->execute(
            $id,
            $request->input('name'),
        );
        try {
            return Response::json(
                ['message' => 'TaskList updated'],
                HttpResponse::HTTP_OK
            );
        } catch (NotFoundHttpException $ex) {
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
