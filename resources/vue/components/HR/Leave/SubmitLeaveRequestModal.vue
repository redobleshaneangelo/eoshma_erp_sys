<template>
    <!-- Modal Backdrop -->
    <div class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center">
        <!-- Modal Container -->
        <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-[#333333]">Submit Leave Request</h2>
                <button
                    @click="$emit('close')"
                    class="text-gray-400 hover:text-gray-600 transition-colors"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <form @submit.prevent="submitForm" class="px-6 py-6 space-y-6">
                <!-- Employee Selection Section -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Employee <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            :value="currentUserName"
                            disabled
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-sm text-gray-700 cursor-not-allowed"
                        />
                        <p class="text-xs text-gray-500 mt-1">Automatically set to your account</p>
                        <p v-if="errors.employeeId" class="text-red-500 text-xs mt-1">{{ errors.employeeId }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Department <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="formData.department"
                            disabled
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-sm text-gray-700 cursor-not-allowed"
                        />
                        <p class="text-xs text-gray-500 mt-1">Auto-filled from employee selection</p>
                    </div>
                </div>

                <!-- Leave Type Section -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Leave Type <span class="text-red-500">*</span>
                    </label>
                    <select
                        v-model="formData.leaveType"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        required
                    >
                        <option value="">Select Leave Type</option>
                        <option
                            v-for="leaveType in leaveTypeOptions"
                            :key="leaveType"
                            :value="leaveType"
                        >
                            {{ leaveType }}
                        </option>
                    </select>
                    <p v-if="errors.leaveType" class="text-red-500 text-xs mt-1">{{ errors.leaveType }}</p>
                </div>

                <!-- Leave Balance (View-Only) -->
                <div v-if="showLeaveBalance" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <p class="text-sm font-semibold text-gray-800 mb-3">Leave Balance</p>

                    <div v-if="selectedTrackedLeaveType" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="bg-white border border-gray-200 rounded-lg px-3 py-2">
                            <p class="text-[11px] uppercase tracking-wide text-gray-500">Annual Allowance</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ selectedLeaveAnnual }} day(s)</p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg px-3 py-2">
                            <p class="text-[11px] uppercase tracking-wide text-gray-500">Used</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ selectedLeaveUsed }} day(s)</p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg px-3 py-2">
                            <p class="text-[11px] uppercase tracking-wide text-gray-500">Remaining</p>
                            <p class="text-sm font-semibold text-[#0c8ce9] mt-1">{{ selectedLeaveRemaining }} day(s)</p>
                        </div>
                    </div>

                    <p v-else class="text-sm text-gray-600">
                        Selected leave type is not tracked yet. Please contact HR for entitlement setup.
                    </p>
                </div>

                <!-- Date Range Section -->
                <div class="grid grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="formData.startDate"
                            type="date"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                            @change="calculateTotalDays"
                            required
                        />
                        <p v-if="errors.startDate" class="text-red-500 text-xs mt-1">{{ errors.startDate }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            End Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="formData.endDate"
                            type="date"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                            @change="calculateTotalDays"
                            required
                        />
                        <p v-if="errors.endDate" class="text-red-500 text-xs mt-1">{{ errors.endDate }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Total Days <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="formData.totalDays"
                            type="number"
                            disabled
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-sm text-gray-700 font-semibold cursor-not-allowed"
                        />
                        <p class="text-xs text-gray-500 mt-1">Auto-calculated</p>
                        <p v-if="errors.totalDays" class="text-red-500 text-xs mt-1">{{ errors.totalDays }}</p>
                    </div>
                </div>

                <!-- Reason / Notes Section -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Reason / Notes <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        v-model="formData.reason"
                        placeholder="Provide reason for leave request..."
                        rows="4"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent resize-none"
                        required
                    ></textarea>
                    <p v-if="errors.reason" class="text-red-500 text-xs mt-1">{{ errors.reason }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ formData.reason.length }}/500 characters</p>
                </div>

                <!-- Attachment Upload Section -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Attachment Upload (Optional)
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-[#0c8ce9] transition-colors cursor-pointer"
                         @click="$refs.fileInput.click()"
                         @dragover.prevent="isDragging = true"
                         @dragleave="isDragging = false"
                         @drop.prevent="handleFileDrop"
                         :class="{ 'bg-blue-50 border-[#0c8ce9]': isDragging }">
                        <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                        </svg>
                        <p class="text-gray-600 font-medium">Click to upload or drag and drop</p>
                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, PDF up to 10MB</p>
                    </div>
                    <input
                        ref="fileInput"
                        type="file"
                        hidden
                        accept=".png,.jpg,.jpeg,.pdf"
                        @change="handleFileSelect"
                    />
                    <div v-if="formData.attachments.length > 0" class="mt-4 space-y-2">
                        <p class="text-sm font-medium text-gray-700">Attached Files:</p>
                        <div v-for="(file, index) in formData.attachments" :key="index" class="flex items-center justify-between bg-gray-50 p-3 rounded border border-gray-200">
                            <span class="text-sm text-gray-700">{{ file.name }}</span>
                            <button
                                @click="removeAttachment(index)"
                                type="button"
                                class="text-red-500 hover:text-red-700 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800">
                        <span class="font-semibold">Note:</span> This request will be submitted to your department head for approval. You will receive email notification once the request is processed.
                    </p>
                </div>

                <!-- Modal Footer / Action Buttons -->
                <div class="flex gap-3 justify-end border-t border-gray-200 -mx-6 px-6 py-4">
                    <button
                        @click="$emit('close')"
                        type="button"
                        class="px-6 py-2.5 border border-gray-300 rounded-lg font-medium text-sm hover:bg-gray-50 transition-colors text-gray-700"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-6 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-medium text-sm hover:bg-blue-700 transition-colors"
                    >
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
    import { computed, onMounted, ref } from 'vue'
    import axios from 'axios'
    import { useAuthStore } from '@/stores/auth'

    const emit = defineEmits(['close', 'submit'])
    const auth = useAuthStore()

    const isDragging = ref(false)

    const formData = ref({
        employeeId: '',
        department: '',
        leaveType: '',
        otherLeaveType: '',
        startDate: '',
        endDate: '',
        totalDays: 0,
        reason: '',
        attachments: []
    })

    const errors = ref({})

    const leaveTypeOptions = ref([])
    const leaveEntitlements = ref({})

    const leaveUsageByType = ref({})

    const showLeaveBalance = computed(() => {
        return Boolean(formData.value.leaveType)
    })

    const selectedTrackedLeaveType = computed(() => {
        if (Object.prototype.hasOwnProperty.call(leaveEntitlements.value, formData.value.leaveType)) {
            return formData.value.leaveType
        }

        return null
    })

    const selectedLeaveAnnual = computed(() => {
        if (!selectedTrackedLeaveType.value) return 0
        return leaveEntitlements.value[selectedTrackedLeaveType.value] || 0
    })

    const selectedLeaveUsed = computed(() => {
        if (!selectedTrackedLeaveType.value) return 0
        return leaveUsageByType.value[selectedTrackedLeaveType.value] || 0
    })

    const selectedLeaveRemaining = computed(() => {
        return Math.max(selectedLeaveAnnual.value - selectedLeaveUsed.value, 0)
    })

    const currentUserName = computed(() => {
        const firstName = String(auth.user?.first_name || '').trim()
        const middleName = String(auth.user?.middle_name || '').trim()
        const lastName = String(auth.user?.last_name || '').trim()

        const fullName = [firstName, middleName, lastName]
            .filter(Boolean)
            .join(' ')
            .trim()

        return fullName || auth.user?.name || auth.user?.user_name || auth.user?.email || 'Current User'
    })

    const trackedLeaveTypes = computed(() => Object.keys(leaveEntitlements.value))
    const countableStatuses = ['pending', 'approved', 'info_requested']

    const fetchLeaveTypes = async () => {
        try {
            const response = await axios.get('/api/leave-types', {
                params: { per_page: 200, status: 'active' }
            })

            const rows = response.data?.data || []
            leaveTypeOptions.value = rows.map((item) => item.name)

            leaveEntitlements.value = rows.reduce((acc, item) => {
                acc[item.name] = Number(item.daysPerYear || 0)
                return acc
            }, {})
        } catch (error) {
            leaveTypeOptions.value = []
            leaveEntitlements.value = {}
            console.error('Failed to load leave type configuration', error)
        }
    }

    const buildUsageMapFromRequests = (rows) => {
        const nextUsage = trackedLeaveTypes.value.reduce((acc, type) => {
            acc[type] = 0
            return acc
        }, {})

        rows.forEach((item) => {
            const status = String(item.status || '').toLowerCase()
            if (!countableStatuses.includes(status)) {
                return
            }

            const leaveType = item.leaveType || item.leave_type
            if (!Object.prototype.hasOwnProperty.call(nextUsage, leaveType)) {
                return
            }

            const totalDays = Number(item.totalDays ?? item.total_days ?? 0)
            if (Number.isFinite(totalDays) && totalDays > 0) {
                nextUsage[leaveType] += totalDays
            }
        })

        leaveUsageByType.value = nextUsage
    }

    const fetchMyLeaveUsage = async () => {
        try {
            const response = await axios.get('/api/leave-requests')
            const rows = response.data?.data || []
            buildUsageMapFromRequests(rows)
        } catch (error) {
            buildUsageMapFromRequests([])
            console.error('Failed to load leave balance usage', error)
        }
    }

    const applyCurrentUserDefaults = () => {
        formData.value.employeeId = auth.user?.id || ''
        formData.value.department = auth.department || auth.user?.dept_name || ''
    }

    const calculateTotalDays = () => {
        if (formData.value.startDate && formData.value.endDate) {
            const start = new Date(formData.value.startDate)
            const end = new Date(formData.value.endDate)
            
            if (start > end) {
                errors.value.endDate = 'End date must be after start date'
                formData.value.totalDays = 0
                return
            }
            
            errors.value.endDate = ''
            const diffTime = Math.abs(end - start)
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1
            formData.value.totalDays = diffDays
        }
    }

    const handleFileSelect = (event) => {
        const files = Array.from(event.target.files)
        addFiles(files)
    }

    const handleFileDrop = (event) => {
        isDragging.value = false
        const files = Array.from(event.dataTransfer.files)
        addFiles(files)
    }

    const addFiles = (files) => {
        files.forEach(file => {
            // Validate file size (10MB max)
            if (file.size > 10 * 1024 * 1024) {
                errors.value.attachments = 'File size exceeds 10MB limit'
                return
            }

            // Validate file type
            const validTypes = ['image/png', 'image/jpeg', 'application/pdf']
            if (!validTypes.includes(file.type)) {
                errors.value.attachments = 'Only PNG, JPG, and PDF files are allowed'
                return
            }

            // Add file to list
            if (!formData.value.attachments.some(f => f.name === file.name)) {
                formData.value.attachments.push({
                    name: file.name,
                    size: file.size,
                    type: file.type,
                    file: file
                })
            }
        })
    }

    const removeAttachment = (index) => {
        formData.value.attachments.splice(index, 1)
    }

    const validateForm = () => {
        errors.value = {}

        if (!formData.value.employeeId) {
            errors.value.employeeId = 'Employee is required'
        }

        if (!formData.value.leaveType) {
            errors.value.leaveType = 'Leave type is required'
        }

        if (!formData.value.startDate) {
            errors.value.startDate = 'Start date is required'
        }

        if (!formData.value.endDate) {
            errors.value.endDate = 'End date is required'
        }

        if (!formData.value.reason.trim()) {
            errors.value.reason = 'Reason is required'
        }

        if (selectedTrackedLeaveType.value && Number(formData.value.totalDays || 0) > Number(selectedLeaveRemaining.value || 0)) {
            errors.value.totalDays = `Requested days exceed your remaining ${selectedTrackedLeaveType.value} balance.`
        }

        return Object.keys(errors.value).length === 0
    }

    const submitForm = () => {
        if (!validateForm()) return

        const submitData = {
            employeeId: auth.user?.id ?? formData.value.employeeId,
            employeeName: currentUserName.value,
            department: formData.value.department,
            leaveType: formData.value.leaveType,
            startDate: formData.value.startDate,
            endDate: formData.value.endDate,
            totalDays: formData.value.totalDays,
            reason: formData.value.reason,
            attachments: formData.value.attachments
        }

        emit('submit', submitData)
    }

    onMounted(() => {
        applyCurrentUserDefaults()
        fetchLeaveTypes()
        fetchMyLeaveUsage()
    })
</script>
