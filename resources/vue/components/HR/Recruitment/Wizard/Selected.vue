<template>
    <div class="space-y-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="font-semibold text-blue-900 mb-1">Interview Management</h4>
            <p class="text-sm text-blue-700">Schedule interviews, set interview dates, and manage candidate results</p>
        </div>

        <!-- Selected Candidates Table -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-6 py-3 bg-gray-100 border-b border-gray-200">
                <h4 class="font-semibold text-gray-900">Selected Candidates for Interviews</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Candidate Name</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Position</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Interview Date</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700 text-xs">Result</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-700 text-xs">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="candidate in selectedCandidates" :key="candidate.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ candidate.name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ candidate.position }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <input 
                                    type="date" 
                                    v-model="candidate.interviewDate"
                                    class="px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                />
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <select 
                                    v-model="candidate.result"
                                    :class="[
                                        'px-2 py-1 border rounded text-sm focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent',
                                        candidate.result === 'Passed' ? 'border-green-300 bg-green-50' :
                                        candidate.result === 'Failed' ? 'border-red-300 bg-red-50' :
                                        'border-gray-300'
                                    ]"
                                >
                                    <option value="">Select Result</option>
                                    <option value="Interview Scheduled">Interview Scheduled</option>
                                    <option value="Passed">Passed</option>
                                    <option value="Failed">Failed</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                <span v-if="candidate.result === 'Passed'" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    Ready to Hire
                                </span>
                                <span v-else-if="candidate.result === 'Failed'" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    Not Selected
                                </span>
                                <span v-else class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                    Interview Scheduled
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="selectedCandidates.length === 0" class="text-center py-8 text-gray-500">
                <p>No candidates available. Please complete the Sourcing stage first.</p>
            </div>
        </div>

        <!-- Ready Candidates Summary -->
        <div v-if="passedCandidates.length > 0" class="bg-green-50 border border-green-200 rounded-lg p-4">
            <h4 class="font-semibold text-green-900 mb-2">Candidates Ready for Hiring</h4>
            <p class="text-sm text-green-700 mb-3">{{ passedCandidates.length }} candidate(s) passed interviews and are ready to proceed to the hiring stage.</p>
            <button
                @click="proceedWithCandidates"
                class="px-6 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors"
            >
                Proceed with {{ passedCandidates.length }} Candidate(s)
            </button>
        </div>

        <!-- Hiring Sub-Wizard Modal -->
        <div v-if="showHiringWizard" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <HiringSubWizard 
                :candidate="hiringCandidate"
                @close="showHiringWizard = false"
                @complete="onHiringComplete"
            />
        </div>
    </div>
</template>

<script setup>
    import { ref, watch, computed } from 'vue'
    import Swal from 'sweetalert2'
    import HiringSubWizard from './HiringSubWizard.vue'

    const props = defineProps({
        requestId: {
            type: String,
            required: true
        },
        candidates: {
            type: Array,
            default: () => []
        }
    })

    const emit = defineEmits(['interviews-scheduled'])

    const showHiringWizard = ref(false)
    const hiringCandidate = ref(null)
    const selectedCandidates = ref([])

    const passedCandidates = computed(() => {
        return selectedCandidates.value.filter(c => c.result === 'Passed')
    })

    const updateCompletionStatus = () => {
        // Check if all candidates have a result recorded
        hasAllResultsRecorded.value = selectedCandidates.value.length > 0 && 
            selectedCandidates.value.every(c => c.result && c.result !== '')
    }

    watch(() => props.candidates, (newCandidates) => {
        selectedCandidates.value = newCandidates.map(c => ({
            ...c,
            interviewDate: '',
            interviewer: '',
            result: ''
        }))
    }, { immediate: true })

    const openHiringWizard = (candidate) => {
        hiringCandidate.value = candidate
        showHiringWizard.value = true
    }

    const onHiringComplete = () => {
        showHiringWizard.value = false
        emit('interviews-scheduled', selectedCandidates.value)
        Swal.fire('Success', 'Candidate hired successfully. Moving to Hired stage.', 'success')
    }

    const proceedWithCandidates = () => {
        emit('interviews-scheduled', passedCandidates.value)
        Swal.fire('Success', `${passedCandidates.value.length} candidate(s) ready for hiring. Moving to Hired stage.`, 'success')
    }
</script>
