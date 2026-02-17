<?php

namespace App\Services\Finance;

use App\Models\ChartAccount;
use App\Models\ChartAccountTransaction;
use App\Models\InvoiceHeader;
use App\Models\InvoicePayment;
use RuntimeException;

class InvoiceLedgerPostingService
{
    public function postInvoice(InvoiceHeader $invoice): void
    {
        if ($invoice->posted_to_gl_at) {
            return;
        }

        $reference = $invoice->invoice_no;
        $description = 'Invoice posting';
        $transactionDate = $invoice->invoice_date;
        $netAmount = round((float) $invoice->net_amount, 2);

        if ($netAmount <= 0) {
            throw new RuntimeException('Invoice net amount must be greater than zero for GL posting.');
        }

        if ($invoice->module === 'receivables') {
            $arAccount = $this->requireAccountByCode('1200', 'Accounts Receivable');
            $this->appendTransaction($arAccount, $transactionDate, $description, $reference, $netAmount, 0, 'invoice', (int) $invoice->id, 'invoice_posted');

            foreach ($invoice->lineItems as $line) {
                $revenueAccount = ChartAccount::find($line->chart_account_id);
                if (!$revenueAccount) {
                    throw new RuntimeException('Missing revenue account for invoice line.');
                }

                $this->appendTransaction(
                    $revenueAccount,
                    $transactionDate,
                    sprintf('Invoice line posting: %s', $line->description),
                    $reference,
                    0,
                    round((float) $line->line_total, 2),
                    'invoice',
                    (int) $invoice->id,
                    'invoice_posted'
                );
            }

            return;
        }

        if ($invoice->module === 'payables') {
            $apAccount = $this->requireAccountByCode('2100', 'Accounts Payable');

            foreach ($invoice->lineItems as $line) {
                $expenseOrAssetAccount = ChartAccount::find($line->chart_account_id);
                if (!$expenseOrAssetAccount) {
                    throw new RuntimeException('Missing expense/asset account for invoice line.');
                }

                $this->appendTransaction(
                    $expenseOrAssetAccount,
                    $transactionDate,
                    sprintf('Invoice line posting: %s', $line->description),
                    $reference,
                    round((float) $line->line_total, 2),
                    0,
                    'invoice',
                    (int) $invoice->id,
                    'invoice_posted'
                );
            }

            $this->appendTransaction($apAccount, $transactionDate, $description, $reference, 0, $netAmount, 'invoice', (int) $invoice->id, 'invoice_posted');
            return;
        }

        throw new RuntimeException('Unsupported invoice module for GL posting.');
    }

    public function postPayment(InvoiceHeader $invoice, InvoicePayment $payment): void
    {
        if ($payment->posted_to_gl_at) {
            return;
        }

        $amount = round((float) $payment->amount, 2);
        if ($amount <= 0) {
            throw new RuntimeException('Payment amount must be greater than zero for GL posting.');
        }

        $cashInBank = $this->requireAccountByCode('1110', 'Cash in Bank');
        $reference = sprintf('%s-PAY-%d', $invoice->invoice_no, $payment->id);
        $description = 'Invoice payment posting';
        $transactionDate = $payment->pay_date;

        if ($invoice->module === 'receivables') {
            $arAccount = $this->requireAccountByCode('1200', 'Accounts Receivable');

            $this->appendTransaction($cashInBank, $transactionDate, $description, $reference, $amount, 0, 'invoice_payment', (int) $payment->id, 'payment_posted');
            $this->appendTransaction($arAccount, $transactionDate, $description, $reference, 0, $amount, 'invoice_payment', (int) $payment->id, 'payment_posted');

            return;
        }

        if ($invoice->module === 'payables') {
            $apAccount = $this->requireAccountByCode('2100', 'Accounts Payable');

            $this->appendTransaction($apAccount, $transactionDate, $description, $reference, $amount, 0, 'invoice_payment', (int) $payment->id, 'payment_posted');
            $this->appendTransaction($cashInBank, $transactionDate, $description, $reference, 0, $amount, 'invoice_payment', (int) $payment->id, 'payment_posted');

            return;
        }

        throw new RuntimeException('Unsupported invoice module for payment GL posting.');
    }

    private function requireAccountByCode(string $code, string $label): ChartAccount
    {
        $account = ChartAccount::query()->where('code', $code)->first();

        if (!$account) {
            throw new RuntimeException("Missing required account {$code} ({$label}) for GL posting.");
        }

        return $account;
    }

    private function appendTransaction(
        ChartAccount $account,
        $transactionDate,
        string $description,
        string $reference,
        float $debit,
        float $credit,
        string $sourceType,
        int $sourceId,
        string $sourceEvent
    ): void {
        $priorBalance = round((float) $account->balance, 2);
        $newBalance = round($priorBalance + $debit - $credit, 2);

        ChartAccountTransaction::create([
            'chart_account_id' => $account->id,
            'payroll_run_id' => null,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'source_event' => $sourceEvent,
            'transaction_date' => $transactionDate,
            'description' => $description,
            'reference' => $reference,
            'debit' => round($debit, 2),
            'credit' => round($credit, 2),
            'balance' => $newBalance
        ]);

        $account->update([
            'total_debit' => round((float) $account->total_debit + $debit, 2),
            'total_credit' => round((float) $account->total_credit + $credit, 2),
            'balance' => $newBalance
        ]);
    }
}
