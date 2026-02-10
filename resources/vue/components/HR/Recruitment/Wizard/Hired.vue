<template>
    <div class="space-y-6">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <h4 class="font-semibold text-green-900 mb-1">Hired Candidates</h4>
            <p class="text-sm text-green-700">These candidates have been successfully hired and are awaiting system access approval</p>
        </div>

        <!-- Hired Candidates Table -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-6 py-3 bg-gray-100 border-b border-gray-200">
                <h4 class="font-semibold text-gray-900">Newly Hired Employees</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Employee ID</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Full Name</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Position</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Department</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Start Date</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">System Access Status</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-700 text-xs">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="employee in hiredEmployees" :key="employee.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ employee.employeeId }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ employee.name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ employee.position }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ employee.department }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(employee.startDate) }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span :class="[
                                    'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                                    employee.systemAccessStatus === 'Pending'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : employee.systemAccessStatus === 'Approved'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800'
                                ]">
                                    {{ employee.systemAccessStatus }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2 flex-wrap">
                                    <button 
                                        @click="viewProfile(employee.id)"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-[#0c8ce9] hover:bg-blue-50 rounded transition-colors border border-blue-200"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View
                                    </button>
                                    <button 
                                        @click="openHiringWizard(employee)"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white bg-green-600 hover:bg-green-700 rounded transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Hire
                                    </button>
                                    
                                    
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="hiredEmployees.length === 0" class="text-center py-8 text-gray-500">
                <p>No hired employees yet. Complete the selection stage first.</p>
            </div>
        </div>

        <!-- Hiring Sub-Wizard Modal -->
        <div v-if="showHiringWizard" class="fixed inset-0 z-50 flex items-center justify-center p-4 h-screen" style="background-color: rgba(0, 0, 0, 0.5);">
            <HiringSubWizard 
                :candidate="hiringCandidate"
                @close="showHiringWizard = false"
                @complete="onHiringComplete"
            />
        </div>

        <!-- Summary Cards -->
        <div v-if="hiredEmployees.length > 0" class="grid grid-cols-4 gap-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide">Total Hired</p>
                <p class="text-3xl font-bold text-blue-900 mt-2">{{ hiredEmployees.length }}</p>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-xs text-yellow-600 font-semibold uppercase tracking-wide">Pending Access</p>
                <p class="text-3xl font-bold text-yellow-900 mt-2">{{ hiredEmployees.filter(e => e.systemAccessStatus === 'Pending').length }}</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-xs text-green-600 font-semibold uppercase tracking-wide">Access Approved</p>
                <p class="text-3xl font-bold text-green-900 mt-2">{{ hiredEmployees.filter(e => e.systemAccessStatus === 'Approved').length }}</p>
            </div>
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <p class="text-xs text-purple-600 font-semibold uppercase tracking-wide">Ready in System</p>
                <p class="text-3xl font-bold text-purple-900 mt-2">{{ hiredEmployees.filter(e => e.systemAccessStatus === 'Approved').length }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, watch } from 'vue'
    import Swal from 'sweetalert2'
    import HiringSubWizard from './HiringSubWizard.vue'

    const props = defineProps({
        requestId: {
            type: String,
            required: true
        },
        candidates: {
            type: Array,
            default: () => []
        }
    })

    const emit = defineEmits(['access-requested'])

    const hiredEmployees = ref([
        {
            id: 1,
            employeeId: 'EMP-001',
            name: 'John Smith',
            position: 'Senior Engineer',
            department: 'Engineering',
            startDate: '2026-02-01',
            systemAccessStatus: 'Pending'
        },
        {
            id: 2,
            employeeId: 'EMP-002',
            name: 'Jane Doe',
            position: 'Senior Engineer',
            department: 'Engineering',
            startDate: '2026-02-01',
            systemAccessStatus: 'Pending'
        }
    ])

    const showHiringWizard = ref(false)
    const hiringCandidate = ref(null)

    watch(() => props.candidates, (newCandidates) => {
        const newEmployees = newCandidates.map((c, index) => ({
            id: Date.now() + index,
            employeeId: 'EMP-' + String(hiredEmployees.value.length + index + 1).padStart(3, '0'),
            name: c.name,
            position: c.position,
            department: 'Engineering', // Would come from the candidate data
            startDate: new Date().toISOString().split('T')[0],
            systemAccessStatus: 'Pending'
        }))
        hiredEmployees.value = [...hiredEmployees.value, ...newEmployees]
    }, { deep: true })

    const viewProfile = (employeeId) => {
        Swal.fire({
            title: 'Employee Profile',
            text: `View profile for employee ${employeeId}`,
            icon: 'info'
        })
    }

    const openHiringWizard = (employee) => {
        hiringCandidate.value = employee
        showHiringWizard.value = true
    }

    const onHiringComplete = () => {
        showHiringWizard.value = false
        Swal.fire('Success', 'Candidate hired successfully.', 'success')
    }

    const formatDate = (date) => {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        })
    }
</script>
