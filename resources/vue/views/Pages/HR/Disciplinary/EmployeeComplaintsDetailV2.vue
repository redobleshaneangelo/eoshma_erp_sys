<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10 space-y-6">
            <button
                @click="router.push({ name: 'employee_complaints' })"
                class="flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-300 transition-colors"
            >
                Back
            </button>

            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-2xl font-bold text-[#333333]">{{ complaint.complaintCode || 'Complaint' }}</h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" :class="statusClass(complaint.status)">
                        {{ statusLabel(complaint.status) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Submitted By</p>
                        <p class="text-gray-900">{{ complaint.submittedByName }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Department</p>
                        <p class="text-gray-900">{{ complaint.department || '--' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Reported Employee</p>
                        <p class="text-gray-900">{{ complaint.reportedEmployeeName || '--' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Complaint Type</p>
                        <p class="text-gray-900">{{ complaint.complaintType }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Incident Date</p>
                        <p class="text-gray-900">{{ formatDate(complaint.incidentDate) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Submitted Date</p>
                        <p class="text-gray-900">{{ formatDate(complaint.createdAt) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-3">Description</h2>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ complaint.description || '--' }}</p>
            </div>

            <div v-if="complaint.rejectionReason" class="bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-xs font-semibold text-red-700 uppercase tracking-wide mb-1">Rejection Reason</p>
                <p class="text-sm text-red-900">{{ complaint.rejectionReason }}</p>
            </div>

            <div v-if="complaint.actionTaken" class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-xs font-semibold text-green-700 uppercase tracking-wide mb-1">Action Taken</p>
                <p class="text-sm text-green-900">{{ complaint.actionTaken }}</p>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 flex justify-end gap-3" v-if="canRenderActions">
                <button
                    v-if="canReject"
                    @click="rejectComplaint"
                    class="px-6 py-2.5 bg-red-600 text-white rounded-lg font-medium text-sm hover:bg-red-700 transition-colors"
                >
                    Reject
                </button>
                <button
                    v-if="canApprove"
                    @click="approveComplaint"
                    class="px-6 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-medium text-sm hover:bg-blue-700 transition-colors"
                >
                    Approve to Pending
                </button>
                <button
                    v-if="canTakeAction"
                    @click="takeAction"
                    class="px-6 py-2.5 bg-green-600 text-white rounded-lg font-medium text-sm hover:bg-green-700 transition-colors"
                >
                    Take Action
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'
import AuthLayout from '@/views/Layouts/AuthLayout.vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const route = useRoute()
const router = useRouter()

const complaint = ref({})

const roleBlob = computed(() => {
    return Array.isArray(auth.role)
        ? auth.role.join(' ').toLowerCase()
        : String(auth.role || '').toLowerCase()
})

const canScreen = computed(() => {
    return roleBlob.value.includes('hr') || roleBlob.value.includes('manager') || roleBlob.value.includes('admin')
})

const canManager = computed(() => {
    return roleBlob.value.includes('manager') || roleBlob.value.includes('admin')
})

const canReject = computed(() => canScreen.value && complaint.value.status === 'submitted')
const canApprove = computed(() => canScreen.value && complaint.value.status === 'submitted')
const canTakeAction = computed(() => canManager.value && complaint.value.status === 'pending')
const canRenderActions = computed(() => canReject.value || canApprove.value || canTakeAction.value)

const fetchDetail = async () => {
    try {
        const response = await axios.get(`/api/disciplinary/complaints/${route.params.id}`)
        complaint.value = response.data?.data || {}
    } catch (error) {
        console.error('Failed to load complaint detail', error)
        Swal.fire({
            icon: 'error',
            title: 'Load failed',
            text: 'Unable to load complaint details.',
            confirmButtonColor: '#ef4444'
        }).then(() => {
            router.push({ name: 'employee_complaints' })
        })
    }
}

const approveComplaint = async () => {
    try {
        await axios.post(`/api/disciplinary/complaints/${complaint.value.id}/approve`)
        await fetchDetail()
        Swal.fire({
            icon: 'success',
            title: 'Approved',
            text: 'Complaint moved to pending manager review.',
            confirmButtonColor: '#0c8ce9'
        })
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Approve failed',
            text: error.response?.data?.message || 'Unable to approve complaint.',
            confirmButtonColor: '#ef4444'
        })
    }
}

const rejectComplaint = async () => {
    const result = await Swal.fire({
        title: 'Reject Complaint',
        input: 'textarea',
        inputLabel: 'Reason',
        inputPlaceholder: 'Provide rejection reason...',
        showCancelButton: true,
        confirmButtonText: 'Reject',
        confirmButtonColor: '#dc2626',
        inputValidator: (value) => {
            if (!value || !value.trim()) {
                return 'Rejection reason is required.'
            }
            return null
        }
    })

    if (!result.isConfirmed) return

    try {
        await axios.post(`/api/disciplinary/complaints/${complaint.value.id}/reject`, {
            reason: result.value
        })
        await fetchDetail()
        Swal.fire({
            icon: 'success',
            title: 'Rejected',
            text: 'Complaint rejected and employee notified.',
            confirmButtonColor: '#0c8ce9'
        })
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Reject failed',
            text: error.response?.data?.message || 'Unable to reject complaint.',
            confirmButtonColor: '#ef4444'
        })
    }
}

const takeAction = async () => {
    const { value: formValues } = await Swal.fire({
        title: 'Take Action',
        html: `
            <div class="text-left">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Record Type</label>
                <input id="recordType" class="swal2-input" placeholder="e.g. Policy Violation" value="${complaint.value.recordType || complaint.value.complaintType || ''}">
                <label class="block text-sm font-semibold text-gray-700 mt-3 mb-2">Action Taken</label>
                <textarea id="actionTaken" class="swal2-textarea" placeholder="Describe the implemented action"></textarea>
                <label class="block text-sm font-semibold text-gray-700 mt-3 mb-2">Manager Notes (Optional)</label>
                <textarea id="managerNotes" class="swal2-textarea" placeholder="Optional notes"></textarea>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Submit Action',
        confirmButtonColor: '#16a34a',
        preConfirm: () => {
            const recordType = document.getElementById('recordType')?.value?.trim()
            const actionTaken = document.getElementById('actionTaken')?.value?.trim()
            const managerNotes = document.getElementById('managerNotes')?.value?.trim()

            if (!recordType || !actionTaken) {
                Swal.showValidationMessage('Record Type and Action Taken are required.')
                return null
            }

            return { recordType, actionTaken, managerNotes }
        }
    })

    if (!formValues) return

    try {
        await axios.post(`/api/disciplinary/complaints/${complaint.value.id}/take-action`, {
            record_type: formValues.recordType,
            action_taken: formValues.actionTaken,
            manager_notes: formValues.managerNotes || null
        })
        await fetchDetail()
        Swal.fire({
            icon: 'success',
            title: 'Reviewed',
            text: 'Complaint reviewed and employee record updated.',
            confirmButtonColor: '#0c8ce9'
        })
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Action failed',
            text: error.response?.data?.message || 'Unable to complete action.',
            confirmButtonColor: '#ef4444'
        })
    }
}

const statusClass = (status) => {
    const map = {
        submitted: 'bg-blue-100 text-blue-800',
        pending: 'bg-yellow-100 text-yellow-800',
        reviewed: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800'
    }

    return map[status] || 'bg-gray-100 text-gray-700'
}

const statusLabel = (status) => {
    const map = {
        submitted: 'Submitted',
        pending: 'Pending Manager Review',
        reviewed: 'Reviewed',
        rejected: 'Rejected'
    }

    return map[status] || status
}

const formatDate = (value) => {
    if (!value) return '--'
    return new Date(value).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

onMounted(() => {
    auth.pageTitle = 'Complaint Detail'
    fetchDetail()
})
</script>
