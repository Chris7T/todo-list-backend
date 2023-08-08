<?php

namespace App\Http\Controllers\Task;

use App\Actions\Task\TaskGetAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskGetController extends Controller
{
    public function __construct(
        private TaskGetAction $taskGetAction
    ) {
    }

    /**
     * @OA\Get(
     *      path="/api/task/get/{id}",
     *      operationId="getTask",
     *      tags={"Tasks"},
     *      summary="Fetch a task",
     *      description="This API fetches a task.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of the task to fetch",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Task fetched successfully.",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="title", type="string", example="Task title"),
     *                  @OA\Property(property="description", type="string", example="Task description"),
     *                  @OA\Property(property="completed", type="boolean", example=false),
     *                  @OA\Property(property="user_id", type="integer", example=1)
     *              )
     *          )
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
    public function __invoke(int $id): JsonResponse|HttpResponse|TaskResource
    {
        try {
            return TaskResource::make(
                $this->taskGetAction->execute($id)
            );
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
