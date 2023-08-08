<?php

namespace App\Http\Controllers\Task;

use App\Actions\Task\TaskCreateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRegisterRequest;
use App\Http\Resources\TaskResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskCreateController extends Controller
{
    public function __construct(
        private TaskCreateAction $taskCreateAction
    ) {
    }

    /**
     * @OA\Post(
     *      path="/api/task/register",
     *      operationId="registerTask",
     *      tags={"Tasks"},
     *      summary="Create a new task",
     *      description="This API creates a new task.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="title", type="string", example="Task title"),
     *              @OA\Property(property="description", type="string", example="Task description"),
     *              @OA\Property(property="date_time", type="string", format="date-time", example="2023-08-05T12:30:00Z")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Task created successfully.",
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
     *          response=500,
     *          description="An unknown error occurred.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The request could not be completed due to an unknown error. Please contact support!")
     *          )
     *      )
     * )
     */
    public function __invoke(TaskRegisterRequest $request): JsonResponse|JsonResource
    {
        try {
            return TaskResource::make(
                $this->taskCreateAction->execute(
                    $request->input('title'),
                    $request->input('description'),
                    $request->input('date_time'),
                    $request->input('task_list_id'),
                )
            )
                ->response()
                ->setStatusCode(HttpResponse::HTTP_CREATED);
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
