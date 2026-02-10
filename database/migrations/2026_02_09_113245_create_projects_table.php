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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            // basic project info
            $table->string('project_name');
            $table->string('project_code');
            $table->string('project_address');
            $table->string('project_type');

            // relationships
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();

            // dates
            $table->date('project_start_date');
            $table->date('project_end_date');
            $table->date('contract_date');

            // contract & progress
            $table->integer('project_progress')->default(0); // e.g. 75.50%
            $table->string('contract_number');
            $table->decimal('contract_amount', 15, 2);
            $table->integer('contract_period'); // usually in days or months

            // budget
            $table->decimal('total_budget', 15, 2);
            $table->decimal('total_utilized', 15, 2)->default(0);
            $table->decimal('retention', 15, 2)->default(0);

            // status
            $table->string('status')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
