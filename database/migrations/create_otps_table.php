<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->onDelete('cascade');
            $table->foreignId('secretary_id')->nullable()->constrained('secretaries')->onDelete('cascade');
            $table->foreignId('manager_id')->nullable()->constrained('managers')->onDelete('cascade');
            $table->string('otp_code');
            $table->string('login_id')->comment('email address or mobile number');
            $table->tinyInteger('type')->default(0)->comment('0 => mobile number , 1 => email');
            $table->tinyInteger('used')->default(0)->comment('0 => not used , 1 => used');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();

            // اضافه کردن ایندکس‌ها
            $table->index('type');
            $table->index('used');
            $table->index('status');
            $table->index('otp_code');
            $table->index(['login_id', 'type']);
            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
