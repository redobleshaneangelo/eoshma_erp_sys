<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices_headers', function (Blueprint $table) {
            $table->id();
            $table->string('module', 20);
            $table->string('invoice_no', 30)->unique();
            $table->string('invoice_type', 100);
            $table->foreignId('counterparty_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->string('status', 20)->default('Posted');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamp('posted_to_gl_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['module', 'status']);
            $table->index(['module', 'invoice_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices_headers');
    }
};
