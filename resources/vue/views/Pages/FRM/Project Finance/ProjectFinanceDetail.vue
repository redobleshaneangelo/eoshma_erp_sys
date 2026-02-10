<template>
    <auth-layout />
        <div class="ps-60 pe-4 bg-[#F3F4F6] min-h-screen">
            <div class="px-8 py-4">
                <!-- Page Header -->
                <div class="mb-8">
                    <router-link to="/project-finance" class="text-[#0c8ce9] text-sm font-semibold hover:underline mb-4 inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Projects
                    </router-link>
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ projectData.name }}</h1>
                            <p class="text-gray-600 text-sm mt-1">{{ projectData.code }} • {{ projectData.client }}</p>
                        </div>
                        <div class="flex gap-3">
                            <button
                                @click="editProject"
                                class="px-6 py-2.5 bg-white text-gray-700 rounded-lg font-semibold text-sm border border-gray-300 hover:bg-gray-50 transition-colors flex items-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </button>
                            <button
                                @click="exportData"
                                class="px-6 py-2.5 bg-white text-gray-700 rounded-lg font-semibold text-sm border border-gray-300 hover:bg-gray-50 transition-colors flex items-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Export
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="mb-8">
                    <nav class="flex gap-10 border-b border-gray-300">
                        <button
                            v-for="tab in tabs"
                            :key="tab.key"
                            @click="activeTab = tab.key"
                            class="py-3 px-1 text-sm font-semibold border-b-2 transition-colors"
                            :class="activeTab === tab.key
                                ? 'border-[#0c8ce9] text-[#0c8ce9]'
                                : 'border-transparent text-gray-600 hover:text-gray-900'"
                        >
                            {{ tab.label }}
                        </button>
                    </nav>
                </div>

                <!-- Overview Tab -->
                <div v-if="activeTab === 'overview'" class="space-y-8">
                    <!-- Key Metrics -->
                    <div class="grid grid-cols-4 gap-6">
                        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                            <p class="text-gray-600 text-sm font-medium">Contract Amount</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">{{ formatCurrency(projectData.contractAmount) }}</p>
                            <p class="text-gray-500 text-xs mt-1">Total value</p>
                        </div>
                        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                            <p class="text-gray-600 text-sm font-medium">Utilized Budget</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">{{ formatCurrency(projectData.utilized) }}</p>
                            <p class="text-gray-500 text-xs mt-1">{{ projectData.utilizationPercent }}% spent</p>
                        </div>
                        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                            <p class="text-gray-600 text-sm font-medium">Remaining Budget</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">{{ formatCurrency(projectData.remaining) }}</p>
                            <p class="text-gray-500 text-xs mt-1">{{ projectData.remainingPercent }}% available</p>
                        </div>
                        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                            <p class="text-gray-600 text-sm font-medium">Retention Held</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">{{ formatCurrency(projectData.retention) }}</p>
                            <p class="text-gray-500 text-xs mt-1">{{ projectData.retentionPercent }}%</p>
                        </div>
                    </div>

                    <!-- Project Information -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Project Information</h3>
                        </div>
                        <div class="px-6 py-6 grid grid-cols-3 gap-8">
                            <div>
                                <p class="text-xs font-medium text-gray-700 uppercase tracking-wide mb-1">Project Code</p>
                                <p class="text-sm font-semibold text-gray-900">{{ projectData.code }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-700 uppercase tracking-wide mb-1">Bid Type</p>
                                <p class="text-sm font-semibold text-gray-900">{{ projectData.bidType }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-700 uppercase tracking-wide mb-1">Billing Type</p>
                                <p class="text-sm font-semibold text-gray-900">{{ projectData.billingType }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-700 uppercase tracking-wide mb-1">Bid Deadline</p>
                                <p class="text-sm font-semibold text-gray-900">{{ projectData.bidDeadline }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-700 uppercase tracking-wide mb-1">Bid Opening Date</p>
                                <p class="text-sm font-semibold text-gray-900">{{ projectData.bidOpening }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-700 uppercase tracking-wide mb-1">Bond Type</p>
                                <p class="text-sm font-semibold text-gray-900">{{ projectData.bondType }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-700 uppercase tracking-wide mb-1">Security Amount</p>
                                <p class="text-sm font-semibold text-gray-900">{{ formatCurrency(projectData.securityAmount) }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-700 uppercase tracking-wide mb-1">Status</p>
                                <span :class="getStatusBadgeClass(projectData.status)" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ projectData.status }}
                                </span>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-700 uppercase tracking-wide mb-1">Client</p>
                                <p class="text-sm font-semibold text-gray-900">{{ projectData.client }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Budget Breakdown Chart -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Budget Status</h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm font-medium text-gray-700">Utilization Progress</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ projectData.utilizationPercent }}%</p>
                                </div>
                                <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-linear-to-r from-green-500 to-yellow-500" :style="{ width: projectData.utilizationPercent + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bidding & Documents Tab -->
                <div v-if="activeTab === 'bidding'" class="space-y-8">
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Bidding Activities -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Bidding Activities</h3>
                                <button 
                                    @click="showAddActivityModal = true"
                                    class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700 transition-colors"
                                >
                                    Add Activity
                                </button>
                            </div>
                            <div class="divide-y divide-gray-200">
                                <div v-for="activity in projectData.biddingActivities" :key="activity.id" class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ activity.name }}</p>
                                            <p class="text-xs text-gray-600 mt-1">{{ activity.date }}</p>
                                        </div>
                                        <button 
                                            @click="deleteActivity(activity.id)"
                                            class="text-red-500 hover:text-red-700 text-xs font-semibold transition-colors"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bidding Documents -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Bidding Documents</h3>
                                <button 
                                    @click="showAddDocumentModal = true"
                                    class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700 transition-colors"
                                >
                                    Add Document
                                </button>
                            </div>
                            <div class="divide-y divide-gray-200">
                                <div v-for="doc in projectData.biddingDocuments" :key="doc.id" class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ doc.name }}</p>
                                                    <p class="text-xs text-gray-600 mt-0.5">Uploaded {{ doc.uploadedAt }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <span v-if="doc.completed" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                Completed
                                            </span>
                                            <button 
                                                @click="downloadDocument(doc.id)"
                                                class="text-[#0c8ce9] hover:text-blue-700 text-xs font-semibold transition-colors"
                                            >
                                                Download
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resource Planning Tab -->
                <div v-if="activeTab === 'resources'" class="space-y-8">
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Material Planning -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Material Planning</h3>
                                <button 
                                    @click="showAddMaterialModal = true"
                                    class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700 transition-colors"
                                >
                                    Add Material
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700">Item</th>
                                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700">Qty</th>
                                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700">Unit Cost</th>
                                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700">Total</th>
                                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="material in projectData.materials" :key="material.id" class="hover:bg-gray-50">
                                            <td class="px-4 py-2 text-gray-900 font-medium">{{ material.name }}</td>
                                            <td class="px-4 py-2 text-right text-gray-600">{{ material.quantity }}</td>
                                            <td class="px-4 py-2 text-right text-gray-600">{{ formatCurrency(material.unitCost) }}</td>
                                            <td class="px-4 py-2 text-right font-semibold text-gray-900">{{ formatCurrency(material.total) }}</td>
                                            <td class="px-4 py-2 text-center">
                                                <button 
                                                    @click="deleteMaterial(material.id)"
                                                    class="text-red-500 hover:text-red-700 text-xs transition-colors"
                                                >
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Equipment Planning -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Equipment Planning</h3>
                                <button 
                                    @click="showAddEquipmentModal = true"
                                    class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700 transition-colors"
                                >
                                    Add Equipment
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700">Equipment</th>
                                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700">Qty</th>
                                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700">Unit Cost</th>
                                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700">Total</th>
                                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="equipment in projectData.equipment" :key="equipment.id" class="hover:bg-gray-50">
                                            <td class="px-4 py-2 text-gray-900 font-medium">{{ equipment.name }}</td>
                                            <td class="px-4 py-2 text-right text-gray-600">{{ equipment.quantity }}</td>
                                            <td class="px-4 py-2 text-right text-gray-600">{{ formatCurrency(equipment.unitCost) }}</td>
                                            <td class="px-4 py-2 text-right font-semibold text-gray-900">{{ formatCurrency(equipment.total) }}</td>
                                            <td class="px-4 py-2 text-center">
                                                <button 
                                                    @click="deleteEquipment(equipment.id)"
                                                    class="text-red-500 hover:text-red-700 text-xs transition-colors"
                                                >
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approvals Tab -->
                <div v-if="activeTab === 'approvals'" class="space-y-8">
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Approval History</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <div v-for="approval in projectData.approvals" :key="approval.id" class="px-6 py-4">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm font-medium text-gray-900">{{ approval.title }}</p>
                                    <span :class="getApprovalStatusClass(approval.status)" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold">
                                        {{ approval.status }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-600">{{ approval.approver }} • {{ approval.date }}</p>
                                <p v-if="approval.notes" class="text-xs text-gray-500 mt-2">{{ approval.notes }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
</template>

<script setup>
import { ref } from 'vue'

const activeTab = ref('overview')
const showAddActivityModal = ref(false)
const showAddDocumentModal = ref(false)
const showAddMaterialModal = ref(false)
const showAddEquipmentModal = ref(false)

const tabs = [
    { key: 'overview', label: 'Overview' },
    { key: 'bidding', label: 'Bidding & Documents' },
    { key: 'resources', label: 'Resource Planning' },
    { key: 'approvals', label: 'Approvals' }
]

const projectData = ref({
    id: 1,
    code: 'MTD-001',
    name: 'Metro Tower Development',
    client: 'Urban Development Corp',
    status: 'Active',
    contractAmount: 850000,
    utilized: 697500,
    utilizationPercent: 82,
    remaining: 152500,
    remainingPercent: 18,
    retention: 42500,
    retentionPercent: 5,
    bidType: 'Open Bidding',
    billingType: 'Monthly Progress Billing',
    bidDeadline: 'Jan 15, 2025',
    bidOpening: 'Jan 20, 2025',
    bondType: 'Performance Bond',
    securityAmount: 85000,
    biddingActivities: [
        { id: 1, name: 'Bid Notice Issued', date: 'Jan 5, 2025' },
        { id: 2, name: 'Pre-bid Conference', date: 'Jan 10, 2025' },
        { id: 3, name: 'Bid Opening', date: 'Jan 20, 2025' }
    ],
    biddingDocuments: [
        { id: 1, name: 'Bill of Quantities.pdf', uploadedAt: '2025-01-05', completed: true },
        { id: 2, name: 'Technical Specifications.pdf', uploadedAt: '2025-01-05', completed: true },
        { id: 3, name: 'Construction Plans.pdf', uploadedAt: '2025-01-06', completed: false }
    ],
    materials: [
        { id: 1, name: 'Concrete (Cubic Meter)', quantity: 500, unitCost: 8000, total: 4000000 },
        { id: 2, name: 'Rebar (Ton)', quantity: 100, unitCost: 45000, total: 4500000 },
        { id: 3, name: 'Steel Beams (Ton)', quantity: 80, unitCost: 60000, total: 4800000 }
    ],
    equipment: [
        { id: 1, name: 'Crane (Monthly Rental)', quantity: 1, unitCost: 150000, total: 150000 },
        { id: 2, name: 'Scaffolding (Monthly Rental)', quantity: 5000, unitCost: 100, total: 500000 },
        { id: 3, name: 'Concrete Mixer (Monthly Rental)', quantity: 10, unitCost: 25000, total: 250000 }
    ],
    approvals: [
        { id: 1, title: 'Initial Budget Approval', status: 'Approved', approver: 'Finance Manager', date: 'Jan 2, 2025', notes: '' },
        { id: 2, title: 'Material Purchase Order', status: 'Pending', approver: 'Project Manager', date: 'Jan 15, 2025', notes: 'Awaiting PM review' },
        { id: 3, title: 'Equipment Rental Budget', status: 'Approved', approver: 'Director', date: 'Jan 8, 2025', notes: '' }
    ]
})

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value)
}

const getStatusBadgeClass = (status) => {
    const classes = {
        'Active': 'bg-green-100 text-green-800',
        'Planning': 'bg-blue-100 text-blue-800',
        'Completed': 'bg-gray-100 text-gray-800',
        'On Hold': 'bg-yellow-100 text-yellow-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const getApprovalStatusClass = (status) => {
    const classes = {
        'Approved': 'bg-green-100 text-green-800',
        'Pending': 'bg-yellow-100 text-yellow-800',
        'Rejected': 'bg-red-100 text-red-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const editProject = () => {
    // Navigate to edit page
}

const exportData = () => {
    // Export project data
}

const deleteActivity = (id) => {
    // Delete activity
}

const downloadDocument = (id) => {
    // Download document
}

const deleteMaterial = (id) => {
    // Delete material
}

const deleteEquipment = (id) => {
    // Delete equipment
}
</script>

<style scoped></style>
