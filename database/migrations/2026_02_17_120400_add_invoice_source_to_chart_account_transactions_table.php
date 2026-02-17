<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_chart_account_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('finance_chart_account_transactions', 'source_type')) {
                $table->string('source_type', 50)->nullable()->after('payroll_run_id');
            }

            if (!Schema::hasColumn('finance_chart_account_transactions', 'source_id')) {
                $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
            }

            if (!Schema::hasColumn('finance_chart_account_transactions', 'source_event')) {
                $table->string('source_event', 50)->nullable()->after('source_id');
            }

            $table->index(['source_type', 'source_id'], 'finance_cat_source_type_source_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('finance_chart_account_transactions', function (Blueprint $table) {
            $table->dropIndex('finance_cat_source_type_source_id_idx');

            if (Schema::hasColumn('finance_chart_account_transactions', 'source_event')) {
                $table->dropColumn('source_event');
            }

            if (Schema::hasColumn('finance_chart_account_transactions', 'source_id')) {
                $table->dropColumn('source_id');
            }

            if (Schema::hasColumn('finance_chart_account_transactions', 'source_type')) {
                $table->dropColumn('source_type');
            }
        });
    }
};
