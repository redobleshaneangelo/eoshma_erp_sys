<template>
    <auth-layout />
        <div class="ps-60 bg-[#F3F4F6] min-h-screen w-full">
            <div class="w-full px-10 py-10">
                <!-- Page Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <button
                            @click="goBack"
                            class="p-2 hover:bg-gray-200 rounded-lg transition-colors"
                        >
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <div>
                            <h1 class="text-3xl font-bold text-[#333333]">{{ reportData.name }}</h1>
                            <p class="text-sm text-gray-600 mt-1">{{ reportData.category }} • {{ reportData.dateRange.start }} to {{ reportData.dateRange.end }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            @click="exportReport('pdf')"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg font-semibold text-sm hover:bg-red-700 transition-colors"
                        >
                            PDF
                        </button>
                        <button
                            @click="exportReport('excel')"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg font-semibold text-sm hover:bg-green-700 transition-colors"
                        >
                            Excel
                        </button>
                        <button
                            @click="exportReport('csv')"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition-colors"
                        >
                            CSV
                        </button>
                        <button
                            @click="printReport"
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg font-semibold text-sm hover:bg-gray-700 transition-colors"
                        >
                            Print
                        </button>
                    </div>
                </div>

                <!-- Key Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div v-for="(metric, index) in categoryMetrics" :key="index" class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-2">{{ metric.label }}</p>
                                <p class="text-3xl font-bold text-[#0c8ce9]">{{ metric.value }}</p>
                            </div>
                            <div class="text-4xl text-gray-300">{{ metric.icon }}</div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2" v-if="metric.change">
                            <span :class="metric.change >= 0 ? 'text-green-600' : 'text-red-600'">
                                {{ metric.change >= 0 ? '+' : '' }}{{ metric.change }}%
                            </span>
                            vs last period
                        </p>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ chartTitles[0] }}</h3>
                        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                            <div class="text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <p class="text-sm">Chart visualization will render here</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-3 text-center">Chart.js / ApexCharts integration ready</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ chartTitles[1] }}</h3>
                        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                            <div class="text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <p class="text-sm">Chart visualization will render here</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-3 text-center">Chart.js / ApexCharts integration ready</p>
                    </div>
                </div>
                
                <!-- Global Filter Panel -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Filters</h3>
                        <button
                            @click="clearAllFilters"
                            class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                        >
                            Clear All
                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Date Range -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                            <input
                                v-model="filters.dateRange"
                                type="text"
                                placeholder="Start - End"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>

                        <!-- Department -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <select
                                v-model="filters.department"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">All Departments</option>
                                <option value="IT">IT</option>
                                <option value="HR">HR</option>
                                <option value="Finance">Finance</option>
                                <option value="Operations">Operations</option>
                            </select>
                        </div>

                        <!-- Employee -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
                            <input
                                v-model="filters.employee"
                                type="text"
                                placeholder="Employee Name"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select
                                v-model="filters.status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>

                        <!-- Employment Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Employment Type</label>
                            <select
                                v-model="filters.employmentType"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">All Types</option>
                                <option value="permanent">Permanent</option>
                                <option value="contract">Contract</option>
                                <option value="temporary">Temporary</option>
                            </select>
                        </div>

                        <!-- Location -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <select
                                v-model="filters.location"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">All Locations</option>
                                <option value="Headquarters">Headquarters</option>
                                <option value="Branch 1">Branch 1</option>
                                <option value="Branch 2">Branch 2</option>
                            </select>
                        </div>

                        <!-- Group By -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Group By</label>
                            <select
                                v-model="filters.groupBy"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">None</option>
                                <option value="department">Department</option>
                                <option value="status">Status</option>
                                <option value="employee">Employee</option>
                            </select>
                        </div>

                        <!-- Sort By -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                            <select
                                v-model="filters.sortBy"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="date">Date (Newest)</option>
                                <option value="name">Name (A-Z)</option>
                                <option value="department">Department</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200 sticky top-0">
                                <tr>
                                    <th v-for="(col, index) in tableColumns" :key="index" class="px-6 py-4 text-left font-semibold text-gray-700">
                                        {{ col }}
                                    </th>
                                    <th class="px-6 py-4 text-left font-semibold text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, index) in paginatedTableData" :key="index" class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                    <td v-for="(col, colIndex) in tableColumns" :key="colIndex" class="px-6 py-4 text-gray-800">
                                        <template v-if="col === 'Status'">
                                            <span :class="[
                                                'inline-block px-3 py-1 rounded-full text-xs font-semibold',
                                                row[col.toLowerCase()] === 'active' ? 'bg-green-100 text-green-800' :
                                                row[col.toLowerCase()] === 'inactive' ? 'bg-red-100 text-red-800' :
                                                'bg-yellow-100 text-yellow-800'
                                            ]">
                                                {{ row[col.toLowerCase()] }}
                                            </span>
                                        </template>
                                        <template v-else>
                                            {{ row[col.toLowerCase()] || '-' }}
                                        </template>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">View</button>
                                            <button class="text-gray-600 hover:text-gray-800 text-sm font-medium">Download</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals Row -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                        <span class="text-sm font-semibold text-gray-800">Total Records: <span class="text-[#0c8ce9]">{{ tableData.length }}</span></span>
                        <div class="flex items-center gap-2">
                            <button
                                @click="previousPage"
                                :disabled="currentPage === 1"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Previous
                            </button>
                            <span class="text-sm text-gray-600">Page {{ currentPage }} of {{ totalPages }}</span>
                            <button
                                @click="nextPage"
                                :disabled="currentPage === totalPages"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
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
import html2pdf from 'html2pdf.js'

