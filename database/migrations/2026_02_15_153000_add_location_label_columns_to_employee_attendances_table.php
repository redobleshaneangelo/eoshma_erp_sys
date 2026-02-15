<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_attendances', 'time_in_location_label')) {
                $table->string('time_in_location_label')->nullable()->after('time_in_geo_accuracy');
            }

            if (!Schema::hasColumn('employee_attendances', 'time_out_location_label')) {
                $table->string('time_out_location_label')->nullable()->after('time_out_geo_accuracy');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_attendances', function (Blueprint $table) {
            if (Schema::hasColumn('employee_attendances', 'time_in_location_label')) {
                $table->dropColumn('time_in_location_label');
            }

            if (Schema::hasColumn('employee_attendances', 'time_out_location_label')) {
                $table->dropColumn('time_out_location_label');
            }
        });
    }
};
