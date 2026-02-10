<template>
    <auth-layout />
        <div class="ps-60 pe-4 bg-[#F3F4F6] min-h-screen">
            <div class="px-8 py-4">

                <!-- Filters Section -->
                <div class="bg-white rounded-lg p-6 mb-8 border border-gray-200 shadow-sm">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Search & Filters</h3>
                        <div class="flex-1 ms-8">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input 
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search by project name or code..." 
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-5 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Status</label>
                            <select 
                                v-model="filters.status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                            >
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="planning">Planning</option>
                                <option value="completed">Completed</option>
                                <option value="on-hold">On Hold</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Client</label>
                            <select 
                                v-model="filters.client"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                            >
                                <option value="">All Clients</option>
                                <option value="urban-dev">Urban Development Corp</option>
                                <option value="retail-group">Retail Group Ltd</option>
                                <option value="healthcare">Healthcare Services</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Budget Range</label>
                            <select 
                                v-model="filters.budgetRange"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                            >
                                <option value="">All Ranges</option>
                                <option value="0-500">₱0 - ₱500K</option>
                                <option value="500-1000">₱500K - ₱1M</option>
                                <option value="1000">₱1M+</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Bid Type</label>
                            <select 
                                v-model="filters.bidType"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                            >
                                <option value="">All Types</option>
                                <option value="open">Open Bidding</option>
                                <option value="selective">Selective Bidding</option>
                                <option value="negotiated">Negotiated</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Action</label>
                            <button
                                @click="clearFilters"
                                class="w-full px-3 py-2 bg-gray-100 text-gray-700 rounded-md font-medium text-sm hover:bg-gray-200 transition-colors"
                            >
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Projects Table -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                        Project Code
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                        Project Name
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                        Client
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                        Contract Amount
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                        Utilized
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                        Retention
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                        Status
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr
                                    v-for="project in filteredProjects"
                                    :key="project.id"
                                    class="hover:bg-gray-50 transition-colors"
                                >
                                    <td class="px-6 py-4 text-sm font-semibold text-[#0c8ce9]">{{ project.code }}</td>
                                    <td class="px-6 py-4">
                                        <router-link 
                                            :to="`/project-finance/${project.id}`"
                                            class="text-sm font-medium text-gray-900 hover:text-[#0c8ce9] transition-colors"
                                        >
                                            {{ project.name }}
                                        </router-link>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ project.client }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ formatCurrency(project.contractAmount) }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                        {{ formatCurrency(project.utilized) }}
                                        <span class="text-gray-600 font-normal">({{ project.utilizationPercent }}%)</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="font-semibold text-gray-900">{{ formatCurrency(project.retention) }}</span>
                                        <span class="text-gray-600 font-normal text-xs">{{ project.retentionPercent }}%</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span
                                            :class="getStatusBadgeClass(project.status)"
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                                        >
                                            {{ project.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <router-link
                                                :to="{ name: 'project_financials_view', params: { id: project.id } }"
                                                class="text-[#0c8ce9] hover:text-blue-700 font-medium text-xs transition-colors"
                                            >
                                                View
                                            </router-link>
                                            <button
                                                @click="editProject(project.id)"
                                                class="text-gray-500 hover:text-gray-700 font-medium text-xs transition-colors"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                @click="deleteProject(project.id)"
                                                class="text-red-500 hover:text-red-700 font-medium text-xs transition-colors"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="filteredProjects.length === 0" class="px-6 py-8 text-center">
                        <p class="text-gray-500 text-sm">No projects found matching your filters</p>
                    </div>
                </div>
            </div>
        </div>
    
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

const searchQuery = ref('')
const filters = ref({
    status: '',
    client: '',
    budgetRange: '',
    bidType: ''
})

const projects = ref([
    {
        id: 1,
        code: 'MTD-001',
        name: 'Metro Tower Development',
        client: 'Urban Development Corp',
        contractAmount: 850000,
        utilized: 697500,
        utilizationPercent: 82,
        retention: 42500,
        retentionPercent: 5,
        status: 'Active',
    },
    {
        id: 2,
        code: 'SCE-002',
        name: 'Shopping Complex Expansion',
        client: 'Retail Group Ltd',
        contractAmount: 650000,
        utilized: 442000,
        utilizationPercent: 68,
        retention: 32500,
        retentionPercent: 5,
        status: 'Active',
    },
    {
        id: 3,
        code: 'HWR-003',
        name: 'Hospital Wing Renovation',
        client: 'Healthcare Services',
        contractAmount: 520000,
        utilized: 234000,
        utilizationPercent: 45,
        retention: 26000,
        retentionPercent: 5,
        status: 'Planning',
    },
    {
        id: 4,
        code: 'OFC-004',
        name: 'Office Park Phase 2',
        client: 'Corporate Solutions',
        contractAmount: 1200000,
        utilized: 1176000,
        utilizationPercent: 98,
        retention: 60000,
        retentionPercent: 5,
        status: 'Active',
    },
    {
        id: 5,
        code: 'RES-005',
        name: 'Residential Complex A',
        client: 'Housing Development Inc',
        contractAmount: 950000,
        utilized: 0,
        utilizationPercent: 0,
        retention: 0,
        retentionPercent: 0,
        status: 'Completed',
    }
])

const filteredProjects = computed(() => {
    return projects.value.filter(project => {
        const matchesSearch = 
            project.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            project.code.toLowerCase().includes(searchQuery.value.toLowerCase())
        
        const matchesStatus = !filters.value.status || project.status.toLowerCase() === filters.value.status.toLowerCase()
        const matchesClient = !filters.value.client || project.client.toLowerCase().includes(filters.value.client)
        
        return matchesSearch && matchesStatus && matchesClient
    })
})

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value)
}

const getStatusBadgeClass = (status) => {
    const classes = {
        'Active': 'bg-green-100 text-green-800',
        'Planning': 'bg-blue-100 text-blue-800',
        'Completed': 'bg-gray-100 text-gray-800',
        'On Hold': 'bg-yellow-100 text-yellow-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const clearFilters = () => {
    searchQuery.value = ''
    filters.value = {
        status: '',
        client: '',
        budgetRange: '',
        bidType: ''
    }
}

const editProject = (id) => {
    // Route to edit page
}

const deleteProject = (id) => {
    // Delete confirmation
}

onMounted(() => {
    auth.pageTitle = 'Project Finance'
})
</script>

<style scoped></style>
