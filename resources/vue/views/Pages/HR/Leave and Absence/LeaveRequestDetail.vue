<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">
            <div class="mb-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <router-link
                        to="/leave-and-absence"
                        class="text-[#0c8ce9] hover:text-blue-700 font-semibold text-sm flex items-center gap-2 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Leave Requests
                    </router-link>
                    <span :class="getStatusBadgeClass(request.status)" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold">
                        <span v-if="request.status === 'pending'" class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                        <span v-else-if="request.status === 'approved'" class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        <span v-else-if="request.status === 'rejected'" class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                        <span v-else-if="request.status === 'info_requested'" class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                        {{ capitalizeStatus(request.status) }}
                    </span>
                </div>

                <div v-if="isReviewer && request.status === 'pending'" class="flex gap-3">
                    <button @click="approveRequest" class="px-6 py-2.5 bg-green-600 text-white rounded-lg font-medium text-sm hover:bg-green-700 transition-colors">Approve</button>
                    <button @click="rejectRequest" class="px-6 py-2.5 bg-red-600 text-white rounded-lg font-medium text-sm hover:bg-red-700 transition-colors">Reject</button>
                    <button @click="requestMoreInfo" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-medium text-sm hover:bg-blue-700 transition-colors">Request Info</button>
                </div>
            </div>

            <div class="grid grid-cols-4 gap-3 mb-8">
                <div class="bg-white rounded-lg p-4 border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Request ID</p>
                    <p class="text-lg font-bold text-[#0c8ce9]">{{ request.requestCode || `LR-${request.id}` }}</p>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Leave Type</p>
                    <p class="text-lg font-bold text-[#333333]">{{ request.leaveType }}</p>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Total Days</p>
                    <p class="text-lg font-bold text-[#333333]">{{ request.totalDays }} days</p>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Submitted</p>
                    <p class="text-lg font-bold text-[#333333]">{{ formatDate(request.submissionDate) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2 space-y-3">
                    <div class="bg-white rounded-lg border border-gray-100 p-6">
                        <h2 class="text-lg font-bold text-[#333333] mb-4">Employee Information</h2>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Name</p>
                                <p class="text-base font-semibold text-[#333333]">{{ request.employeeName }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Department</p>
                                <p class="text-base font-semibold text-[#333333]">{{ request.department }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-100 p-6">
                        <h2 class="text-lg font-bold text-[#333333] mb-4">Leave Details</h2>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Start Date</p>
                                <p class="text-base font-semibold text-[#333333]">{{ formatDate(request.startDate) }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">End Date</p>
                                <p class="text-base font-semibold text-[#333333]">{{ formatDate(request.endDate) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-100 p-6">
                        <h2 class="text-lg font-bold text-[#333333] mb-4">Reason / Notes</h2>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ request.reason }}</p>
                    </div>

                    <div v-if="request.requestInfoMessage" class="bg-blue-50 rounded-lg border border-blue-200 p-6">
                        <h2 class="text-lg font-bold text-blue-900 mb-2">HR Requested Information</h2>
                        <p class="text-sm text-blue-800">{{ request.requestInfoMessage }}</p>
                    </div>

                    <div v-if="isOwner && request.status === 'info_requested'" class="bg-white rounded-lg border border-gray-100 p-6 space-y-3">
                        <h2 class="text-lg font-bold text-[#333333]">Respond to Request Info</h2>
                        <textarea
                            v-model="responseMessage"
                            rows="4"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                            placeholder="Provide the additional information requested by HR..."
                        ></textarea>
                        <button
                            @click="sendBackToHr"
                            class="px-6 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-medium text-sm hover:bg-blue-700 transition-colors"
                        >
                            Send Back to HR
                        </button>
                    </div>
                </div>

                <div class="col-span-1 space-y-3">
                    <div class="bg-white rounded-lg border border-gray-100 p-6">
                        <h2 class="text-lg font-bold text-[#333333] mb-4">Status</h2>
                        <div class="p-4 rounded-lg" :class="statusPanelClass">
                            <p :class="statusTextClass" class="font-semibold text-sm">{{ capitalizeStatus(request.status) }}</p>
                            <p class="text-xs mt-1" :class="statusSubTextClass">Updated {{ formatDateTime(request.reviewedAt || request.submissionDate) }}</p>
                        </div>
                        <div v-if="request.approverNotes" class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Notes / Reason</p>
                            <p class="text-sm text-gray-700">{{ request.approverNotes }}</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-100 p-6">
                        <h2 class="text-lg font-bold text-[#333333] mb-4">Audit Trail</h2>
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Submitted</p>
                                <p class="text-gray-700">{{ formatDateTime(request.submissionDate) }}</p>
                            </div>
                            <div v-if="request.resubmittedAt">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Resubmitted</p>
                                <p class="text-gray-700">{{ formatDateTime(request.resubmittedAt) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fixed bottom-0 right-0 p-2 z-50">
            <img :src="agnes" class="h-auto w-auto" v-if="loading" />
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import agnes from '@/assets/agnes.gif'
import Swal from 'sweetalert2'

const auth = useAuthStore()
const route = useRoute()

const loading = ref(false)
const responseMessage = ref('')

const request = ref({
    id: null,
    requestCode: '',
    employeeName: '',
    requesterEmail: '',
    department: '',
    leaveType: '',
    startDate: '',
    endDate: '',
    totalDays: 0,
    submissionDate: '',
    status: 'pending',
    reason: '',
    approverNotes: '',
    requestInfoMessage: '',
    resubmittedAt: '',
    reviewedAt: '',
})

const roleText = computed(() => {
    if (Array.isArray(auth.role)) return auth.role.join(',').toLowerCase()
    return String(auth.role || '').toLowerCase()
})

const isReviewer = computed(() => {
    return String(auth.department || '').toLowerCase() === 'hr'
        || roleText.value.includes('manager')
        || roleText.value.includes('admin')
})

const currentUserName = computed(() => {
    return [auth.user?.first_name, auth.user?.middle_name, auth.user?.last_name].filter(Boolean).join(' ').trim()
})

const isOwner = computed(() => {
    const requesterEmail = String(request.value.requesterEmail || '').trim().toLowerCase()
    const myEmail = String(auth.user?.email || '').trim().toLowerCase()
    return requesterEmail !== '' && myEmail !== '' && requesterEmail === myEmail
})

const mapRequest = (payload) => ({
    id: payload.id,
    requestCode: payload.requestCode || payload.request_code || `LR-${payload.id}`,
    employeeName: payload.employeeName || payload.employee_name || '',
    requesterEmail: payload.requesterEmail || payload.requester_email || '',
    department: payload.department || '',
    leaveType: payload.leaveType || payload.leave_type || '',
    startDate: payload.startDate || payload.start_date || '',
    endDate: payload.endDate || payload.end_date || '',
    totalDays: payload.totalDays || payload.total_days || 0,
    submissionDate: payload.submissionDate || payload.submission_date || payload.created_at || '',
    status: payload.status || 'pending',
    reason: payload.reason || '',
    approverNotes: payload.approverNotes || payload.approver_notes || '',
    requestInfoMessage: payload.requestInfoMessage || payload.request_info_message || '',
    resubmittedAt: payload.resubmittedAt || payload.resubmitted_at || '',
    reviewedAt: payload.reviewedAt || payload.reviewed_at || '',
})

const fetchRequest = async () => {
    loading.value = true
    try {
        const response = await axios.get(`/api/leave-requests/${route.params.id}`)
        request.value = mapRequest(response.data?.data || {})
    } catch (error) {
        console.error('Failed to load leave request', error)
        const message = error.response?.data?.message || 'Failed to load leave request.'
        Swal.fire({ icon: 'error', title: 'Load failed', text: message, confirmButtonColor: '#ef4444' })
    } finally {
        loading.value = false
    }
}

const approveRequest = async () => {
    const result = await Swal.fire({
        title: 'Approve leave request?',
        input: 'textarea',
        inputLabel: 'Approval Notes (Optional)',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        confirmButtonText: 'Approve'
    })

    if (!result.isConfirmed) return

    try {
        const response = await axios.post(`/api/leave-requests/${request.value.id}/approve`, { notes: result.value || null })
        request.value = mapRequest(response.data?.data || request.value)
        Swal.fire({ icon: 'success', title: 'Approved', text: 'Leave request approved.', confirmButtonColor: '#0c8ce9' })
    } catch (error) {
        const message = error.response?.data?.message || 'Failed to approve request.'
        Swal.fire({ icon: 'error', title: 'Approval failed', text: message, confirmButtonColor: '#ef4444' })
    }
}

const rejectRequest = async () => {
    const result = await Swal.fire({
        title: 'Reject leave request?',
        input: 'textarea',
        inputLabel: 'Rejection Reason',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Reject',
        preConfirm: (value) => {
            if (!value || !value.trim()) {
                Swal.showValidationMessage('Rejection reason is required.')
                return null
            }
            return value.trim()
        }
    })

    if (!result.isConfirmed) return

    try {
        const response = await axios.post(`/api/leave-requests/${request.value.id}/reject`, { reason: result.value })
        request.value = mapRequest(response.data?.data || request.value)
        Swal.fire({ icon: 'success', title: 'Rejected', text: 'Leave request rejected.', confirmButtonColor: '#0c8ce9' })
    } catch (error) {
        const message = error.response?.data?.message || 'Failed to reject request.'
        Swal.fire({ icon: 'error', title: 'Rejection failed', text: message, confirmButtonColor: '#ef4444' })
    }
}

const requestMoreInfo = async () => {
    const result = await Swal.fire({
        title: 'Request more information',
        input: 'textarea',
        inputLabel: 'Message to Employee',
        showCancelButton: true,
        confirmButtonColor: '#0c8ce9',
        confirmButtonText: 'Send',
        preConfirm: (value) => {
            if (!value || !value.trim()) {
                Swal.showValidationMessage('Message is required.')
                return null
            }
            return value.trim()
        }
    })

    if (!result.isConfirmed) return

    try {
        const response = await axios.post(`/api/leave-requests/${request.value.id}/request-info`, { message: result.value })
        request.value = mapRequest(response.data?.data || request.value)
        Swal.fire({ icon: 'success', title: 'Sent', text: 'Request info sent to employee.', confirmButtonColor: '#0c8ce9' })
    } catch (error) {
        const message = error.response?.data?.message || 'Failed to request more information.'
        Swal.fire({ icon: 'error', title: 'Request failed', text: message, confirmButtonColor: '#ef4444' })
    }
}

const sendBackToHr = async () => {
    if (!responseMessage.value.trim()) {
        Swal.fire({ icon: 'error', title: 'Validation Error', text: 'Please provide the requested information.', confirmButtonColor: '#ef4444' })
        return
    }

    try {
        const response = await axios.post(`/api/leave-requests/${request.value.id}/respond-info`, {
            response_message: responseMessage.value.trim(),
        })
        request.value = mapRequest(response.data?.data || request.value)
        responseMessage.value = ''
        Swal.fire({ icon: 'success', title: 'Sent Back', text: 'Your update was sent back to HR.', confirmButtonColor: '#0c8ce9' })
    } catch (error) {
        const message = error.response?.data?.message || 'Failed to send response.'
        Swal.fire({ icon: 'error', title: 'Send failed', text: message, confirmButtonColor: '#ef4444' })
    }
}

const getStatusBadgeClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
        info_requested: 'bg-blue-100 text-blue-800',
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const capitalizeStatus = (status) => {
    if (status === 'info_requested') return 'Info Requested'
    return status.charAt(0).toUpperCase() + status.slice(1)
}

const formatDate = (date) => {
    if (!date) return '--'
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

const formatDateTime = (date) => {
    if (!date) return '--'
    return new Date(date).toLocaleString('en-PH', {
        year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit'
    })
}

const statusPanelClass = computed(() => {
    if (request.value.status === 'approved') return 'bg-green-50 border border-green-200'
    if (request.value.status === 'rejected') return 'bg-red-50 border border-red-200'
    if (request.value.status === 'info_requested') return 'bg-blue-50 border border-blue-200'
    return 'bg-yellow-50 border border-yellow-200'
})

const statusTextClass = computed(() => {
    if (request.value.status === 'approved') return 'text-green-800'
    if (request.value.status === 'rejected') return 'text-red-800'
    if (request.value.status === 'info_requested') return 'text-blue-800'
    return 'text-yellow-800'
})

const statusSubTextClass = computed(() => {
    if (request.value.status === 'approved') return 'text-green-700'
    if (request.value.status === 'rejected') return 'text-red-700'
    if (request.value.status === 'info_requested') return 'text-blue-700'
    return 'text-yellow-700'
})

onMounted(() => {
    auth.pageTitle = 'Leave Request Detail'
    fetchRequest()
})
</script>
