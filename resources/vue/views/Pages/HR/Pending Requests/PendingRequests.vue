<template>
    <!-- Search and Filters -->
    <div class="bg-white rounded-lg p-6 mb-6 border border-gray-200 shadow-sm">
        <div class="grid grid-cols-4 gap-4 mb-4">
            <!-- Search Bar -->
            <div class="col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input 
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by employee name or request ID..." 
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                    />
                </div>
            </div>

            <!-- Request Type Filter -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Request Type</label>
                <select 
                    v-model="filters.type"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                >
                    <option value="">All Types</option>
                    <option value="promotion">Promotion</option>
                    <option value="leave">Leave Request</option>
                    <option value="clearance">Clearance</option>
                    <option value="transfer">Transfer</option>
                    <option value="resignation">Resignation</option>
                </select>
            </div>

            <!-- Priority Filter -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Priority</label>
                <select 
                    v-model="filters.priority"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                >
                    <option value="">All Priority</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>

            <!-- Clear Button -->
            <div class="flex items-end">
                <button
                    @click="clearFilters"
                    class="w-full px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-300 transition-colors"
                >
                    Clear Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Pending Requests Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Request ID</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Employee</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Type</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Priority</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Submitted Date</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="request in filteredRequests" :key="request.id" class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ request.id }}</td>
                        <td class="px-6 py-4 text-gray-700">
                            <div class="font-medium">{{ request.employeeName }}</div>
                            <div class="text-xs text-gray-500">{{ request.employeeId }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="[
                                'inline-block px-3 py-1 rounded-full text-xs font-semibold',
                                request.type === 'promotion' ? 'bg-purple-100 text-purple-800' :
                                request.type === 'leave' ? 'bg-blue-100 text-blue-800' :
                                request.type === 'clearance' ? 'bg-green-100 text-green-800' :
                                request.type === 'transfer' ? 'bg-orange-100 text-orange-800' :
                                'bg-red-100 text-red-800'
                            ]">
                                {{ formatType(request.type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="[
                                'inline-block px-2 py-1 rounded text-xs font-semibold',
                                request.priority === 'high' ? 'bg-red-100 text-red-800' :
                                request.priority === 'medium' ? 'bg-yellow-100 text-yellow-800' :
                                'bg-gray-100 text-gray-800'
                            ]">
                                {{ request.priority }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ formatDate(request.submittedDate) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button
                                    @click="cancelRequest(request)"
                                    class="text-red-600 hover:text-red-700 font-medium text-sm"
                                >
                                    Cancel Request
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div v-if="filteredRequests.length === 0" class="p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">No pending requests</h3>
            <p class="text-gray-600">All requests have been processed or your filters returned no results</p>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
            <span class="text-sm font-semibold text-gray-800">
                Total Requests: <span class="text-[#0c8ce9]">{{ filteredRequests.length }}</span>
            </span>
            <div class="flex items-center gap-2">
                <button
                    @click="previousPage"
                    :disabled="currentPage === 1"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    Previous
                </button>
                <span class="text-sm text-gray-600">Page {{ currentPage }} of {{ totalPages }}</span>
                <button
                    @click="nextPage"
                    :disabled="currentPage === totalPages"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    Next
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed, onMounted } from 'vue'
    import { useRouter } from 'vue-router'
    import Swal from 'sweetalert2'
    import { useAuthStore } from '@/stores/auth'

    const auth = useAuthStore()
    const router = useRouter()
    const searchQuery = ref('')
    const currentPage = ref(1)
    const itemsPerPage = 10

    const filters = ref({
        type: '',
        priority: ''
    })

    const pendingRequests = ref([
        {
            id: 'REQ-001',
            employeeName: 'John Doe',
            employeeId: 'EMP-001',
            type: 'promotion',
            priority: 'high',
            submittedDate: '2026-01-28',
            details: 'Senior Engineer position in Engineering department'
        },
        {
            id: 'REQ-002',
            employeeName: 'Jane Smith',
            employeeId: 'EMP-002',
            type: 'leave',
            priority: 'medium',
            submittedDate: '2026-01-30',
            details: 'Annual leave request for Feb 5-12'
        },
        {
            id: 'REQ-003',
            employeeName: 'Mike Johnson',
            employeeId: 'EMP-003',
            type: 'transfer',
            priority: 'medium',
            submittedDate: '2026-01-27',
            details: 'Transfer from IT to Finance department'
        },
        {
            id: 'REQ-004',
            employeeName: 'Sarah Williams',
            employeeId: 'EMP-004',
            type: 'clearance',
            priority: 'low',
            submittedDate: '2026-01-29',
            details: 'Exit clearance process initiated'
        },
        {
            id: 'REQ-005',
            employeeName: 'Robert Brown',
            employeeId: 'EMP-005',
            type: 'promotion',
            priority: 'high',
            submittedDate: '2026-01-31',
            details: 'Manager position in Sales department'
        },
        {
            id: 'REQ-006',
            employeeName: 'Emily Davis',
            employeeId: 'EMP-006',
            type: 'resignation',
            priority: 'high',
            submittedDate: '2026-02-01',
            details: 'Resignation letter submitted'
        },
        {
            id: 'REQ-007',
            employeeName: 'David Garcia',
            employeeId: 'EMP-007',
            type: 'leave',
            priority: 'low',
            submittedDate: '2026-01-26',
            details: 'Personal leave request for 3 days'
        },
        {
            id: 'REQ-008',
            employeeName: 'Lisa Anderson',
            employeeId: 'EMP-008',
            type: 'transfer',
            priority: 'medium',
            submittedDate: '2026-02-01',
            details: 'Lateral move to Operations department'
        }
    ])

    const filteredRequests = computed(() => {
        return pendingRequests.value.filter(req => {
            const matchSearch = !searchQuery.value || 
                req.employeeName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                req.employeeId.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                req.id.toLowerCase().includes(searchQuery.value.toLowerCase())

            const matchType = !filters.value.type || req.type === filters.value.type
            const matchPriority = !filters.value.priority || req.priority === filters.value.priority

            return matchSearch && matchType && matchPriority
        })
    })

    const totalPages = computed(() => {
        return Math.ceil(filteredRequests.value.length / itemsPerPage)
    })

    const paginatedRequests = computed(() => {
        const start = (currentPage.value - 1) * itemsPerPage
        const end = start + itemsPerPage
        return filteredRequests.value.slice(start, end)
    })

    const formatType = (type) => {
        const typeMap = {
            'promotion': 'Promotion',
            'leave': 'Leave Request',
            'clearance': 'Clearance',
            'transfer': 'Transfer',
            'resignation': 'Resignation'
        }
        return typeMap[type] || type
    }

    const formatDate = (date) => {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        })
    }

    const viewRequest = (request) => {
        Swal.fire({
            title: request.id,
            html: `<div class="text-left">
                <p><strong>Employee:</strong> ${request.employeeName} (${request.employeeId})</p>
                <p><strong>Type:</strong> ${formatType(request.type)}</p>
                <p><strong>Priority:</strong> ${request.priority}</p>
                <p><strong>Submitted:</strong> ${formatDate(request.submittedDate)}</p>
                <p><strong>Details:</strong> ${request.details}</p>
            </div>`,
            icon: 'info',
            confirmButtonColor: '#0c8ce9'
        })
    }

    const cancelRequest = async (request) => {
        const { isConfirmed } = await Swal.fire({
            title: 'Cancel Request?',
            text: `Are you sure you want to cancel this ${formatType(request.type).toLowerCase()}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Cancel Request'
        })

        if (isConfirmed) {
            // API: PUT /api/requests/{requestId}/cancel
            const index = pendingRequests.value.findIndex(r => r.id === request.id)
            if (index !== -1) {
                pendingRequests.value.splice(index, 1)
            }

            Swal.fire({
                icon: 'success',
                title: 'Cancelled',
                text: 'Your request has been cancelled',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
        }
    }

    const clearFilters = () => {
        searchQuery.value = ''
        filters.value = {
            type: '',
            priority: ''
        }
        currentPage.value = 1
    }

    const previousPage = () => {
        if (currentPage.value > 1) {
            currentPage.value--
        }
    }

    const nextPage = () => {
        if (currentPage.value < totalPages.value) {
            currentPage.value++
        }
    }

    onMounted(() => {
        // Set page title
        auth.pageTitle = 'Pending Requests'
    })
</script>
