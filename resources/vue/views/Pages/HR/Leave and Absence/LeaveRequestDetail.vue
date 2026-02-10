<template>
    <auth-layout />
        <div class="ps-60 bg-[#F3F4F6] min-h-screen">
            <div class="px-10 py-10">
                    <!-- Back Button and Header -->
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
                            <span
                                :class="getStatusBadgeClass(request.status)"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                            >
                                <span v-if="request.status === 'pending'" class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                                <span v-else-if="request.status === 'approved'" class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                <span v-else-if="request.status === 'rejected'" class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                {{ capitalizeStatus(request.status) }}
                            </span>
                        </div>
                        <!-- Top Action Buttons - Pending Only -->
                        <div v-if="request.status === 'pending'" class="flex gap-3">
                            <button
                                @click="approveRequest"
                                class="px-6 py-2.5 bg-green-600 text-white rounded-lg font-medium text-sm hover:bg-green-700 transition-colors flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Approve
                            </button>
                            <button
                                @click="rejectRequest"
                                class="px-6 py-2.5 bg-red-600 text-white rounded-lg font-medium text-sm hover:bg-red-700 transition-colors flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Reject
                            </button>
                            <button
                                @click="requestMoreInfo"
                                class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-medium text-sm hover:bg-blue-700 transition-colors flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Request Info
                            </button>
                        </div>

                        <!-- Approved/Rejected Info Button -->
                        <div v-else class="flex gap-3">
                            <button
                                @click="downloadRequest"
                                class="px-6 py-2.5 bg-gray-600 text-white rounded-lg font-medium text-sm hover:bg-gray-700 transition-colors flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Download
                            </button>
                        </div>
                    </div>

                    <!-- Request Information Cards -->
                    <div class="grid grid-cols-4 gap-3 mb-8">
                        <div class="bg-white rounded-lg p-4 border border-gray-100 shadow-sm">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Request ID</p>
                            <p class="text-lg font-bold text-[#0c8ce9]">{{ request.id }}</p>
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
                        <!-- Main Details (Left Column) -->
                        <div class="col-span-2 space-y-3">
                            <!-- Employee Information Section -->
                            <div class="bg-white rounded-lg border border-gray-100 p-6">
                                <h2 class="text-lg font-bold text-[#333333] mb-4 flex items-center gap-2">
                                    <span class="w-1 h-6 bg-[#0c8ce9] rounded"></span>
                                    Employee Information
                                </h2>
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

                            <!-- Leave Details Section -->
                            <div class="bg-white rounded-lg border border-gray-100 p-6">
                                <h2 class="text-lg font-bold text-[#333333] mb-4 flex items-center gap-2">
                                    <span class="w-1 h-6 bg-[#0c8ce9] rounded"></span>
                                    Leave Details
                                </h2>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Leave Type</p>
                                            <p class="text-base font-semibold text-[#333333]">{{ request.leaveType }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Total Duration</p>
                                            <p class="text-base font-semibold text-[#333333]">{{ request.totalDays }} days</p>
                                        </div>
                                    </div>
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
                            </div>

                            <!-- Reason Section -->
                            <div class="bg-white rounded-lg border border-gray-100 p-6">
                                <h2 class="text-lg font-bold text-[#333333] mb-4 flex items-center gap-2">
                                    <span class="w-1 h-6 bg-[#0c8ce9] rounded"></span>
                                    Reason / Notes
                                </h2>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ request.reason }}</p>
                            </div>

                            <!-- Attachments Section -->
                            <div class="bg-white rounded-lg border border-gray-100 p-6">
                                <h2 class="text-lg font-bold text-[#333333] mb-4 flex items-center gap-2">
                                    <span class="w-1 h-6 bg-[#0c8ce9] rounded"></span>
                                    Attachments
                                </h2>
                                <div v-if="request.attachments && request.attachments.length > 0" class="space-y-2">
                                    <div v-for="(attachment, index) in request.attachments" :key="index" class="flex items-center justify-between bg-gray-50 p-3 rounded border border-gray-200">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                            <span class="text-sm text-gray-700">{{ attachment.name }}</span>
                                        </div>
                                        <button class="text-[#0c8ce9] hover:text-blue-700 font-medium text-xs transition-colors">
                                            Download
                                        </button>
                                    </div>
                                </div>
                                <div v-else class="text-sm text-gray-500 italic">
                                    No attachments
                                </div>
                            </div>

                            <!-- Approval Timeline Section -->
                            <div v-if="request.status !== 'pending'" class="bg-white rounded-lg border border-gray-100 p-6">
                                <h2 class="text-lg font-bold text-[#333333] mb-4 flex items-center gap-2">
                                    <span class="w-1 h-6 bg-[#0c8ce9] rounded"></span>
                                    Approval Timeline
                                </h2>
                                <div class="space-y-3">
                                    <div v-if="request.status === 'approved'" class="flex gap-4">
                                        <div class="flex flex-col items-center">
                                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700">Approved</p>
                                            <p class="text-xs text-gray-500">{{ formatDate(request.approvalDate) }}</p>
                                            <p v-if="request.approverNotes" class="text-sm text-gray-600 mt-2">{{ request.approverNotes }}</p>
                                        </div>
                                    </div>
                                    <div v-else-if="request.status === 'rejected'" class="flex gap-4">
                                        <div class="flex flex-col items-center">
                                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700">Rejected</p>
                                            <p class="text-xs text-gray-500">{{ formatDate(request.rejectionDate) }}</p>
                                            <p v-if="request.approverNotes" class="text-sm text-gray-600 mt-2">{{ request.approverNotes }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Approved Leave Details Component -->
                            <ApprovedLeaveDetails
                                v-if="request.status === 'approved'"
                                :approval-data="{
                                    approvalDate: request.approvalDate,
                                    approverNotes: request.approverNotes
                                }"
                            />

                            <!-- Rejected Leave Details Component -->
                            <RejectedLeaveDetails
                                v-else-if="request.status === 'rejected'"
                                :rejection-data="{
                                    rejectionDate: request.rejectionDate,
                                    rejectionReason: request.approverNotes
                                }"
                            />
                        </div>

                        <!-- Action Panel (Right Column) -->
                        <div class="col-span-1">
                            <!-- Status Panel (if not pending) -->
                            <div class="bg-white rounded-lg border border-gray-100 p-6">
                                <h2 class="text-lg font-bold text-[#333333] mb-4">Status</h2>
                                <div class="p-4 rounded-lg"
                                     :class="request.status === 'approved' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
                                    <p :class="request.status === 'approved' ? 'text-green-800' : 'text-red-800'" class="font-semibold text-sm">
                                        {{ capitalizeStatus(request.status) }}
                                    </p>
                                    <p v-if="request.status === 'approved'" class="text-xs text-green-700 mt-1">
                                        Approved on {{ formatDate(request.approvalDate) }}
                                    </p>
                                    <p v-else class="text-xs text-red-700 mt-1">
                                        Rejected on {{ formatDate(request.rejectionDate) }}
                                    </p>
                                </div>
                                <div v-if="request.approverNotes" class="mt-4 pt-4 border-t border-gray-200">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Approver Notes</p>
                                    <p class="text-sm text-gray-700">{{ request.approverNotes }}</p>
                                </div>
                            </div>

                            <!-- Internal Notes Section (if pending) -->
                            <div v-if="request.status === 'pending'" class="mt-3 bg-white rounded-lg border border-gray-100 p-6">
                                <h3 class="text-lg font-bold text-[#333333] mb-4">Internal Notes</h3>
                                <textarea
                                    v-model="internalNotes"
                                    placeholder="Add internal notes for this request..."
                                    rows="5"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent resize-none"
                                ></textarea>
                                <p class="text-xs text-gray-500 mt-2">{{ internalNotes.length }}/500</p>
                            </div>

                            <!-- Audit Trail -->
                            <div class="mt-3 bg-white rounded-lg border border-gray-100 p-6">
                                <h2 class="text-lg font-bold text-[#333333] mb-4">Audit Trail</h2>
                                <div class="space-y-3 text-sm">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Submitted</p>
                                        <p class="text-gray-700">{{ formatDate(request.submissionDate) }}</p>
                                    </div>
                                    <div v-if="request.status !== 'pending'">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                            {{ request.status === 'approved' ? 'Approved' : 'Rejected' }}
                                        </p>
                                        <p class="text-gray-700">
                                            {{ formatDate(request.status === 'approved' ? request.approvalDate : request.rejectionDate) }}
                                        </p>
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
    import { ref, onMounted } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import axios from 'axios'
    import { useAuthStore } from '@/stores/auth'
    import ApprovedLeaveDetails from '@/components/HR/Leave/ApprovedLeaveDetails.vue'
    import RejectedLeaveDetails from '@/components/HR/Leave/RejectedLeaveDetails.vue'
    import agnes from '@/assets/agnes.gif'
    import Swal from 'sweetalert2'

    const auth = useAuthStore()
    const router = useRouter()
    const route = useRoute()

    const loading = ref(false)
    const internalNotes = ref('')

    const request = ref({
        id: 'LR-001',
        employeeName: 'John Smith',
        department: 'IT',
        leaveType: 'Sick Leave',
        startDate: '2026-02-05',
        endDate: '2026-02-06',
        totalDays: 2,
        submissionDate: '2026-01-31',
        status: 'pending',
        reason: 'Medical appointment and follow-up check at the hospital.',
        approverNotes: '',
        attachments: []
    })

    const formatDate = (date) => {
        return new Date(date).toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        })
    }

    const getStatusBadgeClass = (status) => {
        const classes = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'approved': 'bg-green-100 text-green-800',
            'rejected': 'bg-red-100 text-red-800'
        }
        return classes[status] || 'bg-gray-100 text-gray-800'
    }

    const capitalizeStatus = (status) => {
        return status.charAt(0).toUpperCase() + status.slice(1)
    }

    const approveRequest = async () => {
        const { value: notes } = await Swal.fire({
            title: 'Approve Leave Request',
            input: 'textarea',
            inputLabel: 'Approval Notes (Optional)',
            inputPlaceholder: 'Add any notes...',
            showCancelButton: true,
            confirmButtonColor: '#0c8ce9',
            confirmButtonText: 'Approve'
        })

        if (notes !== undefined) {
            request.value.status = 'approved'
            request.value.approvalDate = new Date().toISOString().split('T')[0]
            request.value.approverNotes = notes || ''

            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Leave request approved successfully',
                confirmButtonColor: '#0c8ce9'
            })
        }
    }

    const rejectRequest = async () => {
        const { value: notes } = await Swal.fire({
            title: 'Reject Leave Request',
            input: 'textarea',
            inputLabel: 'Rejection Reason',
            inputPlaceholder: 'Please provide a reason for rejection...',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Reject'
        })

        if (notes !== undefined) {
            request.value.status = 'rejected'
            request.value.rejectionDate = new Date().toISOString().split('T')[0]
            request.value.approverNotes = notes

            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Leave request rejected',
                confirmButtonColor: '#0c8ce9'
            })
        }
    }

    const requestMoreInfo = async () => {
        const { value: message } = await Swal.fire({
            title: 'Request More Information',
            input: 'textarea',
            inputLabel: 'Message to Employee',
            inputPlaceholder: 'What information do you need?',
            showCancelButton: true,
            confirmButtonColor: '#0c8ce9',
            confirmButtonText: 'Send'
        })

        if (message !== undefined) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Employee has been notified to provide more information',
                confirmButtonColor: '#0c8ce9'
            })
        }
    }

    const downloadRequest = () => {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Leave request downloaded as PDF',
            confirmButtonColor: '#0c8ce9'
        })
    }

    onMounted(() => {
        // Load specific leave request from API
        // For now using mock data based on route ID
        const requestId = route.params.id
        
        // Mock leave requests data - Replace with API call
        const mockRequests = {
            'LR-001': {
                id: 'LR-001',
                employeeName: 'John Smith',
                department: 'IT',
                leaveType: 'Sick Leave',
                startDate: '2026-02-05',
                endDate: '2026-02-06',
                totalDays: 2,
                submissionDate: '2026-01-31',
                status: 'pending',
                reason: 'Medical appointment and follow-up check at the hospital.',
                approverNotes: '',
                attachments: []
            },
            'LR-002': {
                id: 'LR-002',
                employeeName: 'Jane Doe',
                department: 'HR',
                leaveType: 'Vacation Leave',
                startDate: '2026-02-10',
                endDate: '2026-02-14',
                totalDays: 5,
                submissionDate: '2026-01-25',
                status: 'approved',
                reason: 'Family trip to Europe',
                approverNotes: 'Approved - Coverage arranged with Sarah for all HR operations. Travel plans confirmed.',
                approvalDate: '2026-01-26',
                attachments: [
                    { name: 'travel_itinerary.pdf', size: '2.4 MB' },
                    { name: 'hotel_confirmation.pdf', size: '1.2 MB' }
                ]
            },
            'LR-003': {
                id: 'LR-003',
                employeeName: 'Bob Johnson',
                department: 'Finance',
                leaveType: 'Emergency Leave',
                startDate: '2026-02-01',
                endDate: '2026-02-01',
                totalDays: 1,
                submissionDate: '2026-01-31',
                status: 'pending',
                reason: 'Family emergency',
                approverNotes: '',
                attachments: []
            },
            'LR-005': {
                id: 'LR-005',
                employeeName: 'Charlie Wilson',
                department: 'Operations',
                leaveType: 'Unpaid Leave',
                startDate: '2026-02-20',
                endDate: '2026-02-24',
                totalDays: 5,
                submissionDate: '2026-01-28',
                status: 'rejected',
                reason: 'Personal reasons - Extended leave for personal matters',
                approverNotes: 'Cannot accommodate at this time. Critical project deadline falls within requested dates. Please resubmit for alternative dates.',
                rejectionDate: '2026-01-29',
                attachments: []
            }
        }
        
        // Load request from mock data
        if (mockRequests[requestId]) {
            request.value = mockRequests[requestId]
        }
    })
</script>
