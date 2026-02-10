<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">

            <!-- Stats Cards -->
            <div class="grid grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide mb-1">Total Requests</p>
                            <p class="text-2xl font-bold text-gray-900">{{ materialRequests.length }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-[#0c8ce9]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide mb-1">Pending</p>
                            <p class="text-2xl font-bold text-amber-600">{{ pendingCount }}</p>
                        </div>
                        <div class="bg-amber-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide mb-1">Urgent</p>
                            <p class="text-2xl font-bold text-red-600">{{ urgentCount }}</p>
                        </div>
                        <div class="bg-red-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide mb-1">Processed</p>
                            <p class="text-2xl font-bold text-green-600">{{ processedCount }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-white rounded-lg p-6 mb-6 border border-gray-200 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Filters & Search</h3>
                    <button 
                        v-if="hasActiveFilters"
                        @click="clearFilters"
                        class="text-sm text-[#0c8ce9] hover:underline font-semibold"
                    >
                        Clear All
                    </button>
                </div>
                <div class="grid grid-cols-5 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Search Material</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input 
                                v-model="filters.search"
                                type="text"
                                placeholder="Search by material..." 
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Status</label>
                        <select 
                            v-model="filters.status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Priority</label>
                        <select 
                            v-model="filters.priority"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="">All Priorities</option>
                            <option value="Urgent">Urgent</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Project</label>
                        <select 
                            v-model="filters.project"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="">All Projects</option>
                            <option value="PRJ-2026-001">Office Building Renovation</option>
                            <option value="PRJ-2026-002">Infrastructure Development</option>
                            <option value="PRJ-2026-003">Warehouse Automation</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Sort By</label>
                        <select 
                            v-model="filters.sortBy"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="delivery">Delivery Date (Nearest)</option>
                            <option value="priority">Priority</option>
                            <option value="created">Recently Created</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Material Request Cards -->
            <div class="space-y-4">
                <div 
                    v-for="request in filteredRequests" 
                    :key="request.id"
                    class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-200 overflow-hidden"
                >
                    <!-- Card Header -->
                    <div class="bg-linear-to-r from-[#0c8ce9] to-blue-600 px-6 py-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-white mb-1">{{ request.materialName }}</h3>
                                <p class="text-xs text-blue-100 font-semibold">{{ request.projectCode }} • Req ID: {{ request.id }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span 
                                    :class="[
                                        'px-3 py-1 rounded-full text-xs font-bold',
                                        priorityStyles[request.priority]
                                    ]"
                                >
                                    {{ request.priority }}
                                </span>
                                <span 
                                    :class="[
                                        'px-3 py-1 rounded-full text-xs font-bold',
                                        statusStyles[request.status]
                                    ]"
                                >
                                    {{ formatStatus(request.status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="px-6 py-4">
                        <!-- Material & Project Info -->
                        <div class="flex items-center gap-2 mb-4">
                            <span class="inline-block w-2 h-2 rounded-full bg-[#0c8ce9]"></span>
                            <p class="text-sm text-gray-600"><span class="font-semibold text-gray-900">{{ request.projectCode }}</span> • {{ request.projectName }}</p>
                        </div>

                        <!-- Delivery Deadline -->
                        <div class="mb-4">
                            <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide mb-1">Delivery Required</p>
                            <p :class="[
                                'text-lg font-bold',
                                isDeliveryUrgent(request.deliveryDate) ? 'text-red-600' : 'text-gray-900'
                            ]">
                                {{ formatDate(request.deliveryDate) }}
                            </p>
                            <p v-if="isDeliveryUrgent(request.deliveryDate)" class="text-xs text-red-600 mt-1">⚠️ Urgent - Within 7 days</p>
                        </div>

                        <!-- Source & Location -->
                        <div class="mb-4">
                            <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide mb-2">Details</p>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <p class="text-xs text-gray-600">Source</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ request.source }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Delivery Location</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ request.deliveryLocation }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Quantity</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ request.requestedQuantity }} {{ request.unit }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Special Instructions -->
                        <div v-if="request.instructions" class="mb-4">
                            <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide mb-2">Special Instructions</p>
                            <p class="text-sm text-gray-700">{{ request.instructions }}</p>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <button 
                            @click="viewRequestDetails(request.id)"
                            class="w-full px-4 py-2 bg-[#0c8ce9] text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-all"
                        >
                            View Details
                        </button>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="filteredRequests.length === 0" class="bg-white rounded-lg border border-gray-200 shadow-sm p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10" />
                    </svg>
                    <p class="text-lg font-semibold text-gray-900 mb-2">No Material Requests</p>
                    <p class="text-sm text-gray-600">No requests match your filters. Try adjusting your search criteria.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

// Filters
const filters = ref({
    search: '',
    status: '',
    priority: '',
    project: '',
    sortBy: 'delivery'
})

// Priority Styles
const priorityStyles = {
    'Urgent': 'bg-red-100 text-red-800 border border-red-300',
    'High': 'bg-orange-100 text-orange-800 border border-orange-300',
    'Medium': 'bg-yellow-100 text-yellow-800 border border-yellow-300',
    'Low': 'bg-blue-100 text-blue-800 border border-blue-300'
}

// Status Styles
const statusStyles = {
    'pending': 'bg-yellow-100 text-yellow-800 border border-yellow-300',
    'processing': 'bg-blue-100 text-blue-800 border border-blue-300',
    'completed': 'bg-green-100 text-green-800 border border-green-300',
    'rejected': 'bg-red-100 text-red-800 border border-red-300'
}

// Mock Data - Material Requests from CRM
const materialRequests = ref([
    {
        id: 1,
        materialName: 'Cement',
        unit: 'Bags',
        requestedQuantity: 500,
        source: 'Local Supplier',
        deliveryDate: '2026-02-15',
        priority: 'Urgent',
        deliveryLocation: 'Site Office',
        projectName: 'Office Building Renovation',
        projectCode: 'PRJ-2026-001',
        requestedDate: '2026-02-05',
        instructions: 'Please ensure delivery before 10 AM. Quality certification required.',
        status: 'pending'
    },
    {
        id: 2,
        materialName: 'Steel Bars',
        unit: 'Pcs',
        requestedQuantity: 200,
        source: 'Steel Mills Inc',
        deliveryDate: '2026-02-18',
        priority: 'High',
        deliveryLocation: 'Site Office',
        projectName: 'Infrastructure Development',
        projectCode: 'PRJ-2026-002',
        requestedDate: '2026-02-04',
        instructions: 'Grade A steel bars as per specifications. Include test certificates.',
        status: 'processing'
    },
    {
        id: 3,
        materialName: 'Sand',
        unit: 'm³',
        requestedQuantity: 100,
        source: 'Quarry A',
        deliveryDate: '2026-02-20',
        priority: 'Medium',
        deliveryLocation: 'Warehouse A',
        projectName: 'Office Building Renovation',
        projectCode: 'PRJ-2026-001',
        requestedDate: '2026-02-05',
        instructions: 'Medium grade sand. Free from clay and other impurities.',
        status: 'pending'
    },
    {
        id: 4,
        materialName: 'Gravel',
        unit: 'm³',
        requestedQuantity: 80,
        source: 'Quarry B',
        deliveryDate: '2026-02-25',
        priority: 'Low',
        deliveryLocation: 'Site Office',
        projectName: 'Warehouse Automation',
        projectCode: 'PRJ-2026-003',
        requestedDate: '2026-02-03',
        instructions: 'Standard gravel mix for foundation work.',
        status: 'completed'
    },
    {
        id: 5,
        materialName: 'Plywood Sheets',
        unit: 'Pcs',
        requestedQuantity: 150,
        source: 'Timber Supplies Co',
        deliveryDate: '2026-02-12',
        priority: 'Urgent',
        deliveryLocation: 'Site Office',
        projectName: 'Office Building Renovation',
        projectCode: 'PRJ-2026-001',
        requestedDate: '2026-02-05',
        instructions: 'Marine grade plywood. Stack properly to avoid moisture damage.',
        status: 'pending'
    }
])

// Computed Properties
const filteredRequests = computed(() => {
    let results = materialRequests.value.filter(req => {
        const matchesSearch = req.materialName.toLowerCase().includes(filters.value.search.toLowerCase()) ||
                             req.projectCode.toLowerCase().includes(filters.value.search.toLowerCase())
        const matchesStatus = !filters.value.status || req.status === filters.value.status
        const matchesPriority = !filters.value.priority || req.priority === filters.value.priority
        const matchesProject = !filters.value.project || req.projectCode === filters.value.project

        return matchesSearch && matchesStatus && matchesPriority && matchesProject
    })

    // Sort
    if (filters.value.sortBy === 'delivery') {
        results.sort((a, b) => new Date(a.deliveryDate) - new Date(b.deliveryDate))
    } else if (filters.value.sortBy === 'priority') {
        const priorityOrder = { 'Urgent': 0, 'High': 1, 'Medium': 2, 'Low': 3 }
        results.sort((a, b) => priorityOrder[a.priority] - priorityOrder[b.priority])
    } else if (filters.value.sortBy === 'created') {
        results.sort((a, b) => new Date(b.requestedDate) - new Date(a.requestedDate))
    }

    return results
})

const hasActiveFilters = computed(() => {
    return filters.value.search || filters.value.status || filters.value.priority || filters.value.project
})

const pendingCount = computed(() => {
    return materialRequests.value.filter(req => req.status === 'pending').length
})

const urgentCount = computed(() => {
    return materialRequests.value.filter(req => req.priority === 'Urgent').length
})

const processedCount = computed(() => {
    return materialRequests.value.filter(req => req.status === 'completed').length
})

// Methods
const clearFilters = () => {
    filters.value = {
        search: '',
        status: '',
        priority: '',
        project: '',
        sortBy: 'delivery'
    }
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatStatus = (status) => {
    const map = {
        'pending': 'Pending',
        'processing': 'Processing',
        'completed': 'Completed',
        'rejected': 'Rejected'
    }
    return map[status] || status
}

const isDeliveryUrgent = (deliveryDate) => {
    const today = new Date()
    const delivery = new Date(deliveryDate)
    const diffTime = delivery - today
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays <= 7
}

const viewRequestDetails = (id) => {
    router.push(`/material-requests/${id}`)
}

onMounted(() => {
    auth.pageTitle = 'Material Requests'
})
</script>
