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
        Schema::create('material_plannings', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('brand');
            $table->string('unit');
            $table->integer('quantity');
            $table->integer('available');
            $table->string('source');
            $table->decimal('unit_cost', 15, 2);
            $table->string('cost_code');
            $table->foreignId('prebidding_id')->constrained('prebiddings')->onDelete('cascade');

            // $table->integer('quantity');
            // $table->string('source');
            // $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_plannings');
    }
};
