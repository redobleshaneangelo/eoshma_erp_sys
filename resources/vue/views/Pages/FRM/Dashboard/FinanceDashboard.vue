<template>
    <div class="bg-[#F3F4F6] min-h-screen">
        <div class="">

        <!-- Key Metrics - Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-linear-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-600 text-sm font-medium">Total Active Projects</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ metrics.activeProjects }}</p>
                        <p class="text-blue-500 text-xs mt-1">{{ metrics.projectPercentage }}% increase</p>
                    </div>
                    <svg class="w-8 h-8 text-blue-600 opacity-20" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>

            <div class="bg-linear-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-600 text-sm font-medium">Total Contract Value</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatCurrency(metrics.totalContractValue) }}</p>
                        <p class="text-green-500 text-xs mt-1">{{ metrics.contractCount }} contracts</p>
                    </div>
                    <svg class="w-8 h-8 text-green-600 opacity-20" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <div class="bg-linear-to-br from-orange-50 to-orange-100 border border-orange-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-600 text-sm font-medium">Total Utilized</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatCurrency(metrics.totalUtilized) }}</p>
                        <p class="text-orange-500 text-xs mt-1">{{ metrics.utilizationPercent }}% of budget</p>
                    </div>
                    <svg class="w-8 h-8 text-orange-600 opacity-20" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>

            <div class="bg-linear-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-600 text-sm font-medium">Retention Held</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatCurrency(metrics.totalRetention) }}</p>
                        <p class="text-purple-500 text-xs mt-1">Pending release</p>
                    </div>
                    <svg class="w-8 h-8 text-purple-600 opacity-20" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Quick Stats and Recent Activity -->
        <div class="grid grid-cols-3 gap-6 mb-8">
            <!-- Project Trends Chart -->
            <div class="col-span-2 bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Project Financial Trends</h3>
                <canvas id="projectTrendChart" class="w-full" style="max-height: 250px;"></canvas>
            </div>

            <!-- Budget Status -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Budget Overview</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-700">Overall Utilization</p>
                                <p class="text-sm font-semibold text-gray-900">{{ metrics.utilizationPercent }}%</p>
                            </div>
                            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-linear-to-r from-green-500 to-yellow-500" :style="{ width: metrics.utilizationPercent + '%' }"></div>
                            </div>
                        </div>
                        <div class="pt-4 border-t border-gray-200">
                            <p class="text-xs text-gray-600 mb-3 font-medium">Status Distribution</p>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-600">Active</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">{{ metrics.activeProjects }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-600">Planning</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800">{{ metrics.planningProjects }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-600">Completed</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-800">{{ metrics.completedProjects }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget Breakdown & Contract Distribution Charts -->
        <div class="grid grid-cols-2 gap-6 mb-8">
            <!-- Budget Breakdown Chart -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Budget Breakdown by Category</h3>
                <canvas id="budgetBreakdownChart" class="w-full" style="max-height: 280px;"></canvas>
            </div>

            <!-- Contract Status Distribution -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contract Status Distribution</h3>
                <canvas id="contractStatusChart" class="w-full" style="max-height: 280px;"></canvas>
            </div>
        </div>

        <!-- Pending Approvals & Alerts -->
        <div class="grid grid-cols-2 gap-6">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Pending Approvals</h3>
                </div>
                <div v-if="pendingApprovals.length > 0" class="divide-y divide-gray-200">
                    <div v-for="approval in pendingApprovals" :key="approval.id" class="px-6 py-3 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ approval.title }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ approval.project }} • {{ approval.date }}</p>
                            </div>
                            <span :class="getApprovalStatusClass(approval.type)" class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold">
                                {{ approval.type }}
                            </span>
                        </div>
                    </div>
                </div>
                <div v-else class="px-6 py-8 text-center text-gray-500 text-sm">
                    No pending approvals
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Alerts & Notifications</h3>
                </div>
                <div v-if="alerts.length > 0" class="divide-y divide-gray-200">
                    <div v-for="alert in alerts" :key="alert.id" :class="getAlertClass(alert.severity)" class="px-6 py-3 flex gap-3">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium">{{ alert.title }}</p>
                            <p class="text-xs mt-1">{{ alert.message }}</p>
                        </div>
                    </div>
                </div>
                <div v-else class="px-6 py-8 text-center text-gray-500 text-sm">
                    No alerts
                </div>
            </div>
        </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Chart from 'chart.js/auto'

const metrics = ref({
    activeProjects: 12,
    projectPercentage: 15,
    totalContractValue: 2500000,
    contractCount: 12,
    totalUtilized: 1875000,
    utilizationPercent: 75,
    totalRetention: 125000,
    activeProjects: 8,
    planningProjects: 3,
    completedProjects: 1
})

const chartInstances = ref({
    projectTrend: null,
    budgetBreakdown: null,
    contractStatus: null
})

const projectTrendData = ref({
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    contractValue: [2200000, 2350000, 2400000, 2450000, 2480000, 2500000],
    utilized: [1650000, 1764000, 1800000, 1837000, 1860000, 1875000]
})

const budgetBreakdownData = ref({
    labels: ['Materials', 'Labor', 'Equipment', 'Services', 'Contingency'],
    data: [850000, 625000, 450000, 400000, 175000]
})

const contractStatusData = ref({
    labels: ['Active', 'Planning', 'Completed', 'On Hold'],
    data: [8, 3, 1, 0]
})

const recentProjects = ref([
    {
        id: 1,
        name: 'Metro Tower Development',
        code: 'MTD-001',
        client: 'Urban Development Corp',
        contractAmount: 850000,
        utilizationPercent: 82,
        status: 'Active'
    },
    {
        id: 2,
        name: 'Shopping Complex Expansion',
        code: 'SCE-002',
        client: 'Retail Group Ltd',
        contractAmount: 650000,
        utilizationPercent: 68,
        status: 'Active'
    },
    {
        id: 3,
        name: 'Hospital Wing Renovation',
        code: 'HWR-003',
        client: 'Healthcare Services',
        contractAmount: 520000,
        utilizationPercent: 45,
        status: 'Planning'
    }
])

const pendingApprovals = ref([
    {
        id: 1,
        title: 'Budget Reallocation Request',
        project: 'Metro Tower Development',
        date: 'Today',
        type: 'Budget'
    },
    {
        id: 2,
        title: 'Additional Material PO',
        project: 'Shopping Complex Expansion',
        date: 'Yesterday',
        type: 'Procurement'
    }
])

const alerts = ref([
    {
        id: 1,
        severity: 'warning',
        title: 'Budget Threshold Alert',
        message: 'Metro Tower project utilization at 82% of budget'
    },
    {
        id: 2,
        severity: 'info',
        title: 'Upcoming Retention Release',
        message: '3 projects eligible for retention release this week'
    }
])

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

const getApprovalStatusClass = (type) => {
    const classes = {
        'Budget': 'bg-purple-100 text-purple-800',
        'Procurement': 'bg-orange-100 text-orange-800',
        'Contract': 'bg-blue-100 text-blue-800'
    }
    return classes[type] || 'bg-gray-100 text-gray-800'
}

const getAlertClass = (severity) => {
    const classes = {
        'error': 'bg-red-50 text-red-700',
        'warning': 'bg-yellow-50 text-yellow-700',
        'info': 'bg-blue-50 text-blue-700'
    }
    return classes[severity] || 'bg-gray-50 text-gray-700'
}

const initProjectTrendChart = () => {
    const ctx = document.getElementById('projectTrendChart')
    if (!ctx) return
    
    try {
        if (chartInstances.value.projectTrend) {
            chartInstances.value.projectTrend.destroy()
            chartInstances.value.projectTrend = null
        }
    } catch (e) {
        console.warn('Error destroying chart:', e)
    }
    
    chartInstances.value.projectTrend = new Chart(ctx, {
        type: 'line',
        data: {
            labels: projectTrendData.value.labels,
            datasets: [
                {
                    label: 'Total Contract Value',
                    data: projectTrendData.value.contractValue,
                    borderColor: '#0c8ce9',
                    backgroundColor: 'rgba(12, 140, 233, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Utilized Budget',
                    data: projectTrendData.value.utilized,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { usePointStyle: true } }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#E5E7EB' }, ticks: { callback: (value) => '₱' + (value / 1000000).toFixed(1) + 'M' } },
                x: { grid: { color: '#E5E7EB' } }
            }
        }
    })
}

const initBudgetBreakdownChart = () => {
    const ctx = document.getElementById('budgetBreakdownChart')
    if (!ctx) return
    
    try {
        if (chartInstances.value.budgetBreakdown) {
            chartInstances.value.budgetBreakdown.destroy()
            chartInstances.value.budgetBreakdown = null
        }
    } catch (e) {
        console.warn('Error destroying chart:', e)
    }
    
    chartInstances.value.budgetBreakdown = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: budgetBreakdownData.value.labels,
            datasets: [{
                data: budgetBreakdownData.value.data,
                backgroundColor: ['#0c8ce9', '#10b981', '#f59e0b', '#a855f7', '#ef4444'],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 15 } }
            }
        }
    })
}

const initContractStatusChart = () => {
    const ctx = document.getElementById('contractStatusChart')
    if (!ctx) return
    
    try {
        if (chartInstances.value.contractStatus) {
            chartInstances.value.contractStatus.destroy()
            chartInstances.value.contractStatus = null
        }
    } catch (e) {
        console.warn('Error destroying chart:', e)
    }
    
    chartInstances.value.contractStatus = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: contractStatusData.value.labels,
            datasets: [{
                label: 'Contracts',
                data: contractStatusData.value.data,
                backgroundColor: ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b'],
                borderColor: ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b'],
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'x',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#E5E7EB' } },
                x: { grid: { display: false } }
            }
        }
    })
}

onMounted(() => {
    // Load data from API
    setTimeout(() => {
        initProjectTrendChart()
        initBudgetBreakdownChart()
        initContractStatusChart()
    }, 150)
})
</script>

<style scoped></style>
