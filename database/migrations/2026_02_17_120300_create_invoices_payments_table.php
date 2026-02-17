<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices_headers')->cascadeOnDelete();
            $table->date('pay_date');
            $table->decimal('amount', 15, 2);
            $table->string('reference_no')->nullable();
            $table->string('attachment_name')->nullable();
            $table->unsignedBigInteger('attachment_size')->nullable();
            $table->string('attachment_type', 100)->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamp('posted_to_gl_at')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['invoice_id', 'pay_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices_payments');
    }
};
