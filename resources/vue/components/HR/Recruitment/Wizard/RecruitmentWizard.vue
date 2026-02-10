<template>
    <div class="bg-white rounded-lg w-full overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="px-8 py-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900">Recruitment Wizard</h2>
            <p class="text-gray-600 text-sm mt-1">Request: {{ requestId }}</p>
        </div>

        <!-- Stepper -->
        <div class="pt-6 px-8">
            <div class="flex items-flex-start justify-between gap-2">
                <div v-for="(step, index) in steps" :key="step.id" class="flex flex-col items-center flex-1">
                    <!-- Step Indicator -->
                    <button
                        @click="currentStep = step.id"
                        :disabled="!isStepAccessible(step.id)"
                        :class="[
                            'h-10 w-10 rounded-full font-semibold text-sm flex items-center justify-center transition-colors mb-2',
                            currentStep === step.id
                                ? 'bg-[#0c8ce9] text-white'
                                : step.completed
                                    ? 'bg-green-500 text-white'
                                    : isStepAccessible(step.id)
                                        ? 'bg-gray-300 text-gray-700 hover:bg-gray-400 cursor-pointer'
                                        : 'bg-gray-200 text-gray-400 cursor-not-allowed'
                        ]"
                    >
                        <span v-if="step.completed">✓</span>
                        <span v-else>{{ index + 1 }}</span>
                    </button>

                    <!-- Step Label -->
                    <p :class="[
                        'text-center text-xs font-semibold',
                        currentStep === step.id || step.completed
                            ? 'text-[#0c8ce9]'
                            : 'text-gray-500'
                    ]">
                        {{ step.label }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-8">
            <!-- Sourcing Stage -->
            <div v-if="currentStep === 'sourcing'">
                <SourcingStage 
                    :requestId="requestId"
                    @candidates-ready="onCandidatesReady"
                />
            </div>

            <!-- Selected Stage -->
            <div v-if="currentStep === 'selected'">
                <SelectedStage 
                    :requestId="requestId"
                    :candidates="selectedCandidates"
                    @interviews-scheduled="onInterviewsScheduled"
                />
            </div>

            <!-- Hired Stage -->
            <div v-if="currentStep === 'hired'">
                <HiredStage 
                    :requestId="requestId"
                    :candidates="hiredCandidates"
                    @access-requested="onAccessRequested"
                />
                
                <!-- Pending Access Helper Message -->
                <div v-if="hasAnyPendingAccess" class="mt-6 bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        <div>
                            <p class="font-semibold text-amber-900 mb-1">System Access Pending</p>
                            <p class="text-sm text-amber-700">All newly hired employees must have System Access Status = Completed before the wizard can be finished.</p>
                        </div>
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
            <button
                v-if="currentStepIndex < steps.length - 1"
                @click="goToNextStep"
                :disabled="!canProceedToNextStep"
                :class="[
                    'px-6 py-2 rounded-lg font-semibold transition-colors flex items-center gap-2',
                    canProceedToNextStep
                        ? 'bg-[#0c8ce9] text-white hover:bg-blue-700'
                        : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                ]"
            >
                Next →
            </button>
            <button
                v-else
                @click="finishWizard"
                :disabled="!canFinishWizard"
                :class="[
                    'px-6 py-2 rounded-lg font-semibold transition-colors flex items-center gap-2',
                    canFinishWizard
                        ? 'bg-green-600 text-white hover:bg-green-700'
                        : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                ]"
            >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                Finish
            </button>
            <button
                @click="saveAndExit"
                class="px-6 py-2 border border-blue-500 text-blue-500 rounded-lg font-semibold hover:bg-blue-500 hover:text-white transition-colors flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V3" />
                </svg>
                Save and Exit
            </button>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed, onMounted } from 'vue'
    import Swal from 'sweetalert2'
    import SourcingStage from './Sourcing.vue'
    import SelectedStage from './Selected.vue'
    import HiredStage from './Hired.vue'
    

    const props = defineProps({
        requestId: {
            type: String,
            required: false,
            default: ''
        }
    })

    const emit = defineEmits(['close', 'complete'])

    const currentStep = ref('sourcing')
    const selectedCandidates = ref([])
    const hiredCandidates = ref([])
    const pendingAccessRequests = ref([])

    const steps = [
        { id: 'sourcing', label: 'Sourcing', completed: false },
        { id: 'selected', label: 'Selected', completed: false },
        { id: 'hired', label: 'Hired', completed: false }
    ]

    const currentStepIndex = computed(() => {
        return steps.findIndex(s => s.id === currentStep.value)
    })

    const hasAnyPendingAccess = computed(() => {
        return hiredCandidates.value.some(c => c.systemAccessStatus === 'Pending')
    })

    const canFinishWizard = computed(() => {
        if (currentStep.value !== 'hired') return false
        if (hiredCandidates.value.length === 0) return false
        return !hasAnyPendingAccess.value
    })

    const canProceedToNextStep = computed(() => {
        if (currentStep.value === 'sourcing') {
            return selectedCandidates.value.length > 0
        } else if (currentStep.value === 'selected') {
            return hiredCandidates.value.length > 0
        } else if (currentStep.value === 'hired') {
            return hiredCandidates.value.length > 0
        }
        return false
    })

    const isStepAccessible = (stepId) => {
        const stepIndex = steps.findIndex(s => s.id === stepId)
        const currentIndex = steps.findIndex(s => s.id === currentStep.value)
        return stepIndex <= currentIndex || steps[stepIndex - 1]?.completed
    }

    const goToPreviousStep = () => {
        if (currentStepIndex.value > 0) {
            currentStep.value = steps[currentStepIndex.value - 1].id
        }
    }

    const goToNextStep = () => {
        if (canProceedToNextStep.value && currentStepIndex.value < steps.length - 1) {
            const currentIdx = currentStepIndex.value
            steps[currentIdx].completed = true
            currentStep.value = steps[currentIdx + 1].id
        }
    }

    const onCandidatesReady = (candidates) => {
        selectedCandidates.value = candidates.filter(c => c.status === 'Approved')
        if (selectedCandidates.value.length > 0) {
            steps[0].completed = true
        }
    }

    const onInterviewsScheduled = (candidates) => {
        // Filter candidates that passed interviews to create hired candidates list
        const passedCandidates = candidates.filter(c => c.result === 'Passed')
        hiredCandidates.value = passedCandidates
        
        // Mark Step 2 as completed when hired candidates are available
        if (hiredCandidates.value.length > 0) {
            steps[1].completed = true
        }
    }

    const onAccessRequested = (candidates) => {
        pendingAccessRequests.value = candidates.map(c => ({
            ...c,
            systemAccessStatus: 'Pending'
        }))
    }

    const finishWizard = () => {
        if (!canFinishWizard.value) return
        
        steps[2].completed = true
        
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            html: 'Recruitment process completed successfully.',
            confirmButtonColor: '#10b981'
        })
        
        emit('complete')
    }

    const saveAndExit = async () => {
        try {
            // Save current wizard state
            const wizardState = {
                requestId: props.requestId,
                currentStep: currentStep.value,
                selectedCandidates: selectedCandidates.value,
                hiredCandidates: hiredCandidates.value,
                pendingAccessRequests: pendingAccessRequests.value,
                stepsCompleted: steps.map(s => ({ id: s.id, completed: s.completed })),
                savedAt: new Date().toISOString()
            }

            // Save to localStorage as backup
            localStorage.setItem(`recruitment_wizard_${props.requestId}`, JSON.stringify(wizardState))

            // API call to save to backend
            // await axios.post(`/api/recruitment/${props.requestId}/wizard/save`, wizardState)

            Swal.fire({
                icon: 'success',
                title: 'Progress Saved',
                html: `Your recruitment wizard progress has been saved.<br/>You can resume from <strong>${getCurrentStepLabel()}</strong> next time.`,
                confirmButtonColor: '#0c8ce9',
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                emit('close')
            })
        } catch (error) {
            console.error('Error saving wizard state:', error)
            Swal.fire({
                icon: 'error',
                title: 'Save Failed',
                text: 'Failed to save progress. Please try again.',
                confirmButtonColor: '#ef4444'
            })
        }
    }

    const getCurrentStepLabel = () => {
        const step = steps.find(s => s.id === currentStep.value)
        return step ? step.label : 'Sourcing'
    }

    const loadSavedState = () => {
        const saved = localStorage.getItem(`recruitment_wizard_${props.requestId}`)
        if (saved) {
            try {
                const state = JSON.parse(saved)
                currentStep.value = state.currentStep
                selectedCandidates.value = state.selectedCandidates
                hiredCandidates.value = state.hiredCandidates
                pendingAccessRequests.value = state.pendingAccessRequests
                
                // Restore steps completion status
                state.stepsCompleted.forEach((completedStep, index) => {
                    if (index < steps.length) {
                        steps[index].completed = completedStep.completed
                    }
                })
            } catch (e) {
                console.warn('Failed to load saved wizard state:', e)
            }
        }
    }

    onMounted(() => {
        // Load previously saved wizard state if available
        loadSavedState()
    })
</script>
