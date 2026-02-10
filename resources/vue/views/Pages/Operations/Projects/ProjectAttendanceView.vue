<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">
            <div class="mb-8">
                <router-link
                    :to="`/projects/${projectId}/monitoring`"
                    class="inline-flex items-center gap-2 text-[#0c8ce9] font-semibold hover:underline mb-4"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Monitoring
                </router-link>

                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Attendance Records</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ project.project_name }} • {{ project.project_code }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-lg font-semibold text-gray-900">Daily Attendance</h2>
                        <div class="flex items-center gap-2">
                            <button
                                @click="openScanDetail('timeIn')"
                                class="px-4 py-2 text-sm font-semibold text-white bg-[#0c8ce9] rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                Time In
                            </button>
                            <button
                                @click="openScanDetail('timeOut')"
                                class="px-4 py-2 text-sm font-semibold border border-[#0c8ce9] text-[#0c8ce9] rounded-lg hover:bg-blue-50 transition-colors"
                            >
                                Time Out
                            </button>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Position</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Time In</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Time Out</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="worker in attendanceList" :key="worker.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ worker.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ worker.position }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ formatTime(worker.timeIn) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ formatTime(worker.timeOut) }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" :class="getAttendanceStatusClasses(worker)">
                                        {{ getAttendanceStatus(worker) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button
                                        @click="openEmployeeModal(worker)"
                                        class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                                    >
                                        View
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="attendanceList.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No attendance records available
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scan Detail Modal -->
    <div v-if="showScanDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">{{ scanModeLabel }} Scan</h3>
                    <button @click="closeScanDetail" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="w-full h-40 border border-dashed border-gray-300 rounded-lg flex items-center justify-center text-xs text-gray-500">
                    Camera Preview
                </div>
                <button
                    @click="captureScan(scanMode)"
                    class="w-full px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                >
                    Capture
                </button>
                <div class="grid grid-cols-2 gap-3 text-xs text-gray-600">
                    <div>
                        <p class="font-semibold uppercase">Captured At</p>
                        <p>{{ currentScan.capturedAt || '---' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold uppercase">Device</p>
                        <p>{{ currentScan.device || '---' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="font-semibold uppercase">Geolocation</p>
                        <p>{{ currentScan.geo || 'Lat: ---, Lng: ---' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="font-semibold uppercase">Employee</p>
                        <p>{{ currentScan.employeeName || '---' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Details Modal -->
    <div v-if="showEmployeeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Attendance Details</h3>
                    <button @click="closeEmployeeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ selectedEmployee.name }}</p>
                    <p class="text-xs text-gray-600">{{ selectedEmployee.position }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Time In</p>
                        <p class="text-sm text-gray-900 mt-1">{{ formatTime(selectedEmployee.timeIn) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Time Out</p>
                        <p class="text-sm text-gray-900 mt-1">{{ formatTime(selectedEmployee.timeOut) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Status</p>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold mt-1" :class="getAttendanceStatusClasses(selectedEmployee)">
                            {{ getAttendanceStatus(selectedEmployee) }}
                        </span>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-2">QR Code</p>
                    <div class="w-full h-40 border border-gray-300 rounded-lg flex items-center justify-center text-xs text-gray-500">
                        QR
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200">
                <button
                    @click="closeEmployeeModal"
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

const projects = [
    { id: 1, project_name: 'Office Building Renovation', project_code: 'PRJ-2026-001' },
    { id: 2, project_name: 'Infrastructure Development Project', project_code: 'PRJ-2026-002' },
    { id: 3, project_name: 'Warehouse Automation System', project_code: 'PRJ-2026-003' },
    { id: 4, project_name: 'Corporate Training Center Construction', project_code: 'PRJ-2026-004' },
    { id: 5, project_name: 'Data Center Expansion', project_code: 'PRJ-2026-005' }
]

const project = ref(projects.find(p => p.id === parseInt(projectId)) || projects[0])

const attendanceList = ref([
    { id: 1, name: 'John Davis', position: 'Project Manager', timeIn: '08:10 AM', timeOut: '05:12 PM', isLate: false, isAwol: false },
    { id: 2, name: 'Sarah Anderson', position: 'Lead Engineer', timeIn: '08:45 AM', timeOut: '', isLate: true, isAwol: false },
    { id: 3, name: 'Michael Chen', position: 'QA Lead', timeIn: '', timeOut: '', isLate: false, isAwol: false },
    { id: 4, name: 'Anna Reyes', position: 'Safety Officer', timeIn: '', timeOut: '', isLate: false, isAwol: true }
])

const showScanDetailModal = ref(false)
const scanMode = ref('timeIn')
const showEmployeeModal = ref(false)
const selectedEmployee = ref({})

const scanData = ref({
    timeIn: {
        capturedAt: '',
        device: '',
        geo: '',
        employeeName: ''
    },
    timeOut: {
        capturedAt: '',
        device: '',
        geo: '',
        employeeName: ''
    }
})

const scanModeLabel = computed(() => (scanMode.value === 'timeIn' ? 'Time In' : 'Time Out'))

const currentScan = computed(() => {
    return scanMode.value === 'timeIn' ? scanData.value.timeIn : scanData.value.timeOut
})

const formatTime = (value) => {
    return value || '--'
}

const getAttendanceStatus = (worker) => {
    if (worker.isAwol) return 'AWOL'
    if (worker.isLate) return 'Late'
    if (worker.timeIn && worker.timeOut) return 'Complete'
    if (worker.timeIn) return 'Time In'
    return 'No Attendance'
}

const getAttendanceStatusClasses = (worker) => {
    const status = getAttendanceStatus(worker)
    const map = {
        Complete: 'bg-green-100 text-green-800',
        'Time In': 'bg-yellow-100 text-yellow-800',
        Late: 'bg-yellow-100 text-yellow-800',
        AWOL: 'bg-red-100 text-red-800',
        'No Attendance': 'bg-gray-200 text-gray-700'
    }
    return map[status] || 'bg-gray-100 text-gray-700'
}

const openEmployeeModal = (worker) => {
    selectedEmployee.value = worker
    showEmployeeModal.value = true
}

const closeEmployeeModal = () => {
    showEmployeeModal.value = false
    selectedEmployee.value = {}
}

const captureScan = (type) => {
    const timestamp = new Date().toLocaleString('en-PH')
    const payload = {
        capturedAt: timestamp,
        device: 'Camera 01',
        geo: 'Lat: 14.5995, Lng: 120.9842',
        employeeName: 'John Davis'
    }
    if (type === 'timeIn') {
        scanData.value.timeIn = payload
    } else {
        scanData.value.timeOut = payload
    }
}

const openScanDetail = (type) => {
    scanMode.value = type
    showScanDetailModal.value = true
}

const closeScanDetail = () => {
    showScanDetailModal.value = false
}
</script>
