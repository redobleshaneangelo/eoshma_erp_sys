<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices_headers', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices_headers', 'invoice_client_id')) {
                $table->foreignId('invoice_client_id')->nullable()->after('counterparty_id')->constrained('invoice_clients')->nullOnDelete();
                $table->index(['module', 'invoice_client_id']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices_headers', function (Blueprint $table) {
            if (Schema::hasColumn('invoices_headers', 'invoice_client_id')) {
                $table->dropForeign(['invoice_client_id']);
                $table->dropIndex('invoices_headers_module_invoice_client_id_index');
                $table->dropColumn('invoice_client_id');
            }
        });
    }
};
