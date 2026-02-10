<template>
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="$emit('close')">
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Create Financial Report</h3>
            
            <form @submit.prevent="submitForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Report Name</label>
                    <input 
                        v-model="formData.name"
                        type="text"
                        placeholder="e.g., January 2026 Analysis"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        required
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input 
                        v-model="formData.dateRange.start"
                        type="date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        required
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input 
                        v-model="formData.dateRange.end"
                        type="date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        required
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Group By</label>
                    <select 
                        v-model="formData.groupBy"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                    >
                        <option value="none">None</option>
                        <option value="category">Category</option>
                        <option value="project">Project</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <input 
                        v-model="formData.includeCharts"
                        type="checkbox"
                        id="charts"
                        class="w-4 h-4 text-[#0c8ce9] rounded"
                    />
                    <label for="charts" class="text-sm text-gray-700">Include Charts</label>
                </div>

                <div class="flex gap-3 pt-4">
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="flex-1 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition-colors"
                    >
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
    category: String
})

const emit = defineEmits(['close', 'create'])

const formData = ref({
    name: '',
    category: props.category,
    dateRange: { start: '', end: '' },
    groupBy: 'category',
    includeCharts: true
})

const submitForm = () => {
    emit('create', formData.value)
    formData.value = {
        name: '',
        category: props.category,
        dateRange: { start: '', end: '' },
        groupBy: 'category',
        includeCharts: true
    }
}
</script>
