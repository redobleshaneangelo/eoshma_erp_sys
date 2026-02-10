<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_attendances', 'qr_payload')) {
                $table->text('qr_payload')->nullable()->after('time_out');
            }
            if (!Schema::hasColumn('employee_attendances', 'time_in_photo_path')) {
                $table->string('time_in_photo_path')->nullable()->after('qr_payload');
            }
            if (!Schema::hasColumn('employee_attendances', 'time_out_photo_path')) {
                $table->string('time_out_photo_path')->nullable()->after('time_in_photo_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_attendances', function (Blueprint $table) {
            if (Schema::hasColumn('employee_attendances', 'time_out_photo_path')) {
                $table->dropColumn('time_out_photo_path');
            }
            if (Schema::hasColumn('employee_attendances', 'time_in_photo_path')) {
                $table->dropColumn('time_in_photo_path');
            }
            if (Schema::hasColumn('employee_attendances', 'qr_payload')) {
                $table->dropColumn('qr_payload');
            }
        });
    }
};
