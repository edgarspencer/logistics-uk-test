<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the tasks.
     */
    public function index(): AnonymousResourceCollection
    {
        $tasks = $this->taskService->getAllTasks();
        
        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created task.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->createTask($request->validated());
        
        return response()->json([
            'message' => 'Task created successfully',
            'data' => new TaskResource($task),
            'secure_urls' => [
                'update' => url("/api/tasks/{$task->id}/{$task->secure_token}"),
                'delete' => url("/api/tasks/{$task->id}/{$task->secure_token}"),
            ],
        ], 201);
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task): TaskResource
    {
        return new TaskResource($task);
    }

    /**
     * Update the specified task.
     */
    public function update(UpdateTaskRequest $request, Task $task, string $token): JsonResponse
    {
        if (!$this->taskService->validateToken($task, $token)) {
            return response()->json([
                'message' => 'Invalid secure token',
            ], 403);
        }

        $task = $this->taskService->updateTask($task, $request->validated());
        
        return response()->json([
            'message' => 'Task updated successfully',
            'data' => new TaskResource($task),
        ]);
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task, string $token): JsonResponse
    {
        if (!$this->taskService->validateToken($task, $token)) {
            return response()->json([
                'message' => 'Invalid secure token',
            ], 403);
        }

        $this->taskService->deleteTask($task);
        
        return response()->json([
            'message' => 'Task deleted successfully',
        ]);
    }
}