const router = useRouter()
const currentPage = ref(1)
const itemsPerPage = 10

// Report data from sessionStorage
const reportData = ref({
    id: 1,
    name: 'Attendance Report - Q1 2024',
    category: 'Attendance',
    dateRange: { start: '2024-01-01', end: '2024-03-31' },
    department: 'IT',
    generatedDate: '2024-04-15',
    generatedBy: 'Admin User'
})

// Global Filters
const filters = ref({
    dateRange: '',
    department: '',
    employee: '',
    status: '',
    employmentType: '',
    location: '',
    groupBy: '',
    sortBy: 'date'
})

// Category-specific metrics
const categoryMetricsMap = {
    'Attendance': [
        { label: 'Total Present', value: '1,245', icon: '✓', change: 5 },
        { label: 'Total Absent', value: '89', icon: '✗', change: -3 },
        { label: 'Late Arrivals', value: '156', icon: '⏰', change: 2 },
        { label: 'On Leave', value: '234', icon: '📅', change: 8 }
    ],
    'Mobilization': [
        { label: 'Internal Transfers', value: '12', icon: '→', change: 4 },
        { label: 'External Assignments', value: '8', icon: '🌍', change: 1 },
        { label: 'Pending Approvals', value: '5', icon: '⏳', change: -2 },
        { label: 'Completed', value: '34', icon: '✓', change: 6 }
    ],
    'Disciplinary': [
        { label: 'Verbal Warnings', value: '23', icon: '⚠️', change: -1 },
        { label: 'Written Warnings', value: '15', icon: '📝', change: 3 },
        { label: 'Suspensions', value: '3', icon: '🚫', change: 0 },
        { label: 'Resolved Cases', value: '41', icon: '✓', change: 5 }
    ],
    'Leave & Absence': [
        { label: 'Annual Leave', value: '342', icon: '🏖️', change: -8 },
        { label: 'Sick Leave', value: '78', icon: '🏥', change: 2 },
        { label: 'Personal Leave', value: '156', icon: '👤', change: 1 },
        { label: 'Pending Approvals', value: '12', icon: '⏳', change: 4 }
    ],
    'Payroll': [
        { label: 'Total Payroll', value: '₱2.5M', icon: '💰', change: 3 },
        { label: 'Deductions', value: '₱185K', icon: '📉', change: 2 },
        { label: 'Bonuses Paid', value: '₱450K', icon: '🎁', change: 5 },
        { label: 'Pending Approvals', value: '8', icon: '⏳', change: -1 }
    ]
}

