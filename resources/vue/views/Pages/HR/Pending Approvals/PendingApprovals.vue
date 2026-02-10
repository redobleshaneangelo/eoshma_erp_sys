<template>
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">HR Pending Approvals</h1>
                <p class="text-sm text-gray-600 mt-1">Review payroll runs awaiting approval.</p>
            </div>
            <button
                @click="fetchPendingRuns"
                class="px-4 py-2 text-sm font-semibold text-white bg-[#0c8ce9] rounded-lg hover:bg-blue-700"
            >
                Refresh
            </button>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
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
    </div>
</template>

<script setup>
    import { ref, onMounted } from 'vue'
    import { useRouter } from 'vue-router'
    import axios from 'axios'

    const router = useRouter()
    const pendingRuns = ref([])

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
        fetchPendingRuns()
    })
</script>