<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10 space-y-6">
            <div>
                <router-link
                    :to="`/projects/${projectId}/monitoring`"
                    class="inline-flex items-center gap-2 text-[#0c8ce9] font-semibold hover:underline mb-4"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Monitoring
                </router-link>

                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ report.title }}</h1>
                        <p class="text-sm text-gray-600 mt-1">Prepared by {{ report.preparedBy }} on {{ formatDate(report.date) }}</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" :class="getReportStatusClasses(report.status)">
                        {{ report.status }}
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Report Period</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Start Period</p>
                        <p class="text-base font-semibold text-gray-900 mt-2">{{ formatDate(report.period_start) }}</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">End Period</p>
                        <p class="text-base font-semibold text-gray-900 mt-2">{{ formatDate(report.period_end) }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Milestones</h3>
                    <div class="space-y-3">
                        <div v-for="milestone in reportMilestones" :key="milestone.id" class="border border-gray-200 rounded-lg p-3">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ milestone.name }}</p>
                                    <p class="text-xs text-gray-500">Target: {{ formatDate(milestone.target) }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" :class="getProgressStatusClasses(milestone.status)">
                                        {{ milestone.status }}
                                    </span>
                                    <button
                                        @click="openStatusModal(milestone)"
                                        class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                                    >
                                        Update Status
                                    </button>
                                    <button
                                        @click="toggleNote(milestone)"
                                        class="px-3 py-1 text-xs font-semibold border border-gray-300 text-gray-700 rounded hover:bg-gray-50"
                                    >
                                        Expand
                                    </button>
                                </div>
                            </div>
                            <div v-if="milestone.showNote && milestone.updateNote" class="mt-3 text-sm text-gray-700 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                {{ milestone.updateNote }}
                            </div>
                        </div>
                        <div v-if="reportMilestones.length === 0" class="text-sm text-gray-500">No milestones recorded.</div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tasks</h3>
                    <div class="space-y-3">
                        <div v-for="task in reportTasks" :key="task.id" class="border border-gray-200 rounded-lg p-3">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ task.name }}</p>
                                    <p class="text-xs text-gray-500">Owner: {{ task.owner }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" :class="getProgressStatusClasses(task.status)">
                                        {{ task.status }}
                                    </span>
                                    <button
                                        @click="openStatusModal(task)"
                                        class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                                    >
                                        Update Status
                                    </button>
                                    <button
                                        @click="toggleNote(task)"
                                        class="px-3 py-1 text-xs font-semibold border border-gray-300 text-gray-700 rounded hover:bg-gray-50"
                                    >
                                        Expand
                                    </button>
                                </div>
                            </div>
                            <div v-if="task.showNote && task.updateNote" class="mt-3 text-sm text-gray-700 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                {{ task.updateNote }}
                            </div>
                        </div>
                        <div v-if="reportTasks.length === 0" class="text-sm text-gray-500">No tasks recorded.</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea
                        v-model="reportForm.description"
                        rows="3"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        placeholder="Summarize progress for this period"
                    ></textarea>
                </div>

                <label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700">
                    <input v-model="reportHasDelays" type="checkbox" class="rounded border-gray-300 text-[#0c8ce9] focus:ring-[#0c8ce9]" />
                    Delays or issues encountered
                </label>

                <div v-if="reportHasDelays" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Reasons for Delay</label>
                        <textarea
                            v-model="reportForm.delayReason"
                            rows="3"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                            placeholder="Explain reasons for delay"
                        ></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Issue Description</label>
                        <textarea
                            v-model="reportForm.issueDescription"
                            rows="3"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                            placeholder="Describe issues encountered"
                        ></textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Planned Work Next Week</label>
                    <textarea
                        v-model="reportForm.plannedWork"
                        rows="3"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        placeholder="Outline next week plans"
                    ></textarea>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Report Attachments</h3>
                    <label class="inline-flex items-center gap-2 text-xs font-semibold text-[#0c8ce9] cursor-pointer">
                        <input type="file" class="hidden" />
                        Add Attachment
                    </label>
                </div>
                <div class="space-y-2">
                    <div v-for="file in reportAttachments" :key="file.id" class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg p-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ file.name }}</p>
                            <p class="text-xs text-gray-500">{{ file.type }} • {{ file.date }}</p>
                        </div>
                        <button class="text-xs font-semibold text-[#0c8ce9] hover:text-blue-700">View</button>
                    </div>
                    <div v-if="reportAttachments.length === 0" class="text-sm text-gray-500">No attachments sent for this report.</div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Attendance Range</h3>
                        <p class="text-xs text-gray-500">{{ formatDate(report.period_start) }} - {{ formatDate(report.period_end) }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="day in attendanceByDay" :key="day.date" class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ formatDate(day.date) }}</p>
                                <p class="text-xs text-gray-500">Present: {{ day.employees.length }} / {{ day.total }}</p>
                            </div>
                            <span class="text-xs font-semibold text-gray-600">{{ day.shift }}</span>
                        </div>
                        <button
                            @click="selectAttendanceDay(day)"
                            class="mt-3 w-full px-3 py-2 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                        >
                            View Employees
                        </button>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-bold text-gray-900">Recorded Attendance</h4>
                        <p class="text-xs text-gray-500">{{ selectedAttendanceDay ? formatDate(selectedAttendanceDay.date) : 'Select a day' }}</p>
                    </div>

                    <div v-if="selectedAttendanceDay" class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Employee</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Position</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Time In</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Time Out</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="employee in selectedAttendanceDay.employees" :key="employee.id" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ employee.name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ employee.position }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ employee.timeIn }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ employee.timeOut }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <button
                                            @click="openAttendanceDetail(employee)"
                                            class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                                        >
                                            View
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="selectedAttendanceDay.employees.length === 0">
                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No attendance recorded.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-sm text-gray-500">Select a day to view recorded attendance.</div>

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h5 class="text-sm font-bold text-gray-900">Attendance Attachments</h5>
                            <label class="inline-flex items-center gap-2 text-xs font-semibold text-[#0c8ce9] cursor-pointer">
                                <input type="file" class="hidden" />
                                Upload File
                            </label>
                        </div>
                        <div class="space-y-2">
                            <div v-for="file in attendanceAttachments" :key="file.id" class="flex items-center justify-between bg-white border border-gray-200 rounded-lg p-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ file.name }}</p>
                                    <p class="text-xs text-gray-500">{{ file.type }} • {{ file.date }}</p>
                                </div>
                                <button class="text-xs font-semibold text-[#0c8ce9] hover:text-blue-700">View</button>
                            </div>
                            <div v-if="attendanceAttachments.length === 0" class="text-sm text-gray-500">No attendance attachments uploaded.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button class="px-6 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    Save Updates
                </button>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div v-if="showStatusModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Update Status</h3>
                    <button @click="closeStatusModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status Type</label>
                    <select
                        v-model="statusForm.type"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                    >
                        <option v-for="status in statusOptions" :key="status" :value="status">{{ status }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea
                        v-model="statusForm.description"
                        rows="4"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        placeholder="Add a short update description"
                    ></textarea>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex gap-3">
                <button
                    @click="closeStatusModal"
                    class="flex-1 px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="saveStatusUpdate"
                    class="flex-1 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                >
                    Save Update
                </button>
            </div>
        </div>
    </div>

    <!-- Attendance Detail Modal -->
    <div v-if="showAttendanceDetail" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Attendance Details</h3>
                    <button @click="closeAttendanceDetail" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ selectedAttendanceEmployee.name }}</p>
                    <p class="text-xs text-gray-600">{{ selectedAttendanceEmployee.position }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Time In</p>
                        <p class="text-sm text-gray-900 mt-1">{{ selectedAttendanceEmployee.timeIn }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Time Out</p>
                        <p class="text-sm text-gray-900 mt-1">{{ selectedAttendanceEmployee.timeOut }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Time In Photo</p>
                        <div class="w-full h-36 border border-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-500">
                            {{ selectedAttendanceEmployee.photoIn || 'No photo' }}
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Time Out Photo</p>
                        <div class="w-full h-36 border border-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-500">
                            {{ selectedAttendanceEmployee.photoOut || 'No photo' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200">
                <button
                    @click="closeAttendanceDetail"
                    class="w-full px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import AuthLayout from '@/views/Layouts/AuthLayout.vue'

const route = useRoute()
const projectId = route.params.id
const reportId = route.params.reportId

const progressReports = [
    {
        id: 1,
        title: 'Weekly Progress Report',
        date: '2026-02-03',
        period_start: '2026-01-27',
        period_end: '2026-02-02',
        preparedBy: 'John Davis',
        status: 'Submitted'
    },
    {
        id: 2,
        title: 'Site Inspection Summary',
        date: '2026-01-27',
        period_start: '2026-01-20',
        period_end: '2026-01-26',
        preparedBy: 'Sarah Anderson',
        status: 'Draft'
    }
]

const report = computed(() => {
    return progressReports.find(item => item.id === Number(reportId)) || progressReports[0]
})

const reportHasDelays = ref(false)
const reportForm = ref({
    description: '',
    delayReason: '',
    issueDescription: '',
    plannedWork: ''
})

const reportMilestones = ref([
    { id: 1, name: 'Foundation Complete', target: '2026-02-10', status: 'In Progress', updateNote: '', showNote: false },
    { id: 2, name: 'Structural Framing', target: '2026-02-20', status: 'Not Started', updateNote: '', showNote: false }
])

const reportTasks = ref([
    { id: 1, name: 'Rebar installation - East Wing', owner: 'Michael Chen', status: 'In Progress', updateNote: '', showNote: false },
    { id: 2, name: 'Concrete pour - Level 2', owner: 'Anna Reyes', status: 'Blocked', updateNote: '', showNote: false }
])

const reportAttachments = ref([
    { id: 1, name: 'site_photos_week2.zip', type: 'Images', date: '2026-02-03' },
    { id: 2, name: 'inspection_report_week2.pdf', type: 'Document', date: '2026-02-03' }
])

const attendanceAttachments = ref([
    { id: 1, name: 'attendance_sheet_day2.xlsx', type: 'Document', date: '2026-02-01' }
])

const sampleEmployees = [
    { id: 1, name: 'John Davis', position: 'Project Manager', timeIn: '08:10 AM', timeOut: '05:12 PM', photoIn: 'Time In Photo', photoOut: 'Time Out Photo' },
    { id: 2, name: 'Sarah Anderson', position: 'Lead Engineer', timeIn: '08:42 AM', timeOut: '05:20 PM', photoIn: 'Time In Photo', photoOut: '' },
    { id: 3, name: 'Michael Chen', position: 'QA Lead', timeIn: '08:15 AM', timeOut: '05:05 PM', photoIn: 'Time In Photo', photoOut: 'Time Out Photo' },
    { id: 4, name: 'Anna Reyes', position: 'Safety Officer', timeIn: '08:05 AM', timeOut: '05:11 PM', photoIn: '', photoOut: '' }
]

const attendanceByDay = computed(() => {
    const days = getDatesInRange(report.value.period_start, report.value.period_end)
    return days.map((date, index) => {
        const count = (index % sampleEmployees.length) + 1
        return {
            date,
            total: sampleEmployees.length,
            shift: index % 2 === 0 ? 'Day Shift' : 'Night Shift',
            employees: sampleEmployees.slice(0, count)
        }
    })
})

const selectedAttendanceDay = ref(null)
const showAttendanceDetail = ref(false)
const selectedAttendanceEmployee = ref({})

const showStatusModal = ref(false)
const statusTarget = ref(null)
const statusOptions = ['Not Started', 'In Progress', 'Blocked', 'Done']
const statusForm = ref({
    type: 'Not Started',
    description: ''
})

const selectAttendanceDay = (day) => {
    selectedAttendanceDay.value = day
}

const openAttendanceDetail = (employee) => {
    selectedAttendanceEmployee.value = employee
    showAttendanceDetail.value = true
}

const closeAttendanceDetail = () => {
    showAttendanceDetail.value = false
    selectedAttendanceEmployee.value = {}
}

const getReportStatusClasses = (status) => {
    const map = {
        Submitted: 'bg-green-100 text-green-800',
        Draft: 'bg-yellow-100 text-yellow-800',
        Reviewed: 'bg-blue-100 text-blue-800'
    }
    return map[status] || 'bg-gray-100 text-gray-700'
}

const getProgressStatusClasses = (status) => {
    const map = {
        'Not Started': 'bg-gray-200 text-gray-700',
        'In Progress': 'bg-yellow-100 text-yellow-800',
        Blocked: 'bg-red-100 text-red-800',
        Done: 'bg-green-100 text-green-800'
    }
    return map[status] || 'bg-gray-100 text-gray-700'
}

const openStatusModal = (item) => {
    statusTarget.value = item
    statusForm.value = {
        type: item.status || statusOptions[0],
        description: item.updateNote || ''
    }
    showStatusModal.value = true
}

const closeStatusModal = () => {
    showStatusModal.value = false
    statusTarget.value = null
}

const saveStatusUpdate = () => {
    if (!statusTarget.value) return
    statusTarget.value.status = statusForm.value.type
    statusTarget.value.updateNote = statusForm.value.description
    if (statusTarget.value.updateNote) {
        statusTarget.value.showNote = true
    }
    closeStatusModal()
}

const toggleNote = (item) => {
    if (!item.updateNote) return
    item.showNote = !item.showNote
}

const formatDate = (date) => {
    if (!date) return '---'
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const getDatesInRange = (startDate, endDate) => {
    const dates = []
    const start = new Date(startDate)
    const end = new Date(endDate)
    if (Number.isNaN(start.valueOf()) || Number.isNaN(end.valueOf())) {
        return dates
    }
    const current = new Date(start)
    while (current <= end) {
        dates.push(current.toISOString().split('T')[0])
        current.setDate(current.getDate() + 1)
    }
    return dates
}
</script>
