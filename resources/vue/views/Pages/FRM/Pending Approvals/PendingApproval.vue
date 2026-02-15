<template>
    <!-- Navigation Tabs -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="border-b border-gray-200 bg-white">
            <div class="flex">
                <button
                    @click="activeTab = 'prebiddings'"
                    :class="[
                        'px-6 py-4 text-sm font-semibold transition-colors border-b-2',
                        activeTab === 'prebiddings'
                            ? 'text-[#0c8ce9] border-[#0c8ce9] bg-white'
                            : 'text-gray-600 border-transparent hover:text-gray-900 hover:bg-gray-100'
                    ]"
                >
                    Prebiddings
                </button>
                <button
                    @click="activeTab = 'tab2'"
                    :class="[
                        'px-6 py-4 text-sm font-semibold transition-colors border-b-2',
                        activeTab === 'tab2'
                            ? 'text-[#0c8ce9] border-[#0c8ce9] bg-white'
                            : 'text-gray-600 border-transparent hover:text-gray-900 hover:bg-gray-100'
                    ]"
                >
                    Payroll Approvals
                </button>
                <button
                    @click="activeTab = 'tab3'"
                    :class="[
                        'px-6 py-4 text-sm font-semibold transition-colors border-b-2',
                        activeTab === 'tab3'
                            ? 'text-[#0c8ce9] border-[#0c8ce9] bg-white'
                            : 'text-gray-600 border-transparent hover:text-gray-900 hover:bg-gray-100'
                    ]"
                >
                    Tab 3
                </button>
            </div>
        </div>
    </div>

    <!-- Prebiddings Tab Content -->
    <div v-if="activeTab === 'prebiddings'">
        <!-- Stats Cards -->
        <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide mb-1">Total Pre-Biddings</p>
                    <p class="text-2xl font-bold text-gray-900">{{ pendingPrebiddingsApprovals.length }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-[#0c8ce9]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide mb-1">This Week</p>
                    <p class="text-2xl font-bold text-amber-600">{{ thisWeekCount }}</p>
                </div>
                <div class="bg-amber-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide mb-1">Pending Approval</p>
                    <p class="text-2xl font-bold text-green-600">{{ pendingApprovalCount }}</p>
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
                    <option value="deadline">Deadline (Nearest)</option>
                    <option value="created">Recently Created</option>
                    <option value="priority">Priority</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Request Cards Grid -->
    <div class="grid grid-cols-1 gap-6">
        <div 
            v-for="request in filteredRequests" 
            :key="request.id"
            class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-200 overflow-hidden"
        >
            <!-- Card Header -->
            <div class="bg-linear-to-r from-[#0c8ce9] to-blue-600 px-6 py-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-white mb-1">{{ request.project_name }}</h3>
                        <div class="flex flex-col gap-1">
                            <p class="text-xs text-blue-100 font-semibold">{{ request.project_code }}</p>
                            <p class="text-xs text-blue-100 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ request.address }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="px-6 py-4">
                <!-- Client & Submitted Info -->
                <div class="flex items-center gap-2 mb-4">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        {{ request.client_name }}
                    </span>
                    <span class="text-xs text-gray-500">Submitted {{ formatDate(request.submitted_at) }}</span>
                </div>

                <!-- Deadline -->
                <div class="mb-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-xs font-semibold text-gray-700">Bidding Deadline</span>
                    </div>
                    <p :class="[
                        'text-sm font-bold',
                        isDeadlineUrgent(request.deadline) ? 'text-red-600' : 'text-gray-900'
                    ]">
                        {{ formatDate(request.deadline) }}
                        <span v-if="isDeadlineUrgent(request.deadline)" class="ml-2 text-xs">({{ daysUntil(request.deadline) }} days left)</span>
                    </p>
                </div>

                <!-- Submitted By -->
                <div class="mb-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="text-xs font-semibold text-gray-700">Submitted By</span>
                    </div>
                    <p class="text-sm text-gray-900">{{ request.submitted_by }}</p>
                </div>

                <!-- Budget Breakdown -->
                <div class="mb-4">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs font-semibold text-gray-700">Budget Breakdown</span>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-600">Materials:</span>
                            <span class="font-semibold text-gray-900">₱{{ parseFloat(request.materials_cost).toLocaleString() }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-600">Equipment:</span>
                            <span class="font-semibold text-gray-900">₱{{ parseFloat(request.equipment_cost).toLocaleString() }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-600">Labor:</span>
                            <span class="font-semibold text-gray-900">₱{{ parseFloat(request.labor_cost).toLocaleString() }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-600">Subcontractor:</span>
                            <span class="font-semibold text-gray-900">₱{{ parseFloat(request.subcontractor_cost).toLocaleString() }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-600">Miscellaneous:</span>
                            <span class="font-semibold text-gray-900">₱{{ parseFloat(request.miscellaneous_cost).toLocaleString() }}</span>
                        </div>
                        <div class="border-t border-gray-300 pt-2 mt-2">
                            <div class="flex justify-between">
                                <span class="text-sm font-bold text-gray-900">Total Budget:</span>
                                <span class="text-sm font-bold text-[#0c8ce9]">₱{{ parseFloat(request.total_budget).toLocaleString() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents -->
                <div class="mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-xs font-semibold text-gray-700">{{ request.document_count }} Documents Attached</span>
                    </div>
                </div>

                <!-- Description -->
                <div v-if="request.description" class="mb-4">
                    <p class="text-xs text-gray-600 line-clamp-2">{{ request.description }}</p>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <button 
                    @click="viewDetails(request.id)"
                    class="w-full px-4 py-2 bg-[#0c8ce9] text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-all"
                >
                    View Full Details
                </button>
            </div>
        </div>
    </div>

        <!-- Empty State -->
        <div v-if="filteredRequests.length === 0" class="bg-white rounded-lg border border-gray-200 shadow-sm p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-lg font-semibold text-gray-900 mb-2">No Pending Pre-Biddings</p>
            <p class="text-sm text-gray-600">All pre-bidding requests have been approved or no requests match your filters.</p>
        </div>
    </div>

    <!-- Tab 2 Content -->
    <div v-if="activeTab === 'tab2'" class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
            <h2 class="text-sm font-semibold text-gray-700">Pending Payroll Runs (Finance Approval)</h2>
        </div>
        <div class="p-6">
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Payroll Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Frequency</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Coverage</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Group</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Pay Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="run in pendingFinancePayrollRuns" :key="run.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ run.name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ run.frequency }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ formatDate(run.start_date) }} - {{ formatDate(run.end_date) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ run.group }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ formatDate(run.pay_date) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ run.status }}</td>
                            <td class="px-4 py-3">
                                <button
                                    @click="viewPayrollApproval(run.id)"
                                    class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                                >
                                    Review
                                </button>
                            </td>
                        </tr>
                        <tr v-if="pendingFinancePayrollRuns.length === 0">
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                                No pending payroll runs for finance approval.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 3 Content -->
    <div v-if="activeTab === 'tab3'">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
            </svg>
            <p class="text-lg font-semibold text-gray-900 mb-2">Tab 3</p>
            <p class="text-sm text-gray-600">Content for Tab 3 will be added here.</p>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

const router = useRouter()
const auth = useAuthStore()

// Active Tab
const activeTab = ref('prebiddings')
const pendingFinancePayrollRuns = ref([])

// Filters
const filters = ref({
    search: '',
    client: '',
    sortBy: 'deadline'
})

// Mock Data - Pre-Bidding Requests pending approval
const pendingPrebiddingsApprovals = ref([])

const fetchPendingPrebiddingApprovals = async () => {
    try {
        const response = await axios.get('/api/pending-approvals/finance/prebiddings', {
            headers: { Authorization: `Bearer ${auth.token}` }
        })
        
        
        // Safely check if pendingPrebiddingsApprovals exists and is an array
        const pendingPrebiddingsApprovalsData = response.data?.pendingApprovals
        if (!pendingPrebiddingsApprovalsData || !Array.isArray(pendingPrebiddingsApprovalsData)) {
            console.error('Invalid pendingPrebiddingsApprovals data in response:', response.data)
            pendingPrebiddingsApprovals.value = []
            return
        }
        
        // Map backend data to frontend structure
        pendingPrebiddingsApprovals.value = pendingPrebiddingsApprovalsData.map(prebidding => {
            // Calculate budget breakdown from category_breakdowns
            let materials_cost = 0
            let equipment_cost = 0
            let labor_cost = 0
            let subcontractor_cost = 0
            let miscellaneous_cost = 0
            
            if (prebidding.category_breakdowns && Array.isArray(prebidding.category_breakdowns)) {
                prebidding.category_breakdowns.forEach(category => {
                    const categoryName = (category.name || '').toLowerCase()
                    if (categoryName.includes('material')) {
                        materials_cost += parseFloat(category.amount || 0)
                    } else if (categoryName.includes('equipment')) {
                        equipment_cost += parseFloat(category.amount || 0)
                    } else if (categoryName.includes('labor')) {
                        labor_cost += parseFloat(category.amount || 0)
                    } else if (categoryName.includes('subcontractor')) {
                        subcontractor_cost += parseFloat(category.amount || 0)
                    } else {
                        miscellaneous_cost += parseFloat(category.amount || 0)
                    }
                })
            }
            
            return {
                id: prebidding.id,
                project_name: prebidding.name || '',
                project_code: prebidding.code || '',
                address: prebidding.address || '',
                client_name: prebidding.client_name || '',
                submitted_by: prebidding.submitted_by || '',
                deadline: prebidding.bid_opening || '',
                total_budget: parseFloat(prebidding.total_budget || 0),
                materials_cost,
                equipment_cost,
                labor_cost,
                subcontractor_cost,
                miscellaneous_cost,
                status: 'Pending Approval',
                description: prebidding.notes || '',
                submitted_at: prebidding.submitted_at || '',
                document_count: prebidding.documents_uploaded || 0
            }
        })
        
    } catch (error) {
        console.error('Error fetching pending approvals:', error)
        if (error.response) {
            console.error('Error response:', error.response.data)
            console.error('Error status:', error.response.status)
        }
        pendingPrebiddingsApprovals.value = []
    }
} 

const fetchPendingFinancePayrollRuns = async () => {
    try {
        const response = await axios.get('/api/payroll-runs')
        const rows = response.data?.data || []
        pendingFinancePayrollRuns.value = rows.filter(run => run.status === 'Pending Finance Approval')
    } catch (error) {
        console.error('Failed to load pending payroll runs for finance', error)
        pendingFinancePayrollRuns.value = []
    }
}

const viewPayrollApproval = (runId) => {
    router.push({
        name: 'payroll_run_detail',
        params: { id: runId },
        query: { mode: 'finance-approval' }
    })
}

// Computed Properties
const filteredRequests = computed(() => {
    let results = pendingPrebiddingsApprovals.value.filter(req => {
        const matchesSearch = req.project_name.toLowerCase().includes(filters.value.search.toLowerCase()) ||
                             req.project_code.toLowerCase().includes(filters.value.search.toLowerCase())
        const matchesClient = !filters.value.client || req.client_name === filters.value.client

        return matchesSearch && matchesClient
    })

    // Sort
    if (filters.value.sortBy === 'deadline') {
        results.sort((a, b) => new Date(a.deadline) - new Date(b.deadline))
    } else if (filters.value.sortBy === 'created') {
        results.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    }

    return results
})

const hasActiveFilters = computed(() => {
    return filters.value.search || filters.value.client
})

const thisWeekCount = computed(() => {
    const weekFromNow = new Date()
    weekFromNow.setDate(weekFromNow.getDate() + 7)
    return pendingPrebiddingsApprovals.value.filter(req => new Date(req.deadline) <= weekFromNow).length
})

const pendingApprovalCount = computed(() => {
    return pendingPrebiddingsApprovals.value.filter(req => req.status === 'Pending Approval').length
})

// Methods
const clearFilters = () => {
    filters.value = {
        search: '',
        client: '',
        sortBy: 'deadline'
    }
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const isDeadlineUrgent = (deadline) => {
    const daysLeft = daysUntil(deadline)
    return daysLeft <= 7
}

const daysUntil = (deadline) => {
    const today = new Date()
    const deadlineDate = new Date(deadline)
    const diffTime = deadlineDate - today
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays
}

const viewDetails = (id) => {
    router.push(`/finance/pending-approvals/prebiddings/${id}`)
}

onMounted(() => {
    fetchPendingPrebiddingApprovals()
    fetchPendingFinancePayrollRuns()
})
</script>