<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('overtime_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('overtime_requests', 'start_time')) {
                $table->time('start_time')->nullable()->after('day_type');
            }
            if (!Schema::hasColumn('overtime_requests', 'approved_hours')) {
                $table->decimal('approved_hours', 5, 2)->nullable()->after('approved_multiplier');
            }
        });
    }

    public function down(): void
    {
        Schema::table('overtime_requests', function (Blueprint $table) {
            if (Schema::hasColumn('overtime_requests', 'approved_hours')) {
                $table->dropColumn('approved_hours');
            }
            if (Schema::hasColumn('overtime_requests', 'start_time')) {
                $table->dropColumn('start_time');
            }
        });
    }
};
