<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices_headers')->cascadeOnDelete();
            $table->unsignedInteger('line_no')->default(1);
            $table->string('description');
            $table->decimal('quantity', 15, 4)->default(1);
            $table->string('unit', 30)->nullable();
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->foreignId('chart_account_id')->constrained('finance_chart_accounts')->restrictOnDelete();
            $table->timestamps();

            $table->index(['invoice_id', 'line_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices_lines');
    }
};
