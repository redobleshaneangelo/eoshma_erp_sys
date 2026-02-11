<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10 space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Payroll Configuration</h1>
                    <p class="text-sm text-gray-600">Manage payroll groups and member counts.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button
                        @click="goBack"
                        class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back
                    </button>
                    <button
                        @click="showCreateModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700 rounded-lg transition-colors"
                    >
                        Create Payroll Group
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
                    <h2 class="text-sm font-semibold text-gray-700">Payroll Groups</h2>
                </div>
                <div class="p-6">
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Payroll Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Payroll Group</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Members</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="group in payrollGroups" :key="group.id" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ group.name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ formatSalaryType(group.salaryType) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ group.members }}</td>
                                </tr>
                                <tr v-if="payrollGroups.length === 0">
                                    <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                                        No payroll groups yet.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Create Payroll Group</h3>
                    <button @click="closeCreateModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payroll Name</label>
                    <input
                        v-model="newGroup.name"
                        type="text"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        placeholder="Enter payroll name"
                    />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payroll Group</label>
                    <select
                        v-model="newGroup.salary_type"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                    >
                        <option value="">Select salary type</option>
                        <option value="fixed">Fixed</option>
                        <option value="hour">Hour</option>
                        <option value="day">Day</option>
                    </select>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex gap-3">
                <button
                    @click="closeCreateModal"
                    class="flex-1 px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="createPayrollGroup"
                    class="flex-1 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                >
                    Create
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'
import AuthLayout from '@/views/Layouts/AuthLayout.vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()
const payrollGroups = ref([])
const showCreateModal = ref(false)
const newGroup = ref({
    name: '',
    salary_type: ''
})

const fetchGroups = async () => {
    try {
        const response = await axios.get('/api/payroll-groups')
        payrollGroups.value = response.data?.data || []
    } catch (error) {
        console.error('Failed to load payroll groups', error)
    }
}

const formatSalaryType = (value) => {
    if (!value) return '--'
    return value.charAt(0).toUpperCase() + value.slice(1)
}

const closeCreateModal = () => {
    showCreateModal.value = false
    newGroup.value = { name: '', salary_type: '' }
}

const goBack = () => {
    router.back()
}

const createPayrollGroup = async () => {
    try {
        await axios.post('/api/payroll-groups', newGroup.value)
        await fetchGroups()
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Payroll group created',
            showConfirmButton: false,
            timer: 2000
        })
        closeCreateModal()
    } catch (error) {
        const message = error.response?.data?.message || 'Failed to create payroll group.'
        Swal.fire({
            icon: 'error',
            title: 'Creation failed',
            text: message,
            confirmButtonColor: '#ef4444'
        })
    }
}

onMounted(() => {
    auth.pageTitle = 'Payroll Configuration'
    fetchGroups()
})
</script>
