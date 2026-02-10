<template>
    <!-- Modal Overlay -->
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
        <!-- Modal Content -->
        <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Create {{ category }} Report</h2>
                <button
                    @click="$emit('close')"
                    class="text-gray-500 hover:text-gray-700 text-2xl"
                >
                    ×
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-6">
                <!-- Report Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Report Name *</label>
                    <input
                        v-model="form.name"
                        type="text"
                        placeholder="e.g., January 2026 Attendance Summary"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    />
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Start Date *</label>
                        <input
                            v-model="form.dateRange.start"
                            type="date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">End Date *</label>
                        <input
                            v-model="form.dateRange.end"
                            type="date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                        />
                    </div>
                </div>

                <!-- Department Selection (Multi-checkbox) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-3">Departments *</label>
                    <div class="space-y-2 bg-gray-50 rounded-lg p-4 border border-gray-200 max-h-40 overflow-y-auto">
                        <label v-for="dept in availableDepartments" :key="dept" class="flex items-center gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                :value="dept"
                                v-model="form.departments"
                                class="w-4 h-4 text-[#0c8ce9] rounded focus:ring-2 focus:ring-[#0c8ce9]"
                            />
                            <span class="text-sm text-gray-700">{{ dept }}</span>
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Selected: {{ form.departments.join(', ') || 'None' }}</p>
                </div>

                <!-- Employee (Optional) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Employee (Optional)</label>
                    <input
                        v-model="form.employee"
                        type="text"
                        placeholder="Search by name or ID"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    />
                </div>

                <!-- Category-Specific Filters -->
                <div v-if="categoryFilters.length > 0">
                    <label class="block text-sm font-semibold text-gray-900 mb-3">Category Filters</label>
                    <div class="space-y-2">
                        <label v-for="filter in categoryFilters" :key="filter" class="flex items-center gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                :value="filter"
                                v-model="form.filters"
                                class="w-4 h-4 text-[#0c8ce9] rounded focus:ring-2 focus:ring-[#0c8ce9]"
                            />
                            <span class="text-sm text-gray-700">{{ filter }}</span>
                        </label>
                    </div>
                </div>

                <!-- Group By -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Group By</label>
                    <select
                        v-model="form.groupBy"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    >
                        <option value="none">None</option>
                        <option value="department">Department</option>
                        <option value="employee">Employee</option>
                        <option value="date">Date</option>
                    </select>
                </div>

                <!-- Include Charts Toggle -->
                <div class="flex items-center gap-3">
                    <input
                        v-model="form.includeCharts"
                        type="checkbox"
                        id="includeCharts"
                        class="w-4 h-4 text-[#0c8ce9] rounded focus:ring-2 focus:ring-[#0c8ce9]"
                    />
                    <label for="includeCharts" class="text-sm font-semibold text-gray-900 cursor-pointer">
                        Include Charts
                    </label>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex gap-3">
                <button
                    @click="$emit('close')"
                    class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="handlePreview"
                    :disabled="!isFormValid"
                    class="flex-1 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    Preview Report
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    category: {
        type: String,
        required: true
    }
})

const emit = defineEmits(['close', 'create'])

const availableDepartments = ['All', 'IT', 'HR', 'Finance', 'Sales', 'Operations', 'Marketing']

// Category-specific filters map
const categoryFiltersMap = {
    'Attendance': ['Present', 'Absent', 'Late', 'On Leave', 'Half Day'],
    'Payroll': ['Gross Pay', 'Deductions', 'Net Pay', 'Overtime', 'Bonuses'],
    'Leave & Absence': ['Annual Leave', 'Sick Leave', 'Personal Leave', 'Maternity Leave', 'Unpaid Leave'],
    'Mobilization': ['Internal Transfer', 'External Assignment', 'Reassignment', 'Promotion'],
    'Disciplinary': ['Verbal Warning', 'Written Warning', 'Suspension', 'Termination'],
    'Other System Reports': ['User Activity', 'System Logs', 'Audit Trail', 'Access Reports']
}

const categoryFilters = computed(() => {
    return categoryFiltersMap[props.category] || []
})

const form = ref({
    name: '',
    category: props.category,
    dateRange: { start: '', end: '' },
    departments: [],
    employee: '',
    filters: [],
    groupBy: 'none',
    includeCharts: false
})

const isFormValid = computed(() => {
    return (
        form.value.name.trim() !== '' &&
        form.value.dateRange.start !== '' &&
        form.value.dateRange.end !== '' &&
        form.value.departments.length > 0
    )
})

const handlePreview = () => {
    if (!isFormValid.value) {
        alert('Please fill in all required fields')
        return
    }
    
    emit('create', {
        name: form.value.name,
        category: props.category,
        dateRange: form.value.dateRange,
        departments: form.value.departments,
        employee: form.value.employee,
        filters: form.value.filters,
        groupBy: form.value.groupBy,
        includeCharts: form.value.includeCharts
    })
}
</script>
