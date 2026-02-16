<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedInteger('days_per_year');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('leave_types')->insert([
            ['name' => 'Vacation Leave', 'days_per_year' => 12, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sick Leave', 'days_per_year' => 10, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Emergency Leave', 'days_per_year' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Personal Leave', 'days_per_year' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};