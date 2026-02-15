<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-[#2B2B2B]">Payroll Liabilities</h2>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select
                            v-model="statusFilter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c8ce9]"
                        >
                            <option value="">All</option>
                            <option value="Posted">Posted</option>
                            <option value="Paid">Paid</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search payroll name, group, description..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c8ce9]"
                        />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Payroll Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Payroll Frequency</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Payroll Start Date - Payroll End Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Payroll Group</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Pay Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="run in paginatedPayrollRuns" :key="run.id" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ run.name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ run.frequency }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ formatDate(run.startDate) }} - {{ formatDate(run.endDate) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ run.group }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                                            :class="getStatusClasses(run.liabilityStatus)"
                                        >
                                            {{ run.liabilityStatus }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ formatDate(run.payDate) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ run.description }}</td>
                                    <td class="px-4 py-3">
                                        <button
                                            @click="openPayrollRun(run)"
                                            class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                                        >
                                            View
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="filteredPayrollRuns.length === 0">
                                    <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">
                                        No liabilities payroll runs available
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between items-center px-1 py-4">
                        <p class="text-sm text-gray-600">
                            Showing {{ paginationStart }} to {{ paginationEnd }} of {{ filteredPayrollRuns.length }} payroll runs
                        </p>
                        <div class="flex gap-2">
                            <button
                                @click="currentPage--"
                                :disabled="currentPage === 1"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Previous
                            </button>
                            <span class="px-4 py-2 text-sm font-medium">Page {{ currentPage }} of {{ totalPages }}</span>
                            <button
                                @click="currentPage++"
                                :disabled="currentPage === totalPages"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

const auth = useAuthStore()
const router = useRouter()

const payrollRuns = ref([])
const statusFilter = ref('')
const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = 10

const toLiabilityStatus = (status) => {
    if (status === 'Completed') return 'Paid'
    if (status === 'Approved') return 'Posted'
    return null
}

const mapRun = (payload) => ({
    id: payload.id,
    name: payload.name,
    frequency: payload.frequency,
    startDate: payload.start_date,
    endDate: payload.end_date,
    group: payload.group,
    originalStatus: payload.status,
    liabilityStatus: toLiabilityStatus(payload.status),
    payDate: payload.pay_date,
    description: payload.description
})

const fetchPayrollRuns = async () => {
    try {
        const response = await axios.get('/api/payroll-runs')
        const rows = response.data?.data || []
        payrollRuns.value = rows
            .map(mapRun)
            .filter((run) => !!run.liabilityStatus)
    } catch (error) {
        console.error('Failed to load liabilities payroll runs', error)
    }
}

const filteredPayrollRuns = computed(() => {
    const query = searchQuery.value.trim().toLowerCase()

    return payrollRuns.value.filter((run) => {
        const matchesStatus = !statusFilter.value || run.liabilityStatus === statusFilter.value

        if (!matchesStatus) {
            return false
        }

        if (!query) {
            return true
        }

        const haystack = [run.name, run.frequency, run.group, run.description]
            .map((value) => String(value || '').toLowerCase())
            .join(' ')

        return haystack.includes(query)
    })
})

const totalPages = computed(() => {
    const total = Math.ceil(filteredPayrollRuns.value.length / itemsPerPage)
    return total > 0 ? total : 1
})

const paginatedPayrollRuns = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage
    return filteredPayrollRuns.value.slice(start, start + itemsPerPage)
})

const paginationStart = computed(() => {
    if (!filteredPayrollRuns.value.length) return 0
    return (currentPage.value - 1) * itemsPerPage + 1
})

const paginationEnd = computed(() => {
    if (!filteredPayrollRuns.value.length) return 0
    return Math.min(currentPage.value * itemsPerPage, filteredPayrollRuns.value.length)
})

const openPayrollRun = (run) => {
    router.push({
        name: 'payroll_run_view',
        params: { id: run.id },
        query: { mode: 'finance-liabilities' }
    })
}

const getStatusClasses = (status) => {
    const map = {
        Posted: 'bg-amber-100 text-amber-800',
        Paid: 'bg-green-100 text-green-800'
    }

    return map[status] || 'bg-gray-100 text-gray-700'
}

const formatDate = (date) => {
    if (!date) return '--'
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

onMounted(() => {
    auth.pageTitle = 'Liabilities'
    fetchPayrollRuns()
})

const resetToFirstPage = () => {
    currentPage.value = 1
}

watch(statusFilter, resetToFirstPage)
watch(searchQuery, resetToFirstPage)
watch(filteredPayrollRuns, () => {
    if (currentPage.value > totalPages.value) {
        currentPage.value = totalPages.value
    }
})
</script>