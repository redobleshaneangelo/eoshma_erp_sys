<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <button
                    @click="goBack"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Disciplinary
                </button>
                <div class="flex items-center gap-2">
                    <template v-if="!isEditing">
                        <button
                            @click="startEdit"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors"
                        >
                            Edit
                        </button>
                        <button
                            @click="confirmDelete"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition-colors"
                        >
                            Delete
                        </button>
                    </template>
                    <template v-else>
                        <button
                            @click="cancelEdit"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            @click="saveEdit"
                            class="px-4 py-2 bg-[#0c8ce9] text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors"
                        >
                            Save Changes
                        </button>
                    </template>
                </div>
            </div>

            <div v-if="!record" class="bg-white rounded-lg p-8 border border-gray-100 text-center text-gray-600">
                Record not found.
            </div>

            <div v-else class="space-y-6">
                <div class="bg-white rounded-lg p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Record ID</p>
                            <p class="text-lg font-bold text-gray-900">{{ record.id }}</p>
                        </div>
                        <span :class="getStatusBadgeClass(record.status)" class="px-3 py-1 rounded-full text-xs font-semibold">
                            {{ record.status }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Employee</p>
                            <p class="text-sm text-gray-900 font-semibold">{{ record.employeeName }}</p>
                            <p class="text-xs text-gray-500">{{ record.employeeId }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Department</p>
                            <p class="text-sm text-gray-900">{{ record.department }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Issued By</p>
                            <p class="text-sm text-gray-900">{{ record.issuedBy }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Date Issued</p>
                            <p class="text-sm text-gray-900">{{ formatDate(record.dateIssued) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 border border-gray-100">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Record Type</label>
                            <template v-if="isEditing">
                                <select
                                    v-model="formData.recordType"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                                >
                                    <option value="Verbal Warning">Verbal Warning</option>
                                    <option value="Written Warning">Written Warning</option>
                                    <option value="Suspension">Suspension</option>
                                    <option value="Termination">Termination Recommendation</option>
                                    <option value="PIP">Performance Improvement Plan</option>
                                    <option value="Other">Other</option>
                                </select>
                            </template>
                            <p v-else class="text-sm text-gray-900">{{ record.recordType }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Severity</label>
                            <template v-if="isEditing">
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input v-model="formData.severity" type="radio" value="Low" class="w-4 h-4 text-[#0c8ce9]" />
                                        <span class="text-sm text-gray-700">Low</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input v-model="formData.severity" type="radio" value="Medium" class="w-4 h-4 text-[#0c8ce9]" />
                                        <span class="text-sm text-gray-700">Medium</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input v-model="formData.severity" type="radio" value="High" class="w-4 h-4 text-[#0c8ce9]" />
                                        <span class="text-sm text-gray-700">High</span>
                                    </label>
                                </div>
                            </template>
                            <span v-else :class="getSeverityBadgeClass(record.severity)" class="px-3 py-1 rounded-full text-xs font-semibold">
                                {{ record.severity }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Status</label>
                            <template v-if="isEditing">
                                <select
                                    v-model="formData.status"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                                >
                                    <option value="Pending">Pending</option>
                                    <option value="Reviewed">Reviewed</option>
                                    <option value="Closed">Closed</option>
                                </select>
                            </template>
                            <p v-else class="text-sm text-gray-900">{{ record.status }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Date Issued</label>
                            <template v-if="isEditing">
                                <input
                                    v-model="formData.dateIssued"
                                    type="date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                                />
                            </template>
                            <p v-else class="text-sm text-gray-900">{{ formatDate(record.dateIssued) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 border border-gray-100">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Description</label>
                    <textarea
                        v-if="isEditing"
                        v-model="formData.description"
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    ></textarea>
                    <p v-else class="text-sm text-gray-700 whitespace-pre-wrap">{{ record.description }}</p>
                </div>

                <div class="bg-white rounded-lg p-6 border border-gray-100">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Action Taken / Next Steps</label>
                    <textarea
                        v-if="isEditing"
                        v-model="formData.actionTaken"
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    ></textarea>
                    <p v-else class="text-sm text-gray-700 whitespace-pre-wrap">{{ record.actionTaken || 'No action recorded.' }}</p>
                </div>

                <div class="bg-white rounded-lg p-6 border border-gray-100">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Internal Comments</label>
                    <textarea
                        v-if="isEditing"
                        v-model="formData.internalComments"
                        rows="2"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    ></textarea>
                    <p v-else class="text-sm text-gray-700 whitespace-pre-wrap">{{ record.internalComments || 'No internal comments.' }}</p>
                </div>

                <div class="bg-white rounded-lg p-6 border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Attachments</p>
                    <div v-if="record.attachments && record.attachments.length" class="space-y-2">
                        <div
                            v-for="(file, idx) in record.attachments"
                            :key="idx"
                            class="flex items-center justify-between bg-gray-50 p-3 rounded-lg"
                        >
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <span class="text-sm text-gray-700">{{ file.name }}</span>
                            </div>
                            <button class="text-[#0c8ce9] hover:text-blue-700 text-xs font-medium">Download</button>
                        </div>
                    </div>
                    <p v-else class="text-sm text-gray-500">No attachments.</p>
                </div>

                <div class="bg-white rounded-lg p-6 border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Audit Trail</p>
                    <div class="space-y-2 text-xs text-gray-700">
                        <div class="flex justify-between">
                            <span>Created by:</span>
                            <span class="font-semibold">{{ record.createdBy }} on {{ formatDate(record.createdDate) }}</span>
                        </div>
                        <div v-if="record.lastEditedBy" class="flex justify-between">
                            <span>Last edited by:</span>
                            <span class="font-semibold">{{ record.lastEditedBy }} on {{ formatDate(record.lastEditedDate) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Issued by:</span>
                            <span class="font-semibold">{{ record.issuedBy }}</span>
                        </div>
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
    import { ref, onMounted } from 'vue'
    import { useRoute, useRouter } from 'vue-router'
    import Swal from 'sweetalert2'
    import { useAuthStore } from '@/stores/auth'
    import agnes from '@/assets/agnes.gif'

    const auth = useAuthStore()
    const router = useRouter()
    const route = useRoute()

    const loading = ref(false)
    const isEditing = ref(false)
    const record = ref(null)
    const formData = ref({})

    const allRecords = [
        {
            id: 'DR-001',
            employeeId: 'EMP-001',
            employeeName: 'John Smith',
            department: 'IT',
            recordType: 'Verbal Warning',
            description: 'Late arrival to work - 3 instances in 2 weeks',
            actionTaken: 'Verbal warning issued. Employee acknowledges and commits to improvement.',
            severity: 'Low',
            dateIssued: '2026-01-15',
            issuedBy: 'Sarah Johnson',
            status: 'Closed',
            attachments: [],
            internalComments: 'Employee seemed receptive to feedback',
            createdBy: 'Sarah Johnson',
            createdDate: '2026-01-15',
            lastEditedBy: 'Sarah Johnson',
            lastEditedDate: '2026-01-20'
        },
        {
            id: 'DR-002',
            employeeId: 'EMP-002',
            employeeName: 'Sarah Johnson',
            department: 'HR',
            recordType: 'Written Warning',
            description: 'Missed deadline on recruitment project without prior notice',
            actionTaken: 'Written warning issued. Performance improvement plan to be created.',
            severity: 'Medium',
            dateIssued: '2026-01-20',
            issuedBy: 'Michael Chen',
            status: 'Reviewed',
            attachments: [],
            internalComments: 'Needs closer monitoring',
            createdBy: 'Michael Chen',
            createdDate: '2026-01-20',
            lastEditedBy: null,
            lastEditedDate: null
        },
        {
            id: 'DR-003',
            employeeId: 'EMP-003',
            employeeName: 'Michael Chen',
            department: 'Finance',
            recordType: 'Suspension',
            description: 'Unauthorized access to financial records',
            actionTaken: '3-day suspension issued. Security briefing scheduled.',
            severity: 'High',
            dateIssued: '2026-01-25',
            issuedBy: 'HR Admin',
            status: 'Pending',
            attachments: [],
            internalComments: 'Monitor access logs closely',
            createdBy: 'HR Admin',
            createdDate: '2026-01-25',
            lastEditedBy: null,
            lastEditedDate: null
        }
    ]

    const formatDate = (date) => {
        return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
    }

    const getSeverityBadgeClass = (severity) => {
        const classes = {
            Low: 'bg-green-100 text-green-800',
            Medium: 'bg-orange-100 text-orange-800',
            High: 'bg-red-100 text-red-800'
        }
        return classes[severity] || 'bg-gray-100 text-gray-800'
    }

    const getStatusBadgeClass = (status) => {
        const classes = {
            Pending: 'bg-yellow-100 text-yellow-800',
            Reviewed: 'bg-blue-100 text-blue-800',
            Closed: 'bg-green-100 text-green-800'
        }
        return classes[status] || 'bg-gray-100 text-gray-800'
    }

    const loadRecord = () => {
        const recordId = route.params.id || route.query.id
        const found = allRecords.find(item => item.id === recordId)
        record.value = found ? { ...found } : null
        formData.value = found ? { ...found } : {}
    }

    const startEdit = () => {
        isEditing.value = true
        formData.value = { ...record.value }
    }

    const cancelEdit = () => {
        isEditing.value = false
        formData.value = { ...record.value }
    }

    const saveEdit = () => {
        record.value = {
            ...record.value,
            ...formData.value,
            lastEditedBy: auth.userName || 'Current User',
            lastEditedDate: new Date().toISOString().split('T')[0]
        }
        isEditing.value = false
        Swal.fire({
            icon: 'success',
            title: 'Saved',
            text: 'Record updated successfully',
            confirmButtonColor: '#0c8ce9'
        })
    }

    const confirmDelete = async () => {
        const { isConfirmed } = await Swal.fire({
            title: 'Delete Record?',
            text: 'This action cannot be undone',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Delete'
        })

        if (isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Deleted',
                text: 'Record deleted successfully',
                confirmButtonColor: '#0c8ce9'
            })
            goBack()
        }
    }

    const goBack = () => {
        router.push('/disciplinary')
    }

    onMounted(() => {
        auth.pageTitle = 'Disciplinary Record'
        loadRecord()
    })
</script>
