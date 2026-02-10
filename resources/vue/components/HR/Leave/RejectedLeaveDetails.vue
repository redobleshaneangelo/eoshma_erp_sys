<template>
    <div class="space-y-6">
        <!-- Rejection Notice Card -->
        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg border border-red-200 p-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-red-200 flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-red-900 mb-1">Request Rejected</h3>
                    <p class="text-sm text-red-700 mb-3">Unfortunately, your leave request could not be approved at this time.</p>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-xs font-semibold text-red-800 uppercase">Rejection Date</p>
                            <p class="text-red-900 font-semibold mt-1">{{ formatDate(rejectionData.rejectionDate) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-red-800 uppercase">Reviewed By</p>
                            <p class="text-red-900 font-semibold mt-1">{{ rejectionData.reviewerName || 'HR Department' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rejection Reason -->
        <div class="bg-white rounded-lg border border-red-200 p-6">
            <h3 class="text-lg font-bold text-[#333333] mb-4 flex items-center gap-2">
                <span class="w-1 h-6 bg-red-600 rounded"></span>
                Reason for Rejection
            </h3>
            <div class="bg-red-50 rounded p-4 border border-red-200">
                <p class="text-sm text-gray-700 leading-relaxed">{{ rejectionData.rejectionReason }}</p>
            </div>
        </div>

        <!-- What You Can Do -->
        <div class="bg-white rounded-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-[#333333] mb-4 flex items-center gap-2">
                <span class="w-1 h-6 bg-amber-600 rounded"></span>
                What You Can Do
            </h3>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="text-sm font-bold text-amber-600">1</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Review the Feedback</p>
                        <p class="text-sm text-gray-600 mt-1">Carefully read the rejection reason to understand why your request could not be approved.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="text-sm font-bold text-amber-600">2</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Contact Your Manager</p>
                        <p class="text-sm text-gray-600 mt-1">Discuss with your manager about alternative dates or solutions to address the concerns.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="text-sm font-bold text-amber-600">3</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Submit a New Request</p>
                        <p class="text-sm text-gray-600 mt-1">Once you have resolved the issues mentioned, feel free to resubmit your leave request.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="text-sm font-bold text-amber-600">4</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Contact HR</p>
                        <p class="text-sm text-gray-600 mt-1">If you have questions about the rejection, reach out to the HR department for clarification.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Policy Information -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-gray-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
                    <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" />
                </svg>
                <div>
                    <p class="text-sm font-semibold text-gray-900">Company Leave Policy</p>
                    <p class="text-sm text-gray-700 mt-1">For more information about leave policies, approved leave types, and approval guidelines, please refer to the employee handbook or contact HR.</p>
                </div>
            </div>
        </div>

        <!-- Appeal Option -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="text-sm font-semibold text-blue-900">Right to Appeal</p>
                    <p class="text-sm text-blue-800 mt-1">If you believe this decision was made in error or circumstances have changed, you may submit an appeal to the HR department within 7 days of this rejection.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { defineProps } from 'vue'

    defineProps({
        rejectionData: {
            type: Object,
            required: true,
            default: () => ({
                rejectionDate: new Date().toISOString().split('T')[0],
                reviewerName: 'HR Department',
                rejectionReason: ''
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
