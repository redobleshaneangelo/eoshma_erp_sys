<template>
    <div class="space-y-8">
        <!-- Payment Terms & Credit -->
        <div>
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#0c8ce9]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Payment Terms & Credit
            </h2>
            <div class="grid grid-cols-3 gap-6 bg-gray-50 rounded-lg p-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Default Payment Terms</label>
                    <p class="text-gray-900 font-semibold">{{ financial.payment_terms }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Credit Limit</label>
                    <p class="text-gray-900 font-semibold">₱{{ formatCurrency(financial.credit_limit) }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Retention Percentage</label>
                    <p class="text-gray-900 font-semibold">{{ financial.retention_percentage }}%</p>
                </div>
            </div>
        </div>

        <!-- Tax & Billing -->
        <div>
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#0c8ce9]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Tax & Billing
            </h2>
            <div class="grid grid-cols-2 gap-6 bg-gray-50 rounded-lg p-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Tax Type</label>
                    <p class="text-gray-900 font-semibold">{{ financial.tax_type }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Billing Status</label>
                    <p>
                        <span :class="billingStatusStyles[financial.billing_status]" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold">
                            {{ financial.billing_status }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Financial Summary Cards -->
        <div>
            <h2 class="text-xl font-bold text-gray-900 mb-4">Financial Summary</h2>
            <div class="grid grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
                    <div class="text-green-700 text-xs font-semibold uppercase tracking-wide">Total Contract Value (Lifetime)</div>
                    <div class="text-3xl font-bold text-green-900 mt-2">₱{{ formatCurrency(financial.total_contract_value) }}</div>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
                    <div class="text-blue-700 text-xs font-semibold uppercase tracking-wide">Outstanding Balance</div>
                    <div class="text-3xl font-bold text-blue-900 mt-2">₱{{ formatCurrency(financial.outstanding_balance) }}</div>
                </div>
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-6 border border-orange-200">
                    <div class="text-orange-700 text-xs font-semibold uppercase tracking-wide">Avg Payment Delay</div>
                    <div class="text-3xl font-bold text-orange-900 mt-2">{{ financial.avg_payment_delay }} days</div>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div>
            <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Payment Activity</h2>
            <div class="space-y-3">
                <div v-for="payment in paymentHistory" :key="payment.id" class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-all">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">{{ payment.description }}</p>
                        <p class="text-xs text-gray-600 mt-1">{{ payment.date }}</p>
                    </div>
                    <div class="text-right">
                        <p :class="payment.type === 'payment' ? 'text-green-600' : 'text-red-600'" class="font-bold text-lg">
                            {{ payment.type === 'payment' ? '+' : '-' }}₱{{ formatCurrency(payment.amount) }}
                        </p>
                        <p class="text-xs text-gray-600">{{ payment.status }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    client: {
        type: Object,
        required: true
    }
})

const financial = {
    payment_terms: 'Net 30',
    credit_limit: 2000000,
    retention_percentage: 5,
    tax_type: 'VAT (12%)',
    billing_status: 'Good Standing',
    total_contract_value: 5000000,
    outstanding_balance: 250000,
    avg_payment_delay: 5
}

const paymentHistory = [
    {
        id: 1,
        description: 'Payment received for Project Alpha',
        date: 'Feb 1, 2026',
        type: 'payment',
        amount: 500000,
        status: 'Completed'
    },
    {
        id: 2,
        description: 'Invoice issued for Project Beta - Phase 2',
        date: 'Jan 28, 2026',
        type: 'invoice',
        amount: 250000,
        status: 'Due Feb 28'
    },
    {
        id: 3,
        description: 'Partial payment received for Project Gamma',
        date: 'Jan 25, 2026',
        type: 'payment',
        amount: 300000,
        status: 'Completed'
    }
]

const billingStatusStyles = {
    'Good Standing': 'bg-green-100 text-green-800',
    'Late Payer': 'bg-yellow-100 text-yellow-800',
    'Billing Blocked': 'bg-red-100 text-red-800'
}

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', { 
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value)
}
</script>
