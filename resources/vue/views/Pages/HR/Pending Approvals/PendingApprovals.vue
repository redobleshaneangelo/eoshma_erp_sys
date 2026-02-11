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
                                    <div class="flex gap-2">
                                        <button
                                            @click="approveOvertime(item)"
                                            class="px-3 py-1 text-xs font-semibold text-white bg-green-600 rounded hover:bg-green-700"
                                        >
                                            Approve
                                        </button>
                                        <button
                                            @click="rejectOvertime(item)"
                                            class="px-3 py-1 text-xs font-semibold text-white bg-red-600 rounded hover:bg-red-700"
                                        >
                                            Reject
                                        </button>
                                    </div>
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

    const openApproval = (run) => {
        router.push({
            name: 'payroll_run_detail',
            params: { id: run.id },
            query: { mode: 'approval' }
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