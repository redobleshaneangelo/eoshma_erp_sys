<?php

namespace Database\Seeders;

use App\Models\InvoiceClient;
use Illuminate\Database\Seeder;

class InvoiceClientSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'Aurora Buildworks Inc.', 'email' => 'finance@aurorabuildworks.test', 'contact_person' => 'Luna Reyes', 'phone' => '09170000001', 'status' => 'active'],
            ['name' => 'Summit Aggregates Supply', 'email' => 'ap@summitaggregates.test', 'contact_person' => 'Mika Tan', 'phone' => '09170000002', 'status' => 'active'],
            ['name' => 'Northlane Equipment Rental', 'email' => 'billing@northlaneequip.test', 'contact_person' => 'Noel Cruz', 'phone' => '09170000003', 'status' => 'active'],
            ['name' => 'King Marcelino Trading', 'email' => 'accounts@kingmarcelino.test', 'contact_person' => 'King Marcelino', 'phone' => '09170000004', 'status' => 'active'],
            ['name' => 'Suisei Hoshimachi Holdings', 'email' => 'payables@suiseihoshi.test', 'contact_person' => 'Suisei Hoshimachi', 'phone' => '09170000005', 'status' => 'active'],
        ];

        foreach ($rows as $row) {
            InvoiceClient::updateOrCreate(
                ['name' => $row['name']],
                $row
            );
        }

        $this->command?->info('InvoiceClientSeeder completed.');
    }
}