const chartTitlesMap = {
    'Attendance': ['Attendance Trend (Daily)', 'Department-wise Attendance'],
    'Mobilization': ['Mobilization Status', 'Transfer Timeline'],
    'Disciplinary': ['Case Resolution Trend', 'Violation Categories'],
    'Leave & Absence': ['Leave Type Distribution', 'Monthly Leave Trend'],
    'Payroll': ['Payroll Distribution', 'Deduction Analysis']
}

const tableColumnsMap = {
    'Attendance': ['Employee', 'Department', 'Status', 'Date', 'Remarks'],
    'Mobilization': ['Employee', 'Type', 'From', 'To', 'Status', 'Date'],
    'Disciplinary': ['Employee', 'Case Type', 'Date', 'Status', 'Resolution'],
    'Leave & Absence': ['Employee', 'Leave Type', 'Start Date', 'End Date', 'Status'],
    'Payroll': ['Employee', 'Gross', 'Deductions', 'Net', 'Status']
}

const mockTableDataMap = {
    'Attendance': [
        { employee: 'John Doe', department: 'IT', status: 'active', date: '2024-04-15', remarks: 'Present' },
        { employee: 'Jane Smith', department: 'HR', status: 'active', date: '2024-04-15', remarks: 'Absent' },
        { employee: 'Mike Johnson', department: 'Finance', status: 'active', date: '2024-04-15', remarks: 'Late' },
        { employee: 'Sarah Williams', department: 'IT', status: 'active', date: '2024-04-15', remarks: 'Present' },
        { employee: 'Robert Brown', department: 'Operations', status: 'inactive', date: '2024-04-15', remarks: 'On Leave' },
        { employee: 'Emily Davis', department: 'IT', status: 'active', date: '2024-04-15', remarks: 'Present' },
        { employee: 'David Garcia', department: 'HR', status: 'active', date: '2024-04-15', remarks: 'Present' },
        { employee: 'Lisa Anderson', department: 'Finance', status: 'active', date: '2024-04-15', remarks: 'Late' },
        { employee: 'James Martin', department: 'Operations', status: 'active', date: '2024-04-15', remarks: 'Present' },
        { employee: 'Patricia Thomas', department: 'IT', status: 'active', date: '2024-04-15', remarks: 'Absent' },
        { employee: 'Christopher Lee', department: 'Finance', status: 'active', date: '2024-04-15', remarks: 'Present' },
        { employee: 'Jennifer White', department: 'HR', status: 'active', date: '2024-04-15', remarks: 'Present' }
    ],
    'Mobilization': [
        { employee: 'John Doe', type: 'Internal Transfer', from: 'IT', to: 'Finance', status: 'active', date: '2024-04-10' },
        { employee: 'Jane Smith', type: 'External Assignment', from: 'HR', to: 'Branch 1', status: 'active', date: '2024-04-12' },
        { employee: 'Mike Johnson', type: 'Promotion', from: 'Finance', to: 'Finance - Senior', status: 'active', date: '2024-04-08' },
        { employee: 'Sarah Williams', type: 'Reassignment', from: 'IT', to: 'IT - New Team', status: 'inactive', date: '2024-04-14' },
        { employee: 'Robert Brown', type: 'Internal Transfer', from: 'Operations', to: 'IT', status: 'active', date: '2024-04-09' },
        { employee: 'Emily Davis', type: 'External Assignment', from: 'IT', to: 'Branch 2', status: 'active', date: '2024-04-11' },
        { employee: 'David Garcia', type: 'Promotion', from: 'HR', to: 'HR - Senior', status: 'active', date: '2024-04-13' },
        { employee: 'Lisa Anderson', type: 'Internal Transfer', from: 'Finance', to: 'Operations', status: 'active', date: '2024-04-07' },
        { employee: 'James Martin', type: 'External Assignment', from: 'Operations', to: 'Corporate', status: 'inactive', date: '2024-04-15' },
        { employee: 'Patricia Thomas', type: 'Reassignment', from: 'IT', to: 'HR', status: 'active', date: '2024-04-06' }
    ],
    'Disciplinary': [
        { employee: 'John Doe', casetype: 'Verbal Warning', date: '2024-04-10', status: 'active', resolution: 'Improvement noted' },
        { employee: 'Jane Smith', casetype: 'Written Warning', date: '2024-04-12', status: 'active', resolution: 'Pending' },
        { employee: 'Mike Johnson', casetype: 'Suspension', date: '2024-04-08', status: 'inactive', resolution: 'Resolved' },
        { employee: 'Sarah Williams', casetype: 'Verbal Warning', date: '2024-04-14', status: 'active', resolution: 'Under review' },
        { employee: 'Robert Brown', casetype: 'Written Warning', date: '2024-04-09', status: 'active', resolution: 'Closed' },
        { employee: 'Emily Davis', casetype: 'Verbal Warning', date: '2024-04-11', status: 'active', resolution: 'Pending' },
        { employee: 'David Garcia', casetype: 'Termination', date: '2024-04-13', status: 'inactive', resolution: 'Finalized' },
        { employee: 'Lisa Anderson', casetype: 'Written Warning', date: '2024-04-07', status: 'active', resolution: 'Resolved' },
        { employee: 'James Martin', casetype: 'Verbal Warning', date: '2024-04-15', status: 'active', resolution: 'Pending review' },
        { employee: 'Patricia Thomas', casetype: 'Written Warning', date: '2024-04-06', status: 'active', resolution: 'Closed' }
    ],
    'Leave & Absence': [
        { employee: 'John Doe', leavetype: 'Annual Leave', startdate: '2024-04-01', enddate: '2024-04-05', status: 'active' },
        { employee: 'Jane Smith', leavetype: 'Sick Leave', startdate: '2024-04-15', enddate: '2024-04-15', status: 'active' },
        { employee: 'Mike Johnson', leavetype: 'Personal Leave', startdate: '2024-04-10', enddate: '2024-04-10', status: 'inactive' },
        { employee: 'Sarah Williams', leavetype: 'Annual Leave', startdate: '2024-04-20', enddate: '2024-04-24', status: 'active' },
        { employee: 'Robert Brown', leavetype: 'Maternity Leave', startdate: '2024-03-15', enddate: '2024-05-15', status: 'active' },
        { employee: 'Emily Davis', leavetype: 'Sick Leave', startdate: '2024-04-08', enddate: '2024-04-09', status: 'inactive' },
        { employee: 'David Garcia', leavetype: 'Annual Leave', startdate: '2024-04-25', enddate: '2024-05-02', status: 'active' },
        { employee: 'Lisa Anderson', leavetype: 'Personal Leave', startdate: '2024-04-12', enddate: '2024-04-12', status: 'active' },
        { employee: 'James Martin', leavetype: 'Annual Leave', startdate: '2024-04-16', enddate: '2024-04-18', status: 'inactive' },
        { employee: 'Patricia Thomas', leavetype: 'Sick Leave', startdate: '2024-04-14', enddate: '2024-04-14', status: 'active' }
    ],
    'Payroll': [
        { employee: 'John Doe', gross: '₱45,000', deductions: '₱8,500', net: '₱36,500', status: 'active' },
        { employee: 'Jane Smith', gross: '₱52,000', deductions: '₱9,200', net: '₱42,800', status: 'active' },
        { employee: 'Mike Johnson', gross: '₱48,500', deductions: '₱8,900', net: '₱39,600', status: 'active' },
        { employee: 'Sarah Williams', gross: '₱51,000', deductions: '₱9,100', net: '₱41,900', status: 'inactive' },
        { employee: 'Robert Brown', gross: '₱46,000', deductions: '₱8,600', net: '₱37,400', status: 'active' },
        { employee: 'Emily Davis', gross: '₱49,000', deductions: '₱9,000', net: '₱40,000', status: 'active' },
        { employee: 'David Garcia', gross: '₱50,500', deductions: '₱9,050', net: '₱41,450', status: 'active' },
        { employee: 'Lisa Anderson', gross: '₱47,000', deductions: '₱8,700', net: '₱38,300', status: 'active' },
        { employee: 'James Martin', gross: '₱53,000', deductions: '₱9,300', net: '₱43,700', status: 'active' },
        { employee: 'Patricia Thomas', gross: '₱44,000', deductions: '₱8,200', net: '₱35,800', status: 'inactive' }
    ]
}

