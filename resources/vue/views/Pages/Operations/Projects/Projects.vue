<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">

            <!-- Navigation Tabs -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mb-6">
                <div class="border-b border-gray-200 bg-white">
                    <div class="flex">
                        <button
                            @click="activeTab = 'biddings-won'"
                            :class="[
                                'px-6 py-4 text-sm font-semibold transition-colors border-b-2',
                                activeTab === 'biddings-won'
                                    ? 'text-[#0c8ce9] border-[#0c8ce9] bg-white'
                                    : 'text-gray-600 border-transparent hover:text-gray-900 hover:bg-gray-100'
                            ]"
                        >
                            Biddings Won
                        </button>
                        <button
                            @click="activeTab = 'ongoing'"
                            :class="[
                                'px-6 py-4 text-sm font-semibold transition-colors border-b-2',
                                activeTab === 'ongoing'
                                    ? 'text-[#0c8ce9] border-[#0c8ce9] bg-white'
                                    : 'text-gray-600 border-transparent hover:text-gray-900 hover:bg-gray-100'
                            ]"
                        >
                            Ongoing Projects
                        </button>
                    </div>
                </div>
            </div>          

            <!-- Biddings Won Tab -->
            <div v-if="activeTab === 'biddings-won'" class="rounded-lg overflow-hidden">
                <!-- Filters Section -->
                <div class="bg-white rounded-lg p-6 mb-6 border border-gray-200 shadow-sm">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Filters</h3>
                        <button 
                            v-if="hasActiveFilters"
                            @click="clearFilters"
                            class="text-sm text-[#0c8ce9] hover:underline font-semibold"
                        >
                            Clear All
                        </button>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Search</label>
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
                            <label class="block text-xs font-medium text-gray-700 mb-2">Client</label>
                            <select 
                                v-model="filters.client"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                            >
                                <option value="">All Clients</option>
                                <option value="ABC Corporation">ABC Corporation</option>
                                <option value="XYZ Holdings">XYZ Holdings</option>
                                <option value="Global Construction Inc">Global Construction Inc</option>
                                <option value="Metro Development Corp">Metro Development Corp</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Sort By</label>
                            <select 
                                v-model="filters.sortBy"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                            >
                                <option value="end-date">End Date</option>
                                <option value="budget">Budget</option>
                            </select>
                        </div>
                    </div>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Project</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Client</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide">Budget</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="project in filteredProjects" :key="project.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ project.project_name }}</p>
                                    <p class="text-xs text-gray-600">{{ project.project_code }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-900">{{ project.client_name }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <p class="text-sm font-semibold text-gray-900">₱{{ formatCurrency(project.total_budget) }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button
                                    @click="viewProject(project.id)"
                                    class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold text-[#0c8ce9] hover:bg-blue-50 rounded transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Empty State -->
                <div v-if="filteredProjects.length === 0" class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-lg font-semibold text-gray-900 mb-2">No Projects</p>
                    <p class="text-sm text-gray-600">No projects match your filters.</p>
                </div>
            </div>

            <!-- Ongoing Projects Tab -->
            <div v-if="activeTab === 'ongoing'" class="bg-white rounded-lg overflow-hidden">
                
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

const activeTab = ref('biddings-won')

const filters = ref({
    search: '',
    client: '',
    sortBy: 'budget'
})

const biddingsWonProjects = ref([])

const filteredProjects = computed(() => {
    let results = biddingsWonProjects.value.filter(proj => {
        const matchesSearch = proj.project_name.toLowerCase().includes(filters.value.search.toLowerCase()) ||
                             proj.project_code.toLowerCase().includes(filters.value.search.toLowerCase())
        const matchesClient = !filters.value.client || proj.client_name === filters.value.client

        return matchesSearch && matchesClient
    })

    // Sort
    if (filters.value.sortBy === 'end-date') {
        results.sort((a, b) => new Date(a.end_date) - new Date(b.end_date))
    } else if (filters.value.sortBy === 'budget') {
        results.sort((a, b) => b.total_budget - a.total_budget)
    }

    return results
})

const hasActiveFilters = computed(() => {
    return filters.value.search || filters.value.client
})

const clearFilters = () => {
    filters.value = {
        search: '',
        client: '',
        sortBy: 'budget'
    }
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

const viewProject = (id) => {
    if (activeTab.value === 'ongoing') {
        router.push(`/projects/${id}/monitoring`)
        return
    }

    router.push(`/projects/${id}`)
}

const fetchBiddingsWon = async () => {
    try {
        const response = await axios.get('/api/biddings-won', {
            headers: {
                Authorization: `Bearer ${auth.token}`
            }
        })
        
        biddingsWonProjects.value = response.data.biddings_won.map(project => ({
            id: project.id,
            project_name: project.project_name,
            project_code: project.project_code,
            client_name: project.client_name,
            total_budget: project.total_budget,
            status: 'pending-setup'
        }))
    } catch (error) {
        console.error('Error fetching biddings won:', error)
    }
}

onMounted(() => {
    auth.pageTitle = 'Projects'
    fetchBiddingsWon()
})
</script>