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
                            to="/prebiddings/create"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-all"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            New Pre-Bidding
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
                        <label class="block text-xs font-medium text-gray-700 mb-2">Status</label>
                        <select 
                            v-model="filters.status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="draft">Draft</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
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
                            <option value="cid">City Infrastructure Development</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Date Range</label>
                        <input 
                            v-model="filters.dateRange"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
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

            <!-- Cards Section -->
            <div class="space-y-4">
                <!-- Empty State -->
                <div v-if="paginatedPrebiddings.length === 0" class="bg-white rounded-lg border border-gray-200 shadow-sm p-12">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div class="text-center">
                            <p class="text-lg font-semibold text-gray-600">No prebiddings found</p>
                            <p class="text-sm text-gray-400 mt-1">Try adjusting your filters or create a new prebidding</p>
                        </div>
                    </div>
                </div>

                <!-- Prebidding Cards -->
                <div 
                    v-for="prebid in paginatedPrebiddings" 
                    :key="prebid.id"
                    class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden"
                >
                    <div class="flex">
                        <!-- Image Section -->
                        <div class="w-64 h-48 shrink-0 bg-gray-200">
                            <img 
                                v-if="prebid.image" 
                                :src="prebid.image"
                                class="w-full h-full object-cover"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center bg-linear-to-br from-gray-100 to-gray-200">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Content Section -->
                        <div class="flex-1 p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 mb-1">{{ prebid.name }}</h3>
                                    <p class="text-sm text-gray-500">{{ prebid.code }}</p>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ml-4" :class="statusStyles[prebid.status]">
                                    {{ formatStatus(prebid.status) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-4 gap-6 mb-4">
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Client</label>
                                    <p class="text-sm text-gray-900 mt-1">{{ prebid.client_name }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Address</label>
                                    <p class="text-sm text-gray-900 mt-1">{{ prebid.address || 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Bid Opening Date</label>
                                    <p class="text-sm text-gray-900 mt-1">{{ formatDate(prebid.bid_opening) }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Branches</label>
                                    <p class="text-sm text-gray-900 mt-1">{{ prebid.branches?.join(', ') || 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="text-xs font-semibold text-gray-500 uppercase block mb-2">Document Progress</label>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-3 bg-gray-200 rounded-full overflow-hidden">
                                        <div 
                                            class="h-full bg-linear-to-r from-[#0c8ce9] to-blue-600 rounded-full transition-all"
                                            :style="{ width: documentsProgress(prebid) + '%' }"
                                        ></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700 w-16">
                                        {{ prebid.uploaded_count }}/{{ prebid.documents_count }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3">
                                <button 
                                    v-if="prebid.status === 'active'"
                                    @click="viewPrebid(prebid.id)"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700 transition-all"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Details
                                </button>
                                <span v-else class="text-sm text-gray-400 font-semibold px-4 py-2">View Details</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="paginatedPrebiddings.length > 0" class="bg-white rounded-lg border border-gray-200 shadow-sm px-6 py-4 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Showing <span class="font-semibold">{{ startIndex + 1 }}</span> to <span class="font-semibold">{{ Math.min(endIndex, filteredPrebiddings.length) }}</span> of <span class="font-semibold">{{ filteredPrebiddings.length }}</span> pre-biddings
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
import axios from 'axios'

const auth = useAuthStore()
const router = useRouter()

// Filters
const filters = ref({
    search: '',
    status: '',
    client: '',
    dateRange: ''
})

const currentPage = ref(1)
const pageSize = ref(10)

// Status Styles
const statusStyles = {
    'active': 'bg-green-100 text-green-800',
    'operations_approval': 'bg-yellow-100 text-yellow-800',
    'finance_approval': 'bg-yellow-100 text-yellow-800',
    'rejected': 'bg-red-100 text-red-800'
}

// Mock data
const prebiddings = ref([])

const fetchPrebiddings = async () => {
    try {
        const response = await axios.get('/api/prebiddings', {
            headers: { Authorization: `Bearer ${auth.token}` }
        })
        prebiddings.value = response.data.prebiddings
    } catch (error) {
        console.error('Error fetching prebiddings:', error)
    }
}

// Computed Properties
const filteredPrebiddings = computed(() => {
    return prebiddings.value.filter(pb => {
        const matchesSearch = pb.name.toLowerCase().includes(filters.value.search.toLowerCase()) ||
                             pb.code.toLowerCase().includes(filters.value.search.toLowerCase())
        const matchesStatus = !filters.value.status || pb.status === filters.value.status
        const matchesClient = !filters.value.client || pb.client_name.toLowerCase().includes(filters.value.client.toLowerCase())

        return matchesSearch && matchesStatus && matchesClient
    })
})

const hasActiveFilters = computed(() => {
    return filters.value.search || filters.value.status || filters.value.client || filters.value.dateRange
})

const totalPages = computed(() => Math.ceil(filteredPrebiddings.value.length / pageSize.value))

const startIndex = computed(() => (currentPage.value - 1) * pageSize.value)
const endIndex = computed(() => startIndex.value + pageSize.value)

const paginatedPrebiddings = computed(() => {
    return filteredPrebiddings.value.slice(startIndex.value, endIndex.value)
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
        client: '',
        dateRange: ''
    }
    currentPage.value = 1
}

const formatStatus = (status) => {
    const map = {
        'active': 'Active',
        'operations_approval': 'Operations Approval',
        'finance_approval': 'Finance Approval',
        'rejected': 'Rejected'
    }
    return map[status] || status
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const viewPrebid = (id) => {
    router.push(`/prebiddings/${id}`)
}

const documentsProgress = (prebid) => {
    if (!prebid.documents_count) {
        return 0
    }
    return Math.min(100, Math.round((prebid.uploaded_count / prebid.documents_count) * 100))
}

onMounted(() => {
    // Fetch prebiddings from API
    auth.pageTitle = 'Prebiddings'
    fetchPrebiddings()
})  
</script>
