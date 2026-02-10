<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">

            <!-- Filters Section -->
            <div class="bg-white rounded-lg p-6 mb-6 border border-gray-200 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Search & Filters</h3>
                    <div class="flex items-center gap-2">
                        <router-link 
                            to="/clients/create"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-all"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            New Client Record
                        </router-link>
                        <button 
                            v-if="hasActiveFilters"
                            @click="clearFilters"
                            class="text-sm text-[#0c8ce9] hover:underline font-semibold"
                        >
                            Clear All
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-5 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Client Name</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input 
                                v-model="filters.search"
                                type="text"
                                placeholder="Search by name, trade name, or TIN..." 
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
                            <option value="Prospect">Prospect</option>
                            <option value="Active">Active</option>
                            <option value="On Hold">On Hold</option>
                            <option value="Archived">Archived</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Client Type</label>
                        <select 
                            v-model="filters.clientType"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="">All Types</option>
                            <option value="Private">Private</option>
                            <option value="Corporate">Corporate</option>
                            <option value="Government">Government</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">CRM Owner</label>
                        <select 
                            v-model="filters.crmOwner"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="">All Owners</option>
                            <option value="John Doe">John Doe</option>
                            <option value="Jane Smith">Jane Smith</option>
                            <option value="Unassigned">Unassigned</option>
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
                            <option :value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Client Name</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Active Projects</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Outstanding Balance</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">CRM Owner</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr 
                                v-for="client in paginatedClients" 
                                :key="client.id"
                                class="hover:bg-blue-50 transition-all duration-200"
                            >
                                <td class="px-6 py-4 text-sm">
                                    <div class="font-semibold text-gray-900">{{ client.legal_name }}</div>
                                    <div class="text-xs text-gray-500">{{ client.trade_name }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold" :class="clientTypeStyles[client.type]">
                                        {{ client.type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" :class="clientStatusStyles[client.status]">
                                        {{ client.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ client.active_contracts }}</td>
                                <td class="px-6 py-4 text-sm font-semibold" :class="client.outstanding_balance > 0 ? 'text-red-600' : 'text-green-600'">
                                    ₱{{ formatCurrency(client.outstanding_balance) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ client.crm_owner || 'Unassigned' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <router-link 
                                            :to="{ name: 'view_client', params: { id: client.id } }"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold text-[#0c8ce9] hover:bg-blue-100 transition-all"
                                            title="View Details"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </router-link>
                                        <button 
                                            @click="logActivity(client.id)"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-all"
                                            title="Log Activity"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </button>
                                        <button 
                                            @click="openContacts(client.id)"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-all"
                                            title="View Contacts"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 8.646 4 4 0 010-8.646M9 9H3v10a6 6 0 006 6h6a6 6 0 006-6V9h-6a4 4 0 00-4-4z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Showing <span class="font-semibold">{{ startIndex + 1 }}</span> to <span class="font-semibold">{{ Math.min(endIndex, filteredClients.length) }}</span> of <span class="font-semibold">{{ filteredClients.length }}</span> clients
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
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()

// Mock data
const clients = ref([
    {
        id: 1,
        legal_name: 'Acme Corporation',
        trade_name: 'Acme Corp',
        type: 'Corporate',
        status: 'Active',
        active_contracts: 5,
        outstanding_balance: 250000,
        last_activity_desc: 'Proposal submitted for Project X',
        last_activity_time: '2 days ago',
        crm_owner: 'John Doe'
    },
    {
        id: 2,
        legal_name: 'BuildTech Solutions',
        trade_name: 'BuildTech',
        type: 'Private',
        status: 'Active',
        active_contracts: 3,
        outstanding_balance: 125000,
        last_activity_desc: 'Contract renewal signed',
        last_activity_time: '5 days ago',
        crm_owner: 'Jane Smith'
    },
    {
        id: 3,
        legal_name: 'City Infrastructure Development',
        trade_name: 'CID',
        type: 'Government',
        status: 'Active',
        active_contracts: 8,
        outstanding_balance: 500000,
        last_activity_desc: 'Monthly compliance check',
        last_activity_time: '1 day ago',
        crm_owner: 'John Doe'
    },
    {
        id: 4,
        legal_name: 'ProDev Builders',
        trade_name: 'ProDev',
        type: 'Corporate',
        status: 'Prospect',
        active_contracts: 0,
        outstanding_balance: 0,
        last_activity_desc: 'Initial inquiry received',
        last_activity_time: '10 days ago',
        crm_owner: null
    },
])

const filters = ref({
    search: '',
    status: '',
    clientType: '',
    crmOwner: ''
})

const currentPage = ref(1)
const pageSize = ref(10)

// Styles
const clientStatusStyles = {
    'Active': 'bg-green-100 text-green-800',
    'Prospect': 'bg-blue-100 text-blue-800',
    'On Hold': 'bg-yellow-100 text-yellow-800',
    'Archived': 'bg-gray-100 text-gray-800'
}

const clientTypeStyles = {
    'Private': 'bg-purple-100 text-purple-800',
    'Corporate': 'bg-indigo-100 text-indigo-800',
    'Government': 'bg-orange-100 text-orange-800'
}

// Computed properties
const filteredClients = computed(() => {
    return clients.value.filter(client => {
        const matchesSearch = client.legal_name.toLowerCase().includes(filters.value.search.toLowerCase()) ||
                             client.trade_name.toLowerCase().includes(filters.value.search.toLowerCase())
        const matchesStatus = !filters.value.status || client.status === filters.value.status
        const matchesType = !filters.value.clientType || client.type === filters.value.clientType
        const matchesOwner = !filters.value.crmOwner || client.crm_owner === filters.value.crmOwner || (filters.value.crmOwner === 'Unassigned' && !client.crm_owner)

        return matchesSearch && matchesStatus && matchesType && matchesOwner
    })
})

const hasActiveFilters = computed(() => {
    return filters.value.search || filters.value.status || filters.value.clientType || filters.value.crmOwner
})

const totalPages = computed(() => Math.ceil(filteredClients.value.length / pageSize.value))

const startIndex = computed(() => (currentPage.value - 1) * pageSize.value)
const endIndex = computed(() => startIndex.value + pageSize.value)

const paginatedClients = computed(() => {
    return filteredClients.value.slice(startIndex.value, endIndex.value)
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
        status: '',
        clientType: '',
        crmOwner: ''
    }
    currentPage.value = 1
}

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', { 
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value)
}

const logActivity = (clientId) => {
    alert(`Logging activity for client ${clientId}`)
    // TODO: Open activity modal
}

const openContacts = (clientId) => {
    alert(`Opening contacts for client ${clientId}`)
    // TODO: Open contacts modal
    //  or drawer
}

onMounted(() => {
    // Initial data fetch can be done here
    auth.pageTitle = 'Clients'
})
</script>

