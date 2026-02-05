<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TaskController extends Controller
{
    // 1️⃣ Get all tasks of logged-in user
    public function index(Request $request)
{
    $tasks = $request->user()->tasks()
        ->with(['blockingTasks', 'dependentTasks']) // eager load relationships
        ->paginate(10); // paginate results

    return response()->json($tasks);
}



    // 2️⃣ Create a task
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:pending,in_progress,completed'
        ]);

        $task = Task::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? 'pending'
        ]);

        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task
        ], 201);
    }

    // 3️⃣ Show single task
    public function show(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($task);
    }

    // 4️⃣ Update task
    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:pending,in_progress,completed'
        ]);

        $task->update($request->only(['title', 'description', 'status']));

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task
        ]);
    }

    // 5️⃣ Delete task
    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);

    }
    // Add a dependency

public function addDependency(Request $request)
{
    $request->validate([
        'task_id' => 'required|exists:tasks,id',
        'blocked_by_task_id' => 'required|exists:tasks,id',
    ]);

    $taskId = $request->task_id;
    $blockedByTaskId = $request->blocked_by_task_id;

    // Prevent self-dependency
    if ($taskId === $blockedByTaskId) {
        return response()->json([
            'message' => 'A task cannot block itself'
        ], 422);
    }

    $task = Task::findOrFail($taskId);
    $blockedByTask = Task::findOrFail($blockedByTaskId);

    // Ownership check
    if ($task->user_id !== auth()->id() || $blockedByTask->user_id !== auth()->id()) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }

    // Prevent circular dependency
    $createsCycle = $blockedByTask
        ->blockingTasks()
        ->where('tasks.id', $task->id)
        ->exists();

    if ($createsCycle) {
        return response()->json([
            'message' => 'Circular dependency detected'
        ], 422);
    }

    // Prevent duplicate dependency
    if ($task->blockingTasks()->where('tasks.id', $blockedByTaskId)->exists()) {
        return response()->json([
            'message' => 'Dependency already exists'
        ], 409);
    }

    // Attach dependency
    $task->blockingTasks()->attach($blockedByTaskId);

    return response()->json([
        'message' => 'Dependency added successfully'
    ], 201);
}

private function createsCycle($taskId, $blockedByTaskId)
{
    $visited = [];

    $stack = [$blockedByTaskId];

    while (!empty($stack)) {
        $current = array_pop($stack);

        if ($current == $taskId) {
            return true;
        }

        if (in_array($current, $visited)) {
            continue;
        }

        $visited[] = $current;

        $parents = DB::table('task_dependencies')
            ->where('task_id', $current)
            ->pluck('blocked_by_task_id')
            ->toArray();

        foreach ($parents as $parent) {
            $stack[] = $parent;
        }
    }

    return false;
}


// Complete a task
public function completeTask(Task $task)
{
    // Check task ownership
    if ($task->user_id !== auth()->id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Check if blocked by pending tasks
    $pendingBlockers = $task->dependentTasks()->where('status', '!=', 'completed')->count();

    if ($pendingBlockers > 0) {
        return response()->json(['error' => 'Cannot complete task. Some dependencies are pending.'], 422);
    }

    $task->update(['status' => 'completed']);

    return response()->json(['message' => 'Task completed successfully', 'task' => $task]);
}


}
