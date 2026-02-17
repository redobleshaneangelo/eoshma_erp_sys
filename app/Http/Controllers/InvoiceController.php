<?php

namespace App\Http\Controllers;

use App\Models\InvoiceAttachment;
use App\Models\InvoiceHeader;
use App\Models\InvoiceLine;
use App\Models\InvoicePayment;
use App\Services\Finance\InvoiceLedgerPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceLedgerPostingService $postingService)
    {
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'module' => ['nullable', 'string', 'in:receivables,payables']
        ]);

        $rows = InvoiceHeader::query()
            ->with(['lineItems.chartAccount:id,code,name,type', 'payments', 'attachments', 'invoiceClient', 'counterparty'])
            ->when(!empty($validated['module']), function ($query) use ($validated) {
                $query->where('module', $validated['module']);
            })
            ->orderByDesc('invoice_date')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'data' => $rows->map(fn (InvoiceHeader $invoice) => $this->toFrontendInvoice($invoice))->values()
        ]);
    }

    public function show(InvoiceHeader $invoice)
    {
        $invoice->load(['lineItems.chartAccount:id,code,name,type', 'payments', 'attachments', 'invoiceClient', 'counterparty']);

        return response()->json([
            'data' => $this->toFrontendInvoice($invoice)
        ]);
    }

    public function store(Request $request)
    {
        $lineItemsInput = $request->input('line_items', []);
        if (is_string($lineItemsInput)) {
            $decoded = json_decode($lineItemsInput, true);
            $lineItemsInput = is_array($decoded) ? $decoded : [];
        }

        $attachmentsInput = $request->input('attachments', []);
        if (is_string($attachmentsInput)) {
            $decoded = json_decode($attachmentsInput, true);
            $attachmentsInput = is_array($decoded) ? $decoded : [];
        }

        $request->merge([
            'line_items' => $lineItemsInput,
            'attachments' => $attachmentsInput,
        ]);

        $validated = $request->validate([
            'module' => ['required', 'string', 'in:receivables,payables'],
            'invoice_type' => ['required', 'string', 'max:100'],
            'counterparty_id' => ['required', 'integer', 'exists:invoice_clients,id'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date'],
            'status' => ['nullable', 'string', 'in:Posted,Paid,Cancelled'],
            'description' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*.name' => ['required_with:attachments', 'string', 'max:255'],
            'attachments.*.size' => ['nullable', 'numeric', 'min:0'],
            'attachments.*.type' => ['nullable', 'string', 'max:100'],
            'attachments_files' => ['nullable', 'array'],
            'attachments_files.*' => ['file', 'max:10240'],
            'line_items' => ['required', 'array', 'min:1'],
            'line_items.*.description' => ['required', 'string', 'max:255'],
            'line_items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'line_items.*.unit' => ['nullable', 'string', 'max:30'],
            'line_items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'line_items.*.chart_account_id' => ['required', 'integer', 'exists:finance_chart_accounts,id']
        ]);

        $authId = optional($request->user())->id;

        $invoice = DB::transaction(function () use ($validated, $authId, $request) {
            $lineRows = collect($validated['line_items'])->values()->map(function ($line, $idx) {
                $quantity = round((float) $line['quantity'], 4);
                $unitPrice = round((float) $line['unit_price'], 2);
                $lineTotal = round($quantity * $unitPrice, 2);

                return [
                    'line_no' => $idx + 1,
                    'description' => $line['description'],
                    'quantity' => $quantity,
                    'unit' => $line['unit'] ?? null,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                    'chart_account_id' => (int) $line['chart_account_id']
                ];
            });

            $total = round((float) $lineRows->sum('line_total'), 2);
            $status = $validated['status'] ?? 'Posted';

            $invoice = InvoiceHeader::create([
                'module' => $validated['module'],
                'invoice_no' => $this->nextInvoiceNumber($validated['module']),
                'invoice_type' => $validated['invoice_type'],
                'counterparty_id' => null,
                'invoice_client_id' => (int) $validated['counterparty_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'status' => $status,
                'total_amount' => $total,
                'net_amount' => $total,
                'paid_amount' => 0,
                'description' => $validated['description'] ?? null,
                'created_by_user_id' => $authId,
                'updated_by_user_id' => $authId
            ]);

            foreach ($lineRows as $line) {
                InvoiceLine::create([
                    'invoice_id' => $invoice->id,
                    ...$line
                ]);
            }

            foreach (($validated['attachments'] ?? []) as $attachment) {
                InvoiceAttachment::create([
                    'invoice_id' => $invoice->id,
                    'attachment_name' => $attachment['name'],
                    'attachment_size' => isset($attachment['size']) ? (int) $attachment['size'] : null,
                    'attachment_type' => $attachment['type'] ?? null,
                    'created_by_user_id' => $authId
                ]);
            }

            foreach (($request->file('attachments_files') ?? []) as $file) {
                $path = $file->store("invoices/{$invoice->module}/{$invoice->invoice_no}/attachments", 'public');

                InvoiceAttachment::create([
                    'invoice_id' => $invoice->id,
                    'attachment_name' => $file->getClientOriginalName(),
                    'attachment_size' => (int) $file->getSize(),
                    'attachment_type' => $file->getClientMimeType(),
                    'attachment_path' => $path,
                    'created_by_user_id' => $authId
                ]);
            }

            $invoice->load('lineItems');

            if (in_array($status, ['Posted', 'Paid'], true)) {
                $this->postingService->postInvoice($invoice);
                $invoice->update(['posted_to_gl_at' => now()]);
            }

            return $invoice;
        });

        $invoice->load(['lineItems.chartAccount:id,code,name,type', 'payments', 'attachments', 'invoiceClient', 'counterparty']);

        return response()->json([
            'data' => $this->toFrontendInvoice($invoice)
        ], 201);
    }

    public function storePayment(Request $request, InvoiceHeader $invoice)
    {
        $validated = $request->validate([
            'pay_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'reference_no' => ['nullable', 'string', 'max:255'],
            'attachment_name' => ['nullable', 'string', 'max:255'],
            'payment_attachment' => ['nullable', 'file', 'max:10240']
        ]);

        if ($invoice->status === 'Cancelled') {
            return response()->json([
                'message' => 'Cannot record payment for a cancelled invoice.'
            ], 422);
        }

        $authId = optional($request->user())->id;

        $result = DB::transaction(function () use ($invoice, $validated, $authId, $request) {
            $invoice->loadMissing('lineItems');

            if (!$invoice->posted_to_gl_at) {
                $this->postingService->postInvoice($invoice);
                $invoice->posted_to_gl_at = now();
            }

            $payment = InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'pay_date' => $validated['pay_date'],
                'amount' => round((float) $validated['amount'], 2),
                'reference_no' => $validated['reference_no'] ?? null,
                'attachment_name' => $validated['attachment_name'] ?? null,
                'created_by_user_id' => $authId
            ]);

            $file = $request->file('payment_attachment');
            if ($file) {
                $path = $file->store("invoices/{$invoice->module}/{$invoice->invoice_no}/payments", 'public');
                $payment->update([
                    'attachment_name' => $file->getClientOriginalName(),
                    'attachment_size' => (int) $file->getSize(),
                    'attachment_type' => $file->getClientMimeType(),
                    'attachment_path' => $path,
                ]);
            }

            $this->postingService->postPayment($invoice, $payment);
            $payment->update(['posted_to_gl_at' => now()]);

            $paidAmount = round((float) InvoicePayment::query()->where('invoice_id', $invoice->id)->sum('amount'), 2);
            $updates = [
                'paid_amount' => $paidAmount,
                'updated_by_user_id' => $authId
            ];

            if ($paidAmount >= round((float) $invoice->net_amount, 2)) {
                $updates['status'] = 'Paid';
                $updates['paid_at'] = now();
            }

            $invoice->update($updates);

            $invoice->load(['lineItems.chartAccount:id,code,name,type', 'payments', 'attachments', 'invoiceClient', 'counterparty']);

            return [
                'invoice' => $invoice,
                'payment' => $payment
            ];
        });

        return response()->json([
            'data' => [
                'invoice' => $this->toFrontendInvoice($result['invoice']),
                'payment' => $this->toFrontendPayment($result['payment'])
            ]
        ]);
    }

    public function payments(InvoiceHeader $invoice)
    {
        $payments = $invoice->payments()->get();

        return response()->json([
            'data' => $payments->map(fn (InvoicePayment $payment) => $this->toFrontendPayment($payment))->values()
        ]);
    }

    public function history(string $module, string $invoiceNo)
    {
        if (!in_array($module, ['receivables', 'payables'], true)) {
            return response()->json(['message' => 'Invalid module.'], 422);
        }

        $invoice = InvoiceHeader::query()
            ->with(['lineItems.chartAccount:id,code,name,type', 'payments', 'attachments', 'invoiceClient', 'counterparty'])
            ->where('module', $module)
            ->where('invoice_no', $invoiceNo)
            ->first();

        if (!$invoice) {
            return response()->json([
                'invoice' => null,
                'payments' => []
            ]);
        }

        return response()->json([
            'invoice' => $this->toFrontendInvoice($invoice),
            'payments' => $invoice->payments->map(fn (InvoicePayment $payment) => $this->toFrontendPayment($payment))->values()
        ]);
    }

    private function nextInvoiceNumber(string $module): string
    {
        $year = now()->year;
        $prefix = $module === 'payables' ? "PI-{$year}-" : "SI-{$year}-";

        $existing = InvoiceHeader::query()
            ->where('module', $module)
            ->where('invoice_no', 'like', $prefix . '%')
            ->pluck('invoice_no');

        $maxSequence = 0;

        foreach ($existing as $invoiceNo) {
            $sequence = (int) str_replace($prefix, '', (string) $invoiceNo);
            if ($sequence > $maxSequence) {
                $maxSequence = $sequence;
            }
        }

        return $prefix . str_pad((string) ($maxSequence + 1), 4, '0', STR_PAD_LEFT);
    }

    private function toFrontendInvoice(InvoiceHeader $invoice): array
    {
        $counterparty = $invoice->invoiceClient;
        $counterpartyName = '';

        if ($counterparty) {
            $counterpartyName = $counterparty->name ?: ($counterparty->contact_person ?? $counterparty->email ?? 'Unnamed Party');
        } elseif ($invoice->counterparty) {
            $legacyCounterparty = $invoice->counterparty;
            $counterpartyName = trim(implode(' ', array_filter([
                $legacyCounterparty->first_name ?? null,
                $legacyCounterparty->last_name ?? null
            ])));
            if ($counterpartyName === '') {
                $counterpartyName = $legacyCounterparty->contact_person ?? $legacyCounterparty->email ?? 'Unnamed Party';
            }
        }

        return [
            'id' => $invoice->id,
            'invoiceNo' => $invoice->invoice_no,
            'invoiceType' => $invoice->invoice_type,
            'counterpartyId' => $invoice->invoice_client_id ?? $invoice->counterparty_id,
            'counterpartyName' => $counterpartyName,
            'invoiceDate' => optional($invoice->invoice_date)->toDateString(),
            'dueDate' => optional($invoice->due_date)->toDateString(),
            'status' => $invoice->status,
            'totalAmount' => (float) $invoice->total_amount,
            'netAmount' => (float) $invoice->net_amount,
            'paidAmount' => (float) $invoice->paid_amount,
            'description' => $invoice->description,
            'attachments' => $invoice->attachments->map(function (InvoiceAttachment $attachment) {
                return [
                    'id' => $attachment->id,
                    'name' => $attachment->attachment_name,
                    'size' => $attachment->attachment_size,
                    'type' => $attachment->attachment_type,
                    'path' => $attachment->attachment_path,
                    'url' => $attachment->attachment_path ? Storage::url($attachment->attachment_path) : null
                ];
            })->values()->all(),
            'paymentHistory' => $invoice->payments->map(fn (InvoicePayment $payment) => $this->toFrontendPayment($payment))->values()->all(),
            'lineItems' => $invoice->lineItems->map(function (InvoiceLine $line) {
                return [
                    'id' => $line->id,
                    'key' => (string) $line->id,
                    'description' => $line->description,
                    'quantity' => (float) $line->quantity,
                    'unit' => $line->unit,
                    'unitPrice' => (float) $line->unit_price,
                    'lineTotal' => (float) $line->line_total,
                    'accountId' => $line->chart_account_id,
                    'accountName' => $line->chartAccount?->name,
                    'accountType' => $line->chartAccount?->type,
                    'accountSearch' => ''
                ];
            })->values()->all()
        ];
    }

    private function toFrontendPayment(InvoicePayment $payment): array
    {
        return [
            'id' => $payment->id,
            'payDate' => optional($payment->pay_date)->toDateString(),
            'amount' => (float) $payment->amount,
            'referenceNo' => $payment->reference_no,
            'attachmentName' => $payment->attachment_name,
            'attachmentUrl' => $payment->attachment_path ? Storage::url($payment->attachment_path) : null
        ];
    }
}
