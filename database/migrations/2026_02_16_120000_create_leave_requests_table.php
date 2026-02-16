<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('employee_name');
            $table->string('department')->nullable();
            $table->string('leave_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('total_days');
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected', 'info_requested'])->default('pending');
            $table->text('approver_notes')->nullable();
            $table->text('request_info_message')->nullable();
            $table->text('request_info_reply')->nullable();
            $table->json('attachments')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('resubmitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
