<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Support\Str;

class TaskService
{
    protected TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * Get all tasks.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTasks()
    {
        return $this->taskRepository->all();
    }

    /**
     * Create a new task.
     *
     * @param array $data
     * @return Task
     */
    public function createTask(array $data): Task
    {
        $data['secure_token'] = $this->generateSecureToken();
        
        return $this->taskRepository->create($data);
    }

    /**
     * Update a task.
     *
     * @param Task $task
     * @param array $data
     * @return Task
     */
    public function updateTask(Task $task, array $data): Task
    {
        return $this->taskRepository->update($task, $data);
    }

    /**
     * Delete a task.
     *
     * @param Task $task
     * @return bool
     */
    public function deleteTask(Task $task): bool
    {
        return $this->taskRepository->delete($task);
    }

    /**
     * Validate task secure token.
     *
     * @param Task $task
     * @param string $token
     * @return bool
     */
    public function validateToken(Task $task, string $token): bool
    {
        return $task->secure_token === $token;
    }

    /**
     * Generate a secure token.
     *
     * @return string
     */
    protected function generateSecureToken(): string
    {
        return Str::random(64);
    }
}