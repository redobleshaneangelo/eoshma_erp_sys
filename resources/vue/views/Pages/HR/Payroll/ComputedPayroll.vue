<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10 space-y-6">
            <div>
                <router-link
                    :to="`/payroll/runs/${runId}`"
                    class="inline-flex items-center gap-2 text-[#0c8ce9] font-semibold hover:underline mb-4"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Payroll Run
                </router-link>

                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Computed Payroll</h1>
                        <p class="text-sm text-gray-600 mt-1">Run ID: {{ runId }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Payroll Summary</h2>
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Employee</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Basic Pay</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Allowance</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Deductions</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Net Pay</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="row in computedRows" :key="row.id" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <p class="font-semibold">{{ row.employee }}</p>
                                    <p class="text-xs text-gray-500">{{ row.role }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">₱{{ formatNumber(row.basicPay) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">₱{{ formatNumber(row.allowance) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">₱{{ formatNumber(row.deductions) }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">₱{{ formatNumber(row.netPay) }}</td>
                            </tr>
                            <tr v-if="computedRows.length === 0">
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No computed payroll data.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3">
                <button
                    @click="recomputePayroll"
                    class="px-4 py-2 text-sm font-semibold border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Recompute
                </button>
                <button
                    @click="sendForApproval"
                    class="px-4 py-2 text-sm font-semibold text-white bg-[#0c8ce9] rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Send for Approval
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AuthLayout from '@/views/Layouts/AuthLayout.vue'
import Swal from 'sweetalert2'
import axios from 'axios'

const route = useRoute()
const router = useRouter()
const runId = route.params.id

const computedRows = ref([])

const fetchComputedPayroll = async () => {
    if (!runId) return
    try {
        const response = await axios.get(`/api/payroll-runs/${runId}/computed-payroll`)
        computedRows.value = response.data?.data || []
    } catch (error) {
        console.error('Failed to load computed payroll', error)
    }
}

onMounted(() => {
    fetchComputedPayroll()
})

const recomputePayroll = () => {
    router.push({ name: 'payroll_run_detail', params: { id: runId } })
}

const sendForApproval = () => {
    Swal.fire({
        title: 'Send for approval?',
        text: 'This will notify the HR head for review.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0c8ce9',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, send'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post(`/api/payroll-runs/${runId}/send-for-approval`)
                .then(() => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Sent to HR head',
                        showConfirmButton: false,
                        timer: 2000
                    })
                    router.push({ name: 'payroll', query: { tab: 'pending' } })
                })
                .catch((error) => {
                    console.error('Failed to send for approval', error)
                })
        }
    })
}

const formatNumber = (value) => {
    return Number(value || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}
</script>