// Load data from sessionStorage if available
onMounted(() => {
    const savedData = sessionStorage.getItem('currentReportData')
    if (savedData) {
        reportData.value = JSON.parse(savedData)
    }
})

// Computed properties
const categoryMetrics = computed(() => {
    return categoryMetricsMap[reportData.value.category] || categoryMetricsMap['Attendance']
})

const chartTitles = computed(() => {
    return chartTitlesMap[reportData.value.category] || chartTitlesMap['Attendance']
})

const tableColumns = computed(() => {
    return tableColumnsMap[reportData.value.category] || tableColumnsMap['Attendance']
})

const tableData = computed(() => {
    return mockTableDataMap[reportData.value.category] || mockTableDataMap['Attendance']
})

const totalPages = computed(() => {
    return Math.ceil(tableData.value.length / itemsPerPage)
})

const paginatedTableData = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage
    const end = start + itemsPerPage
    return tableData.value.slice(start, end)
})

// Methods
const goBack = () => {
    router.back()
}

const exportReport = async (format) => {
    if (format === 'pdf') {
        await exportPDF()
    } else if (format === 'excel') {
        exportExcel()
    } else if (format === 'csv') {
        exportCSV()
    }
}

const exportPDF = async () => {
    try {
        // Create a focused HTML structure for PDF export
        const pdfContent = document.createElement('div')
        pdfContent.style.padding = '20px'
        pdfContent.style.fontFamily = 'Arial, sans-serif'
        
        // Add title and report info
        const titleSection = document.createElement('div')
        titleSection.innerHTML = `
            <h1 style="font-size: 24px; font-weight: bold; margin: 0 0 10px 0; color: #333;">${reportData.value.name}</h1>
            <p style="margin: 0 0 20px 0; color: #666; font-size: 12px;">
                ${reportData.value.category} | ${reportData.value.dateRange.start} to ${reportData.value.dateRange.end}
            </p>
        `
        pdfContent.appendChild(titleSection)
        
        // Add key metrics
        const metricsSection = document.createElement('div')
        metricsSection.innerHTML = '<h2 style="font-size: 16px; font-weight: bold; margin: 20px 0 10px 0; border-bottom: 2px solid #0c8ce9; padding-bottom: 5px;">Key Metrics</h2>'
        const metricsTable = document.createElement('table')
        metricsTable.style.width = '100%'
        metricsTable.style.borderCollapse = 'collapse'
        metricsTable.style.marginBottom = '20px'
        
        categoryMetrics.value.forEach(metric => {
            const row = metricsTable.insertRow()
            row.style.borderBottom = '1px solid #ddd'
            row.innerHTML = `
                <td style="padding: 8px; font-weight: bold; width: 25%;">${metric.label}</td>
                <td style="padding: 8px; color: #0c8ce9; font-weight: bold; width: 25%;">${metric.value}</td>
                <td style="padding: 8px; color: ${metric.change >= 0 ? '#22c55e' : '#ef4444'}; width: 50%;">
                    ${metric.change >= 0 ? '+' : ''}${metric.change}% vs last period
                </td>
            `
        })
        metricsSection.appendChild(metricsTable)
        pdfContent.appendChild(metricsSection)
        
        // Add report data table
        const tableSection = document.createElement('div')
        tableSection.innerHTML = '<h2 style="font-size: 16px; font-weight: bold; margin: 20px 0 10px 0; border-bottom: 2px solid #0c8ce9; padding-bottom: 5px;">Report Data</h2>'
        const dataTable = document.createElement('table')
        dataTable.style.width = '100%'
        dataTable.style.borderCollapse = 'collapse'
        dataTable.style.fontSize = '11px'
        
        // Add table header
        const headerRow = dataTable.insertRow()
        headerRow.style.backgroundColor = '#f3f4f6'
        headerRow.style.fontWeight = 'bold'
        tableColumns.value.forEach(col => {
            const th = document.createElement('th')
            th.textContent = col
            th.style.padding = '8px'
            th.style.border = '1px solid #ddd'
            th.style.textAlign = 'left'
            headerRow.appendChild(th)
        })
        
        // Add table data rows
        tableData.value.forEach((row, index) => {
            const dataRow = dataTable.insertRow()
            dataRow.style.backgroundColor = index % 2 === 0 ? '#ffffff' : '#f9fafb'
            dataRow.style.borderBottom = '1px solid #ddd'
            tableColumns.value.forEach(col => {
                const td = document.createElement('td')
                td.textContent = row[col.toLowerCase()] || '-'
                td.style.padding = '8px'
                td.style.border = '1px solid #ddd'
                dataRow.appendChild(td)
            })
        })
        
        // Add totals row
        const totalsRow = dataTable.insertRow()
        totalsRow.style.backgroundColor = '#f3f4f6'
        totalsRow.style.fontWeight = 'bold'
        const totalCell = totalsRow.insertCell()
        totalCell.colSpan = tableColumns.value.length
        totalCell.textContent = `Total Records: ${tableData.value.length}`
        totalCell.style.padding = '8px'
        totalCell.style.border = '1px solid #ddd'
        
        tableSection.appendChild(dataTable)
        pdfContent.appendChild(tableSection)
        
        // Add footer
        const footerSection = document.createElement('div')
        footerSection.innerHTML = `
            <div style="margin-top: 30px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 11px; color: #666;">
                <p style="margin: 0;">Generated by: ${reportData.value.generatedBy}</p>
                <p style="margin: 0;">Generated on: ${new Date().toLocaleString()}</p>
            </div>
        `
        pdfContent.appendChild(footerSection)
        
        const opt = {
            margin: 10,
            filename: `${reportData.value.name.replace(/\s+/g, '_')}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { orientation: 'portrait', unit: 'mm', format: 'a4' }
        }
        
        html2pdf().set(opt).from(pdfContent).save()
        console.log('PDF exported successfully')
    } catch (error) {
        console.log('PDF export error:', error)
        window.print()
    }
}

const exportExcel = () => {
    try {
        // Create a new workbook structure
        let excelContent = `
        <table border="1">
            <tr>
                <td colspan="4" style="font-weight: bold; font-size: 14px; text-align: center; padding: 10px;">
                    ${reportData.value.name}
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding: 8px;">
                    Category: ${reportData.value.category} | Date Range: ${reportData.value.dateRange.start} to ${reportData.value.dateRange.end}
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding: 8px; font-weight: bold;">
                    Key Metrics
                </td>
            </tr>
        `
        
        // Add metrics data
        categoryMetrics.value.forEach((metric) => {
            excelContent += `
            <tr>
                <td style="padding: 8px;">${metric.label}</td>
                <td style="padding: 8px; font-weight: bold;">${metric.value}</td>
                <td style="padding: 8px;">${metric.change ? metric.change + '%' : 'N/A'}</td>
                <td style="padding: 8px;"></td>
            </tr>
            `
        })
        
        excelContent += `
            <tr>
                <td colspan="4" style="padding: 8px; font-weight: bold;">
                    Report Data
                </td>
            </tr>
            <tr style="background-color: #f0f0f0; font-weight: bold;">
        `
        
        // Add table headers
        tableColumns.value.forEach(col => {
            excelContent += `<td style="padding: 8px;">${col}</td>`
        })
        excelContent += `<td style="padding: 8px;">Actions</td></tr>`
        
        // Add table data
        tableData.value.forEach(row => {
            excelContent += '<tr>'
            tableColumns.value.forEach(col => {
                excelContent += `<td style="padding: 8px;">${row[col.toLowerCase()] || '-'}</td>`
            })
            excelContent += '<td style="padding: 8px;">View, Download</td></tr>'
        })
        
        excelContent += '</table>'
        
        // Create a blob and download
        const blob = new Blob([excelContent], { type: 'application/vnd.ms-excel' })
        const link = document.createElement('a')
        link.href = URL.createObjectURL(blob)
        link.download = `${reportData.value.name.replace(/\s+/g, '_')}.xls`
        link.click()
        
        console.log('Excel exported successfully')
    } catch (error) {
        alert('Error exporting to Excel: ' + error.message)
    }
}

const exportCSV = () => {
    try {
        let csvContent = 'data:text/csv;charset=utf-8,'
        
        // Add header info
        csvContent += `"${reportData.value.name}"\n`
        csvContent += `"Category: ${reportData.value.category}"\n`
        csvContent += `"Date Range: ${reportData.value.dateRange.start} to ${reportData.value.dateRange.end}"\n\n`
        
        // Add metrics section
        csvContent += '"KEY METRICS"\n'
        categoryMetrics.value.forEach(metric => {
            csvContent += `"${metric.label}","${metric.value}","${metric.change || 'N/A'}"%"\n`
        })
        csvContent += '\n'
        
        // Add table header
        csvContent += '"' + tableColumns.value.join('","') + '","Actions"\n'
        
        // Add table data
        tableData.value.forEach(row => {
            const rowData = tableColumns.value.map(col => {
                const value = row[col.toLowerCase()] || '-'
                return `"${value}"`
            })
            csvContent += rowData.join(',') + ',"View, Download"\n'
        })
        
        const encodedUri = encodeURI(csvContent)
        const link = document.createElement('a')
        link.setAttribute('href', encodedUri)
        link.setAttribute('download', `${reportData.value.name.replace(/\s+/g, '_')}.csv`)
        link.click()
        
        console.log('CSV exported successfully')
    } catch (error) {
        alert('Error exporting to CSV: ' + error.message)
    }
}

