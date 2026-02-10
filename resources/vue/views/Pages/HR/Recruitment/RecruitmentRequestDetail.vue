<template>
    <auth-layout>
        <div class="ps-60 bg-[#F3F4F6] min-h-screen">
            <div class="px-10 py-10">
                <!-- Header with Back Button -->
                <div class="mb-8 flex items-center gap-4">
                    <button 
                        @click="goBack"
                        class="px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back
                    </button>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Main Details -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Request Overview Card -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">{{ request.position }}</h2>
                                    <p class="text-gray-600 mt-1">{{ request.department }}</p>
                                </div>
                                <span :class="[
                                    'inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold',
                                    request.status === 'Open' ? 'bg-blue-100 text-blue-800' :
                                    request.status === 'In Recruitment' ? 'bg-yellow-100 text-yellow-800' :
                                    request.status === 'Closed' ? 'bg-green-100 text-green-800' :
                                    'bg-red-100 text-red-800'
                                ]">
                                    {{ request.status }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-6 border-t border-gray-200">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase">Headcount</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ request.headcount }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase">Requested By</p>
                                    <p class="text-sm font-medium text-gray-900 mt-2">{{ request.requestedBy }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase">Date Created</p>
                                    <p class="text-sm font-medium text-gray-900 mt-2">{{ formatDate(request.dateCreated) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase">Priority</p>
                                    <p class="text-sm font-medium text-gray-900 mt-2">{{ request.priority || 'Medium' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Job Description -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Job Description</h3>
                            <p class="text-gray-700 leading-relaxed">
                                {{ request.jobDescription || 'We are looking for a talented professional to join our team. This position offers an excellent opportunity to grow your career with us.' }}
                            </p>
                        </div>

                        <!-- Requirements -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Requirements</h3>
                            <ul class="space-y-3">
                                <li v-for="(req, index) in (request.requirements || defaultRequirements)" :key="index" class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-green-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700">{{ req }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Responsibilities -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Key Responsibilities</h3>
                            <ul class="space-y-3">
                                <li v-for="(resp, index) in (request.responsibilities || defaultResponsibilities)" :key="index" class="flex items-start gap-3">
                                    <span class="text-[#0c8ce9] font-semibold mt-1">•</span>
                                    <span class="text-gray-700">{{ resp }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Compensation -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Compensation & Benefits</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 uppercase mb-2">Salary Range</p>
                                    <p class="text-xl font-bold text-gray-900">{{ request.salaryRange || '₱60,000 - ₱100,000/month' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 uppercase mb-2">Benefits</p>
                                    <div class="space-y-2">
                                        <p class="text-sm text-gray-700">✓ Health Insurance</p>
                                        <p class="text-sm text-gray-700">✓ 13th Month Pay</p>
                                        <p class="text-sm text-gray-700">✓ Performance Bonus</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Actions & Quick Info -->
                    <div class="space-y-6">
                        <!-- Status Actions -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                            <div class="space-y-3">
                                <button 
                                    v-if="request.status === 'Open'"
                                    @click="startRecruitment"
                                    class="w-full px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors flex items-center justify-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Start Recruitment
                                </button>
                                <button 
                                    @click="editRequest"
                                    class="w-full px-4 py-2 bg-gray-100 text-gray-900 rounded-lg font-semibold hover:bg-gray-200 transition-colors flex items-center justify-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit Request
                                </button>
                                <button 
                                    v-if="request.status !== 'Closed'"
                                    @click="closeRequest"
                                    class="w-full px-4 py-2 bg-red-50 text-red-600 rounded-lg font-semibold hover:bg-red-100 transition-colors flex items-center justify-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Close Request
                                </button>
                            </div>
                        </div>

                        <!-- Candidates Card -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Candidates</h3>
                            <div class="flex items-center justify-between mb-4">
                                <p class="text-3xl font-bold text-[#0c8ce9]">{{ candidateCount }}</p>
                                <p class="text-sm text-gray-600">applications</p>
                            </div>
                            <button 
                                @click="viewCandidates"
                                class="w-full px-4 py-2 border border-[#0c8ce9] text-[#0c8ce9] rounded-lg font-semibold hover:bg-blue-50 transition-colors"
                            >
                                View Candidates
                            </button>
                        </div>

                        <!-- Request Info -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Info</h3>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase">Department</p>
                                    <p class="text-sm font-medium text-gray-900 mt-1">{{ request.department }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase">Budget Approved</p>
                                    <p class="text-sm font-medium text-green-600 mt-1">Yes</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase">Target Hire Date</p>
                                    <p class="text-sm font-medium text-gray-900 mt-1">{{ request.targetHireDate || formatDate(request.dateCreated) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>
                            <div class="space-y-4">
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                        <div class="w-0.5 h-12 bg-gray-300 my-2"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Request Created</p>
                                        <p class="text-xs text-gray-500">{{ formatDate(request.dateCreated) }}</p>
                                    </div>
                                </div>
                                <div v-if="request.status !== 'Open'" class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                        <div class="w-0.5 h-12 bg-gray-300 my-2"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Recruitment Started</p>
                                        <p class="text-xs text-gray-500">{{ request.recruitmentStartDate ? formatDate(request.recruitmentStartDate) : 'In Progress' }}</p>
                                    </div>
                                </div>
                                <div v-if="request.status === 'Closed'" class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-3 h-3 bg-gray-500 rounded-full"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Request Closed</p>
                                        <p class="text-xs text-gray-500">{{ request.closedDate ? formatDate(request.closedDate) : 'Pending' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </auth-layout>

    <ApplicantsModal 
        :isOpen="showApplicantsModal"
        :position="request.position"
        @close="closeApplicantsModal"
    />
</template>

<script setup>
    import { ref, onMounted, computed } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import Swal from 'sweetalert2'
    import ApplicantsModal from '@/components/HR/Recruitment/ApplicantsModal.vue'

    const router = useRouter()
    const route = useRoute()

    const defaultRequirements = [
        '3+ years of relevant experience',
        'Bachelor\'s degree in related field',
        'Strong communication skills',
        'Team player mentality',
        'Proficiency in relevant tools/technologies'
    ]

    const defaultResponsibilities = [
        'Perform core job responsibilities and deliver quality outputs',
        'Collaborate with cross-functional teams',
        'Contribute to process improvements',
        'Maintain professional development',
        'Ensure compliance with company policies'
    ]

    const requestId = computed(() => route.params.requestId)
    
    const request = ref({
        id: '',
        position: '',
        department: '',
        requestedBy: '',
        headcount: 0,
        status: '',
        dateCreated: '',
        priority: 'Medium',
        jobDescription: '',
        requirements: [],
        responsibilities: [],
        salaryRange: '',
        targetHireDate: '',
        closedDate: ''
    })

    const candidateCount = ref(12)
    const showApplicantsModal = ref(false)

    const formatDate = (date) => {
        return new Date(date).toLocaleDateString('en-PH', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        })
    }

    const goBack = () => {
        router.back()
    }

    const startRecruitment = () => {
        Swal.fire({
            title: 'Start Recruitment?',
            text: `Start recruitment process for ${request.value.position}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0c8ce9',
            confirmButtonText: 'Yes, Start',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                request.value.status = 'In Recruitment'
                request.value.recruitmentStartDate = new Date().toISOString().split('T')[0]
                Swal.fire({
                    icon: 'success',
                    title: 'Recruitment Started',
                    text: 'The recruitment process has been initiated.'
                })
            }
        })
    }

    const editRequest = () => {
        Swal.fire({
            icon: 'info',
            title: 'Edit Request',
            text: 'Redirect to edit request page'
        })
    }

    const closeRequest = () => {
        Swal.fire({
            title: 'Close Request?',
            text: `Are you sure you want to close this request?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Yes, Close',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                request.value.status = 'Closed'
                request.value.closedDate = new Date().toISOString().split('T')[0]
                Swal.fire({
                    icon: 'success',
                    title: 'Request Closed',
                    text: 'The request has been closed.'
                })
            }
        })
    }

    const viewCandidates = () => {
        showApplicantsModal.value = true
    }

    const closeApplicantsModal = () => {
        showApplicantsModal.value = false
    }

    onMounted(() => {
        // In a real app, fetch request details from API
        // For now, load from route params or create a default request
        request.value = {
            id: requestId.value || 'REQ-001',
            position: 'Senior Engineer',
            department: 'Engineering',
            requestedBy: 'John Manager',
            headcount: 2,
            status: 'Open',
            dateCreated: '2026-01-20',
            priority: 'High',
            jobDescription: 'We are seeking an experienced Senior Engineer to lead our technical initiatives. You will work with a talented team to develop and maintain our core platforms.',
            salaryRange: '₱80,000 - ₱120,000/month',
            targetHireDate: '2026-03-15'
        }
    })
</script>
