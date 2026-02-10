<template>
    <auth-layout />
        <div class="ps-60 bg-[#F3F4F6] min-h-screen">
            <div class="px-10 py-10">
                <div class="min-h-screen bg-gray-50">
                    <!-- Header -->
                    <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
                        <div class="flex items-center gap-4">
                            <button @click="goBack" class="text-gray-600 hover:text-gray-900">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ reportTitle }}</h1>
                                <p class="text-sm text-gray-600">{{ dateRangeText }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">PDF</button>
                            <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">Excel</button>
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">CSV</button>
                            <button class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">Print</button>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="p-6 space-y-6">
                        <!-- Key Metrics -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div v-for="metric in keyMetrics" :key="metric.id" class="bg-white rounded-lg border border-gray-200 p-6">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600 font-medium">{{ metric.label }}</p>
                                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ metric.value }}</p>
                                        <p class="text-sm mt-2" :class="metric.trend >= 0 ? 'text-green-600' : 'text-red-600'">
                                            {{ metric.trend >= 0 ? '+' : '' }}{{ metric.trend }}% last period
                                        </p>
                                    </div>
                                    <div class="text-gray-400" v-if="metric.icon">
                                        <svg v-if="metric.icon === 'budget'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <svg v-else-if="metric.icon === 'spent'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        <svg v-else-if="metric.icon === 'remaining'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <svg v-else-if="metric.icon === 'utilization'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Section -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-white rounded-lg border border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6">Trend Analysis</h3>
                                <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg border border-gray-100">
                                    <div class="text-center">
                                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        <p class="text-gray-500 text-sm">Chart visualization will render here</p>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-3">Chart.js / ApexCharts integration ready</p>
                            </div>

                            <div class="bg-white rounded-lg border border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6">Category Breakdown</h3>
                                <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg border border-gray-100">
                                    <div class="text-center">
                                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        <p class="text-gray-500 text-sm">Chart visualization will render here</p>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-3">Chart.js / ApexCharts integration ready</p>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
                                <button class="text-blue-600 hover:text-blue-700 text-sm font-semibold">Clear All</button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <!-- Date Range Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                                    <input type="text" placeholder="Start - End" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <!-- Project Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option>All Projects</option>
                                        <option>Project A</option>
                                        <option>Project B</option>
                                    </select>
                                </div>

                                <!-- Department Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option>All Departments</option>
                                        <option>Finance</option>
                                        <option>Operations</option>
                                    </select>
                                </div>

                                <!-- Status Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option>All Status</option>
                                        <option>Active</option>
                                        <option>Completed</option>
                                    </select>
                                </div>

                                <!-- Group By Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Group By</label>
                                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option>None</option>
                                        <option>Category</option>
                                        <option>Project</option>
                                    </select>
                                </div>

                                <!-- Sort By Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option>Date (Newest)</option>
                                        <option>Date (Oldest)</option>
                                        <option>Amount (High to Low)</option>
                                        <option>Amount (Low to High)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Data Table -->
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-200">
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Date</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Description</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Category</th>
                                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">Amount</th>
                                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="row in tableData" :key="row.id" class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ row.date }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ row.description }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ row.category }}</td>
                                        <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">{{ formatCurrency(row.amount) }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span :class="['inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold', row.statusClass]">
                                                {{ row.status }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// Get report data from session storage
const reportData = ref(JSON.parse(sessionStorage.getItem('currentReportData') || '{}'))

const reportTitle = computed(() => reportData.value.name || 'Financial Report')
const dateRangeText = computed(() => {
    // Handle both manual reports with dateRange object and generated reports with dateRange object
    if (reportData.value.dateRange) {
        return `${reportData.value.dateRange.start || 'N/A'} to ${reportData.value.dateRange.end || 'N/A'}`
    }
    // Fallback to fromDate/toDate if using old format
    return `${reportData.value.fromDate || 'N/A'} to ${reportData.value.toDate || 'N/A'}`
})

// Key Metrics - These would be computed from actual data
const keyMetrics = ref([
    { id: 1, label: 'Total Budget', value: '$2,450,000', trend: 5, icon: 'budget' },
    { id: 2, label: 'Total Spent', value: '$1,875,320', trend: -3, icon: 'spent' },
    { id: 3, label: 'Remaining Budget', value: '$574,680', trend: 8, icon: 'remaining' },
    { id: 4, label: 'Budget Utilization', value: '76.5%', trend: 2, icon: 'utilization' },
])

// Sample table data - Would be replaced with actual report data
const tableData = ref([
    { id: 1, date: '2026-02-01', description: 'Material Procurement', category: 'Direct Cost', amount: 45000, status: 'Completed', statusClass: 'bg-green-100 text-green-800' },
    { id: 2, date: '2026-02-02', description: 'Labor Cost - Month 1', category: 'Direct Cost', amount: 65000, status: 'Completed', statusClass: 'bg-green-100 text-green-800' },
    { id: 3, date: '2026-02-03', description: 'Equipment Rental', category: 'Equipment', amount: 12500, status: 'Pending', statusClass: 'bg-yellow-100 text-yellow-800' },
    { id: 4, date: '2026-02-04', description: 'Subcontractor Payment', category: 'Subcontractor', amount: 85000, status: 'Completed', statusClass: 'bg-green-100 text-green-800' },
    { id: 5, date: '2026-02-05', description: 'Miscellaneous Expenses', category: 'Overhead', amount: 8920, status: 'Completed', statusClass: 'bg-green-100 text-green-800' },
])

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount)
}

const goBack = () => {
    router.back()
}
</script>
