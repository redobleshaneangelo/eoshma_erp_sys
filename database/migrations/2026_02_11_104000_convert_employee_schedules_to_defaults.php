<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('employee_schedules')) {
            return;
        }

        Schema::create('employee_schedules_defaults', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            $table->unique('employee_id');
        });

        $rows = DB::table('employee_schedules')
            ->orderByDesc('updated_at')
            ->get()
            ->groupBy('employee_id')
            ->map->first();

        foreach ($rows as $row) {
            DB::table('employee_schedules_defaults')->insert([
                'employee_id' => $row->employee_id,
                'start_time' => $row->start_time,
                'end_time' => $row->end_time,
                'created_at' => $row->created_at ?? now(),
                'updated_at' => $row->updated_at ?? now()
            ]);
        }

        Schema::drop('employee_schedules');
        Schema::rename('employee_schedules_defaults', 'employee_schedules');
    }

    public function down(): void
    {
        if (!Schema::hasTable('employee_schedules')) {
            return;
        }

        Schema::create('employee_schedules_with_date', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('schedule_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            $table->unique(['employee_id', 'schedule_date']);
        });

        $rows = DB::table('employee_schedules')->get();
        foreach ($rows as $row) {
            DB::table('employee_schedules_with_date')->insert([
                'employee_id' => $row->employee_id,
                'schedule_date' => Carbon::today()->toDateString(),
                'start_time' => $row->start_time,
                'end_time' => $row->end_time,
                'created_at' => $row->created_at ?? now(),
                'updated_at' => $row->updated_at ?? now()
            ]);
        }

        Schema::drop('employee_schedules');
        Schema::rename('employee_schedules_with_date', 'employee_schedules');
    }
};
