<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">
            <div class="max-w-4xl mx-auto bg-white rounded-lg shadow border border-gray-200 p-8">
                <div class="flex justify-end gap-2 mb-5">
                    <button
                        @click="printPayslip"
                        class="px-4 py-2 text-sm font-semibold border border-gray-300 rounded-lg hover:bg-gray-100"
                    >
                        Print
                    </button>
                    <button
                        @click="exportPayslipPdf"
                        class="px-4 py-2 text-sm font-semibold text-white bg-[#0c8ce9] rounded-lg hover:bg-blue-700"
                    >
                        Export PDF
                    </button>
                </div>

                <div id="payslip-print-area">
                <div class="text-center mb-8">
                    <h2 class="text-4xl font-bold text-gray-900">Payslip</h2>
                    <p class="text-gray-700 mt-3">
                        <span class="font-semibold">Pay Period:</span>
                        {{ formatDate(payslip.periodStart) }} - {{ formatDate(payslip.periodEnd) }}
                    </p>
                </div>

                <div class="space-y-1 text-gray-800 mb-8">
                    <p><span class="font-semibold">Employee Name:</span> {{ payslip.employeeName || '--' }}</p>
                    <p><span class="font-semibold">Employee Code:</span> {{ payslip.employeeCode || '--' }}</p>
                    <p><span class="font-semibold">Position:</span> {{ payslip.position || '--' }}</p>
                </div>

                <div class="overflow-x-auto mb-8">
                    <table class="w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="border border-gray-200 px-4 py-2 text-left">Earnings</th>
                                <th class="border border-gray-200 px-4 py-2 text-right">Amount</th>
                                <th class="border border-gray-200 px-4 py-2 text-left">Deductions</th>
                                <th class="border border-gray-200 px-4 py-2 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-gray-200 px-4 py-2">Base Salary</td>
                                <td class="border border-gray-200 px-4 py-2 text-right">{{ money(payslip.earnings.baseSalary) }}</td>
                                <td class="border border-gray-200 px-4 py-2">Taxes</td>
                                <td class="border border-gray-200 px-4 py-2 text-right">{{ money(payslip.deductions.taxes) }}</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-200 px-4 py-2">Overtime Pay</td>
                                <td class="border border-gray-200 px-4 py-2 text-right">{{ money(payslip.earnings.overtimePay) }}</td>
                                <td class="border border-gray-200 px-4 py-2">Late Penalties</td>
                                <td class="border border-gray-200 px-4 py-2 text-right">{{ money(payslip.deductions.latePenalties) }}</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-200 px-4 py-2">Bonuses</td>
                                <td class="border border-gray-200 px-4 py-2 text-right">{{ money(payslip.earnings.bonuses) }}</td>
                                <td class="border border-gray-200 px-4 py-2">Absences</td>
                                <td class="border border-gray-200 px-4 py-2 text-right">{{ money(payslip.deductions.absences) }}</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-200 px-4 py-2">Gross Salary</td>
                                <td class="border border-gray-200 px-4 py-2 text-right">{{ money(payslip.earnings.grossSalary) }}</td>
                                <td class="border border-gray-200 px-4 py-2">Other Deductions</td>
                                <td class="border border-gray-200 px-4 py-2 text-right">{{ money(payslip.deductions.otherDeductions) }}</td>
                            </tr>
                            <tr class="font-semibold bg-gray-50">
                                <td class="border border-gray-200 px-4 py-2">Total</td>
                                <td class="border border-gray-200 px-4 py-2 text-right">{{ money(totalEarnings) }}</td>
                                <td class="border border-gray-200 px-4 py-2">Total</td>
                                <td class="border border-gray-200 px-4 py-2 text-right">{{ money(payslip.deductions.total) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="overflow-x-auto mb-8">
                    <table class="w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="border border-gray-200 px-4 py-2 text-left">Net Pay</th>
                                <th class="border border-gray-200 px-4 py-2 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-gray-200 px-4 py-2 font-semibold">Net Pay</td>
                                <td class="border border-gray-200 px-4 py-2 text-right font-semibold text-[#0c8ce9]">{{ money(payslip.netPay) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p class="text-gray-600 text-sm">
                    If you need further assistance, please contact HR.
                </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

const route = useRoute()
const auth = useAuthStore()

const payslip = ref({
    periodStart: null,
    periodEnd: null,
    employeeName: '',
    employeeCode: '',
    position: '',
    earnings: {
        baseSalary: 0,
        overtimePay: 0,
        bonuses: 0,
        grossSalary: 0
    },
    deductions: {
        taxes: 0,
        latePenalties: 0,
        absences: 0,
        otherDeductions: 0,
        total: 0
    },
    netPay: 0
})

const totalEarnings = computed(() => {
    return Number(Number(payslip.value.earnings.grossSalary || 0).toFixed(2))
})

const fetchPayslip = async () => {
    try {
        const response = await axios.get(`/api/employee/payslips/${route.params.id}`)
        payslip.value = response.data?.data || payslip.value
    } catch (error) {
        console.error('Failed to load payslip', error)
    }
}

const formatDate = (date) => {
    if (!date) return '--'
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const money = (value) => {
    return `₱${Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
}

onMounted(() => {
    auth.pageTitle = 'Payslip'
    fetchPayslip()
})

const openPrintWindow = (mode = 'print') => {
    const content = document.getElementById('payslip-print-area')
    if (!content) {
        return
    }

    const popup = window.open('', '_blank', 'width=900,height=1000')
    if (!popup) {
        return
    }

    popup.document.write(`
        <html>
            <head>
                <title>${mode === 'pdf' ? 'Payslip PDF Export' : 'Payslip Print'}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 24px; color: #111827; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #d1d5db; padding: 8px 10px; font-size: 12px; }
                    th { background: #f3f4f6; text-align: left; }
                    .text-right { text-align: right; }
                </style>
            </head>
            <body>${content.innerHTML}</body>
        </html>
    `)
    popup.document.close()
    popup.focus()
    popup.print()
}

const printPayslip = () => {
    openPrintWindow('print')
}

const exportPayslipPdf = () => {
    openPrintWindow('pdf')
}
</script>
