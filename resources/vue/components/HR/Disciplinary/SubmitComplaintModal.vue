<template>
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-hidden">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-[#333333]">Submit Complaint</h2>
                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-6 overflow-y-auto max-h-[70vh]">
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

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Incident *</label>
                    <input
                        v-model="formData.incidentDate"
                        type="date"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    />
                </div>

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

                <div>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input
                            v-model="formData.employeeInvolved"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-[#0c8ce9]"
                        />
                        <span class="text-sm font-semibold text-gray-700">Employee Involved</span>
                    </label>
                </div>

                <div v-if="formData.employeeInvolved">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Employee Involved *</label>
                    <select
                        v-model="formData.complaintAgainst"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    >
                        <option value="">Select Employee</option>
                        <option
                            v-for="employee in employeeOptions"
                            :key="employee.id"
                            :value="employee.name"
                        >
                            {{ employee.name }}
                        </option>
                        <option value="Others">Others</option>
                    </select>
                </div>

                <div v-if="formData.employeeInvolved && formData.complaintAgainst === 'Others'">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Employee Name (Others) *</label>
                    <input
                        v-model="formData.complaintAgainstOther"
                        type="text"
                        placeholder="Enter employee name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description / Details *</label>
                    <textarea
                        v-model="formData.description"
                        rows="5"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                        placeholder="Provide detailed information about the complaint..."
                    ></textarea>
                </div>

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
            </div>

            <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
                <button
                    @click="$emit('close')"
                    class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-200 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="submitComplaint"
                    class="px-6 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-medium text-sm hover:bg-blue-700 transition-colors"
                >
                    Submit Complaint
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const emit = defineEmits(['close', 'submit'])

const fileInput = ref(null)
const employeeOptions = ref([])

const formData = ref({
    complaintType: '',
    incidentDate: new Date().toISOString().split('T')[0],
    department: '',
    employeeInvolved: false,
    complaintAgainst: '',
    complaintAgainstOther: '',
    description: '',
    attachments: []
})

const handleFileUpload = (event) => {
    const files = Array.from(event.target.files || [])
    files.forEach(file => {
        formData.value.attachments.push({ name: file.name, size: file.size })
    })
}

const removeAttachment = (idx) => {
    formData.value.attachments.splice(idx, 1)
}

const fetchEmployees = async () => {
    try {
        const response = await axios.get('/api/users')
        const users = response.data?.users || []
        employeeOptions.value = users.map((user) => ({
            id: user.id,
            name: user.name
        }))
    } catch (error) {
        console.error('Failed to load employee options', error)
        employeeOptions.value = []
    }
}

const submitComplaint = () => {
    if (!formData.value.complaintType || !formData.value.incidentDate || !formData.value.description?.trim()) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please fill in all required fields',
            confirmButtonColor: '#0c8ce9'
        })
        return
    }

    if (formData.value.employeeInvolved && !formData.value.complaintAgainst) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please select the employee involved.',
            confirmButtonColor: '#0c8ce9'
        })
        return
    }

    if (
        formData.value.employeeInvolved
        && formData.value.complaintAgainst === 'Others'
        && !formData.value.complaintAgainstOther?.trim()
    ) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please enter the employee name for Others.',
            confirmButtonColor: '#0c8ce9'
        })
        return
    }

    emit('submit', {
        complaintType: formData.value.complaintType,
        incidentDate: formData.value.incidentDate,
        department: formData.value.department,
        employeeInvolved: formData.value.employeeInvolved,
        complaintAgainst: formData.value.complaintAgainst,
        complaintAgainstOther: formData.value.complaintAgainstOther,
        description: formData.value.description,
        attachments: formData.value.attachments
    })
}

onMounted(() => {
    fetchEmployees()
})
</script>