const printReport = () => {
    // Add print styles and trigger print
    const printStyle = document.createElement('style')
    printStyle.innerHTML = `
        @media print {
            body { margin: 0; padding: 0; }
            .ps-56\\.25 { margin: 0; padding: 20px; }
            .text-3xl { font-size: 20pt; font-weight: bold; margin: 10px 0; }
            .text-lg { font-size: 14pt; font-weight: bold; margin: 8px 0; }
            .grid { display: table; width: 100%; }
            .grid > div { display: table-cell; border: 1px solid #ddd; padding: 10px; }
            table { width: 100%; border-collapse: collapse; page-break-inside: avoid; }
            th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
            th { background-color: #f0f0f0; font-weight: bold; }
            .bg-white { background-color: white; }
            .bg-gray-50 { background-color: #f9f9f9; }
            .border { border: 1px solid #ddd; }
            button { display: none; }
            .w-full, .flex { display: block !important; width: 100% !important; }
        }
    `
    document.head.appendChild(printStyle)
    
    // Trigger print dialog
    window.print()
    
    // Remove print styles after print
    setTimeout(() => {
        document.head.removeChild(printStyle)
    }, 1000)
}

const clearAllFilters = () => {
    filters.value = {
        dateRange: '',
        department: '',
        employee: '',
        status: '',
        employmentType: '',
        location: '',
        groupBy: '',
        sortBy: 'date'
    }
}

const previousPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--
    }
}

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++
    }
}
</script>
