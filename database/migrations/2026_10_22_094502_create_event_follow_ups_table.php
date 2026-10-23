
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_follow_ups', function (Blueprint $table) {
            $table->id();

            // Link to the existing event
            $table->foreignId('event_id')
                ->constrained('events')
                ->cascadeOnDelete();

            // Follow-up task information
            $table->string('title');
            $table->text('description')->nullable();

            // Staff member responsible for this task
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Schedule and task management
            $table->date('due_date')->nullable();
            $table->string('priority', 20)->default('medium')->index();
            $table->string('status', 20)->default('pending')->index();

            // Outcome or work completed
            $table->text('outcome')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['event_id', 'status']);
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_follow_ups');
    }
};
