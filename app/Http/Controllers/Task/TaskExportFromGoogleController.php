<?php

namespace App\Http\Controllers\Task;

use App\Actions\Task\TaskExportToGoogleAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class TaskExportFromGoogleController extends Controller
{
    public function __construct(
        private TaskExportToGoogleAction $taskExportFromGoogleAction
    ) {
    }

    /**
     * @OA\Get(
     *      path="/api/task/google/export",
     *      operationId="exportGoogleTask",
     *      tags={"Tasks"},
     *      summary="Export Task to Google",
     *      description="This API exports a task to Google.",
     *      @OA\Response(
     *          response=200,
     *          description="Task exported successfully.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Task exported.")
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
        $this->taskExportFromGoogleAction->execute();
        return Response::json(
            ['message' => 'Task exported'],
            HttpResponse::HTTP_OK
        );
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
