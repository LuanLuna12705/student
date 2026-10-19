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
        Schema::create('club_user', function (Blueprint $table) {
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->string('phone', 32)->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('cohort', 100)->nullable();
            $table->timestamps();

            $table->primary(['club_id', 'user_id']);
            $table->index(['club_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_user');
    }
};
