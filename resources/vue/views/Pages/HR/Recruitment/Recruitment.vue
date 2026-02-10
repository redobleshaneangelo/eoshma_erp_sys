<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">
            <!-- Tabs Navigation -->
            <div class="bg-white border-b border-gray-200 rounded-t-lg mb-0">
                <nav class="flex gap-0 px-6 py-0 overflow-x-auto">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        class="flex items-center gap-2 px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap"
                        :class="activeTab === tab.id
                            ? 'border-[#0c8ce9] text-[#0c8ce9]'
                            : 'border-transparent text-gray-600 hover:text-gray-900'"
                    >
                        <span v-html="tab.icon"></span>
                        {{ tab.label }}
                        <span v-if="tab.count" class="bg-gray-200 text-gray-700 px-2 py-0 rounded-full text-xs font-semibold">
                            {{ tab.count }}
                        </span>
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="bg-white rounded-b-lg">
                <!-- Recruitment Requests Tab -->
                <div v-show="activeTab === 'recruitment'" class="p-8">
                    <div class="space-y-6">
                        <!-- Table -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="w-full">
                                <thead class="bg-gray-100 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Request ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Position</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Department</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Requested By</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Headcount</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="request in manpowerRequests" :key="request.id" class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ request.requestNumber }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ request.position }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ request.department }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ request.requestedBy }}</td>
                                        <td class="px-6 py-4 text-sm text-center font-semibold text-gray-900">{{ request.headcount }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <span :class="[
                                                'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                                                request.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                                                request.status === 'Ongoing' ? 'bg-blue-100 text-blue-800' :
                                                'bg-green-100 text-green-800'
                                            ]">
                                                {{ request.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button 
                                                @click="viewRequestDetails(request.id)"
                                                class="inline-flex items-center gap-1 px-3 py-1 text-sm text-[#0c8ce9] hover:bg-blue-50 rounded transition-colors"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="recruitmentRequests.length === 0" class="text-center py-12 text-gray-500">
                                <svg class="block w-12 h-12 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>No recruitment requests at the moment</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manpower Requests Tab -->
                <div v-show="activeTab === 'manpower'" class="p-8">
                    <div class="space-y-6">
                        <!-- Table -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="w-full">
                                <thead class="bg-gray-100 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Request ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Position</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Project / Site</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Headcount</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Requested By</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="request in recruitmentRequests" :key="request.id" class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ request.requestNumber }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ request.position }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ request.projectSite }}</td>
                                        <td class="px-6 py-4 text-sm text-center font-semibold text-gray-900">{{ request.headcount }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ request.requestedBy }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <span :class="[
                                                'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                                                request.recruitmentStatus === 'Pending'
                                                    ? 'bg-yellow-100 text-yellow-800'
                                                    : request.recruitmentStatus === 'Ongoing'
                                                        ? 'bg-blue-100 text-blue-800'
                                                        : 'bg-green-100 text-green-800'
                                            ]">
                                                {{ request.recruitmentStatus }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button
                                                @click="viewRequestDetails(request.id)"
                                                class="inline-flex items-center gap-1 px-3 py-1 text-sm text-[#0c8ce9] hover:bg-blue-50 rounded transition-colors"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="manpowerRequests.length === 0" class="text-center py-12 text-gray-500">
                                <svg class="block w-12 h-12 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>No manpower requests</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="fixed bottom-0 right-0 p-2 z-50">
                <img :src="agnes" class="h-auto w-auto" v-if="loading" />
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed, onMounted } from 'vue'
    import { useRouter } from 'vue-router'
    import axios from 'axios'
    import Swal from 'sweetalert2'
    import { useAuthStore } from '@/stores/auth'
    import agnes from '@/assets/agnes.gif'

    const router = useRouter()
    const auth = useAuthStore()

    const loading = ref(false)
    const activeTab = ref('recruitment')
    const showRecruitmentWizard = ref(false)
    const selectedRequest = ref(null)

    const recruitmentRequests = ref([
        {
            id: 1,
            requestNumber: 'MR-001',
            position: 'Senior Software Engineer',
            projectSite: 'Metro Manila Project',
            headcount: 2,
            requestedBy: 'John Doe',
            recruitmentStatus: 'Pending'
        },
        {
            id: 2,
            requestNumber: 'MR-002',
            position: 'Project Manager',
            projectSite: 'Cebu Construction Site',
            headcount: 1,
            requestedBy: 'Jane Smith',
            recruitmentStatus: 'Ongoing'
        },
        {
            id: 3,
            requestNumber: 'MR-003',
            position: 'Civil Engineer',
            projectSite: 'Davao Infrastructure',
            headcount: 3,
            requestedBy: 'Robert Johnson',
            recruitmentStatus: 'Ongoing'
        },
        {
            id: 4,
            requestNumber: 'MR-004',
            position: 'Safety Officer',
            projectSite: 'Metro Manila Project',
            headcount: 1,
            requestedBy: 'Maria Garcia',
            recruitmentStatus: 'Completed'
        },
        {
            id: 5,
            requestNumber: 'MR-005',
            position: 'Equipment Operator',
            projectSite: 'Iloilo Development',
            headcount: 4,
            requestedBy: 'Peter Chen',
            recruitmentStatus: 'Pending'
        }
    ])
    
    const manpowerRequests = ref([
        {
            id: 1,
            requestNumber: 'RR-001',
            position: 'Operations Manager',
            department: 'Operations',
            requestedBy: 'Michael Brown',
            headcount: 1,
            status: 'Pending'
        },
        {
            id: 2,
            requestNumber: 'RR-002',
            position: 'HR Specialist',
            department: 'Human Resources',
            requestedBy: 'Sarah Wilson',
            headcount: 2,
            status: 'Pending'
        },
        {
            id: 3,
            requestNumber: 'RR-003',
            position: 'Finance Officer',
            department: 'Finance',
            requestedBy: 'David Lee',
            headcount: 1,
            status: 'Ongoing'
        },
        {
            id: 4,
            requestNumber: 'RR-004',
            position: 'IT Support',
            department: 'Information Technology',
            requestedBy: 'Emily Davis',
            headcount: 2,
            status: 'Ongoing'
        },
        {
            id: 5,
            requestNumber: 'RR-005',
            position: 'Procurement Officer',
            department: 'Procurement',
            requestedBy: 'Jessica Martinez',
            headcount: 1,
            status: 'Completed'
        }
    ])

    const tabs = computed(() => [
        { 
            id: 'recruitment', 
            label: 'Employee Requests', 
            count: recruitmentRequests.value.length,
            icon: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>'
        },
        { 
            id: 'manpower', 
            label: 'Manpower Requests', 
            count: manpowerRequests.value.length,
            icon: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>'
        }
    ])

    const openRecruitmentWizard = (request) => {
        selectedRequest.value = request
        showRecruitmentWizard.value = true
    }

    const handleCompleteRecruitment = async (data) => {
        loading.value = true
        try {
            // API: POST /api/recruitment/complete
            // Payload: { mobilization_request_id, candidates_hired, recruitment_notes }
            // const response = await axios.post(
            //     '/api/recruitment/complete',
            //     {
            //         mobilization_request_id: data.requestId,
            //         candidates_hired: data.candidates.length,
            //         recruitment_notes: data.notes
            //     },
            //     { headers: { Authorization: `Bearer ${auth.token}` } }
            // )
            // const result = response.data
            
            // Remove from recruitment requests and add to manpower requests
            const index = recruitmentRequests.value.findIndex(r => r.id === data.requestId)
            if (index > -1) {
                recruitmentRequests.value.splice(index, 1)
            }

            // manpowerRequests.value.push(result.data)
            
            showRecruitmentWizard.value = false
            Swal.fire('Success', 'Recruitment completed! Request moved to Mobilization.', 'success')
            
        } catch (error) {
            console.error(error)
            Swal.fire('Error', 'Failed to complete recruitment', 'error')
        } finally {
            loading.value = false
        }
    }

    const viewRequestDetails = (requestId) => {
        Swal.fire({
            title: 'Request Details',
            text: `Request ID: ${requestId}`,
            icon: 'info'
        })
    }

    const loadData = async () => {
        loading.value = true
        try {
            // API: GET /api/recruitment/requests
            // const recruitRes = await axios.get(
            //     '/api/recruitment/requests',
            //     { headers: { Authorization: `Bearer ${auth.token}` } }
            // )
            // recruitmentRequests.value = recruitRes.data.data || recruitRes.data

            // API: GET /api/recruitment/manpower-requests
            // const manpowerRes = await axios.get(
            //     '/api/recruitment/manpower-requests',
            //     { headers: { Authorization: `Bearer ${auth.token}` } }
            // )
            // manpowerRequests.value = manpowerRes.data.data || manpowerRes.data
        } catch (error) {
            console.error('Error loading recruitment data:', error)
            Swal.fire('Error', 'Failed to load recruitment data', 'error')
        } finally {
            loading.value = false
        }
    }

    onMounted(() => {
        auth.pageTitle = 'Recruitment'
        loadData()
    })
</script>

