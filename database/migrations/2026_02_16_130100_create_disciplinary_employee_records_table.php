<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disciplinary_employee_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disciplinary_complaint_id')->constrained('disciplinary_complaints')->cascadeOnDelete();
            $table->foreignId('employee_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('record_type');
            $table->text('description');
            $table->date('date_added');
            $table->foreignId('added_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('action_taken')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disciplinary_employee_records');
    }
};
