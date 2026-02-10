<template>
    <auth-layout />
        <div class="ps-60 pe-4 bg-[#F3F4F6] min-h-screen">
            <div class="px-8 py-4">
                <!-- Back Button & Header -->
                <div class="mb-8 flex items-center gap-4">
                    <button 
                        @click="router.back()"
                        class="flex items-center gap-2 text-[#0C8CE9] hover:text-blue-700 font-semibold"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back
                    </button>
                </div>

                <!-- Project Header -->
                <div class="bg-white rounded-lg p-8 mb-8 border border-gray-200 shadow-sm">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ currentProject.name }}</h1>
                            <p class="text-gray-600 mt-2">{{ currentProject.client }} | {{ currentProject.location }}</p>
                        </div>
                        <div class="text-right">
                            <span :class="currentProject.status === 'Active' ? 'text-[#0C8CE9]' : 'text-orange-500'" class="text-lg font-bold">
                                {{ currentProject.status }}
                            </span>
                            <p class="text-gray-600 text-sm">Project Code: {{ currentProject.code }}</p>
                        </div>
                    </div>

                    <!-- Key Financial Metrics -->
                    <div class="grid grid-cols-5 gap-4">
                        <div class="bg-linear-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                            <p class="text-gray-600 text-sm font-medium">Contract Amount</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">₱{{ formatCurrency(currentProject.contractAmount) }}</p>
                        </div>
                        <div class="bg-linear-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                            <p class="text-gray-600 text-sm font-medium">Spent to Date</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">₱{{ formatCurrency(currentProject.spent) }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ currentProject.utilizationPercent }}% utilized</p>
                        </div>
                        <div class="bg-linear-to-br from-orange-50 to-orange-100 rounded-lg p-4 border border-orange-200">
                            <p class="text-gray-600 text-sm font-medium">Remaining Budget</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">₱{{ formatCurrency(currentProject.remaining) }}</p>
                        </div>
                        <div class="bg-linear-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                            <p class="text-gray-600 text-sm font-medium">Retention Amount</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">₱{{ formatCurrency(currentProject.retention) }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ currentProject.retentionPercent }}% of contract</p>
                        </div>
                        <div class="bg-linear-to-br from-red-50 to-red-100 rounded-lg p-4 border border-red-200">
                            <p class="text-gray-600 text-sm font-medium">Variance</p>
                            <p :class="currentProject.variance >= 0 ? 'text-red-600' : 'text-green-600'" class="text-2xl font-bold mt-2">
                                ₱{{ formatCurrency(Math.abs(currentProject.variance)) }}
                            </p>
                            <p class="text-xs text-gray-600 mt-1">{{ currentProject.variance >= 0 ? 'Over' : 'Under' }} budget</p>
                        </div>
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <div class="bg-white border-b border-gray-200 mb-8 rounded-t-lg">
                    <div class="px-8 flex gap-8">
                        <button
                            v-for="tab in tabs"
                            :key="tab.key"
                            @click="activeTab = tab.key"
                            class="py-4 px-2 border-b-2 font-semibold text-sm transition-colors"
                            :class="activeTab === tab.key
                                ? 'border-[#0C8CE9] text-[#0C8CE9]'
                                : 'border-transparent text-gray-600 hover:text-gray-900'"
                        >
                            {{ tab.label }}
                        </button>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="bg-white rounded-lg p-8 border border-gray-200 shadow-sm">
                    <!-- Budget Tab -->
                    <div v-if="activeTab === 'budget'" class="space-y-8">
                        <Budget />
                    </div>

                    <!-- Cost Monitoring Tab -->
                    <div v-if="activeTab === 'cost-monitoring'" class="space-y-8">
                        <CostMonitoring />
                    </div>

                    <!-- Variance Analysis Tab -->
                    <div v-if="activeTab === 'variance'" class="space-y-8">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-6">Variance Analysis</h3>
                            <div class="grid grid-cols-2 gap-6">
                                <!-- Cost Variance -->
                                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                                    <h4 class="font-semibold text-gray-900 mb-4">Cost Variance (CV)</h4>
                                    <p class="text-3xl font-bold text-red-600 mb-2">₱{{ formatCurrency(currentProject.costVariance) }}</p>
                                    <p class="text-sm text-gray-600 mb-4">{{ currentProject.costVariancePercent }}% of spent amount</p>
                                    <div class="w-full bg-gray-300 rounded-full h-2">
                                        <div class="bg-red-500 h-2 rounded-full" :style="{ width: '35%' }"></div>
                                    </div>
                                </div>

                                <!-- Schedule Variance -->
                                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                                    <h4 class="font-semibold text-gray-900 mb-4">Schedule Variance (SV)</h4>
                                    <p class="text-3xl font-bold text-green-600 mb-2">₱{{ formatCurrency(currentProject.scheduleVariance) }}</p>
                                    <p class="text-sm text-gray-600 mb-4">{{ currentProject.scheduleVariancePercent }}% ahead of schedule</p>
                                    <div class="w-full bg-gray-300 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" :style="{ width: '45%' }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cost Performance Index -->
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Cost Performance Indicators</h4>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                    <p class="text-gray-600 text-sm">CPI (Cost Performance Index)</p>
                                    <p class="text-2xl font-bold text-blue-600 mt-2">{{ currentProject.cpi.toFixed(2) }}</p>
                                    <p class="text-xs text-gray-600 mt-1">{{ currentProject.cpi > 1 ? 'Under budget' : 'Over budget' }}</p>
                                </div>
                                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                    <p class="text-gray-600 text-sm">SPI (Schedule Performance Index)</p>
                                    <p class="text-2xl font-bold text-blue-600 mt-2">{{ currentProject.spi.toFixed(2) }}</p>
                                    <p class="text-xs text-gray-600 mt-1">{{ currentProject.spi > 1 ? 'Ahead of schedule' : 'Behind schedule' }}</p>
                                </div>
                                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                    <p class="text-gray-600 text-sm">ETC (Estimate to Complete)</p>
                                    <p class="text-2xl font-bold text-blue-600 mt-2">₱{{ formatCurrency(currentProject.etc) }}</p>
                                    <p class="text-xs text-gray-600 mt-1">Remaining cost needed</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Billing Tab -->
                    <div v-if="activeTab === 'billing'" class="space-y-8">
                        <MilestoneBilling />
                    </div>

                    <!-- Cash Flow Tab -->
                    <div v-if="activeTab === 'cash-flow'" class="space-y-8">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-6">Project Cash Flow Analysis</h3>
                            <div class="grid grid-cols-4 gap-4 mb-8">
                                <div class="bg-linear-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
                                    <p class="text-gray-600 text-sm font-medium">Cumulative Billings</p>
                                    <p class="text-2xl font-bold text-blue-600 mt-2">₱{{ formatCurrency(currentProject.cumulativeBillings) }}</p>
                                </div>
                                <div class="bg-linear-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
                                    <p class="text-gray-600 text-sm font-medium">Cumulative Collections</p>
                                    <p class="text-2xl font-bold text-green-600 mt-2">₱{{ formatCurrency(currentProject.cumulativeCollections) }}</p>
                                </div>
                                <div class="bg-linear-to-br from-orange-50 to-orange-100 p-4 rounded-lg border border-orange-200">
                                    <p class="text-gray-600 text-sm font-medium">Outstanding A/R</p>
                                    <p class="text-2xl font-bold text-orange-600 mt-2">₱{{ formatCurrency(currentProject.outstandingAR) }}</p>
                                </div>
                                <div class="bg-linear-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
                                    <p class="text-gray-600 text-sm font-medium">Collection Rate</p>
                                    <p class="text-2xl font-bold text-purple-600 mt-2">{{ currentProject.collectionRate }}%</p>
                                </div>
                            </div>

                            <!-- Monthly Cash Flow Table -->
                            <div class="bg-white rounded-lg border border-gray-100 overflow-hidden shadow-sm">
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="bg-gray-50 border-b border-gray-200">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Month</th>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Billings (₱)</th>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Collections (₱)</th>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Net Cash Flow (₱)</th>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">A/R Balance (₱)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="month in cashFlowData" :key="month.month" class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ month.month }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-600">₱{{ formatCurrency(month.billings) }}</td>
                                                <td class="px-6 py-4 text-sm font-semibold text-green-600">₱{{ formatCurrency(month.collections) }}</td>
                                                <td class="px-6 py-4 text-sm font-semibold" :class="month.netCashFlow >= 0 ? 'text-green-600' : 'text-red-600'">
                                                    ₱{{ formatCurrency(month.netCashFlow) }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600">₱{{ formatCurrency(month.arBalance) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cost Breakdown Tab -->
                    <div v-if="activeTab === 'breakdown'" class="space-y-8">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-6">Budget Breakdown</h3>
                            <div class="bg-white rounded-lg border border-gray-100 overflow-hidden shadow-sm">
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="bg-gray-50 border-b border-gray-200">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                                    Category
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                                    Allocated (₱)
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                                    Committed (₱)
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                                    Spent (₱)
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                                    Description
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                                    Remaining (₱)
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                                    Status
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="item in budgetBreakdown" :key="item.category" class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ item.category }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-600">{{ formatCurrency(item.allocated) }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-600">{{ formatCurrency(item.committed) }}</td>
                                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ formatCurrency(item.spent) }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-600">{{ item.description }}</td>
                                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ formatCurrency(item.allocated - item.spent) }}</td>
                                                <td class="px-6 py-4 text-sm">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border-2 border-green-500 text-green-600 bg-green-50">
                                                        {{ item.status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Tab -->
                    <div v-if="activeTab === 'documents'" class="space-y-8">
                        <Documents />
                    </div>
                </div>
            </div>
        </div>
    
</template>

<script setup>
    /* =====================================================
    Core Imports
    ===================================================== */
    import { ref, onMounted, computed } from 'vue'
    import { useRouter, useRoute } from 'vue-router'

    /* =====================================================
    Stores & Layouts
    ===================================================== */
    import { useAuthStore } from '@/stores/auth'
    import Budget from '@/components/FRM/PFV_Budget.vue'
    import CostMonitoring from '@/components/FRM/PFV_CostMonitoring.vue'
    import MilestoneBilling from '@/components/FRM/PFV_MilestoneBilling.vue'
    import Documents from '@/components/FRM/PFV_Documents.vue'

    const auth = useAuthStore()
    const route = useRoute()
    const router = useRouter()

    /* =====================================================
    UI State
    ===================================================== */
    const loading = ref(false)
    const activeTab = ref('budget')

    /* =====================================================
    Tabs Configuration
    ===================================================== */
    const tabs = [
        { key: 'budget', label: 'Budget Overview' },
        { key: 'cost-monitoring', label: 'Cost Monitoring' },
        { key: 'variance', label: 'Variance Analysis' },
        { key: 'billing', label: 'Progress Billing' },
        { key: 'cash-flow', label: 'Cash Flow' },
        { key: 'breakdown', label: 'Cost Breakdown' },
        { key: 'documents', label: 'Documents' },
    ]

    /* =====================================================
    Project Data
    ===================================================== */
    const projectId = computed(() => route.params.id)

    const currentProject = ref({
        id: 1,
        code: 'MTD-001',
        name: 'Metro Tower Development',
        client: 'Urban Development Corp',
        location: 'Makati, Metro Manila',
        status: 'Active',
        contractAmount: 850000,
        spent: 697500,
        remaining: 152500,
        retention: 42500,
        retentionPercent: 5,
        variance: 45000,
        utilizationPercent: 82,
        
        // Variance metrics
        costVariance: -24325,
        costVariancePercent: -3.5,
        scheduleVariance: 15000,
        scheduleVariancePercent: 2.1,
        cpi: 1.05,
        spi: 1.08,
        etc: 128200,
        
        // Cash Flow metrics
        cumulativeBillings: 780000,
        cumulativeCollections: 697500,
        outstandingAR: 82500,
        collectionRate: 89,
        
        // Cost Breakdown
        laborBudget: 250000,
        laborSpent: 215000,
        laborPercent: 86,
        
        materialBudget: 350000,
        materialSpent: 285000,
        materialPercent: 81,
        
        equipmentBudget: 150000,
        equipmentSpent: 132000,
        equipmentPercent: 88,
        
        subcontractorBudget: 100000,
        subcontractorSpent: 65500,
        subcontractorPercent: 65,
    })

    /* =====================================================
    Budget Breakdown Data
    ===================================================== */
    const budgetBreakdown = ref([
        { 
            category: 'Labor',
            allocated: 250000,
            committed: 120000,
            spent: 215000,
            description: 'Salaries for skilled and unskilled manpower',
            status: 'On Track'
        },
        { 
            category: 'Materials',
            allocated: 350000,
            committed: 80500,
            spent: 285000,
            description: 'Construction materials and supplies',
            status: 'On Track'
        },
        { 
            category: 'Subcontractors',
            allocated: 100000,
            committed: 50000,
            spent: 65500,
            description: 'Outsourced specialized work',
            status: 'On Track'
        },
        { 
            category: 'Equipment',
            allocated: 150000,
            committed: 30000,
            spent: 132000,
            description: 'Rental and maintenance of heavy equipment',
            status: 'On Track'
        },
        { 
            category: 'Miscellaneous',
            allocated: 80000,
            committed: 15000,
            spent: 40000,
            description: 'Unexpected expenses and contingencies',
            status: 'On Track'
        }
    ])

    /* =====================================================
    Cash Flow Data
    ===================================================== */
    const cashFlowData = ref([
        { month: 'January 2025', billings: 85000, collections: 75000, netCashFlow: -10000, arBalance: 10000 },
        { month: 'February 2025', billings: 92000, collections: 85000, netCashFlow: -7000, arBalance: 17000 },
        { month: 'March 2025', billings: 105000, collections: 98000, netCashFlow: -7000, arBalance: 24000 },
        { month: 'April 2025', billings: 115000, collections: 110000, netCashFlow: -5000, arBalance: 29000 },
        { month: 'May 2025', billings: 108000, collections: 112000, netCashFlow: 4000, arBalance: 21000 },
        { month: 'June 2025', billings: 128000, collections: 135000, netCashFlow: 7000, arBalance: -7000 },
        { month: 'July 2025', billings: 147000, collections: 142000, netCashFlow: -5000, arBalance: 10500 },
    ])

    /* =====================================================
    Helper Functions
    ===================================================== */
    const formatCurrency = (value) => {
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value).replace('₱', '').trim()
    }

    /* =====================================================
    Lifecycle
    ===================================================== */
    onMounted(() => {
        // In a real app, fetch project data based on route.params.id
        loading.value = false
    })
</script>
