<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->renameForeignIdColumn('employee_attendances');
        $this->renameForeignIdColumn('allowances');
        $this->renameForeignIdColumn('deductions');
        $this->renameForeignIdColumn('computed_payroll');
        $this->renameEligibilityColumn();
    }

    public function down(): void
    {
        $this->renameForeignIdColumnBack('employee_attendances');
        $this->renameForeignIdColumnBack('allowances');
        $this->renameForeignIdColumnBack('deductions');
        $this->renameForeignIdColumnBack('computed_payroll');
        $this->renameEligibilityColumnBack();
    }

    private function renameForeignIdColumn(string $table): void
    {
        if (!Schema::hasColumn($table, 'employee_test_id')) {
            return;
        }

        try {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['employee_test_id']);
            });
        } catch (Throwable $e) {
        }

        DB::statement("ALTER TABLE `{$table}` CHANGE `employee_test_id` `employee_id` BIGINT UNSIGNED NOT NULL");

        Schema::table($table, function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
        });
    }

    private function renameForeignIdColumnBack(string $table): void
    {
        if (!Schema::hasColumn($table, 'employee_id')) {
            return;
        }

        try {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['employee_id']);
            });
        } catch (Throwable $e) {
        }

        DB::statement("ALTER TABLE `{$table}` CHANGE `employee_id` `employee_test_id` BIGINT UNSIGNED NOT NULL");

        Schema::table($table, function (Blueprint $table) {
            $table->foreign('employee_test_id')->references('id')->on('employees')->cascadeOnDelete();
        });
    }

    private function renameEligibilityColumn(): void
    {
        $table = 'payroll_run_eligibilities';
        if (!Schema::hasColumn($table, 'employee_test_id')) {
            return;
        }

        try {
            Schema::table($table, function (Blueprint $table) {
                $table->dropUnique(['payroll_run_id', 'employee_test_id']);
                $table->dropForeign(['employee_test_id']);
            });
        } catch (Throwable $e) {
        }

        DB::statement("ALTER TABLE `{$table}` CHANGE `employee_test_id` `employee_id` BIGINT UNSIGNED NOT NULL");

        Schema::table($table, function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->unique(['payroll_run_id', 'employee_id']);
        });
    }

    private function renameEligibilityColumnBack(): void
    {
        $table = 'payroll_run_eligibilities';
        if (!Schema::hasColumn($table, 'employee_id')) {
            return;
        }

        try {
            Schema::table($table, function (Blueprint $table) {
                $table->dropUnique(['payroll_run_id', 'employee_id']);
                $table->dropForeign(['employee_id']);
            });
        } catch (Throwable $e) {
        }

        DB::statement("ALTER TABLE `{$table}` CHANGE `employee_id` `employee_test_id` BIGINT UNSIGNED NOT NULL");

        Schema::table($table, function (Blueprint $table) {
            $table->foreign('employee_test_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->unique(['payroll_run_id', 'employee_test_id']);
        });
    }
};
