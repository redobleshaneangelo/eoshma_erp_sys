<template>
    <AuthLayout>
        <div class="ps-56.25 bg-[#F3F4F6] min-h-screen">
            <div class="container mx-auto p-1">
                <div class="flex flex-col h-full">
                    <div class="mt-4 px-11">

                        <!-- Header -->
                        <div class="flex justify-between items-center mb-6">
                            <div class="text-xl font-bold text-[#1F2937]">
                                QR Code Scanner - Worker Attendance
                            </div>

                            <button
                                @click="router.back()"
                                class="border border-blue-400 text-blue-400 px-6 py-1 rounded-md text-sm hover:bg-blue-50 transition-colors"
                            >
                                Back
                            </button>
                        </div>

                        <!-- Main Content -->
                        <div class="flex justify-center w-full min-h-screen px-11 mt-3">
                            <div class="w-full max-w-4xl">
                                <div class="bg-white rounded-sm p-8 shadow-sm border border-gray-100">
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                                        <!-- Scanner Section -->
                                        <div class="text-center">
                                            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                                Camera Scanner
                                            </h3>

                                            <!-- Mode Toggle -->
                                            <div class="mb-4 flex justify-center">
                                                <div class="bg-gray-100 p-1 rounded-lg flex">
                                                    <button
                                                        @click="scanMode = 'checkin'"
                                                        :class="scanMode === 'checkin'
                                                            ? 'bg-blue-500 text-white'
                                                            : 'text-gray-600 hover:text-gray-800'"
                                                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors"
                                                    >
                                                        Time In
                                                    </button>

                                                    <button
                                                        @click="scanMode = 'checkout'"
                                                        :class="scanMode === 'checkout'
                                                            ? 'bg-orange-500 text-white'
                                                            : 'text-gray-600 hover:text-gray-800'"
                                                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors"
                                                    >
                                                        Time Out
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Video Scanner -->
                                            <div class="mb-4">
                                                <video
                                                    ref="videoElement"
                                                    autoplay
                                                    playsinline
                                                    class="w-full max-w-md mx-auto border-2 border-gray-300 rounded-lg"
                                                ></video>
                                            </div>

                                            <!-- Scanner Controls -->
                                            <div class="space-x-2">
                                                <button
                                                    @click="startScanner"
                                                    :disabled="isScanning"
                                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                                >
                                                    {{ isScanning ? 'Scanning...' : 'Start Scanner' }}
                                                </button>

                                                <button
                                                    @click="stopScanner"
                                                    :disabled="!isScanning"
                                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                                >
                                                    Stop Scanner
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Results Section -->
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                                Scan Results
                                            </h3>

                                            <!-- Current Result -->
                                            <div
                                                v-if="currentResult"
                                                class="mb-6 p-4 rounded-lg border"
                                                :class="currentResult.success
                                                    ? 'bg-green-50 border-green-200'
                                                    : 'bg-red-50 border-red-200'"
                                            >
                                                <div v-if="currentResult.success" class="text-center">
                                                    <div class="text-green-600 font-semibold mb-2">
                                                        ✓ {{
                                                            currentResult.scanMode === 'checkout'
                                                                ? 'Checked Out'
                                                                : 'Checked In'
                                                        }} Successfully!
                                                    </div>

                                                    <div class="text-sm text-gray-700">
                                                        <p><strong>Employee:</strong> {{ currentResult.worker_name }}</p>
                                                        <p>
                                                            <strong>
                                                                {{
                                                                    currentResult.scanMode === 'checkout'
                                                                        ? 'Time Out'
                                                                        : 'Time In'
                                                                }}:
                                                            </strong>
                                                            {{ formatDateTime(currentResult.scan_time) }}
                                                        </p>
                                                        <p v-if="currentResult.contact_number">
                                                            <strong>Contact:</strong> {{ currentResult.contact_number }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div v-else class="text-center">
                                                    <div class="text-red-600 font-semibold">Error</div>
                                                    <div class="text-sm text-gray-600 mt-1">
                                                        {{ currentResult.message }}
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Photo Capture -->
                                            <div
                                                v-if="showPhotoCapture"
                                                class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg"
                                            >
                                                <h4 class="font-semibold text-gray-800 mb-3 text-center">
                                                    Take Your Photo
                                                </h4>

                                                <div class="text-center">
                                                    <video
                                                        ref="photoVideoElement"
                                                        autoplay
                                                        playsinline
                                                        class="w-full max-w-sm mx-auto border-2 border-gray-300 rounded-lg mb-4"
                                                    ></video>

                                                    <div class="space-x-2">
                                                        <button
                                                            @click="capturePhoto"
                                                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm"
                                                        >
                                                            📸 Capture Photo
                                                        </button>

                                                        <button
                                                            @click="cancelPhotoCapture"
                                                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm"
                                                        >
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Worker Information -->
                                            <div class="border-t pt-4">
                                                <h4 class="font-semibold text-gray-700 mb-3">
                                                    Worker Information
                                                </h4>

                                                <div v-if="currentWorker" class="bg-gray-50 p-4 rounded-lg">
                                                    <div v-if="currentWorker.image" class="mb-3">
                                                        <img
                                                            :src="currentWorker.image"
                                                            :alt="currentWorker.worker_name"
                                                            class="w-20 h-20 rounded-full object-cover mx-auto border-2 border-gray-300"
                                                        />
                                                    </div>

                                                    <div class="space-y-2 text-sm">
                                                        <div>
                                                            <strong>Name:</strong> {{ currentWorker.worker_name }}
                                                        </div>
                                                        <div>
                                                            <strong>Contact:</strong> {{ currentWorker.contact_number }}
                                                        </div>
                                                        <div v-if="currentWorker.temp_id_number">
                                                            <strong>ID:</strong> {{ currentWorker.temp_id_number }}
                                                        </div>
                                                        <div v-if="currentWorker.address">
                                                            <strong>Address:</strong> {{ currentWorker.address }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div v-else class="text-gray-500 text-sm text-center py-4">
                                                    No worker information available.
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Loader -->
        <div class="fixed bottom-0 right-0 p-2 z-50">
            <img v-if="loading" :src="agnes" class="h-auto w-auto" />
        </div>

        <!-- Summary Modal -->
        <div
            v-if="showSummaryModal"
            class="fixed inset-0 flex items-center justify-center z-50"
            @click="closeSummaryModal"
        >
            <div
                class="bg-white p-6 rounded-lg shadow-xl max-w-lg w-full mx-4"
                @click.stop
            >
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">
                        Attendance Summary
                    </h3>

                    <div v-if="summaryData" class="space-y-4">
                        <div class="mb-4">
                            <img
                                :src="summaryData.photo"
                                class="w-32 h-32 mx-auto rounded-full object-cover border-4 border-blue-200"
                            />
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-lg text-gray-800 mb-2">
                                {{ summaryData.worker_name }}
                            </h4>

                            <div class="space-y-1 text-sm text-gray-600">
                                <p>
                                    <strong>
                                        {{
                                            summaryData.scanMode === 'checkout'
                                                ? 'Time Out'
                                                : 'Time In'
                                        }}:
                                    </strong>
                                    {{ formatDateTime(summaryData.scan_time) }}
                                </p>

                                <p v-if="summaryData.contact_number">
                                    <strong>Contact:</strong> {{ summaryData.contact_number }}
                                </p>

                                <p>
                                    <strong>Status:</strong>
                                    <span
                                        class="ml-2 px-2 py-1 rounded-full text-xs font-bold border"
                                        :class="summaryData.scanMode === 'checkout'
                                            ? 'bg-blue-100 text-blue-800 border-blue-300'
                                            : 'bg-green-100 text-green-800 border-green-300'"
                                    >
                                        {{
                                            summaryData.scanMode === 'checkout'
                                                ? 'Checked Out'
                                                : 'Checked In'
                                        }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="flex space-x-3 mt-6">
                            <button
                                @click="confirmAttendance"
                                class="flex-1 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium"
                            >
                                ✓ Confirm {{
                                    summaryData.scanMode === 'checkout'
                                        ? 'Time Out'
                                        : 'Time In'
                                }}
                            </button>

                            <button
                                @click="retakePhoto"
                                class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium"
                            >
                                📸 Retake Photo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
/* =====================================================
   Core Imports
===================================================== */
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'
import { useAuthStore } from '@/stores/auth'

/* =====================================================
   Layout & Assets
===================================================== */
import AuthLayout from '@/views/Layouts/AuthLayout.vue'
import agnes from '@/assets/agnes.gif'

/* =====================================================
   Router
===================================================== */
const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

/* =====================================================
   Refs & Reactive State
===================================================== */
const videoElement = ref(null)
const photoVideoElement = ref(null)

const loading = ref(false)
const isScanning = ref(false)

const showPhotoCapture = ref(false)
const showSummaryModal = ref(false)

const currentResult = ref(null)
const currentWorker = ref(null)
const summaryData = ref(null)

const codeReader = ref(null)
const stream = ref(null)
const photoStream = ref(null)

const scanMode = ref('checkin') // 'checkin' | 'checkout'
const currentScanData = ref(null)

const employeeId = ref(null)
const todayRecord = ref(null)

/* =====================================================
   Attendance Records
===================================================== */
const attendanceRecords = ref([])

const getLocalDate = () => {
    return new Date().toLocaleDateString('en-CA')
}

const userLabel = computed(() => {
    const user = auth.user
    if (!user) return 'Unknown User'
    const last = user.last_name || ''
    const first = user.first_name || ''
    const middle = user.middle_name ? `${user.middle_name.charAt(0)}.` : ''
    return `${last}, ${first} ${middle}`.trim()
})

const loadAttendanceRecords = async () => {
    try {
        const today = getLocalDate()
        const response = await axios.get('/api/attendance/records', {
            params: { date: today }
        })
        attendanceRecords.value = response.data?.data || []
        employeeId.value = response.data?.meta?.employee?.id || null
        todayRecord.value = attendanceRecords.value.find(r => r.date === today) || null
    } catch (error) {
        console.error('Failed to load attendance records:', error)
        attendanceRecords.value = []
        todayRecord.value = null
    }
}

/* =====================================================
   Helpers
===================================================== */
const formatDateTime = (dateString) => {
    if (!dateString) return '—'

    const d = new Date(dateString)
    if (isNaN(d.getTime())) {
        console.warn('Invalid date passed to formatDateTime:', dateString)
        return '—'
    }

    return d.toLocaleString()
}

/* =====================================================
   QR Scanner Logic
===================================================== */
const startScanner = async () => {
    if (isScanning.value) {
        return
    }

    try {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error('Camera not supported on this device')
        }

        if (!window.ZXing) {
            await loadZXingLibrary()
        }

        if (!videoElement.value) {
            throw new Error('Camera element not available')
        }

        if (codeReader.value) {
            codeReader.value.reset()
        }

        codeReader.value = new ZXing.BrowserQRCodeReader()
        isScanning.value = true

        codeReader.value.decodeFromVideoDevice(
            undefined,
            videoElement.value,
            (result, err) => {
                if (result) {
                    handleScanResult(result.text)
                }
                if (
                    err &&
                    !(err instanceof ZXing.NotFoundException) &&
                    !(err instanceof ZXing.ChecksumException) &&
                    !(err instanceof ZXing.FormatException)
                ) {
                    console.error('QR scan error:', err)
                }
            }
        )
    } catch (error) {
        console.error('Failed to start scanner:', error)
        currentResult.value = {
            success: false,
            message: error.message || 'Failed to access camera. Please check permissions.'
        }
    }
}

const stopScanner = () => {
    if (codeReader.value && isScanning.value) {
        codeReader.value.reset()
        isScanning.value = false
    }

    if (videoElement.value) {
        videoElement.value.srcObject = null
    }
}

/* =====================================================
   QR Scan Handling
===================================================== */
const handleScanResult = async (data) => {
        try {
            const qrData = JSON.parse(data)

            if (qrData.type !== 'employee_attendance') {
                currentResult.value = {
                    success: false,
                    message: 'Invalid QR code format. This is not an attendance code.'
                }
                return
            }

            const today = getLocalDate()
            if (qrData.date && qrData.date !== today) {
                currentResult.value = {
                    success: false,
                    message: 'This QR code is not for today.'
                }
                return
            }

            if (employeeId.value && qrData.employee_id && String(qrData.employee_id) !== String(employeeId.value)) {
                currentResult.value = {
                    success: false,
                    message: 'This QR code does not belong to your account.'
                }
                return
            }

            const scanTime = new Date().toISOString()

            if (scanMode.value === 'checkin' && todayRecord.value?.timeIn) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'Already Checked In',
                    html: 'You already have a time in for today.',
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: { popup: 'no-wrap-toast' }
                })
                return
            }

            if (scanMode.value === 'checkout' && !todayRecord.value?.timeIn) {
                currentResult.value = {
                    success: false,
                    message: 'No time in found for today. Please check in first.'
                }
                return
            }

            if (scanMode.value === 'checkout' && todayRecord.value?.timeOut) {
                currentResult.value = {
                    success: false,
                    message: 'You already have a time out for today.'
                }
                return
            }

            currentScanData.value = {
                qrPayload: data,
                scanTime,
                scanMode: scanMode.value,
                date: qrData.date || today
            }

            currentWorker.value = {
                worker_name: userLabel.value,
                contact_number: auth.user?.phone || null,
                temp_id_number: employeeId.value || null,
                address: auth.user?.address || null,
                image: auth.user?.profile_photo_url || null
            }

            startPhotoCapture()
        } catch (e) {
            currentResult.value = {
                success: false,
                message: 'Error parsing QR code. Please ensure you are scanning a valid worker QR code.'
            }
        }
    }

