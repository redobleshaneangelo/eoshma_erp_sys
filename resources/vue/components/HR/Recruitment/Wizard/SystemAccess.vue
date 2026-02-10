<template>
    <div class="space-y-6">
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <h4 class="font-semibold text-purple-900 mb-1">System Access Approval</h4>
            <p class="text-sm text-purple-700">Review and approve system access for newly hired employees</p>
        </div>

        <!-- Approval Status Tabs -->
        <div class="flex gap-2">
            <button
                v-for="status in ['Pending', 'Approved', 'Rejected']"
                :key="status"
                @click="filterStatus = status"
                :class="[
                    'px-4 py-2 rounded-lg font-semibold transition-colors',
                    filterStatus === status
                        ? 'bg-[#0c8ce9] text-white'
                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                ]"
            >
                {{ status }}
                <span class="ml-2 inline-flex items-center px-2 py-0.5 bg-white bg-opacity-20 rounded-full text-xs">
                    {{ getCountByStatus(status) }}
                </span>
            </button>
        </div>

        <!-- Pending Approval Section -->
        <div v-if="filterStatus === 'Pending'" class="space-y-4">
            <div v-if="pendingApprovals.length > 0">
                <div v-for="approval in pendingApprovals" :key="approval.id" class="bg-white border border-yellow-200 rounded-lg p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">{{ approval.employeeName }}</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                <span class="font-semibold">{{ approval.position }}</span> · {{ approval.department }}
                            </p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                            Pending Approval
                        </span>
                    </div>

                    <div class="grid grid-cols-4 gap-4 mb-6">
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase">Employee ID</p>
                            <p class="text-sm font-medium text-gray-900 mt-1">{{ approval.employeeId }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase">Username</p>
                            <p class="text-sm font-medium text-gray-900 mt-1">{{ approval.username }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase">Email</p>
                            <p class="text-sm font-medium text-gray-900 mt-1">{{ approval.email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase">Role</p>
                            <p class="text-sm font-medium text-gray-900 mt-1">{{ approval.role }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase">Requested Date</p>
                            <p class="text-sm text-gray-900 mt-1">{{ formatDate(approval.requestedDate) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase">Requested By</p>
                            <p class="text-sm text-gray-900 mt-1">{{ approval.requestedBy }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase">Employment Type</p>
                            <span :class="[
                                'inline-flex items-center px-2 py-1 rounded text-xs font-semibold mt-1',
                                approval.employmentType === 'Internal'
                                    ? 'bg-blue-100 text-blue-800'
                                    : 'bg-orange-100 text-orange-800'
                            ]">
                                {{ approval.employmentType }}
                            </span>
                        </div>
                    </div>

                    <!-- Access Permissions -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h5 class="font-semibold text-gray-900 mb-3 text-sm">System Access Permissions</h5>
                        <div class="grid grid-cols-2 gap-3">
                            <div v-for="permission in approval.permissions" :key="permission" class="flex items-center gap-2">
                                <input 
                                    type="checkbox" 
                                    :checked="true"
                                    class="rounded"
                                />
                                <span class="text-sm text-gray-700">{{ permission }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Actions -->
                    <div class="flex gap-3 border-t border-gray-200 pt-4">
                        <button 
                            @click="approveAccess(approval.id)"
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors flex items-center justify-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Approve Access
                        </button>
                        <button 
                            @click="rejectAccess(approval.id)"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors flex items-center justify-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            Reject Access
                        </button>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-12">
                <p class="text-gray-500 text-lg">No pending approval requests</p>
            </div>
        </div>

        <!-- Approved Section -->
        <div v-if="filterStatus === 'Approved'">
            <div v-if="approvedAccess.length > 0" class="bg-white border border-green-200 rounded-lg overflow-hidden">
                <div class="px-6 py-3 bg-green-50 border-b border-green-200">
                    <h4 class="font-semibold text-green-900">Approved Employees</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Employee ID</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Name</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Position</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Approved Date</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="employee in approvedAccess" :key="employee.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ employee.employeeId }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ employee.employeeName }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ employee.position }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(employee.approvedDate) }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                        ✓ Approved
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div v-else class="text-center py-12">
                <p class="text-gray-500 text-lg">No approved accesses yet</p>
            </div>
        </div>

        <!-- Rejected Section -->
        <div v-if="filterStatus === 'Rejected'">
            <div v-if="rejectedAccess.length > 0" class="bg-white border border-red-200 rounded-lg overflow-hidden">
                <div class="px-6 py-3 bg-red-50 border-b border-red-200">
                    <h4 class="font-semibold text-red-900">Rejected Access Requests</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Employee ID</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Name</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Position</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Rejection Reason</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="employee in rejectedAccess" :key="employee.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ employee.employeeId }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ employee.employeeName }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ employee.position }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ employee.rejectionReason }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                                        ✗ Rejected
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div v-else class="text-center py-12">
                <p class="text-gray-500 text-lg">No rejected accesses</p>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed } from 'vue'
    import Swal from 'sweetalert2'

    const props = defineProps({
        requestId: {
            type: String,
            required: true
        },
        pendingAccess: {
            type: Array,
            default: () => []
        }
    })

    const emit = defineEmits(['access-approved'])

    const filterStatus = ref('Pending')

    const pendingApprovals = ref([
        {
            id: 1,
            employeeId: 'EMP-001',
            employeeName: 'John Smith',
            position: 'Senior Engineer',
            department: 'Engineering',
            username: 'john.smith',
            email: 'john.smith@company.com',
            role: 'Employee',
            requestedDate: '2026-01-28',
            requestedBy: 'Jane Director',
            employmentType: 'Internal',
            permissions: ['Dashboard Access', 'Project Viewing', 'Report Generation', 'Document Upload']
        },
        {
            id: 2,
            employeeId: 'EMP-002',
            employeeName: 'Jane Doe',
            position: 'Senior Engineer',
            department: 'Engineering',
            username: 'jane.doe',
            email: 'jane.doe@company.com',
            role: 'Employee',
            requestedDate: '2026-01-28',
            requestedBy: 'Bob Manager',
            employmentType: 'Internal',
            permissions: ['Dashboard Access', 'Project Viewing', 'Report Generation']
        }
    ])

    const approvedAccess = ref([
        {
            id: 3,
            employeeId: 'EMP-003',
            employeeName: 'Bob Johnson',
            position: 'Sales Executive',
            department: 'Sales',
            approvedDate: '2026-01-27'
        }
    ])

    const rejectedAccess = ref([
        {
            id: 4,
            employeeId: 'EMP-004',
            employeeName: 'Alice Wilson',
            position: 'Marketing Manager',
            department: 'Marketing',
            rejectionReason: 'Background check pending'
        }
    ])

    const getCountByStatus = (status) => {
        if (status === 'Pending') return pendingApprovals.value.length
        if (status === 'Approved') return approvedAccess.value.length
        return rejectedAccess.value.length
    }

    const approveAccess = async (approvalId) => {
        const approval = pendingApprovals.value.find(a => a.id === approvalId)
        if (!approval) return

        pendingApprovals.value = pendingApprovals.value.filter(a => a.id !== approvalId)
        approvedAccess.value.push({
            ...approval,
            approvedDate: new Date().toISOString().split('T')[0]
        })

        emit('access-approved', { employeeId: approval.employeeId, status: 'Approved' })

        Swal.fire('Success', `System access approved for ${approval.employeeName}`, 'success')
    }

    const rejectAccess = async (approvalId) => {
        Swal.fire({
            title: 'Reject Access',
            input: 'textarea',
            inputLabel: 'Rejection Reason',
            inputPlaceholder: 'Enter the reason for rejection...',
            inputAttributes: {
                'aria-label': 'Rejection Reason'
            },
            showCancelButton: true,
            confirmButtonText: 'Reject'
        }).then((result) => {
            if (result.isConfirmed) {
                const approval = pendingApprovals.value.find(a => a.id === approvalId)
                if (!approval) return

                pendingApprovals.value = pendingApprovals.value.filter(a => a.id !== approvalId)
                rejectedAccess.value.push({
                    ...approval,
                    rejectionReason: result.value
                })

                Swal.fire('Rejected', `System access rejected for ${approval.employeeName}`, 'info')
            }
        })
    }

    const formatDate = (date) => {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        })
    }
</script>
