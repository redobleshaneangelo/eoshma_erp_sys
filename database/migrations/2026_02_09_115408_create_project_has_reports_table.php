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
        Schema::create('project_has_reports', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('description');
            $table->string('attachments')->nullable();
            $table->text('issues_encountered')->nullable();

            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            $table->foreignId('milestone_id')
                ->nullable()
                ->constrained('project_has_milestones')
                ->nullOnDelete();

            $table->foreignId('task_id')
                ->nullable()
                ->constrained('project_has_tasks')
                ->nullOnDelete();

            $table->foreignId('prepared_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_has_reports');
    }
};
