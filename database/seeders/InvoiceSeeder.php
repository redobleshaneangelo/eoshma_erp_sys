<?php

namespace Database\Seeders;

use App\Models\ChartAccount;
use App\Models\InvoiceHeader;
use App\Models\InvoiceLine;
use App\Models\InvoicePayment;
use App\Models\InvoiceClient;
use App\Services\Finance\InvoiceLedgerPostingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(InvoiceLedgerPostingService::class);

        $accounts = [
            'revenue' => ChartAccount::query()->where('code', '4000')->first(),
            'expense' => ChartAccount::query()->where('code', '5200')->first(),
        ];

        if (!$accounts['revenue'] || !$accounts['expense']) {
            $this->command?->warn('InvoiceSeeder skipped: required chart accounts (4000, 5200) not found.');
            return;
        }

        $counterpartyId = InvoiceClient::query()->value('id');

        DB::transaction(function () use ($service, $accounts, $counterpartyId) {
            $receivablePosted = $this->createInvoiceIfMissing(
                module: 'receivables',
                invoiceNo: 'SI-2026-9001',
                invoiceType: 'Sales Invoice',
                counterpartyId: $counterpartyId,
                invoiceDate: '2026-02-01',
                dueDate: '2026-02-15',
                description: 'Seeded receivable (posted only)',
                lines: [
                    ['description' => 'Construction milestone billing A', 'quantity' => 1, 'unit' => 'lot', 'unit_price' => 150000, 'chart_account_id' => $accounts['revenue']->id],
                ],
                status: 'Posted'
            );

            if ($receivablePosted) {
                $service->postInvoice($receivablePosted->load('lineItems'));
                $receivablePosted->update(['posted_to_gl_at' => now()]);
            }

            $receivablePaid = $this->createInvoiceIfMissing(
                module: 'receivables',
                invoiceNo: 'SI-2026-9002',
                invoiceType: 'Sales Invoice',
                counterpartyId: $counterpartyId,
                invoiceDate: '2026-02-03',
                dueDate: '2026-02-20',
                description: 'Seeded receivable (paid)',
                lines: [
                    ['description' => 'Construction milestone billing B', 'quantity' => 1, 'unit' => 'lot', 'unit_price' => 95000, 'chart_account_id' => $accounts['revenue']->id],
                    ['description' => 'Project management service', 'quantity' => 1, 'unit' => 'lot', 'unit_price' => 55000, 'chart_account_id' => $accounts['revenue']->id],
                ],
                status: 'Posted'
            );

            if ($receivablePaid) {
                $service->postInvoice($receivablePaid->load('lineItems'));
                $receivablePaid->update(['posted_to_gl_at' => now()]);

                $payment = InvoicePayment::create([
                    'invoice_id' => $receivablePaid->id,
                    'pay_date' => '2026-02-10',
                    'amount' => (float) $receivablePaid->net_amount,
                    'reference_no' => 'RCPT-9002',
                    'attachment_name' => 'seed-receipt-9002.pdf',
                ]);

                $service->postPayment($receivablePaid, $payment);
                $payment->update(['posted_to_gl_at' => now()]);

                $receivablePaid->update([
                    'status' => 'Paid',
                    'paid_amount' => (float) $receivablePaid->net_amount,
                    'paid_at' => now(),
                ]);
            }

            $payablePosted = $this->createInvoiceIfMissing(
                module: 'payables',
                invoiceNo: 'PI-2026-9001',
                invoiceType: 'Purchase Invoice',
                counterpartyId: $counterpartyId,
                invoiceDate: '2026-02-04',
                dueDate: '2026-02-18',
                description: 'Seeded payable (posted only)',
                lines: [
                    ['description' => 'Materials procurement', 'quantity' => 1, 'unit' => 'lot', 'unit_price' => 130000, 'chart_account_id' => $accounts['expense']->id],
                ],
                status: 'Posted'
            );

            if ($payablePosted) {
                $service->postInvoice($payablePosted->load('lineItems'));
                $payablePosted->update(['posted_to_gl_at' => now()]);
            }

            $payablePaid = $this->createInvoiceIfMissing(
                module: 'payables',
                invoiceNo: 'PI-2026-9002',
                invoiceType: 'Purchase Invoice',
                counterpartyId: $counterpartyId,
                invoiceDate: '2026-02-06',
                dueDate: '2026-02-25',
                description: 'Seeded payable (paid)',
                lines: [
                    ['description' => 'Equipment rental', 'quantity' => 1, 'unit' => 'lot', 'unit_price' => 60000, 'chart_account_id' => $accounts['expense']->id],
                    ['description' => 'Fuel and logistics', 'quantity' => 1, 'unit' => 'lot', 'unit_price' => 40000, 'chart_account_id' => $accounts['expense']->id],
                ],
                status: 'Posted'
            );

            if ($payablePaid) {
                $service->postInvoice($payablePaid->load('lineItems'));
                $payablePaid->update(['posted_to_gl_at' => now()]);

                $payment = InvoicePayment::create([
                    'invoice_id' => $payablePaid->id,
                    'pay_date' => '2026-02-14',
                    'amount' => (float) $payablePaid->net_amount,
                    'reference_no' => 'CV-9002',
                    'attachment_name' => 'seed-payment-9002.pdf',
                ]);

                $service->postPayment($payablePaid, $payment);
                $payment->update(['posted_to_gl_at' => now()]);

                $payablePaid->update([
                    'status' => 'Paid',
                    'paid_amount' => (float) $payablePaid->net_amount,
                    'paid_at' => now(),
                ]);
            }
        });

        $this->command?->info('InvoiceSeeder completed. Seeded sample receivables, payables, and GL postings.');
    }

    private function createInvoiceIfMissing(
        string $module,
        string $invoiceNo,
        string $invoiceType,
        ?int $counterpartyId,
        string $invoiceDate,
        string $dueDate,
        string $description,
        array $lines,
        string $status = 'Posted'
    ): ?InvoiceHeader {
        if (InvoiceHeader::query()->where('invoice_no', $invoiceNo)->exists()) {
            return null;
        }

        $lineRows = collect($lines)->values()->map(function (array $line, int $idx) {
            $quantity = round((float) ($line['quantity'] ?? 0), 4);
            $unitPrice = round((float) ($line['unit_price'] ?? 0), 2);
            $lineTotal = round($quantity * $unitPrice, 2);

            return [
                'line_no' => $idx + 1,
                'description' => (string) ($line['description'] ?? ''),
                'quantity' => $quantity,
                'unit' => $line['unit'] ?? null,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
                'chart_account_id' => (int) $line['chart_account_id'],
            ];
        });

        $total = round((float) $lineRows->sum('line_total'), 2);

        $invoice = InvoiceHeader::create([
            'module' => $module,
            'invoice_no' => $invoiceNo,
            'invoice_type' => $invoiceType,
            'counterparty_id' => null,
            'invoice_client_id' => $counterpartyId,
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
            'status' => $status,
            'total_amount' => $total,
            'net_amount' => $total,
            'paid_amount' => 0,
            'description' => $description,
        ]);

        foreach ($lineRows as $line) {
            InvoiceLine::create([
                'invoice_id' => $invoice->id,
                ...$line,
            ]);
        }

        return $invoice;
    }
}
