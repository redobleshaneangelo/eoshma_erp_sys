<template>
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">HR Pending Approvals</h1>
                <p class="text-sm text-gray-600 mt-1">Review payroll runs awaiting approval.</p>
            </div>
            <button
                @click="refreshAll"
                class="px-4 py-2 text-sm font-semibold text-white bg-[#0c8ce9] rounded-lg hover:bg-blue-700"
            >
                Refresh
            </button>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
                <div class="flex gap-3">
                    <button
                        @click="activeTab = 'payroll'"
                        :class="activeTab === 'payroll' ? 'bg-[#0c8ce9] text-white' : 'text-gray-700 hover:bg-gray-100'"
                        class="px-3 py-1.5 rounded text-sm font-semibold transition-colors"
                    >
                        Pending Payroll Runs
                    </button>
                    <button
                        @click="activeTab = 'overtime'"
                        :class="activeTab === 'overtime' ? 'bg-[#0c8ce9] text-white' : 'text-gray-700 hover:bg-gray-100'"
                        class="px-3 py-1.5 rounded text-sm font-semibold transition-colors"
                    >
                        Overtime Requests
                    </button>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'payroll'" class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
                <h2 class="text-sm font-semibold text-gray-700">Pending Payroll Runs</h2>
            </div>
            <div class="p-6">
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Payroll Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Frequency</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Coverage</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Group</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Pay Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="run in pendingRuns" :key="run.id" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ run.name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ run.frequency }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ formatDate(run.startDate) }} - {{ formatDate(run.endDate) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ run.group }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ formatDate(run.payDate) }}</td>
                                <td class="px-4 py-3">
                                    <button
                                        @click="openApproval(run)"
                                        class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                                    >
                                        Review
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="pendingRuns.length === 0">
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
                                    No pending payroll runs.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'overtime'" class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
                <h2 class="text-sm font-semibold text-gray-700">Overtime Requests</h2>
            </div>
            <div class="p-6">
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Employee</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Hours</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Start Time</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Day Type</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Reason</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="item in overtimeRequests" :key="item.id" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ item.employee.name }}
                                    <div class="text-xs text-gray-500">{{ item.employee.position }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ formatDate(item.date) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ item.hours }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ item.startTime || '--' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ item.dayTypeLabel }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ item.reason || '--' }}</td>
                                <td class="px-4 py-3">
                                    <button
                                        @click="openOvertimeModal(item)"
                                        class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                                    >
                                        Open Request
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="overtimeRequests.length === 0">
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
                                    No pending overtime requests.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-if="showOvertimeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Overtime Request</h3>
                        <button @click="closeOvertimeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6 space-y-4 text-sm text-gray-700">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Employee</p>
                        <p class="font-semibold text-gray-900">{{ selectedOvertime.employee?.name }}</p>
                        <p class="text-xs text-gray-500">{{ selectedOvertime.employee?.position }}</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Date</p>
                            <p class="font-semibold text-gray-900">{{ formatDate(selectedOvertime.date) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Hours</p>
                            <p class="font-semibold text-gray-900">{{ selectedOvertime.hours }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Start Time</p>
                            <p class="font-semibold text-gray-900">{{ selectedOvertime.startTime || '--' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Day Type</p>
                            <p class="font-semibold text-gray-900">{{ selectedOvertime.dayTypeLabel }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Reason</p>
                        <p class="text-sm text-gray-700">{{ selectedOvertime.reason || '--' }}</p>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-200 flex gap-3">
                    <button
                        @click="closeOvertimeModal"
                        class="flex-1 px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                    >
                        Close
                    </button>
                    <button
                        @click="approveOvertime(selectedOvertime)"
                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors"
                    >
                        Approve
                    </button>
                    <button
                        @click="rejectOvertime(selectedOvertime)"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors"
                    >
                        Reject
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, onMounted } from 'vue'
    import { useRouter } from 'vue-router'
    import axios from 'axios'
    import Swal from 'sweetalert2'

    const router = useRouter()
    const activeTab = ref('payroll')
    const pendingRuns = ref([])
    const overtimeRequests = ref([])
    const showOvertimeModal = ref(false)
    const selectedOvertime = ref({})

    const mapRun = (payload) => ({
        id: payload.id,
        name: payload.name,
        frequency: payload.frequency,
        startDate: payload.start_date,
        endDate: payload.end_date,
        group: payload.group,
        status: payload.status,
        payDate: payload.pay_date,
        description: payload.description
    })

    const fetchPendingRuns = async () => {
        try {
            const response = await axios.get('/api/payroll-runs')
            const rows = response.data?.data || []
            pendingRuns.value = rows.map(mapRun).filter(run => run.status === 'Pending')
        } catch (error) {
            console.error('Failed to load pending payroll runs', error)
        }
    }

    const fetchPendingOvertime = async () => {
        try {
            const response = await axios.get('/api/pending-approvals/hr/overtime')
            overtimeRequests.value = response.data?.data || []
        } catch (error) {
            console.error('Failed to load overtime requests', error)
        }
    }

    const approveOvertime = async (item) => {
        try {
            const result = await Swal.fire({
                icon: 'question',
                title: 'Approve overtime?'
                ,
                text: `Approve ${item.employee.name} for ${item.hours} hour(s) on ${formatDate(item.date)}?`,
                showCancelButton: true,
                confirmButtonText: 'Approve',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#16a34a'
            })

            if (!result.isConfirmed) {
                return
            }

            await axios.put(`/api/pending-approvals/hr/overtime/${item.id}/approve`)
            await fetchPendingOvertime()
            closeOvertimeModal()
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Overtime approved',
                showConfirmButton: false,
                timer: 2000
            })
        } catch (error) {
            console.error('Failed to approve overtime request', error)
            const message = error.response?.data?.message || 'Failed to approve overtime request.'
            Swal.fire({
                icon: 'error',
                title: 'Approval failed',
                text: message,
                confirmButtonColor: '#ef4444'
            })
        }
    }

    const rejectOvertime = async (item) => {
        try {
            const result = await Swal.fire({
                icon: 'warning',
                title: 'Reject overtime?'
                ,
                text: `Reject ${item.employee.name} for ${item.hours} hour(s) on ${formatDate(item.date)}?`,
                showCancelButton: true,
                confirmButtonText: 'Reject',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc2626'
            })

            if (!result.isConfirmed) {
                return
            }

            await axios.put(`/api/pending-approvals/hr/overtime/${item.id}/reject`)
            await fetchPendingOvertime()
            closeOvertimeModal()
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Overtime rejected',
                showConfirmButton: false,
                timer: 2000
            })
        } catch (error) {
            console.error('Failed to reject overtime request', error)
            const message = error.response?.data?.message || 'Failed to reject overtime request.'
            Swal.fire({
                icon: 'error',
                title: 'Rejection failed',
                text: message,
                confirmButtonColor: '#ef4444'
            })
        }
    }

    const refreshAll = () => {
        fetchPendingRuns()
        fetchPendingOvertime()
    }

    const openOvertimeModal = (item) => {
        selectedOvertime.value = item
        showOvertimeModal.value = true
    }

    const closeOvertimeModal = () => {
        showOvertimeModal.value = false
        selectedOvertime.value = {}
    }

    const openApproval = (run) => {
        router.push({
            name: 'payroll_run_detail',
            params: { id: run.id },
            query: { mode: 'eligibility' }
        })
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
        refreshAll()
    })
</script>