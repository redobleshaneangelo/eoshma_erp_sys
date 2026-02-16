<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10 space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3 ms-auto" v-if="activeTab === 'payroll_runs'">
                    <button
                        @click="openPayrollConfiguration"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-[#0c8ce9] border border-[#0c8ce9] hover:bg-blue-50 rounded-lg transition-colors"
                    >
                        Payroll Configuration
                    </button>
                    <button
                        @click="showCreateModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700 rounded-lg transition-colors"
                    >
                        Create Payroll Run
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50">
                    <div class="flex flex-wrap">
                        <button
                            v-for="tab in tabs"
                            :key="tab.key"
                            @click="activeTab = tab.key"
                            :class="[
                                'px-6 py-4 text-sm font-semibold transition-colors border-b-2',
                                activeTab === tab.key
                                    ? 'text-[#0c8ce9] border-[#0c8ce9] bg-white'
                                    : 'text-gray-600 border-transparent hover:text-gray-900 hover:bg-gray-100'
                            ]"
                        >
                            {{ tab.label }}
                        </button>
                    </div>
                </div>

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
                                <tr v-for="run in filteredPayrollRuns" :key="run.id" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ run.name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ run.frequency }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ formatDate(run.startDate) }} - {{ formatDate(run.endDate) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ run.group }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            v-if="activeTab === 'payroll_runs'"
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                                            :class="getStatusClasses('Completed')"
                                        >
                                            Completed
                                        </span>
                                        <span
                                            v-else
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                                            :class="getStatusClasses(run.status)"
                                        >
                                            {{ getStatusLabel(run.status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ formatDate(run.payDate) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ run.description }}</td>
                                    <td class="px-4 py-3">
                                        <button
                                            @click="openPayrollRun(run)"
                                            class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                                        >
                                            {{ getActionLabel }}
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="filteredPayrollRuns.length === 0">
                                    <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">
                                        No payroll runs available
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Payroll Run Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Create Payroll Run</h3>
                    <button @click="closeCreateModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payroll Name</label>
                    <input
                        v-model="newPayrollRun.name"
                        type="text"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        placeholder="Enter payroll name"
                    />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Payroll Start Date</label>
                        <input
                            v-model="newPayrollRun.startDate"
                            type="date"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Payroll End Date</label>
                        <input
                            v-model="newPayrollRun.endDate"
                            type="date"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payroll Group</label>
                    <select
                        v-model="newPayrollRun.payrollGroupId"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                    >
                        <option value="">Select payroll group</option>
                        <option v-for="group in payrollGroups" :key="group.id" :value="group.id">
                            {{ group.name }} ({{ group.payrollFrequency }})
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pay Date</label>
                    <input
                        v-model="newPayrollRun.payDate"
                        type="date"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                    />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea
                        v-model="newPayrollRun.description"
                        rows="3"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        placeholder="Add description"
                    ></textarea>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex gap-3">
                <button
                    @click="closeCreateModal"
                    class="flex-1 px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="createPayrollRun"
                    class="flex-1 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                >
                    Create
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AuthLayout from '@/views/Layouts/AuthLayout.vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()

onMounted(() => {
    auth.pageTitle = 'Payroll'
    fetchPayrollRuns()
    fetchPayrollGroups()
    if (route.query.tab) {
        activeTab.value = String(route.query.tab)
    }
})

watch(
    () => route.query.tab,
    (value) => {
        if (value) {
            activeTab.value = String(value)
        }
    }
)

const tabs = [
    { key: 'payroll_runs', label: 'Payroll Runs' },
    { key: 'draft', label: 'Draft' },
    { key: 'rejected', label: 'Rejected' },
    { key: 'pending', label: 'Pending' },
    { key: 'approved', label: 'Approved' }
]

const activeTab = ref('payroll_runs')
const showCreateModal = ref(false)

const payrollRuns = ref([])
const payrollGroups = ref([])

const mapRun = (payload) => ({
    id: payload.id,
    name: payload.name,
    frequency: payload.frequency,
    startDate: payload.start_date,
    endDate: payload.end_date,
    group: payload.group,
    payrollGroupId: payload.payroll_group_id,
    status: payload.status,
    payDate: payload.pay_date,
    description: payload.description,
    rejectReason: payload.reject_reason || null
})

const fetchPayrollRuns = async () => {
    try {
        const response = await axios.get('/api/payroll-runs')
        const rows = response.data?.data || []
        payrollRuns.value = rows.map(mapRun)
    } catch (error) {
        console.error('Failed to load payroll runs', error)
    }
}

const fetchPayrollGroups = async () => {
    try {
        const response = await axios.get('/api/payroll-groups')
        payrollGroups.value = response.data?.data || []
    } catch (error) {
        console.error('Failed to load payroll groups', error)
    }
}

const openPayrollConfiguration = () => {
    router.push({ name: 'payroll_configuration' })
}

const newPayrollRun = ref({
    name: '',
    startDate: '',
    endDate: '',
    payrollGroupId: '',
    status: 'Draft',
    payDate: '',
    description: ''
})

const filteredPayrollRuns = computed(() => {
    if (activeTab.value === 'payroll_runs') return payrollRuns.value.filter(run => run.status === 'Completed')
    if (activeTab.value === 'draft') return payrollRuns.value.filter(run => run.status === 'Draft')
    if (activeTab.value === 'rejected') return payrollRuns.value.filter(run => run.status === 'Rejected')
    if (activeTab.value === 'pending') return payrollRuns.value.filter(run => ['Pending', 'Pending Finance Approval'].includes(run.status))
    if (activeTab.value === 'approved') return payrollRuns.value.filter(run => run.status === 'Approved')
    return payrollRuns.value
})

const getActionLabel = computed(() => {
    if (activeTab.value === 'payroll_runs') return 'View'
    if (activeTab.value === 'draft') return 'Open'
    if (activeTab.value === 'rejected') return 'Open'
    if (activeTab.value === 'pending') return 'View'
    if (activeTab.value === 'approved') return 'View'
    return 'Open'
})

const createPayrollRun = async () => {
    if (!newPayrollRun.value.name) return
    try {
        const payload = {
            name: newPayrollRun.value.name,
            start_date: newPayrollRun.value.startDate,
            end_date: newPayrollRun.value.endDate,
            payroll_group_id: newPayrollRun.value.payrollGroupId,
            status: newPayrollRun.value.status,
            pay_date: newPayrollRun.value.payDate,
            description: newPayrollRun.value.description
        }
        const response = await axios.post('/api/payroll-runs', payload)
        const savedRun = response.data?.data
        if (savedRun?.id) {
            payrollRuns.value.unshift(mapRun(savedRun))
            closeCreateModal()
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Payroll run created',
                showConfirmButton: false,
                timer: 2000
            })
            router.push({
                name: 'payroll_run_detail',
                params: { id: savedRun.id },
                query: {
                    name: savedRun.name,
                    frequency: savedRun.frequency,
                    startDate: savedRun.start_date,
                    endDate: savedRun.end_date,
                    group: savedRun.group,
                    status: savedRun.status,
                    payDate: savedRun.pay_date,
                    description: savedRun.description
                }
            })
        }
    } catch (error) {
        console.error('Failed to create payroll run', error)
        Swal.fire({
            icon: 'error',
            title: 'Create failed',
            text: 'Please check the form and try again.'
        })
    }
}

