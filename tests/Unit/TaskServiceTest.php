<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Repositories\TaskRepository;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TaskService $taskService;
    protected $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockRepository = Mockery::mock(TaskRepository::class);
        $this->taskService = new TaskService($this->mockRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test creating a task generates a secure token.
     */
    public function test_create_task_generates_secure_token(): void
    {
        $taskData = [
            'name' => 'Test Task',
            'description' => 'Test description that is long enough.',
        ];
        
        $this->mockRepository
            ->shouldReceive('create')
            ->once()
            ->withArgs(function ($data) use ($taskData) {
                return $data['name'] === $taskData['name'] &&
                       $data['description'] === $taskData['description'] &&
                       isset($data['secure_token']) &&
                       strlen($data['secure_token']) === 64;
            })
            ->andReturn(new Task($taskData));
        
        $task = $this->taskService->createTask($taskData);
        
        $this->assertInstanceOf(Task::class, $task);
    }

    /**
     * Test validating token.
     */
    public function test_validate_token(): void
    {
        $task = Task::factory()->create([
            'secure_token' => 'valid-token',
        ]);
        
        $this->assertTrue($this->taskService->validateToken($task, 'valid-token'));
        $this->assertFalse($this->taskService->validateToken($task, 'invalid-token'));
    }
}