/* =====================================================
   Photo Capture
===================================================== */
const startPhotoCapture = async () => {
    try {
        showPhotoCapture.value = true

        if (isScanning.value) {
            stopScanner()
        }

        photoStream.value = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user' }
        })

        photoVideoElement.value.srcObject = photoStream.value
        photoVideoElement.value.play()
    } catch (error) {
        console.error('Failed to start photo capture:', error)
        alert('Failed to access camera for photo capture. Please check permissions.')
        showPhotoCapture.value = false
    }
}

const capturePhoto = () => {
    try {
        const canvas = document.createElement('canvas')
        const video = photoVideoElement.value

        canvas.width = video.videoWidth
        canvas.height = video.videoHeight

        const ctx = canvas.getContext('2d')
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height)

        const photoDataUrl = canvas.toDataURL('image/jpeg', 0.8)

        if (currentScanData.value) {
            const { scanTime, scanMode } = currentScanData.value
            summaryData.value = {
                worker_name: userLabel.value,
                contact_number: auth.user?.phone || null,
                worker_id: employeeId.value,
                scan_time: scanTime,
                scanMode,
                generated_at: scanTime,
                photo: photoDataUrl
            }
        } else {
            summaryData.value = {
                ...currentResult.value,
                photo: photoDataUrl
            }
        }

        stopPhotoCapture()
        showSummaryModal.value = true
    } catch (error) {
        console.error('Failed to capture photo:', error)
        alert('Failed to capture photo. Please try again.')
    }
}

