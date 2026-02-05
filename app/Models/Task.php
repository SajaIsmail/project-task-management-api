<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
    ];

    /**
     * Tasks that THIS task is blocked by
     * (i.e., dependencies)
     */
    public function blockingTasks()
    {
        return $this->belongsToMany(
            Task::class,
            'task_dependencies',
            'task_id',
            'blocked_by_task_id'
        );
    }

    /**
     * Tasks that depend on THIS task
     */
    public function dependentTasks()
    {
        return $this->belongsToMany(
            Task::class,
            'task_dependencies',
            'blocked_by_task_id',
            'task_id'
        );
    }
}
