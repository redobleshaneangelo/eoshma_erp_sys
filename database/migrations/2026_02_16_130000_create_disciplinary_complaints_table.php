<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disciplinary_complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submitted_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('submitted_by_name');
            $table->string('department')->nullable();
            $table->string('complaint_type');
            $table->date('incident_date');
            $table->text('description');
            $table->boolean('employee_involved')->default(false);
            $table->foreignId('reported_employee_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reported_employee_name')->nullable();
            $table->string('reported_employee_department')->nullable();
            $table->json('attachments')->nullable();
            $table->enum('status', ['submitted', 'rejected', 'pending', 'reviewed'])->default('submitted');
            $table->foreignId('hr_reviewer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('hr_reviewed_at')->nullable();
            $table->text('hr_rejection_reason')->nullable();
            $table->foreignId('manager_reviewer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('manager_reviewed_at')->nullable();
            $table->string('record_type')->nullable();
            $table->text('action_taken')->nullable();
            $table->text('manager_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disciplinary_complaints');
    }
};