const cancelPhotoCapture = () => {
    stopPhotoCapture()
    showPhotoCapture.value = false
    currentScanData.value = null
}

const stopPhotoCapture = () => {
    if (photoStream.value) {
        photoStream.value.getTracks().forEach(track => track.stop())
        photoStream.value = null
    }

    if (photoVideoElement.value) {
        photoVideoElement.value.srcObject = null
    }

    showPhotoCapture.value = false
}

/* =====================================================
   Attendance Confirmation
===================================================== */
const confirmAttendance = async () => {
    if (summaryData.value && currentScanData.value) {
        const { scanTime, scanMode, qrPayload, date } = currentScanData.value
        const timeValue = new Date(scanTime).toTimeString().slice(0, 8)

        try {
            if (scanMode === 'checkin') {
                await axios.patch(`/api/attendance/records/${date}`, {
                    time_in: timeValue,
                    time_in_photo: summaryData.value.photo,
                    qr_payload: qrPayload
                })

                await loadAttendanceRecords()

                currentResult.value = {
                    success: true,
                    worker_name: userLabel.value,
                    contact_number: auth.user?.phone || null,
                    scan_time: scanTime,
                    scanMode: 'checkin'
                }
            } else {
                await axios.patch(`/api/attendance/records/${date}`, {
                    time_out: timeValue,
                    time_out_photo: summaryData.value.photo,
                    qr_payload: qrPayload
                })

                await loadAttendanceRecords()

                currentResult.value = {
                    success: true,
                    worker_name: userLabel.value,
                    contact_number: auth.user?.phone || null,
                    scan_time: scanTime,
                    scanMode: 'checkout'
                }
            }

            // Reset and close
            closeSummaryModal()
            summaryData.value = null
            currentScanData.value = null

            // Auto-hide success message after 3 seconds (don't restart scanner)
            setTimeout(() => {
                currentResult.value = null
                currentWorker.value = null
            }, 3000)
        } catch (error) {
            console.error('Failed to save attendance:', error)

            // Handle specific API validation errors
            if (error.response && error.response.status === 422 && error.response.data.error) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'Attendance Error',
                    html: error.response.data.message || error.response.data.error,
                    showConfirmButton: false,
                    timer: 4000,
                    customClass: { popup: 'no-wrap-toast' }
                })
            } else {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error',
                    html: 'Failed to save attendance record.',
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: { popup: 'no-wrap-toast' }
                })
            }

            // Reset state on error
            closeSummaryModal()
            summaryData.value = null
            currentScanData.value = null
            currentWorker.value = null
        }
    }
}

/* =====================================================
   Modal Controls
===================================================== */
const retakePhoto = () => {
    closeSummaryModal()
    startPhotoCapture()
}

const closeSummaryModal = () => {
    showSummaryModal.value = false
    summaryData.value = null
}

/* =====================================================
   ZXing Loader
===================================================== */
const loadZXingLibrary = () => {
    return new Promise((resolve, reject) => {
        if (window.ZXing) {
            resolve()
            return
        }

        const script = document.createElement('script')
        script.src = 'https://cdn.jsdelivr.net/npm/@zxing/library@0.19.3/umd/index.min.js'
        script.onload = resolve
        script.onerror = reject
        document.head.appendChild(script)
    })
}

/* =====================================================
   Lifecycle
===================================================== */
onMounted(() => {
    if (route.query?.mode === 'out') {
        scanMode.value = 'checkout'
    }
    if (route.query?.mode === 'in') {
        scanMode.value = 'checkin'
    }
    loadAttendanceRecords()
})

onUnmounted(() => {
    stopScanner()
    stopPhotoCapture()
})
</script>
