<template>
    <!-- Category Tab Navigation (Sticky) -->
    <div class="bg-[#F3F4F6] sticky top-0 z-10 w-full pt-5">
        <div class="w-full">
            <div class="flex gap-8 overflow-x-auto border-b border-gray-200">
                <button
                    v-for="category in categories"
                    :key="category"
                    @click="activeCategory = category"
                    :class="activeCategory === category ? 'border-b-2 border-[#0c8ce9] text-[#0c8ce9]' : 'text-gray-600 hover:text-gray-900'"
                    class="pb-4 font-semibold text-sm transition-colors whitespace-nowrap"
                >
                    {{ category }}
                </button>
            </div>
        </div>
    </div>

    <!-- Category Content Area -->
    <div class="w-full mt-5">
        <!-- Sub-Tab Navigation (Generated vs Manual) -->
        <div class="flex gap-4 mb-8 border-b border-gray-200">
            <button
                @click="activeSubTab = 'generated'"
                :class="activeSubTab === 'generated' ? 'border-b-2 border-[#0c8ce9] text-[#0c8ce9]' : 'text-gray-600 hover:text-gray-900'"
                class="pb-4 font-semibold text-sm transition-colors"
            >
                Generated Reports
            </button>
            <button
                @click="activeSubTab = 'manual'"
                :class="activeSubTab === 'manual' ? 'border-b-2 border-[#0c8ce9] text-[#0c8ce9]' : 'text-gray-600 hover:text-gray-900'"
                class="pb-4 font-semibold text-sm transition-colors"
            >
                Manual Reports
            </button>
        </div>

        <!-- Generated Reports Sub-Tab -->
        <div v-if="activeSubTab === 'generated'">
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Report Name</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Date Range</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Description</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Last Generated</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="report in getGeneratedReportsByCategory(activeCategory)" :key="report.id" class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ report.name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ report.dateRange.start }} to {{ report.dateRange.end }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ report.description }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ report.lastGenerated }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    Completed
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-4">
                                    <button @click="previewReport(report)" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">View</button>
                                    <button class="text-blue-600 hover:text-blue-700 font-semibold text-sm">Download</button>
                                    <button class="text-blue-600 hover:text-blue-700 font-semibold text-sm">Print</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-sm text-gray-600 mt-4">Total Reports: {{ getGeneratedReportsByCategory(activeCategory).length }}</p>
        </div>

        <!-- Manual Reports Sub-Tab -->
        <div v-if="activeSubTab === 'manual'">
            <div class="mb-6">
                <button
                    @click="showCreateModal = true"
                    class="px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition-colors inline-flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Report
                </button>
            </div>

            <div v-if="getManualReportsByCategory(activeCategory).length === 0" class="bg-white rounded-lg border border-gray-200 p-8 text-center">
                <p class="text-gray-600">No manual reports created for this category yet.</p>
            </div>

            <div v-else class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Report Name</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Date Range</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Created Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Created By</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="report in getManualReportsByCategory(activeCategory)" :key="report.id" class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ report.name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ report.dateRange.start }} to {{ report.dateRange.end }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ report.createdDate }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ auth.user?.name || 'Current User' }}</td>
                            <td class="px-6 py-4 text-center">
                                <span :class="['inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold', report.status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800']">
                                    {{ report.status.charAt(0).toUpperCase() + report.status.slice(1) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-4">
                                    <button @click="navigateToPreview(report)" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">View</button>
                                    <button class="text-blue-600 hover:text-blue-700 font-semibold text-sm">Download</button>
                                    <button class="text-blue-600 hover:text-blue-700 font-semibold text-sm">Print</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-sm text-gray-600 mt-4">Total Reports: {{ getManualReportsByCategory(activeCategory).length }}</p>
        </div>
    </div>

    <!-- Create Manual Report Modal (Category-aware) -->
    <CreateFinancialReportModal
        v-if="showCreateModal"
        :category="activeCategory"
        @close="showCreateModal = false"
        @create="handleCreateReport"
    />
    
</template>

<script setup>
import { onMounted, ref } from 'vue'
import CreateFinancialReportModal from '@/components/FRM/Reports/CreateFinancialReportModal.vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

// Report categories
const categories = [
    'Budget Analysis',
    'Cost Breakdown',
    'Variance Analysis',
    'Cash Flow',
    'Project Financial Summary',
    'Accounts Payable',
    'Accounts Receivable',
    'General Ledger'
]

// Active tab state
const activeCategory = ref('Budget Analysis')
const activeSubTab = ref('generated')
const showCreateModal = ref(false)

// Manual reports storage (each is individual, not part of a "run")
const manualReports = ref([
    {
        id: 1,
        name: 'January 2026 Budget Analysis',
        category: 'Budget Analysis',
        dateRange: { start: '2026-01-01', end: '2026-01-31' },
        projects: ['All'],
        groupBy: 'category',
        includeCharts: true,
        createdDate: '2026-02-01',
        status: 'draft'
    },
    {
        id: 2,
        name: 'Q1 2026 Variance Report',
        category: 'Variance Analysis',
        dateRange: { start: '2026-01-01', end: '2026-03-31' },
        projects: ['All'],
        groupBy: 'project',
        includeCharts: true,
        createdDate: '2026-02-01',
        status: 'draft'
    },
    {
        id: 3,
        name: 'January 2026 Cash Flow',
        category: 'Cash Flow',
        dateRange: { start: '2026-01-01', end: '2026-01-31' },
        projects: ['All'],
        groupBy: 'none',
        includeCharts: true,
        createdDate: '2026-02-01',
        status: 'draft'
    }
])

// Get manual reports filtered by category
const getManualReportsByCategory = (category) => {
    return manualReports.value.filter(r => r.category === category)
}

// Get generated reports filtered by category
const getGeneratedReportsByCategory = (category) => {
    const allReports = [
        { id: 1, name: 'Budget Analysis', category: 'Budget Analysis', description: 'Monitor budget utilization across all projects', dateRange: { start: '2026-01-01', end: '2026-01-31' }, frequency: 'Monthly', lastGenerated: '2026-02-01' },
        { id: 2, name: 'Cost Breakdown', category: 'Cost Breakdown', description: 'Detailed breakdown of project expenses', dateRange: { start: '2026-01-01', end: '2026-01-31' }, frequency: 'Monthly', lastGenerated: '2026-02-01' },
        { id: 3, name: 'Variance Report', category: 'Variance Analysis', description: 'Track cost and schedule variances', dateRange: { start: '2026-01-01', end: '2026-02-03' }, frequency: 'Weekly', lastGenerated: '2026-02-03' },
        { id: 4, name: 'Cash Flow Analysis', category: 'Cash Flow', description: 'Track inflows and outflows', dateRange: { start: '2026-01-01', end: '2026-02-03' }, frequency: 'Weekly', lastGenerated: '2026-02-03' },
        { id: 5, name: 'Project Summary', category: 'Project Financial Summary', description: 'Comprehensive project financial overview', dateRange: { start: '2026-01-01', end: '2026-01-31' }, frequency: 'Monthly', lastGenerated: '2026-02-01' },
        { id: 6, name: 'Accounts Payable', category: 'Accounts Payable', description: 'AP aging and payment tracking', dateRange: { start: '2026-01-01', end: '2026-02-03' }, frequency: 'Weekly', lastGenerated: '2026-02-03' },
        { id: 7, name: 'Accounts Receivable', category: 'Accounts Receivable', description: 'AR aging and collection status', dateRange: { start: '2026-01-01', end: '2026-02-03' }, frequency: 'Weekly', lastGenerated: '2026-02-03' },
        { id: 8, name: 'General Ledger', category: 'General Ledger', description: 'GL account balances and reconciliation', dateRange: { start: '2026-01-01', end: '2026-01-31' }, frequency: 'Monthly', lastGenerated: '2026-02-01' },
    ]
    return allReports.filter(r => r.category === category)
}

// Navigate to preview page with individual report
const navigateToPreview = (report) => {
    sessionStorage.setItem('currentReportData', JSON.stringify(report))
    router.push({ name: 'financial_report_preview', params: { reportId: report.id } })
}

// Preview generated report (from FinancialGeneratedReportsByCategory)
const previewReport = (report) => {
    sessionStorage.setItem('currentReportData', JSON.stringify(report))
    router.push({ name: 'financial_report_preview', params: { reportId: report.id } })
}

// Handle creating new manual report
const handleCreateReport = (formData) => {
    const newReport = {
        id: Math.max(...manualReports.value.map(r => r.id), 0) + 1,
        ...formData,
        createdDate: new Date().toISOString().split('T')[0],
        status: 'draft'
    }
    manualReports.value.push(newReport)
    sessionStorage.setItem('currentReportData', JSON.stringify(newReport))
    showCreateModal.value = false
    router.push({ name: 'financial_report_preview', params: { reportId: newReport.id } })
}

onMounted(() => {
    auth.pageTitle = 'Financial Reports'
})  
</script>
