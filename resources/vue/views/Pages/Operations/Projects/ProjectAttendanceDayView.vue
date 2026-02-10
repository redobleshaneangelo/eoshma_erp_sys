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

                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Attendance Records</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ attendanceDay.label }} • {{ formatDate(attendanceDay.date) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Recorded Attendance</h2>
                <div class="border border-gray-200 rounded-lg overflow-hidden">
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
                            <tr v-for="employee in attendanceEmployees" :key="employee.id" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ employee.name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ employee.position }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ employee.timeIn || '--' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ employee.timeOut || '--' }}</td>
                                <td class="px-4 py-3">
                                    <button
                                        @click="openAttendanceEmployee(employee)"
                                        class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                                    >
                                        View
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="attendanceEmployees.length === 0">
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No attendance recorded.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Sent Attachments</h2>
                    <label class="inline-flex items-center gap-2 text-xs font-semibold text-[#0c8ce9] cursor-pointer">
                        <input type="file" class="hidden" />
                        Upload File
                    </label>
                </div>
                <div class="space-y-2">
                    <div v-for="file in attendanceDayAttachments" :key="file.id" class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg p-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ file.name }}</p>
                            <p class="text-xs text-gray-500">{{ file.type }} • {{ file.date }}</p>
                        </div>
                        <button class="text-xs font-semibold text-[#0c8ce9] hover:text-blue-700">View</button>
                    </div>
                    <div v-if="attendanceDayAttachments.length === 0" class="text-sm text-gray-500">No attachments sent for this day.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Employee Detail Modal -->
    <div v-if="showAttendanceEmployeeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Employee Attendance</h3>
                    <button @click="closeAttendanceEmployee" class="text-gray-400 hover:text-gray-600">
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
                        <p class="text-sm text-gray-900 mt-1">{{ selectedAttendanceEmployee.timeIn || '--' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Time Out</p>
                        <p class="text-sm text-gray-900 mt-1">{{ selectedAttendanceEmployee.timeOut || '--' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Time In Photo</p>
                        <div class="w-full h-36 border border-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-500">
                            {{ selectedAttendanceEmployee.timeInPhoto || 'No photo' }}
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Time Out Photo</p>
                        <div class="w-full h-36 border border-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-500">
                            {{ selectedAttendanceEmployee.timeOutPhoto || 'No photo' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200">
                <button
                    @click="closeAttendanceEmployee"
                    class="w-full px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRoute } from 'vue-router'
import AuthLayout from '@/views/Layouts/AuthLayout.vue'

const route = useRoute()
const projectId = route.params.id
const dayId = Number(route.params.dayId)

const attendanceDaily = [
    { id: 1, label: 'Day 2 | ID-002', date: '2026-01-13' },
    { id: 2, label: 'Day 1 | ID-001', date: '2026-01-12' }
]

const attendanceRecords = {
    1: [
        { id: 1, name: 'John Davis', position: 'Project Manager', timeIn: '08:10 AM', timeOut: '05:12 PM', timeInPhoto: 'Time In Photo', timeOutPhoto: 'Time Out Photo' },
        { id: 2, name: 'Sarah Anderson', position: 'Lead Engineer', timeIn: '08:42 AM', timeOut: '05:20 PM', timeInPhoto: 'Time In Photo', timeOutPhoto: '' }
    ],
    2: [
        { id: 3, name: 'Michael Chen', position: 'QA Lead', timeIn: '08:15 AM', timeOut: '05:05 PM', timeInPhoto: 'Time In Photo', timeOutPhoto: 'Time Out Photo' },
        { id: 4, name: 'Anna Reyes', position: 'Safety Officer', timeIn: '08:05 AM', timeOut: '05:11 PM', timeInPhoto: '', timeOutPhoto: '' }
    ]
}

const attendanceDayAttachments = ref([
    { id: 1, name: 'attendance_sheet_day2.xlsx', type: 'Document', date: '2026-01-13' },
    { id: 2, name: 'site_photos_day2.zip', type: 'Images', date: '2026-01-13' }
])

const attendanceDay = computed(() => {
    return attendanceDaily.find(item => item.id === dayId) || attendanceDaily[0]
})

const attendanceEmployees = computed(() => {
    return attendanceRecords[attendanceDay.value.id] || []
})

const showAttendanceEmployeeModal = ref(false)
const selectedAttendanceEmployee = ref({})

const openAttendanceEmployee = (employee) => {
    selectedAttendanceEmployee.value = employee
    showAttendanceEmployeeModal.value = true
}

const closeAttendanceEmployee = () => {
    showAttendanceEmployeeModal.value = false
    selectedAttendanceEmployee.value = {}
}

const formatDate = (date) => {
    if (!date) return '--'
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}
</script>