const openPayrollRun = (run) => {
    if (activeTab.value === 'payroll_runs') {
        router.push({ name: 'payroll_run_view', params: { id: run.id } })
        return
    }
    router.push({
        name: 'payroll_run_detail',
        params: { id: run.id },
        query: { fromTab: activeTab.value }
    })
}

const closeCreateModal = () => {
    showCreateModal.value = false
    newPayrollRun.value = {
        name: '',
        startDate: '',
        endDate: '',
        payrollGroupId: '',
        status: 'Draft',
        payDate: '',
        description: ''
    }
}

const getStatusClasses = (status) => {
    const map = {
        Completed: 'bg-green-100 text-green-800',
        Draft: 'bg-gray-200 text-gray-700',
        Rejected: 'bg-red-100 text-red-800',
        Pending: 'bg-yellow-100 text-yellow-800',
        'Pending Finance Approval': 'bg-amber-100 text-amber-800',
        Approved: 'bg-blue-100 text-blue-800'
    }
    return map[status] || 'bg-gray-100 text-gray-700'
}

const getStatusLabel = (status) => {
    const map = {
        Completed: 'Completed',
        Draft: 'Draft',
        Rejected: 'Rejected',
        Pending: 'Draft Pending',
        'Pending Finance Approval': 'Pending Finance Approval',
        Approved: 'Approved'
    }
    return map[status] || status
}

const formatDate = (date) => {
    if (!date) return '--'
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}
</script>