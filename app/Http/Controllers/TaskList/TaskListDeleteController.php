<?php

namespace App\Http\Controllers\TaskList;

use App\Actions\TaskList\TaskListDeleteAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskListDeleteController extends Controller
{
    public function __construct(
        private TaskListDeleteAction $taskListDeleteAction
    ) {
    }

    /**
     * @OA\Delete(
     *     tags={"Task List"},
     *     path="/api/task-list/register/{id}",
     *     summary="Delete a task list",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the task to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Task successfully deleted"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated."),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task list not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Task list not found."),
     *         )
     *     )
     * )
     */
    public function __invoke(int $id): JsonResponse|HttpResponse
    {
        try {
            $this->taskListDeleteAction->execute($id);
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
