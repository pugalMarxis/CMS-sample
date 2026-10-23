<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_follow_ups', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('description');
            $table->text('initial_notes')->nullable()->after('outcome');
            $table->dateTime('reminder_at')->nullable()->after('due_date');
        });
    }

    public function down(): void
    {
        Schema::table('event_follow_ups', function (Blueprint $table) {
            $table->dropColumn(['reason', 'initial_notes', 'reminder_at']);
        });
    }
};