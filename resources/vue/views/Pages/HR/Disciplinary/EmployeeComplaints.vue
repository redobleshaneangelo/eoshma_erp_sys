<template>
    <auth-layout />
        <div class="ps-60 bg-[#F3F4F6] min-h-screen">
            <div class="px-10 py-10">
                    <!-- Page Header -->
                    <div class="mb-6 flex items-center justify-between">
                        <button
                            v-if="canSubmitComplaint"
                            @click="openAddComplaintModal"
                            class="px-6 py-2.5 bg-[#0c8ce9] ms-auto text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition-colors flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Complaint
                        </button>
                    </div>

                    <!-- Analytics Cards -->
                    <div class="grid grid-cols-5 gap-4 mb-8">
                        <div class="bg-white rounded-lg p-4 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Total Complaints</p>
                            <p class="text-2xl font-bold text-[#333333]">{{ allComplaints.length }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Pending</p>
                            <p class="text-2xl font-bold text-orange-600">{{ complaintsByStatus.pending }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Approved</p>
                            <p class="text-2xl font-bold text-green-600">{{ complaintsByStatus.approved }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Rejected</p>
                            <p class="text-2xl font-bold text-red-600">{{ complaintsByStatus.rejected }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">High Severity</p>
                            <p class="text-2xl font-bold text-red-700">{{ highSeverityComplaints }}</p>
                        </div>
                    </div>

                    <!-- Filters Section -->
                    <div class="p-6 bg-white rounded-lg">
                        <div class="flex justify-between items-center mb-10">
                            <div class="flex-1">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </span>
                                    <input 
                                        v-model="filters.searchTerm"
                                        type="text"
                                        placeholder="Search by employee name or ID..." 
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-4 gap-4 mb-10">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">Department</label>
                                <select 
                                    v-model="filters.department"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                >
                                    <option value="">All Departments</option>
                                    <option value="IT">IT</option>
                                    <option value="HR">HR</option>
                                    <option value="Finance">Finance</option>
                                    <option value="Operations">Operations</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">Complaint Type</label>
                                <select 
                                    v-model="filters.complaintType"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                >
                                    <option value="">All Types</option>
                                    <option value="Harassment">Harassment</option>
                                    <option value="Policy Violation">Policy Violation</option>
                                    <option value="Safety">Safety</option>
                                    <option value="Discrimination">Discrimination</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">Status</label>
                                <select 
                                    v-model="filters.status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                >
                                    <option value="">All Statuses</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Rejected">Rejected</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">Severity</label>
                                <select 
                                    v-model="filters.severity"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                >
                                    <option value="">All Severity</option>
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                </select>
                            </div>
                        </div>
                        <!-- Table Section -->

                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-linear-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Complaint ID</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Employee</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Department</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Type</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Severity</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Submitted Date</th>
                                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="complaint in filteredComplaints" :key="complaint.id" class="hover:bg-blue-50 transition-all duration-200">
                                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ complaint.id }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ complaint.employeeName }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ complaint.department }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ complaint.complaintType }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                <span :class="[
                                                    'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                                                    complaint.severity === 'Low'
                                                        ? 'bg-green-100 text-green-800'
                                                        : complaint.severity === 'Medium'
                                                            ? 'bg-orange-100 text-orange-800'
                                                            : 'bg-red-100 text-red-800'
                                                ]">
                                                    {{ complaint.severity }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <span :class="[
                                                    'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                                                    complaint.status === 'Pending'
                                                        ? 'bg-yellow-100 text-yellow-800'
                                                        : complaint.status === 'Approved'
                                                            ? 'bg-green-100 text-green-800'
                                                            : 'bg-red-100 text-red-800'
                                                ]">
                                                    {{ complaint.status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ formatDate(complaint.submittedDate) }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <button 
                                                    @click="viewComplaint(complaint)"
                                                    class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md"
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
                            </div>
                            
                            <!-- Empty State -->
                            <div v-if="filteredComplaints.length === 0" class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                                <p class="text-gray-500 text-lg font-medium">No complaints found</p>
                                <p class="text-gray-400 text-sm mt-1">Try adjusting your search or filter criteria</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add/Edit Complaint Modal -->
            <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-hidden">
                    <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-[#333333]">Submit Complaint</h2>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-6 overflow-y-auto max-h-[70vh]">
                        <!-- Employee Selection (HR Only) -->
                        <div v-if="canApprove">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Employee *</label>
                            <select
                                v-model="formData.employeeId"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                            >
                                <option value="">Select Employee</option>
                                <option value="EMP-001">John Smith (EMP-001)</option>
                                <option value="EMP-002">Sarah Johnson (EMP-002)</option>
                                <option value="EMP-003">Michael Chen (EMP-003)</option>
                                <option value="EMP-004">Emily Davis (EMP-004)</option>
                                <option value="EMP-005">Robert Wilson (EMP-005)</option>
                            </select>
                        </div>

                        <!-- Complaint Type -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Complaint Type *</label>
                            <select
                                v-model="formData.complaintType"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                            >
                                <option value="">Select Type</option>
                                <option value="Harassment">Harassment</option>
                                <option value="Policy Violation">Policy Violation</option>
                                <option value="Safety">Safety</option>
                                <option value="Discrimination">Discrimination</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <!-- Date of Incident -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Incident *</label>
                            <input
                                v-model="formData.incidentDate"
                                type="date"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                            />
                        </div>

                        <!-- Department -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Department (Optional)</label>
                            <select
                                v-model="formData.department"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                            >
                                <option value="">Select Department</option>
                                <option value="IT">IT</option>
                                <option value="HR">HR</option>
                                <option value="Finance">Finance</option>
                                <option value="Operations">Operations</option>
                            </select>
                        </div>

                        <!-- Severity (HR Only) -->
                        <div v-if="canApprove">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Severity Level</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input v-model="formData.severity" type="radio" value="Low" class="w-4 h-4" />
                                    <span class="text-sm text-gray-700">Low</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input v-model="formData.severity" type="radio" value="Medium" class="w-4 h-4" />
                                    <span class="text-sm text-gray-700">Medium</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input v-model="formData.severity" type="radio" value="High" class="w-4 h-4" />
                                    <span class="text-sm text-gray-700">High</span>
                                </label>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Description / Details *</label>
                            <textarea
                                v-model="formData.description"
                                rows="5"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                                placeholder="Provide detailed information about the complaint..."
                            ></textarea>
                        </div>

                        <!-- Attachments -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Attachments</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                                <input
                                    type="file"
                                    @change="handleFileUpload"
                                    multiple
                                    class="hidden"
                                    ref="fileInput"
                                    accept=".pdf,.doc,.docx,.jpg,.png"
                                />
                                <button
                                    @click="$refs.fileInput.click()"
                                    class="text-[#0c8ce9] hover:text-blue-700 font-medium text-sm"
                                >
                                    Click to upload or drag and drop
                                </button>
                                <p class="text-xs text-gray-500 mt-1">PDF, DOCX, JPG up to 10MB</p>
                            </div>
                            <div v-if="formData.attachments.length > 0" class="mt-4 space-y-2">
                                <div
                                    v-for="(file, idx) in formData.attachments"
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
                                        @click="removeAttachment(idx)"
                                        class="text-red-600 hover:text-red-700 text-xs font-medium"
                                    >
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Assigned HR/Manager (HR Only) -->
                        <div v-if="canApprove">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Assign To HR/Manager (Optional)</label>
                            <select
                                v-model="formData.assignedTo"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                            >
                                <option value="">Unassigned</option>
                                <option value="hr-001">Sarah Johnson (HR)</option>
                                <option value="mgr-001">Michael Chen (Manager)</option>
                                <option value="mgr-002">Emily Davis (Manager)</option>
                            </select>
                        </div>



                        <!-- Internal Notes (HR Only) -->
                        <div v-if="canApprove">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Internal Notes</label>
                            <textarea
                                v-model="formData.internalNotes"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                                placeholder="HR staff notes (not visible to employee)..."
                            ></textarea>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
                        <button
                            @click="closeModal"
                            class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-200 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            @click="saveComplaint"
                            class="px-6 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-medium text-sm hover:bg-blue-700 transition-colors"
                        >
                            Submit Complaint
                        </button>
                    </div>
                </div>
            </div>

    </template>

<script setup>
    import { ref, computed, onMounted } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import Swal from 'sweetalert2'
    import { useAuthStore } from '@/stores/auth'

    const auth = useAuthStore()
    const router = useRouter()
    const route = useRoute()

    const showModal = ref(false)
    const isEditing = ref(false)
    const fileInput = ref(null)

    // Role-based permissions
    const canSubmitComplaint = computed(() => ['employee', 'hr_manager', 'manager'].includes(auth.role))
    const canApprove = computed(() => auth.role === 'hr_manager')

    // Filters
    const filters = ref({
        searchTerm: '',
        department: '',
        complaintType: '',
        status: '',
        severity: ''
    })

    // Form data
    const formData = ref({
        employeeId: '',
        complaintType: '',
        incidentDate: new Date().toISOString().split('T')[0],
        department: '',
        severity: 'Medium',
        description: '',
        attachments: [],
        assignedTo: '',
        status: 'Pending',
        internalNotes: ''
    })



    // Mock data - API endpoints will replace this
    // GET /api/complaints - fetch all complaints
    // POST /api/complaints - create complaint
    // PUT /api/complaints/{id} - update complaint
    // PATCH /api/complaints/{id}/approve - approve complaint
    // PATCH /api/complaints/{id}/reject - reject complaint
    // POST /api/complaints/{id}/link-disciplinary - link to disciplinary record
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

    // Computed properties
    const filteredComplaints = computed(() => {
        return allComplaints.value.filter(complaint => {
            const matchesSearch = filters.value.searchTerm === '' ||
                complaint.employeeName.toLowerCase().includes(filters.value.searchTerm.toLowerCase()) ||
                complaint.employeeId.toLowerCase().includes(filters.value.searchTerm.toLowerCase())
            const matchesDept = filters.value.department === '' || complaint.department === filters.value.department
            const matchesType = filters.value.complaintType === '' || complaint.complaintType === filters.value.complaintType
            const matchesStatus = filters.value.status === '' || complaint.status === filters.value.status
            const matchesSeverity = filters.value.severity === '' || complaint.severity === filters.value.severity

            return matchesSearch && matchesDept && matchesType && matchesStatus && matchesSeverity
        })
    })

    const complaintsByStatus = computed(() => ({
        pending: allComplaints.value.filter(c => c.status === 'Pending').length,
        approved: allComplaints.value.filter(c => c.status === 'Approved').length,
        rejected: allComplaints.value.filter(c => c.status === 'Rejected').length
    }))

    const highSeverityComplaints = computed(() => {
        return allComplaints.value.filter(c => c.severity === 'High' && c.status === 'Pending').length
    })

    // Methods
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

    const openAddComplaintModal = () => {
        isEditing.value = false
        formData.value = {
            employeeId: '',
            complaintType: '',
            incidentDate: new Date().toISOString().split('T')[0],
            department: '',
            severity: 'Medium',
            description: '',
            attachments: [],
            assignedTo: '',
            status: 'Pending',
            internalNotes: ''
        }
        showModal.value = true
    }

    const closeModal = () => {
        showModal.value = false
    }

    const viewComplaint = (complaint) => {
        router.push(`/disciplinary/complaints/${complaint.id}`)
    }

    const handleFileUpload = (event) => {
        const files = Array.from(event.target.files)
        files.forEach(file => {
            formData.value.attachments.push({ name: file.name, size: file.size })
        })
    }

    const removeAttachment = (idx) => {
        formData.value.attachments.splice(idx, 1)
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

    const saveComplaint = () => {
        if (!formData.value.complaintType || !formData.value.incidentDate || !formData.value.description) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill in all required fields',
                confirmButtonColor: '#0c8ce9'
            })
            return
        }

        if (isEditing.value) {
            // API: PUT /api/complaints/{id}
            const idx = allComplaints.value.findIndex(c => c.id === formData.value.id)
            if (idx !== -1) {
                allComplaints.value[idx] = { ...allComplaints.value[idx], ...formData.value }
            }
        } else {
            // API: POST /api/complaints
            const newComplaint = {
                id: `CMP-${String(allComplaints.value.length + 1).padStart(3, '0')}`,
                ...formData.value,
                submittedBy: auth.userName || 'Current User',
                submittedDate: new Date().toISOString().split('T')[0],
                reviewedBy: null,
                reviewedDate: null,
                linkedDisciplinaryId: null
            }
            allComplaints.value.push(newComplaint)
        }

        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: isEditing.value ? 'Complaint updated successfully' : 'Complaint submitted successfully',
            confirmButtonColor: '#0c8ce9'
        })
        closeModal()
    }



    const clearFilters = () => {
        filters.value = {
            searchTerm: '',
            department: '',
            complaintType: '',
            status: '',
            severity: ''
        }
    }

    onMounted(() => {
        // Initialize complaints data
        // API: GET /api/complaints
    })
</script>
