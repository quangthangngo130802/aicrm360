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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // ====== Thông tin cá nhân ======
            $table->string('name');                  // Họ tên khách hàng
            $table->string('phone')->nullable();     // SĐT cá nhân
            $table->string('email')->nullable();     // Email cá nhân
            $table->string('career')->nullable();    // Ngành nghề
            $table->string('area')->nullable();      // Khu vực
            $table->unsignedBigInteger('source_id')->nullable(); // Nguồn (id)

            // ====== Thông tin công ty ======
            $table->string('company_name')->nullable();       // Tên công ty
            $table->string('company_phone')->nullable();      // SĐT công ty
            $table->string('company_tax_code')->nullable();   // Mã số thuế công ty

            // ====== Địa chỉ & mã số thuế ======
            $table->string('address')->nullable();        // Địa chỉ cá nhân
            $table->string('tax_code')->nullable();       // Mã số thuế cá nhân

            // ====== Mạng xã hội ======
            $table->string('facebook_link')->nullable();  // Link Facebook
            $table->string('youtube_link')->nullable();   // Link YouTube
            $table->string('instagram_link')->nullable(); // Link Instagram

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
