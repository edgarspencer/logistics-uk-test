<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test listing all tasks.
     */
    public function test_can_list_all_tasks(): void
    {
        Task::factory()->count(3)->create();
        
        $response = $this->getJson('/api/tasks');
        
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    /**
     * Test creating a task.
     */
    public function test_can_create_task(): void
    {
        $taskData = [
            'name' => 'Test Task',
            'description' => 'This is a test task description that meets the minimum length requirement.',
        ];
        
        $response = $this->postJson('/api/tasks', $taskData);
        
        $response->assertStatus(201)
            ->assertJsonPath('message', 'Task created successfully')
            ->assertJsonPath('data.name', $taskData['name'])
            ->assertJsonPath('data.description', $taskData['description'])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'description',
                    'created_at',
                    'updated_at',
                ],
                'secure_urls' => [
                    'update',
                    'delete',
                ],
            ]);
        
        $this->assertDatabaseHas('tasks', [
            'name' => $taskData['name'],
            'description' => $taskData['description'],
        ]);
    }

    /**
     * Test validation when creating a task.
     */
    public function test_validates_task_creation(): void
    {
        $response = $this->postJson('/api/tasks', [
            'name' => 'AB', // Too short
            'description' => 'Too short', // Too short
        ]);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'description']);
    }

    /**
     * Test viewing a single task.
     */
    public function test_can_view_single_task(): void
    {
        $task = Task::factory()->create();
        
        $response = $this->getJson("/api/tasks/{$task->id}");
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonPath('data.id', $task->id);
    }

    /**
     * Test updating a task with valid token.
     */
    public function test_can_update_task_with_valid_token(): void
    {
        $task = Task::factory()->create();
        $updateData = [
            'name' => 'Updated Task Name',
            'description' => 'This is the updated task description that meets the minimum length.',
        ];
        
        $response = $this->putJson("/api/tasks/{$task->id}/{$task->secure_token}", $updateData);
        
        $response->assertStatus(200)
            ->assertJsonPath('message', 'Task updated successfully')
            ->assertJsonPath('data.name', $updateData['name'])
            ->assertJsonPath('data.description', $updateData['description']);
        
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => $updateData['name'],
            'description' => $updateData['description'],
        ]);
    }

    /**
     * Test updating a task with invalid token.
     */
    public function test_cannot_update_task_with_invalid_token(): void
    {
        $task = Task::factory()->create();
        
        $response = $this->putJson("/api/tasks/{$task->id}/invalid-token", [
            'name' => 'Updated Task',
        ]);
        
        $response->assertStatus(403)
            ->assertJsonPath('message', 'Invalid secure token');
    }

    /**
     * Test deleting a task with valid token.
     */
    public function test_can_delete_task_with_valid_token(): void
    {
        $task = Task::factory()->create();
        
        $response = $this->deleteJson("/api/tasks/{$task->id}/{$task->secure_token}");
        
        $response->assertStatus(200)
            ->assertJsonPath('message', 'Task deleted successfully');
        
        $this->assertSoftDeleted('tasks', [
            'id' => $task->id,
        ]);
    }

    /**
     * Test deleting a task with invalid token.
     */
    public function test_cannot_delete_task_with_invalid_token(): void
    {
        $task = Task::factory()->create();
        
        $response = $this->deleteJson("/api/tasks/{$task->id}/invalid-token");
        
        $response->assertStatus(403)
            ->assertJsonPath('message', 'Invalid secure token');
        
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test partial update of a task.
     */
    public function test_can_partially_update_task(): void
    {
        $task = Task::factory()->create([
            'name' => 'Original Name',
            'description' => 'Original description that is long enough to meet requirements.',
        ]);
        
        $response = $this->putJson("/api/tasks/{$task->id}/{$task->secure_token}", [
            'name' => 'Updated Name Only',
        ]);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Updated Name Only',
            'description' => 'Original description that is long enough to meet requirements.',
        ]);
    }
}