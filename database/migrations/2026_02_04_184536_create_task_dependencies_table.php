<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskDependenciesTable extends Migration
{
    public function up()
{
    Schema::create('task_dependencies', function (Blueprint $table) {
        $table->id();
        $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
        $table->foreignId('blocked_by_task_id')->constrained('tasks')->onDelete('cascade');
        $table->unique(['task_id', 'blocked_by_task_id']); // Prevent duplicates
        $table->timestamps();

        // ✅ Add indexes for faster queries
        $table->index('task_id');
        $table->index('blocked_by_task_id');
    });
}


    public function down()
    {
        Schema::dropIfExists('task_dependencies');
    }
}
