<template>
    <div class="flex flex-col">
        <div class="flex flex-col">
            <div class="font-medium text-[#1F2937] mb-3">Cost Overview</div>
            <div class="flex flex-col mb-5">
                <div class="grid grid-cols-4 gap-6 gap-x-20">
                    <div class="">
                        <p class="text-[#808081] text-sm font-medium">Approved Budget</p>
                        <p class="text-[#333333] text-2xl font-medium mt-2">₱{{ totalBudget.toLocaleString() }}</p>
                    </div>
                    <div class="">
                        <p class="text-[#808081] text-sm font-medium">Actual Cost to Date</p>
                        <p class="text-[#333333] text-2xl font-medium mt-2">₱{{ totalSpent.toLocaleString() }}</p>
                    </div>
                    <div class="">
                        <p class="text-[#808081] text-sm font-medium">Committed Cost</p>
                        <p class="text-[#333333] text-2xl font-medium mt-2">₱{{ (totalUsed - totalSpent).toLocaleString() }}</p>
                    </div>
                    <div class="">
                        <p class="text-[#808081] text-sm font-medium">Remaining Budgets</p>
                        <p class="text-[#333333] text-2xl font-medium mt-2">₱{{ (totalBudget - totalUsed).toLocaleString() }}</p>
                    </div>
                </div>
            </div>
            <div class="font-medium text-[#1F2937] mb-3">Budget Utilization</div>
            <div class="flex flex-col mb-2">
                <div class="mb-5">
                    <div class="font-semibold text-[#333333] mb-2 text-sm">
                        Total Budget: ₱{{ totalBudget.toLocaleString() }}
                    </div>
                    <div class="relative w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                        <div
                            class="absolute left-0 top-0 h-full bg-slate-400 transition-all duration-500"
                            :style="{ width: ((totalUsed / totalBudget) * 100) + '%' }"
                        ></div>
                        <div
                            class="absolute left-0 top-0 h-full bg-slate-800 transition-all duration-500"
                            :style="{ width: ((totalSpent / totalBudget) * 100) + '%' }"
                        ></div>
                    </div>
                    <p class="text-gray-400 mt-2 text-sm">
                        {{ projectProgress  }}% of Budget Set
                    </p>
                </div>
                <div class="font-medium text-[#1F2937] mb-3">Cost Monitoring Charts</div>
                <div class="flex gap-x-10 pb-5">
                    <div class="w-1/2">
                        <div class="flex justify-between mb-3">
                            <p class="text-[#333333] text-sm font-medium">Remaining vs Actual</p>
                            <p class="text-[#333333] text-xs font-medium border px-1">Last 7 Days</p>
                        </div>
                        <div class="flex bg-white justify-center items-center text-center h-50 border border-gray-100 rounded-lg">
                            <div class="text-black text-xs">(chart here)</div>
                        </div>
                    </div>
                    <div class="w-1/2">
                        <div class="flex justify-between mb-3">
                            <p class="text-[#333333] text-sm font-medium">Budget Cost by Category</p>
                            <p class="text-[#333333] text-xs font-medium border px-1">Last 7 Days</p>
                        </div>
                        <div class="flex bg-white justify-center items-center text-center h-50 border border-gray-100 rounded-lg">
                            <div class="text-black text-xs">(chart here)</div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-x-10 pb-5 mb-1">
                    <div class="w-1/2">
                        <div class="flex justify-between mb-3">
                            <p class="text-[#333333] text-sm font-medium">Committed vs Actual</p>
                            <p class="text-[#333333] text-xs font-medium border px-1">Last 7 Days</p>
                        </div>
                        <div class="flex bg-white justify-center items-center text-center h-50 border border-gray-100 rounded-lg">
                            <div class="text-black text-xs">(chart here)</div>
                        </div>
                    </div>
                    <div class="w-1/2">
                        <div class="flex justify-between mb-3">
                            <p class="text-[#333333] text-sm font-medium">Committed by Category</p>
                            <p class="text-[#333333] text-xs font-medium border px-1">Last 7 Days</p>
                        </div>
                        <div class="flex bg-white justify-center items-center text-center h-50 border border-gray-100 rounded-lg">
                            <div class="text-black text-xs">(chart here)</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="font-medium text-[#1F2937] mb-3">Cost Postings</div>
            <div class="text-sm text-gray-700 mb-5">
                <div
                    v-for="section in costPostingsSection"
                    :key="section.key"
                >
                    <div
                        class="flex items-center cursor-pointer py-3 text-[#1f2937] font-semibold"
                        @click="toggle(section)"
                    >
                        <img
                            :src="section.open ? open : close"
                            class="w-4 h-auto mr-2"
                        />
                        {{ section.title }}
                    </div>
                    <div v-if="section.open" class="pb-4">
                        <div v-if="section.rows.length > 0" class="bg-white rounded-lg border border-gray-100 overflow-hidden shadow-sm">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Reference No.</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Source</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Cost Category</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Amount (₱)</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Cost Code</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="row in section.rows"
                                            :key="row.reference"
                                            class="border-b border-gray-200 hover:bg-gray-50 transition-colors"
                                        >
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ row.date }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ row.reference }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ row.source }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ row.category }}</td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ format(row.amount) }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ row.costCode }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div
                            v-else
                            class="text-gray-600 text-sm py-4 px-6"
                        >
                            No records available
                        </div>
                    </div>
                </div>
            </div>
            <div class="font-medium text-[#1F2937] mb-3">Committed Costs</div>
            <div class="text-sm text-gray-700 pb-5 mb-5">
                <div
                    v-for="section in committedCostsSection"
                    :key="section.key"
                >
                    <div
                        class="flex items-center cursor-pointer py-3 text-[#1f2937] font-semibold"
                        @click="toggle(section)"
                    >
                        <img
                            :src="section.open ? open : close"
                            class="w-4 h-auto mr-2"
                        />
                        {{ section.title }}
                    </div>
                    <div v-if="section.open" class="pb-4">
                        <div v-if="section.rows.length > 0" class="bg-white rounded-lg border border-gray-100 overflow-hidden shadow-sm">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Reference No.</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Source</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Cost Category</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Amount (₱)</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Cost Code</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="row in section.rows"
                                            :key="row.reference"
                                            class="border-b border-gray-200 hover:bg-gray-50 transition-colors"
                                        >
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ row.date }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ row.reference }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ row.source }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ row.category }}</td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ format(row.amount) }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ row.costCode }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div
                            v-else
                            class="text-gray-600 text-sm py-4 px-6"
                        >
                            No records available
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    /* =====================================================
    Core Imports
    ===================================================== */
    import { ref, reactive, computed, onMounted } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import axios from 'axios'
    import Swal from 'sweetalert2'

    /* =====================================================
    Assets
    ===================================================== */
    import close from '@/assets/close.png'
    import open from '@/assets/open.png'

    /* =====================================================
    Router
    ===================================================== */
    const route = useRoute()
    const router = useRouter()

    /* =====================================================
    Helpers / Utilities
    ===================================================== */
    const showToast = ({
        icon = 'info',
        title = '',
        html = '',
        position = 'top-end',
        timer = 3000
    }) => {
        Swal.fire({
            toast: true,
            position,
            icon,
            title,
            html,
            showConfirmButton: false,
            timer,
            customClass: { popup: 'no-wrap-toast' }
        })
    }

    const format = (val) => val.toLocaleString()

    const formatDate = (date) => {
        const d = new Date(date)
        const months = [
            'January','February','March','April','May','June',
            'July','August','September','October','November','December'
        ]
        return `${months[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`
    }

    /* =====================================================
    Budget State (Mock / API-ready)
    ===================================================== */
    const budget = ref({
        categories: {
            labor: {
                allocated: 500000,
                committed: 120000,
                spent: 300000,
                description: 'Salaries for skilled and unskilled manpower'
            },
            materials: {
                allocated: 600000,
                committed: 80000,
                spent: 350000,
                description: 'Construction materials and supplies'
            },
            subcontractors: {
                allocated: 300000,
                committed: 50000,
                spent: 180000,
                description: 'Outsourced specialized work'
            },
            equipment: {
                allocated: 100000,
                committed: 20000,
                spent: 70000,
                description: 'Rental and maintenance of heavy equipment'
            },
            miscellaneous: {
                allocated: 80000,
                committed: 15000,
                spent: 40000,
                description: 'Unexpected expenses and contingencies'
            }
        }
    })

    /* =====================================================
    Computed: Budget Totals
    ===================================================== */
    const totalBudget = computed(() =>
        Object.values(budget.value.categories)
            .reduce((sum, cat) => sum + cat.allocated, 0)
    )

    const totalSpent = computed(() =>
        Object.values(budget.value.categories)
            .reduce((sum, cat) => sum + cat.spent, 0)
    )

    const totalUsed = computed(() =>
        Object.values(budget.value.categories)
            .reduce((sum, cat) => sum + cat.spent + cat.committed, 0)
    )

    /* =====================================================
    Computed: Progress
    ===================================================== */
    const projectProgress = computed(() => {
        if (!totalBudget.value) return 0
        return Math.floor((totalUsed.value / totalBudget.value) * 100)
    })

    const categoryProgress = (category) => {
        if (!category.allocated) return 0
        return Math.floor(
            ((category.spent + category.committed) / category.allocated) * 100
        )
    }

    /* =====================================================
    Cost Postings Sections
    ===================================================== */
    const costPostingsSection = reactive([
        {
            key: 'payroll',
            title: 'Payroll Postings',
            type: 'payroll',
            open: true,
            rows: [
                {
                    date: 'January 6, 2025',
                    reference: 'INV-045',
                    source: 'Payroll',
                    category: 'Labor',
                    amount: 100000,
                    costCode: 'L-001',
                },
                {
                    date: 'January 5, 2025',
                    reference: 'PAY-012',
                    source: 'Payroll',
                    category: 'Labor',
                    amount: 50000,
                    costCode: 'L-001',
                },
                {
                    date: 'January 4, 2025',
                    reference: 'INV-050',
                    source: 'Payroll',
                    category: 'Labor',
                    amount: 500000,
                    costCode: 'L-002',
                },
            ],
        },
        {
            key: 'supplier',
            title: 'Supplier Invoices',
            type: 'supplier',
            open: false,
            rows: [],
        },
        {
            key: 'inventory',
            title: 'Inventory',
            type: 'inventory',
            open: false,
            rows: [],
        },
        {
            key: 'subcontractor',
            title: 'Subcontractor Billings',
            type: 'subcontractor',
            open: false,
            rows: [],
        },
    ])

    /* =====================================================
    Committed Costs Sections
    ===================================================== */
    const committedCostsSection = reactive([
        {
            key: 'payroll',
            title: 'Payroll',
            type: 'payroll',
            open: true,
            rows: [
                {
                    date: 'January 6, 2025',
                    reference: 'INV-045',
                    source: 'Payroll',
                    category: 'Labor',
                    amount: 100000,
                    costCode: 'L-001',
                },
                {
                    date: 'January 5, 2025',
                    reference: 'PAY-012',
                    source: 'Payroll',
                    category: 'Labor',
                    amount: 50000,
                    costCode: 'L-001',
                },
                {
                    date: 'January 4, 2025',
                    reference: 'INV-050',
                    source: 'Payroll',
                    category: 'Labor',
                    amount: 500000,
                    costCode: 'L-002',
                },
            ],
        },
        {
            key: 'supplier',
            title: 'Supplier',
            type: 'supplier',
            open: false,
            rows: [],
        },
        {
            key: 'inventory',
            title: 'Inventory',
            type: 'inventory',
            open: false,
            rows: [],
        },
        {
            key: 'subcontractor',
            title: 'Subcontractor Billings',
            type: 'subcontractor',
            open: false,
            rows: [],
        },
    ])

    /* =====================================================
    UI Actions
    ===================================================== */
    const toggle = (section) => {
        section.open = !section.open
    }

    /* =====================================================
    Lifecycle
    ===================================================== */
    onMounted(() => {
        // initialization logic
    })
</script>
