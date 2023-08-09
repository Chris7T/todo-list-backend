<?php

namespace App\Http\Controllers\Task;

use App\Actions\Task\TaskImportFromGoogleAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class TaskImportFromGoogleController extends Controller
{
    public function __construct(
        private TaskImportFromGoogleAction $taskImportFromGoogleAction
    ) {
    }

    /**
     * @OA\Get(
     *      path="/api/task/google/import",
     *      operationId="importGoogleTask",
     *      tags={"Tasks"},
     *      summary="Import Task from Google",
     *      description="This API imports a task from Google.",
     *      @OA\Response(
     *          response=200,
     *          description="Task imported successfully.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Task imported.")
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
    public function __invoke(): JsonResponse
    {
        try {
            $this->taskImportFromGoogleAction->execute();

            return Response::json(['message' => 'Task imported'], HttpResponse::HTTP_OK);
        } catch (\Exception $ex) {
            Log::critical('Controller: ' . self::class, ['exception' => $ex->getMessage()]);

            return Response::json(
                ['message' => config('messages.error.server')],
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
