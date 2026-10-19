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
        Schema::table('users', function (Blueprint $table): void {
            $table->string('student_code', 32)->nullable()->unique();
            $table->string('major')->nullable();
            $table->string('class_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['student_code']);
            $table->dropColumn([
                'student_code',
                'major',
                'class_name',
                'date_of_birth',
                'gender',
            ]);
        });
    }
};
