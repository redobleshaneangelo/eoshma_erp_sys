<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10 space-y-6">
            <div>
                <button
                    @click="router.push({ name: 'disciplinary' })"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-300 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </button>
            </div>

            <div class="grid grid-cols-4 gap-4">
                <div class="bg-white rounded-lg p-4 border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Employee Complaints</p>
                    <p class="text-2xl font-bold text-[#333333]">{{ employeeComplaints.length }}</p>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Pending Manager Review</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ pendingComplaints.length }}</p>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Reviewed</p>
                    <p class="text-2xl font-bold text-green-600">{{ reviewedComplaints.length }}</p>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Rejected</p>
                    <p class="text-2xl font-bold text-red-600">{{ rejectedCount }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 space-y-4">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search complaint code, submitter, or reported employee..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                    />
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100 border-b border-gray-200">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Code</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Submitted By</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Reported Employee</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Type</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Incident Date</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Status</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Submitted</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="item in filteredRows" :key="item.id" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-semibold text-gray-900">{{ item.complaintCode }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ item.submittedByName }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ item.reportedEmployeeName || '--' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ item.complaintType }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ formatDate(item.incidentDate) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" :class="statusClass(item.status)">
                                            {{ statusLabel(item.status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ formatDate(item.createdAt) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <button
                                            @click="openDetails(item)"
                                            class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700"
                                        >
                                            View
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="filteredRows.length === 0">
                                    <td colspan="8" class="px-4 py-10 text-center text-gray-500">No complaints found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AuthLayout from '@/views/Layouts/AuthLayout.vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()

const loading = ref(false)
const search = ref('')
const rows = ref([])

const employeeComplaints = computed(() => rows.value.filter((item) => ['submitted', 'rejected'].includes(item.status)))
const pendingComplaints = computed(() => rows.value.filter((item) => item.status === 'pending'))
const reviewedComplaints = computed(() => rows.value.filter((item) => item.status === 'reviewed'))
const rejectedCount = computed(() => rows.value.filter((item) => item.status === 'rejected').length)

const filteredRows = computed(() => {
    const keyword = String(search.value || '').trim().toLowerCase()
    if (!keyword) return employeeComplaints.value

    return employeeComplaints.value.filter((item) => {
        const blob = [
            item.complaintCode,
            item.submittedByName,
            item.reportedEmployeeName,
            item.complaintType,
            item.department
        ].join(' ').toLowerCase()

        return blob.includes(keyword)
    })
})

const fetchComplaints = async () => {
    try {
        loading.value = true
        const response = await axios.get('/api/disciplinary/complaints')
        rows.value = response.data?.data || []
    } catch (error) {
        console.error('Failed to load complaints', error)
        rows.value = []
    } finally {
        loading.value = false
    }
}

const openDetails = (item) => {
    router.push({ name: 'employee_complaints_detail', params: { id: item.id } })
}

const statusClass = (status) => {
    const map = {
        submitted: 'bg-blue-100 text-blue-800',
        pending: 'bg-yellow-100 text-yellow-800',
        reviewed: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800'
    }

    return map[status] || 'bg-gray-100 text-gray-700'
}

const statusLabel = (status) => {
    const map = {
        submitted: 'Submitted',
        pending: 'Pending Manager Review',
        reviewed: 'Reviewed',
        rejected: 'Rejected'
    }

    return map[status] || status
}

const formatDate = (value) => {
    if (!value) return '--'
    return new Date(value).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

onMounted(() => {
    auth.pageTitle = 'Employee Complaints'
    fetchComplaints()
})
</script>
