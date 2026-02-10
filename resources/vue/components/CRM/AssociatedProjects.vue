<template>
    <div>
        <!-- Header with Action -->
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-gray-900">Projects Commissioned</h3>
            <div class="text-sm font-semibold text-gray-600">
                <span class="text-[#0c8ce9]">{{ projects.length }}</span> project(s)
            </div>
        </div>

        <!-- Projects Table -->
        <div v-if="projects.length > 0" class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Project Name</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Start Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">End Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Contract Value</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="project in projects" :key="project.id" class="hover:bg-blue-50 transition-all duration-200">
                            <td class="px-6 py-4 text-sm">
                                <div class="font-semibold text-gray-900">{{ project.name }}</div>
                                <div class="text-xs text-gray-500">{{ project.code }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span :class="['inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold', projectStatusStyles[project.status]]">
                                    {{ project.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(project.start_date) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span v-if="project.end_date">{{ formatDate(project.end_date) }}</span>
                                <span v-else class="text-gray-400">Ongoing</span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">₱{{ formatCurrency(project.contract_value) }}</td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div 
                                            class="h-full bg-linear-to-r from-[#0c8ce9] to-blue-600 rounded-full"
                                            :style="{ width: project.progress + '%' }"
                                        ></div>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-600 w-8 text-right">{{ project.progress }}%</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-gray-50 rounded-lg border border-gray-200 p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-gray-600 font-semibold mb-2">No Projects Associated</p>
            <p class="text-sm text-gray-500">This client has not yet commissioned any projects. Projects will appear here once they are created and linked.</p>
        </div>

        <!-- Summary Cards -->
        <div v-if="projects.length > 0" class="grid grid-cols-4 gap-4 mt-8">
            <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                <div class="text-gray-600 text-xs font-semibold uppercase tracking-wide">Total Projects</div>
                <div class="text-3xl font-bold text-gray-900 mt-2">{{ projects.length }}</div>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                <div class="text-gray-600 text-xs font-semibold uppercase tracking-wide">Active</div>
                <div class="text-3xl font-bold text-gray-900 mt-2">{{ activeCount }}</div>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                <div class="text-gray-600 text-xs font-semibold uppercase tracking-wide">Total Contract Value</div>
                <div class="text-2xl font-bold text-gray-900 mt-2">₱{{ formatCurrency(totalValue) }}</div>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                <div class="text-gray-600 text-xs font-semibold uppercase tracking-wide">Avg Progress</div>
                <div class="text-3xl font-bold text-gray-900 mt-2">{{ avgProgress }}%</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    clientId: {
        type: [String, Number],
        required: true
    }
})

// Mock projects data - Replace with API call
const projects = ref([
    {
        id: 1,
        name: 'Office Building Renovation',
        code: 'PRJ-001',
        status: 'In Progress',
        start_date: '2024-01-15',
        end_date: '2024-12-31',
        contract_value: 2500000,
        progress: 65
    },
    {
        id: 2,
        name: 'Infrastructure Development',
        code: 'PRJ-002',
        status: 'Completed',
        start_date: '2023-06-01',
        end_date: '2024-05-31',
        contract_value: 5000000,
        progress: 100
    },
    {
        id: 3,
        name: 'Supply Chain Optimization',
        code: 'PRJ-003',
        status: 'In Progress',
        start_date: '2024-03-01',
        end_date: null,
        contract_value: 1200000,
        progress: 42
    }
])

// Project Status Styles
const projectStatusStyles = {
    'Planning': 'bg-gray-100 text-gray-800',
    'In Progress': 'bg-blue-100 text-blue-800',
    'Completed': 'bg-green-100 text-green-800',
    'On Hold': 'bg-yellow-100 text-yellow-800',
    'Cancelled': 'bg-red-100 text-red-800'
}

// Computed Properties
const activeCount = computed(() => 
    projects.value.filter(p => p.status === 'In Progress').length
)

const totalValue = computed(() =>
    projects.value.reduce((sum, p) => sum + p.contract_value, 0)
)

const avgProgress = computed(() => {
    if (projects.value.length === 0) return 0
    const sum = projects.value.reduce((total, p) => total + p.progress, 0)
    return Math.round(sum / projects.value.length)
})

// Helper Methods
const formatDate = (date) => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value)
}
</script>
