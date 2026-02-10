<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statutory_compliance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained('payroll_runs')->cascadeOnDelete();
            $table->string('name');
            $table->date('coverage_start');
            $table->date('coverage_end');
            $table->decimal('employee_share', 12, 2)->default(0);
            $table->decimal('employer_share', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->date('due_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statutory_compliance');
    }
};
