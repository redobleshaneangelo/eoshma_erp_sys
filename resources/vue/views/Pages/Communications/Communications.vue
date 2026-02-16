<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10 space-y-6">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex gap-2">
                            <button
                                @click="activeTab = 'announcements'"
                                :class="activeTab === 'announcements' ? 'bg-[#0c8ce9] text-white' : 'text-gray-700 hover:bg-gray-100'"
                                class="px-3 py-1.5 rounded text-sm font-semibold transition-colors"
                            >
                                Announcements
                            </button>
                            <button
                                @click="activeTab = 'bulletin'"
                                :class="activeTab === 'bulletin' ? 'bg-[#0c8ce9] text-white' : 'text-gray-700 hover:bg-gray-100'"
                                class="px-3 py-1.5 rounded text-sm font-semibold transition-colors"
                            >
                                Bulletin
                            </button>
                            <button
                                @click="activeTab = 'hr'"
                                :class="activeTab === 'hr' ? 'bg-[#0c8ce9] text-white' : 'text-gray-700 hover:bg-gray-100'"
                                class="px-3 py-1.5 rounded text-sm font-semibold transition-colors"
                            >
                                HR
                            </button>
                        </div>

                        <div class="relative" v-if="activeTab === 'hr'">
                            <button
                                @click="showContactDropdown = !showContactDropdown"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-[#0c8ce9] rounded-lg hover:bg-blue-700"
                            >
                                Contact HR
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div
                                v-if="showContactDropdown"
                                class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-lg z-10 overflow-hidden"
                            >
                                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Contact HR</p>
                                </div>
                                <button
                                    type="button"
                                    @click="selectContactAction('leave')"
                                    class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-3"
                                >
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-[#0c8ce9]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </span>
                                    Submit Leave Request
                                </button>
                                <button
                                    type="button"
                                    @click="selectContactAction('complaint')"
                                    class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-3"
                                >
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-red-100 text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                        </svg>
                                    </span>
                                    Complaint
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div v-if="activeTab === 'announcements'" class="space-y-4">
                        <div class="w-full border border-gray-200 rounded-lg bg-white p-6">
                            <div class="flex items-center justify-between gap-3 mb-4">
                                <h2 class="text-lg font-bold text-gray-900">Company Announcements</h2>
                                <span class="text-xs text-gray-500">{{ announcements.length }} item(s)</span>
                            </div>

                            <div class="space-y-3">
                                <div
                                    v-for="item in announcements"
                                    :key="item.id"
                                    class="border border-gray-200 rounded-lg p-4"
                                >
                                    <div class="flex items-center justify-between gap-3">
                                        <h3 class="text-sm font-semibold text-gray-900">{{ item.title }}</h3>
                                        <span class="text-xs text-gray-500">{{ formatDate(item.date) }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700 mt-2">{{ item.message }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="activeTab === 'bulletin'" class="space-y-4">
                        <h2 class="text-lg font-bold text-gray-900">Bidding Bulletin</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                            <div
                                v-for="post in bulletinItems"
                                :key="post.id"
                                class="border border-gray-200 rounded-lg bg-white p-4"
                            >
                                <p class="text-xs font-semibold uppercase tracking-wide text-[#0c8ce9]">Bidding Activity</p>
                                <p class="text-sm font-semibold text-gray-900 mt-2">{{ post.activity }}</p>
                                <p class="text-xs text-gray-500 mt-2">Project: {{ post.projectName }}</p>
                                <p class="text-xs text-gray-500">Date: {{ formatDate(post.date) }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="activeTab === 'hr'" class="space-y-4">
                        <h2 class="text-lg font-bold text-gray-900">My Requests / Complaints</h2>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Subject</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Details</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="request in myHrRequests" :key="request.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ formatDate(request.date) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ request.type }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ request.subject }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" :class="getRequestStatusClasses(request.status)">
                                                {{ request.status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ request.details || '--' }}</td>
                                    </tr>
                                    <tr v-if="myHrRequests.length === 0">
                                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                            No requests or complaints found.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <SubmitLeaveRequestModal
        v-if="showSubmitModal"
        @close="showSubmitModal = false"
        @submit="handleSubmitLeaveRequest"
    />

    <SubmitComplaintModal
        v-if="showComplaintModal"
        @close="showComplaintModal = false"
        @submit="handleSubmitComplaint"
    />
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import AuthLayout from '@/views/Layouts/AuthLayout.vue'
import SubmitLeaveRequestModal from '@/components/HR/Leave/SubmitLeaveRequestModal.vue'
import SubmitComplaintModal from '@/components/HR/Disciplinary/SubmitComplaintModal.vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

const activeTab = ref('announcements')
const showContactDropdown = ref(false)
const showSubmitModal = ref(false)
const showComplaintModal = ref(false)

const announcements = ref([])
const bulletinItems = ref([])
const myHrRequests = ref([])

const fallbackAnnouncements = [
    {
        id: 1,
        title: 'Quarterly Townhall Meeting',
        date: '2026-02-20',
        message: 'All employees are invited to the quarterly townhall this Friday at 3:00 PM in the main conference hall.'
    },
    {
        id: 2,
        title: 'Payroll Processing Reminder',
        date: '2026-02-18',
        message: 'Please submit all pending attendance corrections before payroll cutoff to avoid delays.'
    }
]

const fallbackBulletinItems = [
    { id: 1, activity: 'Bid Opening', projectName: 'Central Warehouse Expansion', date: '2026-02-17' },
    { id: 2, activity: 'Technical Review', projectName: 'Metro Rail Support Works', date: '2026-02-18' },
    { id: 3, activity: 'Client Clarification', projectName: 'Riverside Flood Control', date: '2026-02-19' },
    { id: 4, activity: 'Submission Deadline', projectName: 'City Hall Renovation', date: '2026-02-21' }
]

const fetchAnnouncements = async () => {
    try {
        const response = await axios.get('/api/communications/announcements')
        const rows = response.data?.data || []
        announcements.value = rows.length ? rows : fallbackAnnouncements
    } catch {
        announcements.value = fallbackAnnouncements
    }
}

const fetchBulletin = async () => {
    try {
        const response = await axios.get('/api/communications/bulletin')
        const rows = response.data?.data || []
        bulletinItems.value = rows.length ? rows : fallbackBulletinItems
    } catch {
        bulletinItems.value = fallbackBulletinItems
    }
}

const fetchMyHrRequests = async () => {
    try {
        const response = await axios.get('/api/communications/hr/requests', {
            params: { mine: 1 }
        })
        myHrRequests.value = response.data?.data || []
    } catch {
        myHrRequests.value = []
    }
}

const selectContactAction = (action) => {
    showContactDropdown.value = false

    if (action === 'leave') {
        showSubmitModal.value = true
        return
    }

    showComplaintModal.value = true
}

const handleSubmitLeaveRequest = (formData) => {
    const newItem = {
        id: `LEAVE-${Date.now()}`,
        date: new Date().toISOString().split('T')[0],
        type: 'Leave Request',
        subject: `${formData.leaveType} (${formData.totalDays} day${formData.totalDays > 1 ? 's' : ''})`,
        status: 'Pending',
        details: formData.reason
    }

    myHrRequests.value.unshift(newItem)
    showSubmitModal.value = false

    Swal.fire({
        icon: 'success',
        title: 'Submitted',
        text: 'Leave request submitted successfully.',
        confirmButtonColor: '#0c8ce9'
    })
}

const handleSubmitComplaint = (formData) => {
    const involvedEmployee = formData.complaintAgainst === 'Others'
        ? formData.complaintAgainstOther
        : formData.complaintAgainst

    const subjectSuffix = formData.employeeInvolved && involvedEmployee
        ? ` vs ${involvedEmployee}`
        : ''

    const newItem = {
        id: `CMP-${Date.now()}`,
        date: new Date().toISOString().split('T')[0],
        type: 'Complaint',
        subject: `${formData.complaintType}${subjectSuffix}`,
        status: 'Pending',
        details: formData.description
    }

    myHrRequests.value.unshift(newItem)
    showComplaintModal.value = false

    Swal.fire({
        icon: 'success',
        title: 'Submitted',
        text: 'Complaint submitted successfully.',
        confirmButtonColor: '#0c8ce9'
    })
}

const getRequestStatusClasses = (status) => {
    const map = {
        Pending: 'bg-yellow-100 text-yellow-800',
        Approved: 'bg-green-100 text-green-800',
        Rejected: 'bg-red-100 text-red-800',
        Resolved: 'bg-blue-100 text-blue-800'
    }

    return map[status] || 'bg-gray-100 text-gray-700'
}

const formatDate = (date) => {
    if (!date) return '--'
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

onMounted(() => {
    auth.pageTitle = 'Communications'
    fetchAnnouncements()
    fetchBulletin()
    fetchMyHrRequests()
})
</script>
