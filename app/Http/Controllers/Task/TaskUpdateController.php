<?php

namespace App\Http\Controllers\Task;

use App\Actions\Task\TaskUpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaskUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskUpdateController extends Controller
{
    public function __construct(
        private TaskUpdateAction $taskUpdateAction
    ) {
    }

    /**
     * @OA\Put(
     *      path="/api/task/update/{id}",
     *      operationId="updateTask",
     *      tags={"Tasks"},
     *      summary="Update a task",
     *      description="This API updates a task.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of the task to update",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Task updated successfully.",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="string", example="Task updated.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Task not found.",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="string", example="Task not found.")
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
    public function __invoke(int $id, TaskUpdateRequest $request): JsonResponse
    {
        try {
            $this->taskUpdateAction->execute(
                $id,
                $request->input('title'),
                $request->input('description'),
                $request->input('date_time'),
            );
            return Response::json(
                ['message' => 'Task updated'],
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
