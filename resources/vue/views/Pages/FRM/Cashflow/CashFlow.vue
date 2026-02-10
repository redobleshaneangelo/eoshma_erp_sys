<template>
    <auth-layout />
        <div class="ps-60 pe-4 bg-[#F3F4F6] min-h-screen">
            <div class="px-8 py-4">
                <div class="w-full pb-20">
                    <div class="flex justify-center pt-4 pb-4">
                        <div class="w-full max-w-7xl">

                            <!-- Summary Cards -->
                            <div class="grid grid-cols-5 gap-4 mb-6">
                                <div class="bg-linear-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Current Cash</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">₱4,850,000</p>
                                    <p class="text-xs text-gray-600 mt-1">Available funds</p>
                                </div>
                                <div class="bg-linear-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">This Month Inflow</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">₱1,250,000</p>
                                    <p class="text-xs text-gray-600 mt-1">Expected billings</p>
                                </div>
                                <div class="bg-linear-to-br from-red-50 to-red-100 rounded-lg p-4 border border-red-200">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">This Month Outflow</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">₱950,000</p>
                                    <p class="text-xs text-gray-600 mt-1">Due payments</p>
                                </div>
                                <div class="bg-linear-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Projected Balance</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">₱5,150,000</p>
                                    <p class="text-xs text-gray-600 mt-1">End of month</p>
                                </div>
                                <div class="bg-linear-to-br from-orange-50 to-orange-100 rounded-lg p-4 border border-orange-200">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Unallocated</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">₱285,000</p>
                                    <p class="text-xs text-gray-600 mt-1">Needs allocation</p>
                                </div>
                            </div>

                            <!-- Project Fund Allocation Section -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6 overflow-hidden">
                                <div class="px-6 py-4 bg-linear-to-r from-[#0c8ce9]/10 to-transparent border-b border-gray-200">
                                    <h3 class="font-semibold text-[#1f2937]">Fund Allocation by Project</h3>
                                    <p class="text-sm text-gray-600 mt-1">Allocated and unallocated budgets across active projects</p>
                                </div>
                                <div class="divide-y divide-gray-200">
                                    <div v-for="project in projects" :key="project.id" class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex items-start justify-between mb-3">
                                            <div>
                                                <h4 class="font-semibold text-gray-900">{{ project.name }}</h4>
                                                <p class="text-xs text-gray-500 mt-1">Budget: ₱{{ project.budget.toLocaleString() }} | Allocated: ₱{{ project.allocated.toLocaleString() }}</p>
                                            </div>
                                            <span class="text-sm font-semibold text-[#0c8ce9]">{{ Math.round((project.allocated / project.budget) * 100) }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div
                                                class="bg-[#0c8ce9] h-2 rounded-full"
                                                :style="{ width: Math.round((project.allocated / project.budget) * 100) + '%' }"
                                            ></div>
                                        </div>
                                        <div class="flex gap-6 mt-3 text-xs">
                                            <span class="text-gray-600">Unallocated: <strong class="text-orange-600">₱{{ (project.budget - project.allocated).toLocaleString() }}</strong></span>
                                            <span class="text-gray-600">Spent to date: <strong class="text-red-600">₱{{ project.spent.toLocaleString() }}</strong></span>
                                            <span class="text-gray-600">Balance: <strong class="text-green-600">₱{{ (project.allocated - project.spent).toLocaleString() }}</strong></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cashflow Trend Chart Placeholder -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
                                <h3 class="font-semibold text-[#1f2937] mb-4">30-Day Cashflow Forecast</h3>
                                <div class="flex bg-gray-50 justify-center items-center text-center h-64 rounded-lg border border-gray-200">
                                    <div class="text-gray-500 text-sm">(Cashflow Trend Chart - Ready for Chart.js/D3 integration)</div>
                                </div>
                            </div>

                            <!-- Daily Transactions -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                                <div class="px-6 py-4 bg-linear-to-r from-[#0c8ce9]/10 to-transparent border-b border-gray-200">
                                    <h3 class="font-semibold text-[#1f2937]">Recent Cash Movements</h3>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="bg-gray-50 border-b border-gray-200">
                                            <tr>
                                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Date</th>
                                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Reference</th>
                                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Description</th>
                                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Project</th>
                                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide">Type</th>
                                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">Amount</th>
                                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">Running Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <tr v-for="(trans, idx) in cashflowTableData" :key="idx" class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ formatDate(trans.date) }}</td>
                                                <td class="px-6 py-4 text-sm font-medium text-[#0c8ce9]">{{ trans.referenceNo }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-700">{{ trans.sourcePayee }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-700">{{ trans.project }}</td>
                                                <td class="px-6 py-4 text-center">
                                                    <span :class="cashflowStatusStyles[trans.type]" class="px-3 py-1 rounded-full text-xs font-bold border">
                                                        {{ trans.type }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm font-semibold text-right text-gray-900">₱{{ trans.amount.toLocaleString() }}</td>
                                                <td class="px-6 py-4 text-sm font-semibold text-right text-[#0c8ce9]">₱{{ trans.balance.toLocaleString() }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fixed bottom-0 right-0 p-2 z-50">
            <img :src="agnes" class="h-auto w-auto" v-if="loading" />
        </div>
    
</template>

<script setup>
    import { ref, onMounted } from 'vue'
    import { useAuthStore } from '@/stores/auth'
    import agnes from '@/assets/agnes.gif'

    const auth = useAuthStore()
    const loading = ref(false)

    /* =====================================================
    Projects & Fund Allocation
    ===================================================== */
    const projects = ref([
        {
            id: 1,
            name: 'Project Alpha - High-rise Commercial',
            budget: 2500000,
            allocated: 2100000,
            spent: 1650000
        },
        {
            id: 2,
            name: 'Project Beta - Residential Complex',
            budget: 1800000,
            allocated: 1350000,
            spent: 980000
        },
        {
            id: 3,
            name: 'Project Gamma - Infrastructure',
            budget: 3200000,
            allocated: 2800000,
            spent: 2150000
        }
    ])

    /* =====================================================
    Cashflow Transactions
    ===================================================== */
    const cashflowTableData = ref([
        {
            date: '2026-01-20',
            referenceNo: 'REF-001',
            sourcePayee: 'Metro Development Corp',
            project: 'Project Alpha',
            type: 'Inflow',
            amount: 450000,
            balance: 4850000
        },
        {
            date: '2026-01-19',
            referenceNo: 'REF-002',
            sourcePayee: 'BuildCo Contractors',
            project: 'Project Alpha',
            type: 'Outflow',
            amount: 250000,
            balance: 4400000
        },
        {
            date: '2026-01-18',
            referenceNo: 'REF-003',
            sourcePayee: 'Urban Construction Ltd',
            project: 'Project Beta',
            type: 'Inflow',
            amount: 320000,
            balance: 4650000
        },
        {
            date: '2026-01-17',
            referenceNo: 'REF-004',
            sourcePayee: 'Steel Supplies Ltd',
            project: 'Project Alpha',
            type: 'Outflow',
            amount: 180000,
            balance: 4330000
        },
        {
            date: '2026-01-16',
            referenceNo: 'REF-005',
            sourcePayee: 'City Complex Developers',
            project: 'Project Gamma',
            type: 'Inflow',
            amount: 280000,
            balance: 4510000
        }
    ])

    /* =====================================================
    Status Styles
    ===================================================== */
    const cashflowStatusStyles = {
        'Inflow': 'bg-[#dbfce7] text-[#00a63e] border-[#bbf8d1]',
        'Outflow': 'bg-[#ffe2e2] text-[#651A1A] border-[#ffcccc]'
    }

    /* =====================================================
    Helpers
    ===================================================== */
    const formatDate = (date) => {
        const d = new Date(date)
        const months = [
            'January','February','March','April','May','June',
            'July','August','September','October','November','December'
        ]
        return `${months[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`
    }

    /* =====================================================
    Lifecycle
    ===================================================== */
    onMounted(() => {
        // init logic here
        auth.pageTitle = 'Cash Flow'
    })
</script>
