<template>
    <div class="mb-6 flex items-center justify-end">
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

    <div class="bg-white border-b border-gray-200 rounded-t-lg mb-0">
        <nav class="flex gap-0 px-6 py-0 overflow-x-auto">
            <button
                v-for="category in categories"
                :key="category"
                @click="activeCategory = category"
                class="flex items-center gap-2 px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap"
                :class="activeCategory === category
                    ? 'border-[#0c8ce9] text-[#0c8ce9]'
                    : 'border-transparent text-gray-600 hover:text-gray-900'"
            >
                {{ category }}
            </button>
        </nav>
    </div>

    <div class="bg-white pt-0 mb-0">
        <div class="p-6">
            <GeneratedReportsByCategory
                :category="activeCategory"
                @preview="previewReport"
            />
        </div>
    </div>

    <!-- Create Manual Report Modal (Category-aware) -->
    <CreateManualReportModal
        v-if="showCreateModal"
        :category="activeCategory"
        @close="showCreateModal = false"
        @create="handleCreateReport"
    />
    
</template>

<script setup>
import { onMounted, ref } from 'vue'
import GeneratedReportsByCategory from '@/components/HR/Reports/GeneratedReportsByCategory.vue'
import CreateManualReportModal from '@/components/HR/Reports/CreateManualReportModal.vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

// Report categories
const categories = [
    'Attendance',
    'Disciplinary',
    'Leave & Absence',
    'Payroll',
    'Other System Reports'
]

// Active tab state
const activeCategory = ref('Attendance')
const showCreateModal = ref(false)

// Manual reports storage (each is individual, not part of a "run")
const manualReports = ref([
    {
        id: 1,
        name: 'January 2026 Attendance Summary',
        category: 'Attendance',
        dateRange: { start: '2026-01-01', end: '2026-01-31' },
        departments: ['IT', 'HR', 'Finance'],
        employee: '',
        groupBy: 'department',
        includeCharts: true,
        createdDate: '2026-02-01',
        status: 'draft'
    },
    {
        id: 2,
        name: 'Q1 2026 Payroll Analysis',
        category: 'Payroll',
        dateRange: { start: '2026-01-01', end: '2026-03-31' },
        departments: ['All'],
        employee: '',
        groupBy: 'department',
        includeCharts: true,
        createdDate: '2026-02-01',
        status: 'draft'
    },
    {
        id: 3,
        name: 'Leave Requests - January 2026',
        category: 'Leave & Absence',
        dateRange: { start: '2026-01-01', end: '2026-01-31' },
        departments: ['All'],
        employee: '',
        groupBy: 'none',
        includeCharts: true,
        createdDate: '2026-02-01',
        status: 'draft'
    }
])

// Preview generated report (from GeneratedReportsByCategory)
const previewReport = (report) => {
    sessionStorage.setItem('currentReportData', JSON.stringify(report))
    router.push({ name: 'hr_report_preview', params: { reportId: report.id } })
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
    router.push({ name: 'hr_report_preview', params: { reportId: newReport.id } })
}

onMounted(() => {
    auth.pageTitle = 'Reports'
})  
</script>
