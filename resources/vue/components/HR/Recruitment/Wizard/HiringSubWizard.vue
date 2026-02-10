<template>
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="px-8 py-6 border-b border-gray-200 bg-linear-to-r from-green-50 to-gray-50">
            <h2 class="text-2xl font-bold text-gray-900">Hiring Process</h2>
            <p class="text-gray-600 text-sm mt-1">{{ candidate.name }} - {{ candidate.position }}</p>
        </div>

        <!-- Stepper -->
        <div class="px-8 py-6 border-b border-gray-200 bg-white overflow-x-auto">
            <div class="flex items-center justify-between min-w-max">
                <div v-for="(step, index) in hiringSteps" :key="step.id" class="flex items-center">
                    <button
                        @click="currentStep = step.id"
                        :disabled="!isStepAccessible(step.id)"
                        :class="[
                            'relative z-10 h-10 w-10 rounded-full font-semibold text-xs flex items-center justify-center transition-all shrink-0',
                            currentStep === step.id
                                ? 'bg-green-600 text-white shadow-lg'
                                : isStepAccessible(step.id)
                                    ? 'bg-gray-100 text-gray-700 hover:bg-gray-200 cursor-pointer'
                                    : 'bg-gray-50 text-gray-400 cursor-not-allowed'
                        ]"
                    >
                        {{ index + 1 }}
                    </button>
                    <div class="text-center mx-2">
                        <p :class="[
                            'text-xs font-semibold whitespace-nowrap',
                            currentStep === step.id || index === 0
                                ? 'text-green-600'
                                : 'text-gray-500'
                        ]">
                            {{ step.label }}
                        </p>
                    </div>
                    <div v-if="index < hiringSteps.length - 1" :class="[
                        'h-0.5 w-12 shrink-0',
                        isStepAccessible(hiringSteps[index + 1].id) ? 'bg-green-600' : 'bg-gray-200'
                    ]"></div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-8">
            <!-- Step 1: Account Creation -->
            <div v-if="currentStep === 'account'" class="space-y-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">System Account Creation</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Employee ID</label>
                        <input 
                            type="text" 
                            :value="hiringData.employeeId"
                            disabled
                            class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600"
                        />
                        <p class="text-xs text-gray-500 mt-1">Auto-generated</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                        <input 
                            v-model="hiringData.username"
                            type="text" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input 
                            v-model="hiringData.email"
                            type="email" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Initial Role</label>
                        <input 
                            type="text" 
                            value="Employee"
                            disabled
                            class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">System Access Status</label>
                        <input 
                            type="text" 
                            value="Pending"
                            disabled
                            class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600"
                        />
                    </div>
                </div>
            </div>

            <!-- Step 2: Personal Details -->
            <div v-if="currentStep === 'personal'" class="space-y-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Personal Details</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                        <input 
                            v-model="hiringData.fullName"
                            type="text" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Birth</label>
                        <input 
                            v-model="hiringData.dob"
                            type="date" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Number</label>
                        <input 
                            v-model="hiringData.phone"
                            type="tel" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                        <textarea 
                            v-model="hiringData.address"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- Step 3: Document Submission -->
            <div v-if="currentStep === 'documents'" class="space-y-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Document Submission</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Government IDs</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                            <input 
                                type="file" 
                                multiple
                                class="hidden"
                                ref="govIdInput"
                            />
                            <button 
                                @click="$refs.govIdInput?.click()"
                                class="text-[#0c8ce9] hover:text-blue-700 font-semibold"
                            >
                                Upload Government IDs
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Resume</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                            <input 
                                type="file" 
                                class="hidden"
                                ref="resumeInput"
                            />
                            <button 
                                @click="$refs.resumeInput?.click()"
                                class="text-[#0c8ce9] hover:text-blue-700 font-semibold"
                            >
                                Upload Resume
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Offer Letter</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                            <input 
                                type="file" 
                                class="hidden"
                                ref="offerInput"
                            />
                            <button 
                                @click="$refs.offerInput?.click()"
                                class="text-[#0c8ce9] hover:text-blue-700 font-semibold"
                            >
                                Upload Offer Letter
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Medical / Clearance</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                            <input 
                                type="file" 
                                class="hidden"
                                ref="medicalInput"
                            />
                            <button 
                                @click="$refs.medicalInput?.click()"
                                class="text-[#0c8ce9] hover:text-blue-700 font-semibold"
                            >
                                Upload Medical Clearance
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Contract Setup -->
            <div v-if="currentStep === 'contract'" class="space-y-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Contract & Employment Details</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Employment Type</label>
                        <select 
                            v-model="hiringData.employmentType"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                        >
                            <option value="">Select Type</option>
                            <option value="Internal">Internal</option>
                            <option value="Contractual">Contractual</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Department</label>
                        <select 
                            v-model="hiringData.department"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                        >
                            <option value="">Select Department</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Sales">Sales</option>
                            <option value="Marketing">Marketing</option>
                            <option value="HR">HR</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Position</label>
                        <input 
                            v-model="hiringData.position"
                            type="text" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                        <input 
                            v-model="hiringData.startDate"
                            type="date" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                        />
                    </div>
                    <div v-if="hiringData.employmentType === 'Contractual'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Contract Duration (months)</label>
                        <input 
                            v-model="hiringData.contractDuration"
                            type="number" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Salary Rate <span class="text-red-600">*</span></label>
                        <input 
                            v-model="hiringData.salaryRate"
                            type="number" 
                            placeholder="Enter salary rate"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Payroll Group</label>
                        <select 
                            v-model="hiringData.payrollGroup"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                        >
                            <option value="">Select Group</option>
                            <option value="Standard">Standard</option>
                            <option value="Executive">Executive</option>
                            <option value="Contractual">Contractual</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-8 py-4 border-t border-gray-200 bg-gray-50 flex justify-between">
            <button
                v-if="currentStepIndex > 0"
                @click="goToPreviousStep"
                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-100 transition-colors"
            >
                ← Back
            </button>
            <div></div>
            <div class="space-x-3">
                <button
                    v-if="currentStepIndex < hiringSteps.length - 1"
                    @click="goToNextStep"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors"
                >
                    Next →
                </button>
                <button
                    v-else
                    @click="completeHiring"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                    Complete Hiring
                </button>
                <button 
                    @click="$emit('close')"
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-100 transition-colors"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed } from 'vue'
    import Swal from 'sweetalert2'

    const props = defineProps({
        candidate: {
            type: Object,
            required: true
        }
    })

    const emit = defineEmits(['close', 'complete'])

    const currentStep = ref('account')
    const hiringSteps = [
        { id: 'account', label: 'Account' },
        { id: 'personal', label: 'Personal' },
        { id: 'documents', label: 'Documents' },
        { id: 'contract', label: 'Contract' }
    ]

    const hiringData = ref({
        employeeId: 'EMP-' + Math.random().toString(36).substr(2, 6).toUpperCase(),
        username: '',
        email: '',
        fullName: props.candidate.name,
        dob: '',
        phone: '',
        address: '',
        employmentType: '',
        department: '',
        position: props.candidate.position,
        startDate: '',
        contractDuration: '',
        salaryRate: '',
        payrollGroup: ''
    })

    const currentStepIndex = computed(() => {
        return hiringSteps.findIndex(s => s.id === currentStep.value)
    })

    const isStepAccessible = (stepId) => {
        const stepIndex = hiringSteps.findIndex(s => s.id === stepId)
        const currentIndex = hiringSteps.findIndex(s => s.id === currentStep.value)
        return stepIndex <= currentIndex
    }

    const goToPreviousStep = () => {
        if (currentStepIndex.value > 0) {
            currentStep.value = hiringSteps[currentStepIndex.value - 1].id
        }
    }

    const goToNextStep = () => {
        if (currentStepIndex.value < hiringSteps.length - 1) {
            currentStep.value = hiringSteps[currentStepIndex.value + 1].id
        }
    }

    const completeHiring = () => {
        if (!hiringData.value.employmentType || !hiringData.value.department || !hiringData.value.startDate || !hiringData.value.salaryRate) {
            Swal.fire('Error', 'Please fill in all required fields', 'error')
            return
        }

        Swal.fire('Success', 'Employee account created successfully', 'success')
        emit('complete')
    }
</script>
