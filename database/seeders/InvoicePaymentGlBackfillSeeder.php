<?php

namespace Database\Seeders;

use App\Models\ChartAccountTransaction;
use App\Models\InvoicePayment;
use App\Services\Finance\InvoiceLedgerPostingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoicePaymentGlBackfillSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(InvoiceLedgerPostingService::class);

        DB::transaction(function () use ($service) {
            $payments = InvoicePayment::query()->with('invoice.lineItems')->get();

            foreach ($payments as $payment) {
                if (!$payment->invoice) {
                    continue;
                }

                $exists = ChartAccountTransaction::query()
                    ->where('source_type', 'invoice_payment')
                    ->where('source_id', $payment->id)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $payment->update(['posted_to_gl_at' => null]);
                $service->postPayment($payment->invoice, $payment);
                $payment->update(['posted_to_gl_at' => now()]);
            }
        });

        $this->command?->info('Invoice payment GL backfill completed.');
    }
}
