<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">
            <div class="mb-6 flex items-center justify-end">
                <router-link
                    to="/disciplinary/complaints"
                    class="px-6 py-2.5 bg-[#0c8ce9] text-white rounded-md font-semibold text-sm hover:bg-blue-700 transition-colors flex items-center gap-2"
                >
                    Employee Complaints
                </router-link>
            </div>

            <div class="bg-white border-b border-gray-200 rounded-t-lg mb-0">
                <nav class="flex gap-0 px-6 py-0 overflow-x-auto">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        class="flex items-center gap-2 px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap"
                        :class="activeTab === tab.id ? 'border-[#0c8ce9] text-[#0c8ce9]' : 'border-transparent text-gray-600 hover:text-gray-900'"
                    >
                        {{ tab.label }}
                        <span class="bg-gray-200 text-gray-700 px-2 py-0 rounded-full text-xs font-semibold">
                            {{ tab.count }}
                        </span>
                    </button>
                </nav>
            </div>

            <div class="bg-white rounded-b-lg p-8">
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Submitted By</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Reported Employee</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Incident Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Updated</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr
                                v-for="record in filteredRecords"
                                :key="record.id"
                                class="hover:bg-gray-50 transition-colors"
                            >
                                <td class="px-6 py-4 text-sm font-semibold text-[#0c8ce9]">{{ record.complaintCode }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ record.submittedByName }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ record.reportedEmployeeName || '--' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ record.complaintType }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ formatDate(record.incidentDate) }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span :class="getStatusBadgeClass(record.status)" class="px-2 py-1 rounded-full text-xs font-semibold">
                                        {{ statusLabel(record.status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ formatDate(record.updatedAt || record.createdAt) }}</td>
                                <td class="px-6 py-4 text-center">
                                    <button
                                        @click="viewRecord(record)"
                                        class="inline-flex items-center gap-1 px-3 py-1 text-sm text-[#0c8ce9] hover:bg-blue-50 rounded transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="filteredRecords.length === 0" class="text-center py-12 text-gray-500">
                        <svg class="block w-12 h-12 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>No complaints in this queue.</p>
                    </div>
                </div>
            </div>

            <div class="fixed bottom-0 right-0 p-2 z-50">
                <img :src="agnes" class="h-auto w-auto" v-if="loading" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import agnes from '@/assets/agnes.gif'

const auth = useAuthStore()
const router = useRouter()

const loading = ref(false)
const activeTab = ref('pending')
const allRecords = ref([])

const tabs = computed(() => [
    {
        id: 'pending',
        label: 'Pending',
        count: allRecords.value.filter((item) => item.status === 'pending').length,
    },
    {
        id: 'reviewed',
        label: 'Reviewed',
        count: allRecords.value.filter((item) => item.status === 'reviewed').length,
    }
])

const filteredRecords = computed(() => {
    return allRecords.value.filter((item) => item.status === activeTab.value)
})

const fetchRecords = async () => {
    try {
        loading.value = true
        const response = await axios.get('/api/disciplinary/complaints')
        allRecords.value = response.data?.data || []
    } catch (error) {
        console.error('Failed to load disciplinary records', error)
        allRecords.value = []
    } finally {
        loading.value = false
    }
}

const formatDate = (date) => {
    if (!date) return '--'
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

const statusLabel = (status) => {
    const map = {
        pending: 'Pending',
        reviewed: 'Reviewed',
    }

    return map[status] || status
}

const getStatusBadgeClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        reviewed: 'bg-green-100 text-green-800',
    }

    return classes[status] || 'bg-gray-100 text-gray-800'
}

const viewRecord = (record) => {
    router.push({ name: 'employee_complaints_detail', params: { id: record.id } })
}

onMounted(() => {
    auth.pageTitle = 'Disciplinary'
    fetchRecords()
})
</script>
