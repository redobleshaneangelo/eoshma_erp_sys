<template>
    <auth-layout />
    <div class="ps-60 pe-4 bg-[#F3F4F6] min-h-screen">
        <div class="px-8 py-6">
            <div class="w-full max-w-6xl mx-auto space-y-6">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">Payment History</h1>
                    <button
                        @click="goBack"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-100"
                    >
                        Back
                    </button>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6 grid grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Module</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ moduleLabel }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Invoice No.</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ invoice?.invoiceNo || '--' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Vendor / Client</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ invoice?.counterpartyName || '--' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Payments</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ payments.length }}</p>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100 border-b border-gray-200">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Pay Date</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Amount</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Reference</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Attachment</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="payment in payments" :key="payment.id" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-700">{{ formatDate(payment.payDate) }}</td>
                                    <td class="px-4 py-3 text-gray-900 font-semibold">₱{{ Number(payment.amount || 0).toLocaleString() }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ payment.referenceNo || '--' }}</td>
                                    <td class="px-4 py-3 text-gray-700">
                                        <a
                                            v-if="payment.attachmentUrl"
                                            :href="payment.attachmentUrl"
                                            target="_blank"
                                            class="text-[#0c8ce9] hover:underline"
                                        >
                                            {{ payment.attachmentName || 'Open attachment' }}
                                        </a>
                                        <span v-else>{{ payment.attachmentName || '--' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button
                                            @click="viewPayment(payment)"
                                            class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                                        >
                                            View
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="payments.length === 0">
                                    <td colspan="5" class="px-4 py-10 text-center text-gray-500">No payment history found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showPaymentViewModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Payment View</h3>
                    <button @click="showPaymentViewModal = false" class="text-gray-600 hover:text-gray-900">✕</button>
                </div>
                <div class="p-6 space-y-3 text-sm">
                    <p><span class="font-semibold text-gray-700">Pay Date:</span> {{ formatDate(selectedPayment?.payDate) }}</p>
                    <p><span class="font-semibold text-gray-700">Amount:</span> ₱{{ Number(selectedPayment?.amount || 0).toLocaleString() }}</p>
                    <p><span class="font-semibold text-gray-700">Reference:</span> {{ selectedPayment?.referenceNo || '--' }}</p>
                    <p>
                        <span class="font-semibold text-gray-700">Attachment:</span>
                        <a
                            v-if="selectedPayment?.attachmentUrl"
                            :href="selectedPayment.attachmentUrl"
                            target="_blank"
                            class="text-[#0c8ce9] hover:underline"
                        >
                            {{ selectedPayment?.attachmentName || 'Open attachment' }}
                        </a>
                        <span v-else>{{ selectedPayment?.attachmentName || '--' }}</span>
                    </p>
                </div>
                <div class="p-6 border-t border-gray-200 flex justify-end">
                    <button @click="showPaymentViewModal = false" class="px-4 py-2 bg-[#0c8ce9] text-white rounded-lg text-sm font-semibold">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const route = useRoute()
const router = useRouter()

const invoice = ref(null)
const payments = ref([])

const showPaymentViewModal = ref(false)
const selectedPayment = ref(null)

const moduleKey = computed(() => String(route.params.module || '').toLowerCase())

const moduleLabel = computed(() => {
    if (moduleKey.value === 'receivables') return 'Receivables'
    if (moduleKey.value === 'payables') return 'Payables'
    return 'Finance'
})

const loadData = async () => {
    const invoiceNo = String(route.params.invoiceNo || '')

    if (!invoiceNo) {
        invoice.value = null
        payments.value = []
        return
    }

    try {
        const response = await axios.get(`/api/invoices-history/${moduleKey.value}/${invoiceNo}`)
        invoice.value = response.data?.invoice || null
        payments.value = response.data?.payments || []
    } catch (error) {
        invoice.value = null
        payments.value = []
    }
}

const viewPayment = (payment) => {
    selectedPayment.value = payment
    showPaymentViewModal.value = true
}

const goBack = () => {
    if (moduleKey.value === 'payables') {
        router.push({ name: 'payables' })
        return
    }

    router.push({ name: 'receivables' })
}

const formatDate = (value) => {
    if (!value) return '--'
    return new Date(value).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

onMounted(() => {
    auth.pageTitle = 'Payment History'
    loadData()
})
</script>