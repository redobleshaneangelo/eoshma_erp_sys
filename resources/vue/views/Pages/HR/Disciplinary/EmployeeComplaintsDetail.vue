<template>
    <auth-layout />
        <div class="ps-60 bg-[#F3F4F6] min-h-screen">
            <div class="px-10 py-10">
                <!-- Page Header -->
                <div class="mb-6 flex items-center justify-between">
                    <button
                        @click="goBack"
                        class="flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-300 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back
                    </button>
                </div>

                <!-- Complaint Header Card -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h1 class="text-2xl font-bold text-[#333333]">Complaint {{ complaint.id }}</h1>
                        <div class="flex gap-2">
                            <span :class="getSeverityBadgeClass(complaint.severity)" class="px-3 py-1 rounded-full text-xs font-semibold">
                                {{ complaint.severity }}
                            </span>
                            <span :class="getStatusBadgeClass(complaint.status)" class="px-3 py-1 rounded-full text-xs font-semibold">
                                {{ complaint.status }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="grid grid-cols-3 gap-6 mb-6">
                    <!-- Left Column - Main Details -->
                    <div class="col-span-2">
                        <!-- Complaint Details Card -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Details</h2>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Employee</p>
                                    <p class="text-sm text-gray-900 font-semibold">{{ complaint.employeeName }}</p>
                                    <p class="text-xs text-gray-500">{{ complaint.employeeId }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Department</p>
                                    <p class="text-sm text-gray-900">{{ complaint.department }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Complaint Type</p>
                                    <p class="text-sm text-gray-900">{{ complaint.complaintType }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Incident Date</p>
                                    <p class="text-sm text-gray-900">{{ formatDate(complaint.incidentDate) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Submitted Date</p>
                                    <p class="text-sm text-gray-900">{{ formatDate(complaint.submittedDate) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Submitted By</p>
                                    <p class="text-sm text-gray-900">{{ complaint.submittedBy }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Description Card -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Description</h2>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ complaint.description }}</p>
                        </div>

                        <!-- Attachments Card -->
                        <div v-if="complaint.attachments && complaint.attachments.length > 0" class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Attachments</h2>
                            <div class="space-y-2">
                                <div
                                    v-for="(file, idx) in complaint.attachments"
                                    :key="idx"
                                    class="flex items-center justify-between bg-gray-50 p-3 rounded-lg"
                                >
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-sm text-gray-700">{{ file.name }}</span>
                                    </div>
                                    <button
                                        @click="downloadAttachment(file)"
                                        class="text-[#0c8ce9] hover:text-blue-700 text-xs font-medium"
                                    >
                                        Download
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Internal Notes Card (HR Only) -->
                        <div v-if="canApprove && complaint.internalNotes" class="bg-white rounded-lg border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Internal Notes</h2>
                            <p class="text-sm text-gray-700 bg-yellow-50 p-3 rounded-lg">{{ complaint.internalNotes }}</p>
                        </div>
                    </div>

                    <!-- Right Column - Sidebar -->
                    <div>
                        <!-- Assigned To Card -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                            <h3 class="text-sm font-bold text-gray-900 mb-3">Assigned To</h3>
                            <p class="text-sm text-gray-700">{{ complaint.assignedTo || 'Unassigned' }}</p>
                        </div>

                        <!-- Linked Disciplinary Card -->
                        <div v-if="complaint.linkedDisciplinaryId" class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                            <h3 class="text-sm font-bold text-gray-900 mb-3">Linked Disciplinary</h3>
                            <p class="text-sm text-[#0c8ce9] font-semibold cursor-pointer hover:underline">
                                {{ complaint.linkedDisciplinaryId }}
                            </p>
                        </div>

                        <!-- Audit Trail Card -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-sm font-bold text-gray-900 mb-3">Audit Trail</h3>
                            <div class="space-y-3 text-xs text-gray-700">
                                <div class="pb-3 border-b border-gray-200">
                                    <p class="font-semibold text-gray-900">Submitted</p>
                                    <p class="text-gray-500">{{ complaint.submittedBy }} on {{ formatDate(complaint.submittedDate) }}</p>
                                </div>
                                <div v-if="complaint.reviewedBy" class="pb-3 border-b border-gray-200">
                                    <p class="font-semibold text-gray-900">Reviewed</p>
                                    <p class="text-gray-500">{{ complaint.reviewedBy }} on {{ formatDate(complaint.reviewedDate) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div v-if="canApprove && complaint.status === 'Pending'" class="bg-white rounded-lg border border-gray-200 p-6 flex gap-3 justify-end">
                    <button
                        @click="rejectComplaint"
                        class="px-6 py-2.5 bg-red-600 text-white rounded-lg font-medium text-sm hover:bg-red-700 transition-colors"
                    >
                        Reject
                    </button>
                    <button
                        @click="approveComplaint"
                        class="px-6 py-2.5 bg-green-600 text-white rounded-lg font-medium text-sm hover:bg-green-700 transition-colors"
                    >
                        Approve & Create Disciplinary
                    </button>
                </div>
            </div>
        </div>
</template>

<script setup>
    import { ref, onMounted, computed } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import Swal from 'sweetalert2'
    import { useAuthStore } from '@/stores/auth'

    const router = useRouter()
    const route = useRoute()
    const auth = useAuthStore()

    const complaint = ref({})
    const canApprove = computed(() => auth.role === 'hr_manager')

    // Mock data - same as parent component
    const allComplaints = ref([
        {
            id: 'CMP-001',
            employeeId: 'EMP-001',
            employeeName: 'John Smith',
            department: 'IT',
            complaintType: 'Harassment',
            description: 'Experienced inappropriate behavior from manager during team meeting on Jan 15. Comments were unprofessional and made me uncomfortable.',
            severity: 'High',
            submittedDate: '2026-01-25',
            status: 'Approved',
            attachments: [],
            assignedTo: 'Sarah Johnson',
            internalNotes: 'Manager meeting scheduled for follow-up. Document and escalate if needed.',
            submittedBy: 'John Smith',
            reviewedBy: 'Sarah Johnson',
            reviewedDate: '2026-01-26',
            incidentDate: '2026-01-15',
            linkedDisciplinaryId: 'DR-004'
        },
        {
            id: 'CMP-002',
            employeeId: 'EMP-002',
            employeeName: 'Sarah Johnson',
            department: 'HR',
            complaintType: 'Policy Violation',
            description: 'Colleague failed to follow proper leave approval process and took unauthorized time off.',
            severity: 'Medium',
            submittedDate: '2026-01-28',
            status: 'Pending',
            attachments: [],
            assignedTo: 'Michael Chen',
            internalNotes: 'Requires policy review and employee counseling',
            submittedBy: 'Sarah Johnson',
            reviewedBy: null,
            reviewedDate: null,
            incidentDate: '2026-01-22',
            linkedDisciplinaryId: null
        },
        {
            id: 'CMP-003',
            employeeId: 'EMP-003',
            employeeName: 'Michael Chen',
            department: 'Finance',
            complaintType: 'Safety',
            description: 'Safety hazard in the finance department office area. Broken furniture and electrical outlet malfunction pose risks.',
            severity: 'High',
            submittedDate: '2026-01-27',
            status: 'Approved',
            attachments: [],
            assignedTo: 'Sarah Johnson',
            internalNotes: 'Maintenance team notified. Follow up on repairs.',
            submittedBy: 'Michael Chen',
            reviewedBy: 'Sarah Johnson',
            reviewedDate: '2026-01-27',
            incidentDate: '2026-01-25',
            linkedDisciplinaryId: null
        }
    ])

    const formatDate = (date) => {
        return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
    }

    const getSeverityBadgeClass = (severity) => {
        const classes = {
            'Low': 'bg-green-100 text-green-800',
            'Medium': 'bg-orange-100 text-orange-800',
            'High': 'bg-red-100 text-red-800'
        }
        return classes[severity] || 'bg-gray-100 text-gray-800'
    }

    const getStatusBadgeClass = (status) => {
        const classes = {
            'Pending': 'bg-yellow-100 text-yellow-800',
            'Approved': 'bg-green-100 text-green-800',
            'Rejected': 'bg-red-100 text-red-800'
        }
        return classes[status] || 'bg-gray-100 text-gray-800'
    }

    const goBack = () => {
        router.push('/disciplinary/complaints')
    }

    const downloadAttachment = (file) => {
        Swal.fire({
            icon: 'info',
            title: 'Download',
            text: `Downloading ${file.name}...`,
            confirmButtonColor: '#0c8ce9'
        })
        // API: GET /api/complaints/{complaintId}/attachments/{attachmentId}/download
    }

    const approveComplaint = async () => {
        const { value: shouldCreateDisciplinary } = await Swal.fire({
            title: 'Approve Complaint?',
            html: 'Do you want to create a linked Disciplinary Record for follow-up?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Create Record',
            cancelButtonText: 'Just Approve',
            confirmButtonColor: '#0c8ce9',
            cancelButtonColor: '#6b7280'
        })

        if (shouldCreateDisciplinary !== undefined) {
            const idx = allComplaints.value.findIndex(c => c.id === complaint.value.id)
            if (idx !== -1) {
                allComplaints.value[idx].status = 'Approved'
                allComplaints.value[idx].reviewedBy = auth.userName || 'Current User'
                allComplaints.value[idx].reviewedDate = new Date().toISOString().split('T')[0]

                if (shouldCreateDisciplinary) {
                    const disciplinaryId = `DR-${String(Math.random()).slice(2, 5)}`
                    allComplaints.value[idx].linkedDisciplinaryId = disciplinaryId

                    Swal.fire({
                        icon: 'success',
                        title: 'Approved & Linked',
                        html: `Complaint approved<br/>Disciplinary Record: <strong>${disciplinaryId}</strong>`,
                        confirmButtonColor: '#0c8ce9'
                    })
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Approved',
                        text: 'Complaint approved successfully',
                        confirmButtonColor: '#0c8ce9'
                    })
                }
                
                complaint.value = allComplaints.value[idx]
            }
        }
    }

    const rejectComplaint = async () => {
        const { value: rejectionReason } = await Swal.fire({
            title: 'Reject Complaint?',
            input: 'textarea',
            inputLabel: 'Reason for rejection',
            inputPlaceholder: 'Enter reason...',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Reject',
            inputValidator: (value) => {
                if (!value) return 'Please provide a reason'
            }
        })

        if (rejectionReason) {
            const idx = allComplaints.value.findIndex(c => c.id === complaint.value.id)
            if (idx !== -1) {
                allComplaints.value[idx].status = 'Rejected'
                allComplaints.value[idx].reviewedBy = auth.userName || 'Current User'
                allComplaints.value[idx].reviewedDate = new Date().toISOString().split('T')[0]
                allComplaints.value[idx].internalNotes = rejectionReason

                Swal.fire({
                    icon: 'success',
                    title: 'Rejected',
                    text: 'Complaint rejected successfully',
                    confirmButtonColor: '#0c8ce9'
                })

                complaint.value = allComplaints.value[idx]
            }
        }
    }

    onMounted(() => {
        const complaintId = route.params.id
        const found = allComplaints.value.find(c => c.id === complaintId)
        
        if (found) {
            complaint.value = found
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Not Found',
                text: 'Complaint not found',
                confirmButtonColor: '#0c8ce9'
            }).then(() => {
                router.push('/complaints')
            })
        }
    })
</script>
