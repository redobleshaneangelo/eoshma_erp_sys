<?php

namespace Database\Seeders;

use App\Models\ChartAccount;
use Illuminate\Database\Seeder;

class InvoiceDropdownAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // Receivables dropdown (revenue)
            ['code' => '4901', 'name' => 'Demo Revenue - Project Billing A', 'type' => 'revenue', 'subcategory' => 'Revenue'],
            ['code' => '4902', 'name' => 'Demo Revenue - Project Billing B', 'type' => 'revenue', 'subcategory' => 'Revenue'],
            ['code' => '4903', 'name' => 'Demo Revenue - Service Fees', 'type' => 'revenue', 'subcategory' => 'Revenue'],
            ['code' => '4904', 'name' => 'Demo Revenue - Retainage Release', 'type' => 'revenue', 'subcategory' => 'Revenue'],

            // Payables dropdown (expense)
            ['code' => '6901', 'name' => 'Demo Expense - Site Materials', 'type' => 'expense', 'subcategory' => 'Direct Costs'],
            ['code' => '6902', 'name' => 'Demo Expense - Equipment Rental', 'type' => 'expense', 'subcategory' => 'Direct Costs'],
            ['code' => '6903', 'name' => 'Demo Expense - Fuel & Logistics', 'type' => 'expense', 'subcategory' => 'Operating Expenses'],
            ['code' => '6904', 'name' => 'Demo Expense - Subcontractor Fees', 'type' => 'expense', 'subcategory' => 'Direct Costs'],
        ];

        foreach ($accounts as $account) {
            ChartAccount::updateOrCreate(
                ['code' => $account['code']],
                [
                    'name' => $account['name'],
                    'type' => $account['type'],
                    'subcategory' => $account['subcategory'],
                    'description' => 'Seeded for invoice line-item account dropdown testing',
                    'status' => 'active',
                    'total_debit' => 0,
                    'total_credit' => 0,
                    'balance' => 0,
                ]
            );
        }

        $this->command?->info('InvoiceDropdownAccountSeeder completed.');
    }
}
