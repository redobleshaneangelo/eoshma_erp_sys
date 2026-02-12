<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('users')
            ->leftJoin('employees', 'employees.user_id', '=', 'users.id')
            ->select('users.id as user_id', 'employees.rate')
            ->get();

        foreach ($rows as $row) {
            $rate = $row->rate !== null ? (float) $row->rate : null;
            $salaryType = match ($rate) {
                800.0 => 'day',
                28800.0 => 'fixed',
                default => 'hour'
            };

            DB::table('users_salary_type')->updateOrInsert(
                ['user_id' => $row->user_id],
                [
                    'salary_type' => $salaryType,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('users_salary_type')->truncate();
    }
};
