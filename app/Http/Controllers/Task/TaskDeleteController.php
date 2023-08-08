<?php

namespace App\Http\Controllers\Task;

use App\Actions\Task\TaskDeleteAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskDeleteController extends Controller
{
    public function __construct(
        private TaskDeleteAction $taskDeleteAction
    ) {
    }

    /**
     * @OA\Delete(
     *      path="/api/task/delete/{id}",
     *      operationId="deleteTask",
     *      tags={"Tasks"},
     *      summary="Delete a task",
     *      description="This API deletes a task.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of the task to delete",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Task deleted successfully."
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Task not found.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Task not found.")
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
    public function __invoke(int $id): JsonResponse|HttpResponse
    {
        try {
            $this->taskDeleteAction->execute($id);
            return Response::noContent();
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
