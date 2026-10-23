<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_follow_up_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_follow_up_id')->constrained()->cascadeOnDelete();
            $table->string('update_type', 30);
            $table->text('notes');
            $table->text('next_action')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['event_follow_up_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_follow_up_updates');
    }
};