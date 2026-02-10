<template>
    <div class="flex flex-col">
        <div class="flex flex-col">
            <div class="font-medium text-[#1F2937] mb-3">Budget Overview</div>
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
                
                <div class="font-medium text-[#1F2937] mb-3">
                    Category Breakdown
                </div>

                <div
                    v-for="(category, name) in budget.categories"
                    :key="name"
                    class="mb-3"
                >
                    <div class="flex justify-between">
                        <span class="text-sm font-semibold capitalize">{{ name }}</span>
                        <span class="text-sm text-gray-400">
                            {{ categoryProgress(category) }}%
                        </span>
                    </div>

                    <div class="relative w-full bg-gray-200 h-2 rounded-full overflow-hidden my-2">
                        <div
                            class="absolute left-0 top-0 h-full bg-slate-400 transition-all duration-500"
                            :style="{ width: ((category.spent + category.committed) / category.allocated * 100) + '%' }"
                        ></div>
                        <div
                            class="absolute left-0 top-0 h-full bg-slate-800 transition-all duration-500"
                            :style="{ width: (category.spent / category.allocated * 100) + '%' }"
                        ></div>
                    </div>

                    <p class="text-gray-400 text-sm">
                        ₱{{ category.spent.toLocaleString() }} spent
                        <span v-if="category.committed">
                            + ₱{{ category.committed.toLocaleString() }} committed
                        </span>
                        out of ₱{{ category.allocated.toLocaleString() }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    /* =====================================================
    Core Imports
    ===================================================== */
    import { ref, onMounted, onBeforeUnmount, nextTick, watch, computed } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import axios from 'axios'
    import Swal from 'sweetalert2'

    /* =====================================================
    Router
    ===================================================== */
    const route = useRoute()
    const router = useRouter()

    /* =====================================================
    Helpers
    ===================================================== */
    const showToast = ({ icon = 'info', title = '', html = '', position = 'top-end', timer = 3000 }) => {
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

    /* =====================================================
    Lifecycle
    ===================================================== */
    onMounted(() => {
        // Initialization logic here
    })

    /* =====================================================
    Budget Data (mock / API-ready)
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
        Object.values(budget.value.categories).reduce((sum, cat) => sum + cat.allocated, 0)
    )

    const totalUsed = computed(() =>
        Object.values(budget.value.categories).reduce((sum, cat) => sum + cat.spent + cat.committed, 0)
    )

    const totalSpent = computed(() =>
        Object.values(budget.value.categories).reduce((sum, cat) => sum + cat.spent, 0)
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
        return Math.floor(((category.spent + category.committed) / category.allocated) * 100)
    }

    /* =====================================================
    Computed: Table Data
    ===================================================== */
    const budgetTable = computed(() =>
        Object.entries(budget.value.categories).map(([category, data]) => {
            const used = data.spent + data.committed
            const remaining = data.allocated - used

            let status = 'On Track'
            if (used === data.allocated) status = 'Fully Used'
            if (used > data.allocated) status = 'Over Budget'

            return {
                category,
                allocated: data.allocated,
                committed: data.committed,
                spent: data.spent,
                description: data.description,
                remaining,
                status
            }
        })
    )

    /* =====================================================
    UI: Status Badge Styles
    ===================================================== */
    const statusStyles = {
        'On Track': 'text-green-600 border-green-600 bg-green-50',
        'Fully Used': 'text-yellow-600 border-yellow-600 bg-yellow-50',
        'Over Budget': 'text-red-600 border-red-600 bg-red-50'
    }
</script>