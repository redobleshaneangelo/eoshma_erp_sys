<template>
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="bg-linear-to-r from-purple-600 to-purple-700 px-6 py-4 flex items-center justify-between shrink-0 rounded-t-lg">
                <div>
                    <h2 class="text-xl font-bold text-white">Promote Employee</h2>
                    <p class="text-sm text-purple-100">{{ employee.name }}</p>
                </div>
                <button
                    @click="$emit('close')"
                    class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition-colors"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form - Scrollable Content -->
            <form @submit.prevent="handleSubmit" class="p-6 space-y-5 overflow-y-auto flex-1">
                <!-- Current Information -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Current Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-gray-600">Current Job Title</label>
                            <p class="text-sm font-semibold text-gray-900">{{ employee.role }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Current Department</label>
                            <p class="text-sm font-semibold text-gray-900">{{ employee.department }}</p>
                        </div>
                    </div>
                </div>

                <!-- New Position Information -->
                <div>
                    <div class="grid grid-cols-2 gap-4 space-y-4">
                        <!-- New Job Title -->
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                New Job Title <span class="text-red-600">*</span>
                            </label>
                            <input
                                v-model="formData.newRole"
                                type="text"
                                placeholder="e.g., Senior Engineer, Manager"
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                            />
                            <p v-if="errors.newRole" class="text-red-600 text-xs mt-1">{{ errors.newRole }}</p>
                        </div>

                        <!-- New Department -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Department <span class="text-red-600">*</span>
                            </label>
                            <select
                                v-model="formData.newDepartment"
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                            >
                                <option value="">Select department</option>
                                <option value="Engineering">Engineering</option>
                                <option value="Sales">Sales</option>
                                <option value="Marketing">Marketing</option>
                                <option value="HR">HR</option>
                                <option value="Finance">Finance</option>
                                <option value="Operations">Operations</option>
                            </select>
                            <p v-if="errors.newDepartment" class="text-red-600 text-xs mt-1">{{ errors.newDepartment }}</p>
                        </div>

                        <!-- Effective Date -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Effective Date <span class="text-red-600">*</span>
                            </label>
                            <input
                                v-model="formData.effectiveDate"
                                type="date"
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                            />
                            <p v-if="errors.effectiveDate" class="text-red-600 text-xs mt-1">{{ errors.effectiveDate }}</p>
                        </div>

                        <!-- New Salary -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                New Salary <span class="text-red-600">*</span>
                            </label>
                            <input
                                v-model="formData.newSalary"
                                type="number"
                                placeholder="0.00"
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                            />
                            <p v-if="errors.newSalary" class="text-red-600 text-xs mt-1">{{ errors.newSalary }}</p>
                        </div>
                    </div>
                </div>

                <!-- Promotion Notes -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Promotion Notes
                    </label>
                    <textarea
                        v-model="formData.notes"
                        rows="3"
                        placeholder="Add any additional notes about this promotion"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all resize-none"
                    ></textarea>
                </div>

                <!-- Confirmation Message -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <p class="text-sm text-purple-900">
                        <span class="font-semibold">Confirmation:</span> This promotion will update the employee's job title, department, and salary effective on the specified date.
                    </p>
                </div>
            </form>

            <!-- Footer -->
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3 rounded-b-lg shrink-0">
                <button
                    @click="$emit('close')"
                    type="button"
                    class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="handleSubmit"
                    :disabled="loading"
                    class="px-6 py-2.5 bg-purple-600 text-white rounded-lg font-semibold text-sm hover:bg-purple-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors flex items-center gap-2"
                >
                    <svg v-if="loading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ loading ? 'Processing...' : 'Promote Employee' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, defineProps, defineEmits } from 'vue'
    import Swal from 'sweetalert2'

    const props = defineProps({
        employee: {
            type: Object,
            required: true
        },
        loading: {
            type: Boolean,
            default: false
        }
    })

    const emit = defineEmits(['close', 'promote'])

    const formData = ref({
        newRole: '',
        newDepartment: '',
        effectiveDate: new Date().toISOString().split('T')[0],
        newSalary: '',
        notes: ''
    })

    const errors = ref({})

    const validateForm = () => {
        errors.value = {}

        if (!formData.value.newRole || formData.value.newRole.trim() === '') {
            errors.value.newRole = 'New job title is required'
        }

        if (!formData.value.newDepartment) {
            errors.value.newDepartment = 'New department is required'
        }

        if (!formData.value.effectiveDate) {
            errors.value.effectiveDate = 'Effective date is required'
        }

        if (!formData.value.newSalary || formData.value.newSalary <= 0) {
            errors.value.newSalary = 'Valid salary is required'
        }

        return Object.keys(errors.value).length === 0
    }

    const handleSubmit = () => {
        if (validateForm()) {
            // Show confirmation dialog
            Swal.fire({
                title: 'Confirm Promotion',
                html: `<div class="text-left">
                    <p class="mb-3"><strong>Employee:</strong> ${props.employee.name}</p>
                    <p class="mb-3"><strong>New Position:</strong> ${formData.value.newRole}</p>
                    <p class="mb-3"><strong>New Department:</strong> ${formData.value.newDepartment}</p>
                    <p class="mb-3"><strong>Effective Date:</strong> ${formData.value.effectiveDate}</p>
                    <p><strong>New Salary:</strong> ${formData.value.newSalary}</p>
                </div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#9333ea',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Send Request',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Emit the promotion event
                    emit('promote', formData.value)
                    
                    // Show success toast
                    Swal.fire({
                        icon: 'success',
                        title: 'Request Sent',
                        text: 'Promotion request sent to the admin for approval',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    })
                }
            })
        }
    }
</script>
