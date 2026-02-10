<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">
            <div class="mb-6 flex items-center justify-end">
                <div class="flex gap-3">
                    <router-link
                        to="/disciplinary/complaints"
                        class="px-6 py-2.5 bg-[#0c8ce9] text-white rounded-md font-semibold text-sm hover:bg-blue-700 transition-colors flex items-center gap-2"
                    >
                        Employee Complaints
                    </router-link>
                    <!-- <button
                        type="button"
                        @click="handleAddRecord"
                        class="px-6 py-2.5 bg-[#0c8ce9] text-white rounded-md font-semibold text-sm hover:bg-blue-700 transition-colors flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Record
                    </button> -->
                </div>
            </div>
            <div class="bg-white border-b border-gray-200 rounded-t-lg mb-0">
                <nav class="flex gap-0 px-6 py-0 overflow-x-auto">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        class="flex items-center gap-2 px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap"
                        :class="activeTab === tab.id
                            ? 'border-[#0c8ce9] text-[#0c8ce9]'
                            : 'border-transparent text-gray-600 hover:text-gray-900'"
                    >
                        <span v-html="tab.icon"></span>
                        {{ tab.label }}
                        <span class="bg-gray-200 text-gray-700 px-2 py-0 rounded-full text-xs font-semibold">
                            {{ tab.count }}
                        </span>
                    </button>
                </nav>
            </div>

            <div class="bg-white rounded-b-lg">
                <div class="p-8">
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-100 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Record ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Employee</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Record Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Date Issued</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Issued By</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Severity</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr
                                    v-for="record in filteredRecords"
                                    :key="record.id"
                                    class="hover:bg-gray-50 transition-colors"
                                >
                                    <td class="px-6 py-4 text-sm font-semibold text-[#0c8ce9]">{{ record.id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <div class="font-semibold">{{ record.employeeName }}</div>
                                        <div class="text-xs text-gray-500">{{ record.employeeId }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ record.department }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ record.recordType }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ formatDate(record.dateIssued) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ record.issuedBy }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span :class="getSeverityBadgeClass(record.severity)" class="px-2 py-1 rounded-full text-xs font-semibold">
                                            {{ record.severity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span :class="getStatusBadgeClass(record.status)" class="px-2 py-1 rounded-full text-xs font-semibold">
                                            {{ record.status }}
                                        </span>
                                    </td>
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
                            <p>No disciplinary records found</p>
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
    import { ref, computed, onMounted } from 'vue'
    import { useRouter } from 'vue-router'
    import { useAuthStore } from '@/stores/auth'
    import agnes from '@/assets/agnes.gif'

    const auth = useAuthStore()
    const router = useRouter()

    const loading = ref(false)
    const activeTab = ref('Pending')

    const allRecords = ref([
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
    ])

    const tabs = computed(() => [
        {
            id: 'Pending',
            label: 'Pending',
            count: allRecords.value.filter(r => r.status === 'Pending').length,
            icon: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>'
        },
        {
            id: 'Reviewed',
            label: 'Reviewed',
            count: allRecords.value.filter(r => r.status === 'Reviewed').length,
            icon: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>'
        },
        {
            id: 'Closed',
            label: 'Closed',
            count: allRecords.value.filter(r => r.status === 'Closed').length,
            icon: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>'
        }
    ])

    const filteredRecords = computed(() => {
        return allRecords.value.filter(record => record.status === activeTab.value)
    })

    const handleAddRecord = () => {
        router.push('/disciplinary/add')
    }

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
            'Reviewed': 'bg-blue-100 text-blue-800',
            'Closed': 'bg-green-100 text-green-800'
        }
        return classes[status] || 'bg-gray-100 text-gray-800'
    }

    const viewRecord = (record) => {
        router.push(`/disciplinary/${record.id}`)
    }

    onMounted(() => {
        auth.pageTitle = 'Disciplinary'
    })
</script>
