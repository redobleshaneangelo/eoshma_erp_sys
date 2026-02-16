<template>
    <auth-layout />
        <div class="ps-60 bg-[#F3F4F6] min-h-screen">
            <div class="px-10 py-10">

                    <!-- Tab Navigation -->
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

                    <!-- Filters Section -->
                    <div class="bg-white pt-0 mb-0">
                        <div class="p-6">
                            <div class="grid grid-cols-5 gap-4">
                            <input 
                                v-model="filters.search"
                                type="text"
                                placeholder="Search by employee name or ID..."
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
                                <option value="Sick Leave">Sick Leave</option>
                                <option value="Vacation Leave">Vacation Leave</option>
                                <option value="Emergency Leave">Emergency Leave</option>
                                <option value="Unpaid Leave">Unpaid Leave</option>
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

                    <!-- Leave Requests Table -->
                    <div class="bg-white rounded-b-lg overflow-hidden p-6">
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                            Request ID
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                            Employee Name
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                            Department
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                            Leave Type
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                            Duration
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                            Total Days
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                            Submission Date
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="request in filteredRequests"
                                        :key="request.id"
                                        class="border-b border-gray-200 hover:bg-gray-50 transition-colors"
                                    >
                                        <td class="px-6 py-4 text-sm font-medium text-[#0c8ce9]">{{ request.id }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-[#333333]">{{ request.employeeName }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ request.department }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ request.leaveType }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ formatDate(request.startDate) }} - {{ formatDate(request.endDate) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-[#333333]">{{ request.totalDays }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(request.submissionDate) }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <span
                                                :class="getStatusBadgeClass(request.status)"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            >
                                                <span v-if="request.status === 'pending'" class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5"></span>
                                                <span v-else-if="request.status === 'approved'" class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                                <span v-else-if="request.status === 'rejected'" class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                                                {{ capitalizeStatus(request.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm">
                                            <button
                                                @click="viewRequestDetail(request.id)"
                                                class="text-[#0c8ce9] hover:text-blue-700 font-medium transition-colors"
                                            >
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div v-if="filteredRequests.length === 0" class="px-6 py-12 text-center">
                            <div class="text-gray-400 mb-3">
                                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="text-gray-600 font-medium">No leave requests found</p>
                            <p class="text-sm text-gray-500 mt-1">Try adjusting your filters</p>
                        </div>

                        <!-- Pagination -->
                        <div v-if="filteredRequests.length > 0" class="bg-gray-50 px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <select
                                    v-model="pagination.pageSize"
                                    class="px-3 py-2 border border-gray-300 rounded text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                                >
                                    <option value="10">10 per page</option>
                                    <option value="25">25 per page</option>
                                    <option value="50">50 per page</option>
                                </select>
                                <span class="text-sm text-gray-600">
                                    Showing {{ (pagination.currentPage - 1) * pagination.pageSize + 1 }} to {{ Math.min(pagination.currentPage * pagination.pageSize, filteredRequests.length) }} of {{ filteredRequests.length }}
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

            <!-- Submit Leave Request Modal -->
            <SubmitLeaveRequestModal
                v-if="showSubmitModal"
                @close="showSubmitModal = false"
                @submit="handleSubmitRequest"
            />
    
</template>

<script setup>
    import { ref, computed, onMounted } from 'vue'
    import { useRouter } from 'vue-router'
    import axios from 'axios'
    import { useAuthStore } from '@/stores/auth'
    import SubmitLeaveRequestModal from '@/components/HR/Leave/SubmitLeaveRequestModal.vue'
    import agnes from '@/assets/agnes.gif'
    import Swal from 'sweetalert2'

    const auth = useAuthStore()
    const router = useRouter()

    const loading = ref(false)
    const activeTab = ref('pending')
    const showSubmitModal = ref(false)

    // Tabs configuration
    const tabs = computed(() => [
        {
            id: 'pending',
            label: 'Pending Requests',
            count: leaveRequests.value.filter(r => r.status === 'pending').length,
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

    // Filter state
    const filters = ref({
        search: '',
        department: '',
        leaveType: '',
        startDate: ''
    })

    // Pagination state
    const pagination = ref({
        currentPage: 1,
        pageSize: 10
    })

    // Mock data - Replace with API call
    const leaveRequests = ref([
        {
            id: 'LR-001',
            employeeName: 'John Smith',
            department: 'IT',
            leaveType: 'Sick Leave',
            startDate: '2026-02-05',
            endDate: '2026-02-06',
            totalDays: 2,
            submissionDate: '2026-01-31',
            status: 'pending',
            reason: 'Medical appointment',
            approverNotes: '',
            attachments: []
        },
        {
            id: 'LR-002',
            employeeName: 'Jane Doe',
            department: 'HR',
            leaveType: 'Vacation Leave',
            startDate: '2026-02-10',
            endDate: '2026-02-14',
            totalDays: 5,
            submissionDate: '2026-01-25',
            status: 'approved',
            reason: 'Family trip',
            approverNotes: 'Approved - Coverage arranged',
            approvalDate: '2026-01-26',
            attachments: []
        },
        {
            id: 'LR-003',
            employeeName: 'Bob Johnson',
            department: 'Finance',
            leaveType: 'Emergency Leave',
            startDate: '2026-02-01',
            endDate: '2026-02-01',
            totalDays: 1,
            submissionDate: '2026-01-31',
            status: 'pending',
            reason: 'Family emergency',
            approverNotes: '',
            attachments: []
        },
        {
            id: 'LR-004',
            employeeName: 'Alice Brown',
            department: 'Sales',
            leaveType: 'Vacation Leave',
            startDate: '2026-01-15',
            endDate: '2026-01-17',
            totalDays: 3,
            submissionDate: '2026-01-10',
            status: 'approved',
            reason: 'Vacation',
            approverNotes: 'Approved',
            approvalDate: '2026-01-11',
            attachments: []
        },
        {
            id: 'LR-005',
            employeeName: 'Charlie Wilson',
            department: 'Operations',
            leaveType: 'Unpaid Leave',
            startDate: '2026-02-20',
            endDate: '2026-02-24',
            totalDays: 5,
            submissionDate: '2026-01-28',
            status: 'rejected',
            reason: 'Personal reasons',
            approverNotes: 'Cannot accommodate at this time',
            rejectionDate: '2026-01-29',
            attachments: []
        },
        {
            id: 'LR-006',
            employeeName: 'Diana Garcia',
            department: 'IT',
            leaveType: 'Sick Leave',
            startDate: '2026-02-03',
            endDate: '2026-02-04',
            totalDays: 2,
            submissionDate: '2026-01-30',
            status: 'pending',
            reason: 'Flu symptoms',
            approverNotes: '',
            attachments: []
        },
        {
            id: 'LR-007',
            employeeName: 'Edward Martinez',
            department: 'Finance',
            leaveType: 'Vacation Leave',
            startDate: '2026-01-20',
            endDate: '2026-01-24',
            totalDays: 5,
            submissionDate: '2026-01-05',
            status: 'approved',
            reason: 'Planned vacation',
            approverNotes: 'Approved - All tasks covered',
            approvalDate: '2026-01-06',
            attachments: []
        },
        {
            id: 'LR-008',
            employeeName: 'Fiona Anderson',
            department: 'Sales',
            leaveType: 'Emergency Leave',
            startDate: '2026-01-28',
            endDate: '2026-01-28',
            totalDays: 1,
            submissionDate: '2026-01-28',
            status: 'approved',
            reason: 'Family matter',
            approverNotes: 'Approved',
            approvalDate: '2026-01-28',
            attachments: []
        },
        {
            id: 'LR-009',
            employeeName: 'George Thomas',
            department: 'IT',
            leaveType: 'Unpaid Leave',
            startDate: '2026-03-01',
            endDate: '2026-03-07',
            totalDays: 7,
            submissionDate: '2026-01-20',
            status: 'rejected',
            reason: 'Extended leave request',
            approverNotes: 'Cannot approve overlapping with project deadline',
            rejectionDate: '2026-01-22',
            attachments: []
        },
        {
            id: 'LR-010',
            employeeName: 'Helen White',
            department: 'HR',
            leaveType: 'Sick Leave',
            startDate: '2026-02-08',
            endDate: '2026-02-09',
            totalDays: 2,
            submissionDate: '2026-01-31',
            status: 'pending',
            reason: 'Doctor visit',
            approverNotes: '',
            attachments: []
        }
    ])

    // Computed property for filtered requests
    const filteredRequests = computed(() => {
        let filtered = leaveRequests.value.filter(request => {
            // Filter by tab status
            if (request.status !== activeTab.value) return false

            // Filter by search
            if (filters.value.search) {
                const search = filters.value.search.toLowerCase()
                if (!request.employeeName.toLowerCase().includes(search) && 
                    !request.id.toLowerCase().includes(search)) {
                    return false
                }
            }

            // Filter by department
            if (filters.value.department && request.department !== filters.value.department) {
                return false
            }

            // Filter by leave type
            if (filters.value.leaveType && request.leaveType !== filters.value.leaveType) {
                return false
            }

            // Filter by start date
            if (filters.value.startDate && new Date(request.startDate) < new Date(filters.value.startDate)) {
                return false
            }

            return true
        })

        return filtered
    })

    // Computed property for paginated requests
    const paginatedRequests = computed(() => {
        const start = (pagination.value.currentPage - 1) * pagination.value.pageSize
        const end = start + parseInt(pagination.value.pageSize)
        return filteredRequests.value.slice(start, end)
    })

    // Computed property for total pages
    const totalPages = computed(() => {
        return Math.ceil(filteredRequests.value.length / parseInt(pagination.value.pageSize))
    })

    // Methods
    const formatDate = (date) => {
        return new Date(date).toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        })
    }

    const getStatusBadgeClass = (status) => {
        const classes = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'approved': 'bg-green-100 text-green-800',
            'rejected': 'bg-red-100 text-red-800'
        }
        return classes[status] || 'bg-gray-100 text-gray-800'
    }

    const capitalizeStatus = (status) => {
        return status.charAt(0).toUpperCase() + status.slice(1)
    }

    const openSubmitLeaveModal = () => {
        showSubmitModal.value = true
    }

    const handleSubmitRequest = (formData) => {
        // Here you would normally send this to your API
        const newRequest = {
            id: `LR-${String(leaveRequests.value.length + 1).padStart(3, '0')}`,
            ...formData,
            submissionDate: new Date().toISOString().split('T')[0],
            status: 'pending',
            approverNotes: '',
            attachments: []
        }
        leaveRequests.value.unshift(newRequest)
        showSubmitModal.value = false
        
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Leave request submitted successfully',
            confirmButtonColor: '#0c8ce9'
        })
    }

    const viewRequestDetail = (requestId) => {
        router.push(`/leave-and-absence/${requestId}`)
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
        if (pagination.value.currentPage > 1) {
            pagination.value.currentPage--
        }
    }

    const nextPage = () => {
        if (pagination.value.currentPage < totalPages.value) {
            pagination.value.currentPage++
        }
    }

    onMounted(() => {
        // Load leave requests from API
        // For now using mock data
        auth.pageTitle = 'Leave & Absence'
    })
</script>
