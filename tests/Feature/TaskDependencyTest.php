<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskDependencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_cannot_be_completed_if_blocked()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $taskA = Task::create([
            'title' => 'Task A',
            'status' => 'pending',
            'user_id' => $user->id,
        ]);

        $taskB = Task::create([
            'title' => 'Task B',
            'status' => 'pending',
            'user_id' => $user->id,
        ]);

        $taskB->blockingTasks()->attach($taskA->id);

        $response = $this->putJson("/api/tasks/{$taskB->id}", [
            'status' => 'completed'
        ]);

        $response->assertStatus(422);
    }
}
