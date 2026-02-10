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
        Schema::create('equipment_plannings', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('brand');
            $table->string('conditions');
            $table->string('type');
            $table->integer('quantity');
            $table->integer('available');
            $table->decimal('unit_cost', 15, 2);
            $table->foreignId('prebidding_id')->constrained('prebiddings')->onDelete('cascade');

            // $table->integer('quantity');
            // $table->foreignId('equipment_id')->constrained('equipments')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_plannings');
    }
};
