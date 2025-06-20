<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('doctor_selected_clinics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('clinic_id')->nullable(); // NULL یعنی مشاوره آنلاین
            $table->timestamps();

            // کلید خارجی
            $table->foreign('doctor_id')
                ->references('id')
                ->on('doctors')
                ->onDelete('cascade');
            $table->foreign('clinic_id')
                ->references('id')
                ->on('clinics')
                ->onDelete('set null');

            // ایندکس برای بهینه‌سازی
            $table->unique('doctor_id'); // هر پزشک فقط یک کلینیک انتخاب‌شده دارد
            $table->index('clinic_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_selected_clinics');
    }
};
