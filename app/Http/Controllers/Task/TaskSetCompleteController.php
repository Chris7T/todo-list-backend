<?php

namespace App\Http\Controllers\Task;

use App\Actions\Task\TaskSetCompleteAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskSetCompleteController extends Controller
{
    public function __construct(
        private TaskSetCompleteAction $taskSetCompleteAction
    ) {
    }

    /**
     * @OA\Patch(
     *      path="/api/task/complete/{id}",
     *      operationId="completeTask",
     *      tags={"Tasks"},
     *      summary="Complete a task",
     *      description="This API marks a task as completed.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of the task to complete",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Task completed successfully.",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="string", example="Task completed.")
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
    public function __invoke(int $id): JsonResponse
    {
        try {
            $this->taskSetCompleteAction->execute($id);
            return Response::json(
                ['message' => 'Task completed'],
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
