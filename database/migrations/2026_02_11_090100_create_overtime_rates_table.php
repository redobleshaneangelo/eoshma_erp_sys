<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('overtime_rates', function (Blueprint $table) {
            $table->id();
            $table->string('day_type')->unique();
            $table->string('label');
            $table->decimal('multiplier', 6, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('overtime_rates')->insert([
            [
                'day_type' => 'regular_day',
                'label' => 'Regular Day',
                'multiplier' => 1.25,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'day_type' => 'rest_day_special_holiday',
                'label' => 'Rest Day / Special Holiday',
                'multiplier' => 1.69,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'day_type' => 'regular_holiday',
                'label' => 'Regular Holiday',
                'multiplier' => 2.60,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'day_type' => 'regular_holiday_rest_day',
                'label' => 'Regular Holiday + Rest Day',
                'multiplier' => 3.38,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('overtime_rates');
    }
};
