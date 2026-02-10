<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prebiddings', function (Blueprint $table) {
            $table->id();

            // Project Information
            $table->string('name');
            $table->string('code')->unique();
            $table->string('type');
            $table->text('address');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('image')->nullable();
            $table->text('notes')->nullable();

            // Bid Details
            $table->date('bid_deadline');
            $table->date('bid_opening');
            $table->string('bid_type');
            $table->string('bond_type');
            $table->decimal('security_amount', 15, 2);

            // Project Setup
            // $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null');
            // $table->foreignId('foreman_id')->nullable()->constrained('users')->onDelete('set null');

            $table->string('supervisor_id');
            $table->string('foreman_id');
            $table->foreignId('submitted_by')->constrained('users')->onDelete('cascade');

            // Financial
            $table->decimal('total_budget', 15, 2);
            $table->decimal('total_utilized', 15, 2)->default(0);
            $table->decimal('contract_amount', 15, 2);
            $table->string('billing_type');
            $table->decimal('retention', 15, 2);

            $table->string('status')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prebiddings');
    }
};
