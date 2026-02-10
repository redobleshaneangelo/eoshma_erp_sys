<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">
            <!-- Back Button & Header -->
            <div class="mb-8">
                <router-link 
                    to="/material-requests"
                    class="inline-flex items-center gap-2 text-[#0c8ce9] font-semibold hover:underline mb-4"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Material Requests
                </router-link>

                <div v-if="request" class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ request.materialName }}</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ request.projectCode }} • {{ request.projectName }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span :class="statusStyles[request.status]" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold">
                            {{ formatStatus(request.status) }}
                        </span>
                        <span :class="priorityStyles[request.priority]" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold">
                            {{ request.priority.toUpperCase() }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div v-if="request" class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200 bg-gray-50">
                    <nav class="flex gap-0 px-6">
                        <button
                            v-for="tab in tabs"
                            :key="tab.key"
                            @click="activeTab = tab.key"
                            class="py-4 px-4 border-b-2 font-semibold text-sm transition-colors"
                            :class="activeTab === tab.key
                                ? 'border-[#0c8ce9] text-[#0c8ce9]'
                                : 'border-transparent text-gray-600 hover:text-gray-900'"
                        >
                            {{ tab.label }}
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-8">
                    <!-- Overview Tab -->
                    <div v-if="activeTab === 'overview'" class="space-y-6">
                        <!-- Request & Status Information -->
                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Request Information</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Request ID</span>
                                        <span class="text-sm font-semibold text-gray-900">#{{ request.id }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Status</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ formatStatus(request.status) }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Priority</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ request.priority }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Requested Date</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ formatDate(request.requestedDate) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Material Information</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Material Name</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ request.materialName }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Unit</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ request.unit }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Requested Quantity</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ request.requestedQuantity }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Source</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ request.source }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Project Information -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Project Information</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between border-b border-gray-200 pb-2">
                                    <span class="text-sm text-gray-600">Project Name</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ request.projectName }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 pb-2">
                                    <span class="text-sm text-gray-600">Project Code</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ request.projectCode }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Tab -->
                    <div v-if="activeTab === 'delivery'" class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Delivery Details</h3>
                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <label class="text-xs font-semibold text-gray-700 uppercase">Required Delivery Date</label>
                                <p :class="[
                                    'text-base font-semibold mt-1',
                                    isDeliveryUrgent(request.deliveryDate) ? 'text-red-600' : 'text-gray-900'
                                ]">
                                    {{ formatDate(request.deliveryDate) }}
                                </p>
                                <p v-if="isDeliveryUrgent(request.deliveryDate)" class="text-xs text-red-600 mt-2">
                                    ⚠️ Urgent delivery (within 7 days)
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-700 uppercase">Delivery Location</label>
                                <p class="text-base font-semibold text-gray-900 mt-1">{{ request.deliveryLocation }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions Tab -->
                    <div v-if="activeTab === 'instructions'" class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Special Instructions</h3>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                            <p class="text-base text-gray-700 leading-relaxed">
                                {{ request.instructions || 'No special instructions provided' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-lg border border-gray-200 shadow-sm p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10" />
                </svg>
                <p class="text-lg font-semibold text-gray-900 mb-2">Request Not Found</p>
                <p class="text-sm text-gray-600 mb-4">The material request you're looking for doesn't exist.</p>
                <button
                    @click="goBack"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Go Back
                </button>
            </div>

            <!-- Action Buttons -->
            <div v-if="request" class="mt-8 flex justify-end gap-3">
                <button 
                    @click="sendToProcurement"
                    class="px-6 py-3 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-all"
                >
                    Send to Procurement
                </button>

                <button 
                    v-if="request.status === 'pending'"
                    @click="rejectRequest"
                    class="px-6 py-3 bg-white border-2 border-red-500 text-red-600 rounded-lg font-semibold hover:bg-red-50 transition-all"
                >
                    Reject Request
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import Swal from 'sweetalert2'

const router = useRouter()
const route = useRoute()

const request = ref(null)
const activeTab = ref('overview')

const tabs = [
    { key: 'overview', label: 'Overview' },
    { key: 'delivery', label: 'Delivery' },
    { key: 'instructions', label: 'Instructions' }
]

// Priority Styles
const priorityStyles = {
    'Urgent': 'bg-red-100 text-red-800 border border-red-300',
    'High': 'bg-orange-100 text-orange-800 border border-orange-300',
    'Medium': 'bg-yellow-100 text-yellow-800 border border-yellow-300',
    'Low': 'bg-blue-100 text-blue-800 border border-blue-300'
}

// Status Styles
const statusStyles = {
    'pending': 'bg-yellow-100 text-yellow-800 border border-yellow-300',
    'processing': 'bg-blue-100 text-blue-800 border border-blue-300',
    'completed': 'bg-green-100 text-green-800 border border-green-300',
    'rejected': 'bg-red-100 text-red-800 border border-red-300'
}

// Mock Data - Material Requests (same as parent)
const allMaterialRequests = [
    {
        id: 1,
        materialName: 'Cement',
        unit: 'Bags',
        requestedQuantity: 500,
        source: 'Local Supplier',
        deliveryDate: '2026-02-15',
        priority: 'Urgent',
        deliveryLocation: 'Site Office',
        projectName: 'Office Building Renovation',
        projectCode: 'PRJ-2026-001',
        requestedDate: '2026-02-05',
        instructions: 'Please ensure delivery before 10 AM. Quality certification required.',
        status: 'pending'
    },
    {
        id: 2,
        materialName: 'Steel Bars',
        unit: 'Pcs',
        requestedQuantity: 200,
        source: 'Steel Mills Inc',
        deliveryDate: '2026-02-18',
        priority: 'High',
        deliveryLocation: 'Site Office',
        projectName: 'Infrastructure Development',
        projectCode: 'PRJ-2026-002',
        requestedDate: '2026-02-04',
        instructions: 'Grade A steel bars as per specifications. Include test certificates.',
        status: 'processing'
    },
    {
        id: 3,
        materialName: 'Sand',
        unit: 'm³',
        requestedQuantity: 100,
        source: 'Quarry A',
        deliveryDate: '2026-02-20',
        priority: 'Medium',
        deliveryLocation: 'Warehouse A',
        projectName: 'Office Building Renovation',
        projectCode: 'PRJ-2026-001',
        requestedDate: '2026-02-05',
        instructions: 'Medium grade sand. Free from clay and other impurities.',
        status: 'pending'
    },
    {
        id: 4,
        materialName: 'Gravel',
        unit: 'm³',
        requestedQuantity: 80,
        source: 'Quarry B',
        deliveryDate: '2026-02-25',
        priority: 'Low',
        deliveryLocation: 'Site Office',
        projectName: 'Warehouse Automation',
        projectCode: 'PRJ-2026-003',
        requestedDate: '2026-02-03',
        instructions: 'Standard gravel mix for foundation work.',
        status: 'completed'
    },
    {
        id: 5,
        materialName: 'Plywood Sheets',
        unit: 'Pcs',
        requestedQuantity: 150,
        source: 'Timber Supplies Co',
        deliveryDate: '2026-02-12',
        priority: 'Urgent',
        deliveryLocation: 'Site Office',
        projectName: 'Office Building Renovation',
        projectCode: 'PRJ-2026-001',
        requestedDate: '2026-02-05',
        instructions: 'Marine grade plywood. Stack properly to avoid moisture damage.',
        status: 'pending'
    }
]

// Methods
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatStatus = (status) => {
    const map = {
        'pending': 'Pending',
        'processing': 'Processing',
        'completed': 'Completed',
        'rejected': 'Rejected'
    }
    return map[status] || status
}

const isDeliveryUrgent = (deliveryDate) => {
    const today = new Date()
    const delivery = new Date(deliveryDate)
    const diffTime = delivery - today
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays <= 7
}

const goBack = () => {
    router.push('/material-requests')
}

const rejectRequest = () => {
    if (!request.value) return
    
    Swal.fire({
        title: 'Reject Request?',
        text: 'Are you sure you want to reject this request?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Reject',
        input: 'textarea',
        inputPlaceholder: 'Reason for rejection (optional)',
        inputAttributes: {
            'aria-label': 'Reason for rejection'
        }
    }).then((result) => {
        if (result.isConfirmed && request.value) {
            request.value.status = 'rejected'
            Swal.fire({
                icon: 'success',
                title: 'Rejected',
                text: `Request #${request.value.id} has been rejected.`,
                timer: 1500,
                showConfirmButton: false
            })
        }
    })
}

const sendToProcurement = () => {
    if (!request.value) return
    
    Swal.fire({
        title: 'Send to Procurement?',
        html: `
            <div class="text-left">
                <p class="text-sm text-gray-700 mb-3">Send this material request to the Procurement department?</p>
                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Material:</span>
                        <span class="font-semibold text-gray-900">${request.value.materialName}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Quantity:</span>
                        <span class="font-semibold text-gray-900">${request.value.requestedQuantity} ${request.value.unit}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Project:</span>
                        <span class="font-semibold text-gray-900">${request.value.projectCode}</span>
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0c8ce9',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Send to Procurement',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'rounded-lg',
            confirmButton: 'px-6 py-2 rounded-lg font-semibold',
            cancelButton: 'px-6 py-2 rounded-lg font-semibold'
        }
    }).then((result) => {
        if (result.isConfirmed && request.value) {
            // TODO: API call to send to procurement
            // await axios.post(`/api/scm/material-requests/${request.value.id}/send-to-procurement`)
            
            Swal.fire({
                title: 'Sent to Procurement!',
                text: `Material request #${request.value.id} has been sent to the Procurement department.`,
                icon: 'success',
                confirmButtonColor: '#0c8ce9',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'rounded-lg',
                    confirmButton: 'px-6 py-2 rounded-lg font-semibold'
                }
            })
        }
    })
}

// Load request on mount
onMounted(() => {
    const requestId = parseInt(route.params.id)
    request.value = allMaterialRequests.find(req => req.id === requestId)
})
</script>
