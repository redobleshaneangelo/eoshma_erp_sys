<template>
    <div class="space-y-6">
        <!-- Approval Details Card -->
        <div class="bg-linear-to-br from-green-50 to-green-100 rounded-lg border border-green-200 p-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-green-200 flex items-center justify-center shrink-0">
                    <svg class="w-7 h-7 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-green-900 mb-1">Request Approved</h3>
                    <p class="text-sm text-green-700 mb-3">This leave request has been approved and the employee can proceed with their leave plans.</p>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-xs font-semibold text-green-800 uppercase">Approval Date</p>
                            <p class="text-green-900 font-semibold mt-1">{{ formatDate(approvalData.approvalDate) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-green-800 uppercase">Approved By</p>
                            <p class="text-green-900 font-semibold mt-1">{{ approvalData.approverName || 'HR Department' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approver Notes -->
        <div v-if="approvalData.approverNotes" class="bg-white rounded-lg border border-green-200 p-6">
            <h3 class="text-lg font-bold text-[#333333] mb-4 flex items-center gap-2">
                <span class="w-1 h-6 bg-green-600 rounded"></span>
                Approval Notes
            </h3>
            <div class="bg-green-50 rounded p-4 border border-green-200">
                <p class="text-sm text-gray-700 leading-relaxed">{{ approvalData.approverNotes }}</p>
            </div>
        </div>

        <!-- Employee Communication -->
        <div class="bg-white rounded-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-[#333333] mb-4 flex items-center gap-2">
                <span class="w-1 h-6 bg-blue-600 rounded"></span>
                Next Steps
            </h3>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mt-0.5">
                        <span class="text-sm font-bold text-blue-600">1</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Plan Your Leave</p>
                        <p class="text-sm text-gray-600 mt-1">Coordinate with your team members and document any handover requirements.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mt-0.5">
                        <span class="text-sm font-bold text-blue-600">2</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Notify Your Manager</p>
                        <p class="text-sm text-gray-600 mt-1">Inform your direct manager about your leave dates and coverage arrangements.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mt-0.5">
                        <span class="text-sm font-bold text-blue-600">3</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Update Your Status</p>
                        <p class="text-sm text-gray-600 mt-1">Set your status to 'On Leave' in the system during your leave period.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Important Information -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="text-sm font-semibold text-blue-900">Important Notice</p>
                    <p class="text-sm text-blue-800 mt-1">Any changes to your approved leave dates must be resubmitted for approval. Please contact HR if you need to modify your leave period.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { defineProps } from 'vue'

    defineProps({
        approvalData: {
            type: Object,
            required: true,
            default: () => ({
                approvalDate: new Date().toISOString().split('T')[0],
                approverName: 'HR Department',
                approverNotes: ''
            })
        }
    })

    const formatDate = (date) => {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        })
    }
</script>
