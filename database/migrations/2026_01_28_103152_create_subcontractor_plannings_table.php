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
        Schema::create('subcontractor_plannings', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('scope');
            $table->decimal('planned_cost', 15, 2);
            $table->string('cost_code');
            $table->foreignId('prebidding_id')->constrained('prebiddings')->onDelete('cascade');

            // $table->integer('quantity');
            // $table->foreignId('subcontractor_id')->constrained('subcontractors')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcontractor_plannings');
    }
};
