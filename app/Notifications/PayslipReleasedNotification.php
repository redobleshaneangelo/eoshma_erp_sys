<?php

namespace App\Notifications;

use App\Models\EmployeePayslip;
use App\Models\PayrollRun;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PayslipReleasedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly PayrollRun $payrollRun,
        private readonly EmployeePayslip $payslip
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'kind' => 'payslip_released',
            'title' => 'Payslip Released',
            'message' => "Your payslip for {$this->payrollRun->name} is now available.",
            'payroll_run_id' => $this->payrollRun->id,
            'payroll_run_name' => $this->payrollRun->name,
            'payslip_id' => $this->payslip->id,
            'pay_date' => optional($this->payrollRun->pay_date)->toDateString()
        ];
    }
}
