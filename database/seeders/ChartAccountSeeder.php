<?php

namespace Database\Seeders;

use App\Models\ChartAccount;
use Illuminate\Database\Seeder;

class ChartAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['code' => '1100', 'name' => 'Cash on Hand', 'type' => 'asset', 'subcategory' => 'Current Assets'],
            ['code' => '1110', 'name' => 'Cash in Bank', 'type' => 'asset', 'subcategory' => 'Current Assets'],
            ['code' => '1120', 'name' => 'Petty Cash', 'type' => 'asset', 'subcategory' => 'Current Assets'],
            ['code' => '1130', 'name' => 'Undeposited Collections', 'type' => 'asset', 'subcategory' => 'Current Assets'],
            ['code' => '1200', 'name' => 'Accounts Receivable', 'type' => 'asset', 'subcategory' => 'Current Assets'],
            ['code' => '1210', 'name' => 'Retention Receivable', 'type' => 'asset', 'subcategory' => 'Current Assets'],
            ['code' => '1300', 'name' => 'Inventory – Materials', 'type' => 'asset', 'subcategory' => 'Current Assets'],
            ['code' => '1310', 'name' => 'Inventory – Fuel & Supplies', 'type' => 'asset', 'subcategory' => 'Current Assets'],
            ['code' => '1400', 'name' => 'Advances & Prepayments', 'type' => 'asset', 'subcategory' => 'Current Assets'],
            ['code' => '1410', 'name' => 'Employee Cash Advances', 'type' => 'asset', 'subcategory' => 'Current Assets'],
            ['code' => '1420', 'name' => 'Supplier Advances', 'type' => 'asset', 'subcategory' => 'Current Assets'],
            ['code' => '1500', 'name' => 'Construction Equipment', 'type' => 'asset', 'subcategory' => 'Non-Current Assets'],
            ['code' => '1510', 'name' => 'Vehicles', 'type' => 'asset', 'subcategory' => 'Non-Current Assets'],
            ['code' => '1520', 'name' => 'Office Equipment', 'type' => 'asset', 'subcategory' => 'Non-Current Assets'],
            ['code' => '1600', 'name' => 'Accumulated Depreciation – Equipment', 'type' => 'asset', 'subcategory' => 'Non-Current Assets'],
            ['code' => '1610', 'name' => 'Accumulated Depreciation – Vehicles', 'type' => 'asset', 'subcategory' => 'Non-Current Assets'],

            ['code' => '2100', 'name' => 'Accounts Payable – Trade', 'type' => 'liability', 'subcategory' => 'Current Liabilities'],
            ['code' => '2110', 'name' => 'Accounts Payable – Non-Trade', 'type' => 'liability', 'subcategory' => 'Current Liabilities'],
            ['code' => '2200', 'name' => 'Payroll Payable – Employees', 'type' => 'liability', 'subcategory' => 'Current Liabilities'],
            ['code' => '2210', 'name' => 'Withholding Tax Payable', 'type' => 'liability', 'subcategory' => 'Current Liabilities'],
            ['code' => '2220', 'name' => 'SSS Payable', 'type' => 'liability', 'subcategory' => 'Current Liabilities'],
            ['code' => '2230', 'name' => 'PhilHealth Payable', 'type' => 'liability', 'subcategory' => 'Current Liabilities'],
            ['code' => '2240', 'name' => 'Pag-IBIG Payable', 'type' => 'liability', 'subcategory' => 'Current Liabilities'],
            ['code' => '2250', 'name' => 'Other Payroll Deductions Payable', 'type' => 'liability', 'subcategory' => 'Current Liabilities'],
            ['code' => '2300', 'name' => 'Accrued Expenses', 'type' => 'liability', 'subcategory' => 'Current Liabilities'],
            ['code' => '2310', 'name' => 'Retention Payable', 'type' => 'liability', 'subcategory' => 'Current Liabilities'],
            ['code' => '2320', 'name' => 'Advances & Deposits Payable', 'type' => 'liability', 'subcategory' => 'Current Liabilities'],
            ['code' => '2330', 'name' => 'VAT Payable', 'type' => 'liability', 'subcategory' => 'Current Liabilities'],
            ['code' => '2340', 'name' => 'Income Tax Payable', 'type' => 'liability', 'subcategory' => 'Current Liabilities'],
            ['code' => '2400', 'name' => 'Bank Loans Payable', 'type' => 'liability', 'subcategory' => 'Long-Term Liabilities'],
            ['code' => '2410', 'name' => 'Lease Liabilities', 'type' => 'liability', 'subcategory' => 'Long-Term Liabilities'],

            ['code' => '3000', 'name' => 'Owner’s Capital', 'type' => 'equity', 'subcategory' => 'Equity'],
            ['code' => '3100', 'name' => 'Retained Earnings', 'type' => 'equity', 'subcategory' => 'Equity'],
            ['code' => '3200', 'name' => 'Current Year Profit / Loss', 'type' => 'equity', 'subcategory' => 'Equity'],

            ['code' => '4000', 'name' => 'Construction Revenue', 'type' => 'revenue', 'subcategory' => 'Revenue'],
            ['code' => '4010', 'name' => 'Variation Orders Revenue', 'type' => 'revenue', 'subcategory' => 'Revenue'],
            ['code' => '4020', 'name' => 'Service Revenue', 'type' => 'revenue', 'subcategory' => 'Revenue'],

            ['code' => '5100', 'name' => 'Direct Labor – Project', 'type' => 'expense', 'subcategory' => 'Direct Costs'],
            ['code' => '5110', 'name' => 'Overtime – Project', 'type' => 'expense', 'subcategory' => 'Direct Costs'],
            ['code' => '5120', 'name' => 'Site Allowances', 'type' => 'expense', 'subcategory' => 'Direct Costs'],
            ['code' => '5200', 'name' => 'Construction Materials', 'type' => 'expense', 'subcategory' => 'Direct Costs'],
            ['code' => '5210', 'name' => 'Fuel & Lubricants', 'type' => 'expense', 'subcategory' => 'Direct Costs'],
            ['code' => '5300', 'name' => 'Equipment Rental', 'type' => 'expense', 'subcategory' => 'Direct Costs'],
            ['code' => '5310', 'name' => 'Equipment Depreciation', 'type' => 'expense', 'subcategory' => 'Direct Costs'],

            ['code' => '6100', 'name' => 'Salaries – Admin', 'type' => 'expense', 'subcategory' => 'Operating Expenses'],
            ['code' => '6110', 'name' => 'Employer Contributions', 'type' => 'expense', 'subcategory' => 'Operating Expenses'],
            ['code' => '6200', 'name' => 'Office Supplies', 'type' => 'expense', 'subcategory' => 'Operating Expenses'],
            ['code' => '6210', 'name' => 'Utilities Expense', 'type' => 'expense', 'subcategory' => 'Operating Expenses'],
            ['code' => '6220', 'name' => 'Repairs & Maintenance', 'type' => 'expense', 'subcategory' => 'Operating Expenses'],
            ['code' => '6230', 'name' => 'Communication Expense', 'type' => 'expense', 'subcategory' => 'Operating Expenses'],
            ['code' => '6240', 'name' => 'Professional Fees', 'type' => 'expense', 'subcategory' => 'Operating Expenses'],
            ['code' => '6300', 'name' => 'Depreciation Expense', 'type' => 'expense', 'subcategory' => 'Operating Expenses'],
            ['code' => '6310', 'name' => 'Training & Seminars', 'type' => 'expense', 'subcategory' => 'Operating Expenses'],
            ['code' => '6320', 'name' => 'Miscellaneous Expense', 'type' => 'expense', 'subcategory' => 'Operating Expenses'],
        ];

        foreach ($accounts as $account) {
            ChartAccount::updateOrCreate(
                ['code' => $account['code']],
                [
                    'name' => $account['name'],
                    'type' => $account['type'],
                    'subcategory' => $account['subcategory'],
                    'description' => null,
                    'status' => 'active',
                    'total_debit' => 0,
                    'total_credit' => 0,
                    'balance' => 0
                ]
            );
        }
    }
}
