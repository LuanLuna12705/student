<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->index(['registration_deadline', 'event_date'], 'events_registration_window_index');
        });

        Schema::table('event_user', function (Blueprint $table): void {
            $table->index(['event_id', 'status'], 'event_user_event_status_index');
        });

        Schema::table('club_user', function (Blueprint $table): void {
            $table->index(['user_id', 'status'], 'club_user_user_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->dropIndex('events_registration_window_index');
        });

        Schema::table('event_user', function (Blueprint $table): void {
            $table->dropIndex('event_user_event_status_index');
        });

        Schema::table('club_user', function (Blueprint $table): void {
            $table->dropIndex('club_user_user_status_index');
        });
    }
};
