<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">
            <div class="mb-6 flex justify-end">
                <button
                    @click="router.push({ name: 'leave_configuration' })"
                    class="px-6 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition-colors"
                >
                    Leave & Absence Configuration
                </button>
            </div>

            <div class="bg-white border-b border-gray-200 rounded-t-lg mb-0">
                <nav class="flex gap-0 px-6 py-0 overflow-x-auto">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        class="flex items-center gap-2 px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap"
                        :class="activeTab === tab.id
                            ? 'border-[#0c8ce9] text-[#0c8ce9]'
                            : 'border-transparent text-gray-600 hover:text-gray-900'"
                    >
                        <span v-html="tab.icon"></span>
                        {{ tab.label }}
                        <span v-if="tab.count" class="bg-gray-200 text-gray-700 px-2 py-0 rounded-full text-xs font-semibold">
                            {{ tab.count }}
                        </span>
                    </button>
                </nav>
            </div>

            <div class="bg-white pt-0 mb-0">
                <div class="p-6">
                    <div class="grid grid-cols-5 gap-4">
                        <input
                            v-model="filters.search"
                            type="text"
                            placeholder="Search by employee name or request code..."
                            class="px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                        <select
                            v-model="filters.department"
                            class="px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="">All Departments</option>
                            <option value="IT">IT</option>
                            <option value="HR">HR</option>
                            <option value="Finance">Finance</option>
                            <option value="Sales">Sales</option>
                            <option value="Operations">Operations</option>
                        </select>
                        <select
                            v-model="filters.leaveType"
                            class="px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="">All Leave Types</option>
                            <option v-for="type in leaveTypeOptions" :key="type" :value="type">{{ type }}</option>
                        </select>
                        <input
                            v-model="filters.startDate"
                            type="date"
                            class="px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                        <button
                            @click="clearFilters"
                            class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-200 transition-colors"
                        >
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-b-lg overflow-hidden p-6">
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Request ID</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Employee Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Leave Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Total Days</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Submission Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="request in paginatedRequests"
                                :key="request.id"
                                class="border-b border-gray-200 hover:bg-gray-50 transition-colors"
                            >
                                <td class="px-6 py-4 text-sm font-medium text-[#0c8ce9]">{{ request.requestCode }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-[#333333]">{{ request.employeeName }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ request.department }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ request.leaveType }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(request.startDate) }} - {{ formatDate(request.endDate) }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-[#333333]">{{ request.totalDays }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(request.submissionDate) }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span :class="getStatusBadgeClass(request.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        <span v-if="request.status === 'pending'" class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5"></span>
                                        <span v-else-if="request.status === 'approved'" class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                        <span v-else-if="request.status === 'rejected'" class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                                        <span v-else-if="request.status === 'info_requested'" class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1.5"></span>
                                        {{ capitalizeStatus(request.status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <button @click="viewRequestDetail(request.id)" class="text-[#0c8ce9] hover:text-blue-700 font-medium transition-colors">View</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="filteredRequests.length === 0" class="px-6 py-12 text-center">
                    <div class="text-gray-400 mb-3">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-gray-600 font-medium">No leave requests found</p>
                    <p class="text-sm text-gray-500 mt-1">Try adjusting your filters</p>
                </div>

                <div v-if="filteredRequests.length > 0" class="bg-gray-50 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <select v-model="pagination.pageSize" class="px-3 py-2 border border-gray-300 rounded text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]">
                            <option value="10">10 per page</option>
                            <option value="25">25 per page</option>
                            <option value="50">50 per page</option>
                        </select>
                        <span class="text-sm text-gray-600">
                            Showing {{ (pagination.currentPage - 1) * Number(pagination.pageSize) + 1 }} to {{ Math.min(pagination.currentPage * Number(pagination.pageSize), filteredRequests.length) }} of {{ filteredRequests.length }}
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <button
                            @click="previousPage"
                            :disabled="pagination.currentPage === 1"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            Previous
                        </button>
                        <div class="flex gap-1">
                            <button
                                v-for="page in totalPages"
                                :key="page"
                                @click="pagination.currentPage = page"
                                :class="pagination.currentPage === page ? 'bg-[#0c8ce9] text-white' : 'border border-gray-300 hover:bg-gray-100'"
                                class="px-3 py-2 rounded text-sm font-medium transition-colors"
                            >
                                {{ page }}
                            </button>
                        </div>
                        <button
                            @click="nextPage"
                            :disabled="pagination.currentPage === totalPages"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="fixed bottom-0 right-0 p-2 z-50">
        <img :src="agnes" class="h-auto w-auto" v-if="loading" />
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import agnes from '@/assets/agnes.gif'

const auth = useAuthStore()
const router = useRouter()

const loading = ref(false)
const activeTab = ref('pending')
const leaveRequests = ref([])
const leaveTypeOptions = ref([])

const tabs = computed(() => [
    {
        id: 'pending',
        label: 'Pending Requests',
        count: leaveRequests.value.filter(r => ['pending', 'info_requested'].includes(r.status)).length,
        icon: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>'
    },
    {
        id: 'approved',
        label: 'Approved',
        count: leaveRequests.value.filter(r => r.status === 'approved').length,
        icon: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>'
    },
    {
        id: 'rejected',
        label: 'Rejected',
        count: leaveRequests.value.filter(r => r.status === 'rejected').length,
        icon: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg>'
    }
])

const filters = ref({
    search: '',
    department: '',
    leaveType: '',
    startDate: ''
})

const pagination = ref({
    currentPage: 1,
    pageSize: 10
})

const mapLeaveRequest = (payload) => ({
    id: payload.id,
    requestCode: payload.requestCode || payload.request_code || `LR-${payload.id}`,
    employeeName: payload.employeeName || payload.employee_name || '',
    department: payload.department || '',
    leaveType: payload.leaveType || payload.leave_type || '',
    startDate: payload.startDate || payload.start_date || '',
    endDate: payload.endDate || payload.end_date || '',
    totalDays: payload.totalDays || payload.total_days || 0,
    submissionDate: payload.submissionDate || payload.submission_date || payload.created_at || '',
    status: payload.status || 'pending',
})

const fetchLeaveRequests = async () => {
    loading.value = true
    try {
        const response = await axios.get('/api/leave-requests')
        const rows = response.data?.data || []
        leaveRequests.value = rows.map(mapLeaveRequest)
    } catch (error) {
        console.error('Failed to load leave requests', error)
        leaveRequests.value = []
    } finally {
        loading.value = false
    }
}

const fetchLeaveTypes = async () => {
    try {
        const response = await axios.get('/api/leave-types', {
            params: { per_page: 200, status: 'active' }
        })

        leaveTypeOptions.value = (response.data?.data || []).map((item) => item.name)
    } catch (error) {
        console.error('Failed to load leave types', error)
        leaveTypeOptions.value = []
    }
}

const filteredRequests = computed(() => {
    return leaveRequests.value.filter(request => {
        if (activeTab.value === 'pending') {
            if (!['pending', 'info_requested'].includes(request.status)) return false
        } else if (request.status !== activeTab.value) {
            return false
        }

        if (filters.value.search) {
            const search = filters.value.search.toLowerCase()
            if (
                !request.employeeName.toLowerCase().includes(search)
                && !String(request.requestCode || '').toLowerCase().includes(search)
            ) {
                return false
            }
        }

        if (filters.value.department && request.department !== filters.value.department) return false
        if (filters.value.leaveType && request.leaveType !== filters.value.leaveType) return false
        if (filters.value.startDate && new Date(request.startDate) < new Date(filters.value.startDate)) return false

        return true
    })
})

const paginatedRequests = computed(() => {
    const start = (pagination.value.currentPage - 1) * Number(pagination.value.pageSize)
    const end = start + Number(pagination.value.pageSize)
    return filteredRequests.value.slice(start, end)
})

const totalPages = computed(() => {
    return Math.max(1, Math.ceil(filteredRequests.value.length / Number(pagination.value.pageSize)))
})

const formatDate = (date) => {
    if (!date) return '--'
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

const getStatusBadgeClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
        info_requested: 'bg-blue-100 text-blue-800',
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const capitalizeStatus = (status) => {
    if (status === 'info_requested') return 'Info Requested'
    return status.charAt(0).toUpperCase() + status.slice(1)
}

const viewRequestDetail = (requestId) => {
    router.push({ name: 'leave_request_detail', params: { id: requestId } })
}

const clearFilters = () => {
    filters.value = {
        search: '',
        department: '',
        leaveType: '',
        startDate: ''
    }
    pagination.value.currentPage = 1
}

const previousPage = () => {
    if (pagination.value.currentPage > 1) pagination.value.currentPage--
}

const nextPage = () => {
    if (pagination.value.currentPage < totalPages.value) pagination.value.currentPage++
}

onMounted(() => {
    auth.pageTitle = 'Leave & Absence'
    fetchLeaveTypes()
    fetchLeaveRequests()
})
</script>
