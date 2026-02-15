<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('chart_accounts') && !Schema::hasTable('finance_chart_accounts')) {
            Schema::rename('chart_accounts', 'finance_chart_accounts');
        }

        if (Schema::hasTable('chart_account_transactions') && !Schema::hasTable('finance_chart_account_transactions')) {
            Schema::rename('chart_account_transactions', 'finance_chart_account_transactions');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('finance_chart_account_transactions') && !Schema::hasTable('chart_account_transactions')) {
            Schema::rename('finance_chart_account_transactions', 'chart_account_transactions');
        }

        if (Schema::hasTable('finance_chart_accounts') && !Schema::hasTable('chart_accounts')) {
            Schema::rename('finance_chart_accounts', 'chart_accounts');
        }
    }
};
