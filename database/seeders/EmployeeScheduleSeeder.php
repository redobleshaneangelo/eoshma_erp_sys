<?php

namespace Database\Seeders;

use App\Models\EmployeeSchedule;
use App\Models\EmployeeTest;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EmployeeScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $employees = EmployeeTest::query()->pluck('id');

        foreach ($employees as $employeeId) {
            EmployeeSchedule::updateOrCreate(
                ['employee_id' => $employeeId],
                [
                    'start_time' => '08:00:00',
                    'end_time' => '17:00:00'
                ]
            );
        }
    }
}
