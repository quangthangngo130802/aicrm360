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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->string('customer_name'); // Tên khách hàng
            $table->unsignedBigInteger('user_id')->nullable(); // Nhân viên phụ trách

            $table->dateTime('scheduled_at'); // Ngày & giờ hẹn
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending'); // Trạng thái

            $table->text('note')->nullable(); // Ghi chú

            $table->timestamps();

            // Nếu có bảng nhân viên thì có thể dùng khóa ngoại:
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
