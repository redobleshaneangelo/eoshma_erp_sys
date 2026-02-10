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
        Schema::create('category_breakdowns', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->foreignId('prebidding_id')->constrained('prebiddings')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_breakdowns');
    }
};
