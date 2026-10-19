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
    Schema::create('events', function (Blueprint $table) {
        $table->id();
        $table->string('title');                  // Tên sự kiện
        $table->text('description');             // Mô tả chi tiết
        $table->string('location');              // Địa điểm tổ chức
        $table->dateTime('event_date');          // Ngày giờ diễn ra sự kiện
        $table->dateTime('registration_deadline'); // Hạn đăng ký (áp dụng logic trước 7 ngày)
        $table->integer('max_participants')->default(100); // Số lượng sinh viên tối đa
        $table->string('status')->default('upcoming'); // Trạng thái: upcoming, ongoing, ended
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
