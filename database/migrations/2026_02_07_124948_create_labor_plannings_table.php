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
        Schema::create('labor_plannings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('prebidding_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('role');
            $table->integer('headcount');
            $table->decimal('total_cost', 15, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labor_plannings');
    }
};
