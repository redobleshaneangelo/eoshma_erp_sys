<template>
    <!-- Filters Section -->
    <div class="bg-white rounded-lg p-6 mb-6 border border-gray-200 shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-gray-900">Search & Filters</h3>
            <button 
                v-if="hasActiveFilters"
                @click="clearFilters"
                class="text-sm text-[#0c8ce9] hover:underline font-semibold"
            >
                Clear All
            </button>
        </div>
        <div class="grid grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Project Name</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input 
                        v-model="filters.search"
                        type="text"
                        placeholder="Search by project..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                    />
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Type</label>
                <select 
                    v-model="filters.type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                >
                    <option value="">All Types</option>
                    <option value="prebidding">Pre-Bidding</option>
                    <option value="project-setup">Project Setup</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Client</label>
                <select 
                    v-model="filters.client"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                >
                    <option value="">All Clients</option>
                    <option value="acme">Acme Corporation</option>
                    <option value="buildtech">BuildTech Solutions</option>
                    <option value="abc">ABC Corporation</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Items per Page</label>
                <select 
                    v-model.number="pageSize"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                >
                    <option :value="10">10</option>
                    <option :value="25">25</option>
                    <option :value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-600 uppercase">Total Pending</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ pendingApprovals.length }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-[#0c8ce9]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-600 uppercase">Pre-Biddings</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ prebiddingsCount }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-600 uppercase">Project Setups</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ projectSetupsCount }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-600 uppercase">Avg. Processing Time</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">2.5 days</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Project Name</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Submitted Date</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Budget</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr 
                        v-for="approval in paginatedApprovals" 
                        :key="approval.id"
                        class="hover:bg-blue-50 transition-all duration-200"
                    >
                        <td class="px-6 py-4 text-sm">
                            <div class="font-semibold text-gray-900">{{ approval.project_name }}</div>
                            <div class="text-xs text-gray-500">{{ approval.project_code }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" :class="typeStyles[approval.type]">
                                {{ formatType(approval.type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ approval.client_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(approval.submitted_date) }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">₱{{ formatCurrency(approval.budget) }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                Pending Review
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button 
                                @click="viewApproval(approval.id, approval.type)"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700 transition-all"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Review
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Showing <span class="font-semibold">{{ startIndex + 1 }}</span> to <span class="font-semibold">{{ Math.min(endIndex, filteredApprovals.length) }}</span> of <span class="font-semibold">{{ filteredApprovals.length }}</span> items
            </div>
            <div class="flex gap-2">
                <button 
                    @click="currentPage--" 
                    :disabled="currentPage === 1"
                    class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                >
                    Previous
                </button>
                <div class="flex items-center gap-1">
                    <button 
                        v-for="page in visiblePages" 
                        :key="page"
                        @click="currentPage = page"
                        :class="currentPage === page ? 'bg-[#0c8ce9] text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'"
                        class="px-3 py-2 rounded-lg text-sm font-semibold transition-all"
                    >
                        {{ page }}
                    </button>
                </div>
                <button 
                    @click="currentPage++" 
                    :disabled="currentPage === totalPages"
                    class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                >
                    Next
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// Filters
const filters = ref({
    search: '',
    type: '',
    client: ''
})

const currentPage = ref(1)
const pageSize = ref(10)

// Type Styles
const typeStyles = {
    'prebidding': 'bg-purple-100 text-purple-800',
    'project-setup': 'bg-green-100 text-green-800'
}

// Mock data - API ready structure
const pendingApprovals = ref([
    {
        id: 1,
        project_name: 'Office Building Renovation',
        project_code: 'PRJ-2026-001',
        client_name: 'ABC Corporation',
        type: 'project-setup',
        submitted_date: '2026-02-01',
        budget: 2500000,
        status: 'pending'
    },
    {
        id: 2,
        project_name: 'Infrastructure Development',
        project_code: 'PRJ-2026-002',
        client_name: 'BuildTech Solutions',
        type: 'prebidding',
        submitted_date: '2026-02-03',
        budget: 5000000,
        status: 'pending'
    },
    {
        id: 3,
        project_name: 'Commercial Complex Construction',
        project_code: 'PRJ-2026-003',
        client_name: 'Acme Corporation',
        type: 'prebidding',
        submitted_date: '2026-02-04',
        budget: 8500000,
        status: 'pending'
    },
    {
        id: 4,
        project_name: 'Road Network Upgrade',
        project_code: 'PRJ-2026-004',
        client_name: 'ABC Corporation',
        type: 'project-setup',
        submitted_date: '2026-02-05',
        budget: 3200000,
        status: 'pending'
    },
    {
        id: 5,
        project_name: 'Hospital Expansion',
        project_code: 'PRJ-2026-005',
        client_name: 'BuildTech Solutions',
        type: 'prebidding',
        submitted_date: '2026-02-05',
        budget: 12000000,
        status: 'pending'
    }
])

// Computed Properties
const filteredApprovals = computed(() => {
    return pendingApprovals.value.filter(approval => {
        const matchesSearch = approval.project_name.toLowerCase().includes(filters.value.search.toLowerCase()) ||
                             approval.project_code.toLowerCase().includes(filters.value.search.toLowerCase())
        const matchesType = !filters.value.type || approval.type === filters.value.type
        const matchesClient = !filters.value.client || approval.client_name.toLowerCase().includes(filters.value.client.toLowerCase())

        return matchesSearch && matchesType && matchesClient
    })
})

const hasActiveFilters = computed(() => {
    return filters.value.search || filters.value.type || filters.value.client
})

const prebiddingsCount = computed(() => {
    return pendingApprovals.value.filter(a => a.type === 'prebidding').length
})

const projectSetupsCount = computed(() => {
    return pendingApprovals.value.filter(a => a.type === 'project-setup').length
})

const totalPages = computed(() => Math.ceil(filteredApprovals.value.length / pageSize.value))

const startIndex = computed(() => (currentPage.value - 1) * pageSize.value)
const endIndex = computed(() => startIndex.value + pageSize.value)

const paginatedApprovals = computed(() => {
    return filteredApprovals.value.slice(startIndex.value, endIndex.value)
})

const visiblePages = computed(() => {
    const pages = []
    const maxVisible = 5
    let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
    let end = Math.min(totalPages.value, start + maxVisible - 1)
    if (end - start + 1 < maxVisible) {
        start = Math.max(1, end - maxVisible + 1)
    }
    for (let i = start; i <= end; i++) {
        pages.push(i)
    }
    return pages
})

// Methods
const clearFilters = () => {
    filters.value = {
        search: '',
        type: '',
        client: ''
    }
    currentPage.value = 1
}

const formatType = (type) => {
    const map = {
        'prebidding': 'Pre-Bidding',
        'project-setup': 'Project Setup'
    }
    return map[type] || type
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatCurrency = (value) => {
    return parseFloat(value).toLocaleString('en-PH', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

const viewApproval = (id, type) => {
    router.push(`/admin/pending-approvals/${id}?type=${type}`)
}
</script>
