<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <button
                    @click="goBack"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Disciplinary
                </button>
                <button
                    @click="saveRecord"
                    class="px-4 py-2 bg-[#0c8ce9] text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors"
                >
                    Create Record
                </button>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-lg p-6 border border-gray-100">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Employee</label>
                            <select
                                v-model="formData.employeeId"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                            >
                                <option value="">Select Employee</option>
                                <option value="EMP-001">John Smith (EMP-001)</option>
                                <option value="EMP-002">Sarah Johnson (EMP-002)</option>
                                <option value="EMP-003">Michael Chen (EMP-003)</option>
                                <option value="EMP-004">Emily Davis (EMP-004)</option>
                                <option value="EMP-005">Robert Wilson (EMP-005)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Record Type</label>
                            <select
                                v-model="formData.recordType"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                            >
                                <option value="">Select Record Type</option>
                                <option value="Verbal Warning">Verbal Warning</option>
                                <option value="Written Warning">Written Warning</option>
                                <option value="Suspension">Suspension</option>
                                <option value="Termination">Termination Recommendation</option>
                                <option value="PIP">Performance Improvement Plan</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Severity</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input v-model="formData.severity" type="radio" value="Low" class="w-4 h-4 text-[#0c8ce9]" />
                                    <span class="text-sm text-gray-700">Low</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input v-model="formData.severity" type="radio" value="Medium" class="w-4 h-4 text-[#0c8ce9]" />
                                    <span class="text-sm text-gray-700">Medium</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input v-model="formData.severity" type="radio" value="High" class="w-4 h-4 text-[#0c8ce9]" />
                                    <span class="text-sm text-gray-700">High</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Date Issued</label>
                            <input
                                v-model="formData.dateIssued"
                                type="date"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Status</label>
                            <select
                                v-model="formData.status"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                            >
                                <option value="Pending">Pending</option>
                                <option value="Reviewed">Reviewed</option>
                                <option value="Closed">Closed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 border border-gray-100">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Description</label>
                    <textarea
                        v-model="formData.description"
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    ></textarea>
                </div>

                <div class="bg-white rounded-lg p-6 border border-gray-100">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Action Taken / Next Steps</label>
                    <textarea
                        v-model="formData.actionTaken"
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    ></textarea>
                </div>

                <div class="bg-white rounded-lg p-6 border border-gray-100">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Internal Comments</label>
                    <textarea
                        v-model="formData.internalComments"
                        rows="2"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    ></textarea>
                </div>
            </div>

            <div class="fixed bottom-0 right-0 p-2 z-50">
                <img :src="agnes" class="h-auto w-auto" v-if="loading" />
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, onMounted } from 'vue'
    import { useRouter } from 'vue-router'
    import Swal from 'sweetalert2'
    import { useAuthStore } from '@/stores/auth'
    import agnes from '@/assets/agnes.gif'

    const auth = useAuthStore()
    const router = useRouter()

    const loading = ref(false)
    const formData = ref({
        employeeId: '',
        recordType: '',
        severity: 'Medium',
        dateIssued: new Date().toISOString().split('T')[0],
        description: '',
        actionTaken: '',
        status: 'Pending',
        internalComments: ''
    })

    const saveRecord = () => {
        if (!formData.value.employeeId || !formData.value.recordType || !formData.value.description) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill in all required fields',
                confirmButtonColor: '#0c8ce9'
            })
            return
        }

        Swal.fire({
            icon: 'success',
            title: 'Created',
            text: 'Record created successfully',
            confirmButtonColor: '#0c8ce9'
        })
        goBack()
    }

    const goBack = () => {
        router.push('/disciplinary')
    }

    onMounted(() => {
        auth.pageTitle = 'Add Disciplinary Record'
    })
</script>
