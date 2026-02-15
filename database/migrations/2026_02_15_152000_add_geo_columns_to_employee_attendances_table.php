<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_attendances', 'time_in_latitude')) {
                $table->decimal('time_in_latitude', 10, 7)->nullable()->after('time_in_photo_path');
            }
            if (!Schema::hasColumn('employee_attendances', 'time_in_longitude')) {
                $table->decimal('time_in_longitude', 10, 7)->nullable()->after('time_in_latitude');
            }
            if (!Schema::hasColumn('employee_attendances', 'time_in_geo_accuracy')) {
                $table->decimal('time_in_geo_accuracy', 8, 2)->nullable()->after('time_in_longitude');
            }

            if (!Schema::hasColumn('employee_attendances', 'time_out_latitude')) {
                $table->decimal('time_out_latitude', 10, 7)->nullable()->after('time_out_photo_path');
            }
            if (!Schema::hasColumn('employee_attendances', 'time_out_longitude')) {
                $table->decimal('time_out_longitude', 10, 7)->nullable()->after('time_out_latitude');
            }
            if (!Schema::hasColumn('employee_attendances', 'time_out_geo_accuracy')) {
                $table->decimal('time_out_geo_accuracy', 8, 2)->nullable()->after('time_out_longitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_attendances', function (Blueprint $table) {
            foreach ([
                'time_in_latitude',
                'time_in_longitude',
                'time_in_geo_accuracy',
                'time_out_latitude',
                'time_out_longitude',
                'time_out_geo_accuracy'
            ] as $column) {
                if (Schema::hasColumn('employee_attendances', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
