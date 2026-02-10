<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">
            <!-- Back Button & Header -->
            <div class="mb-8">
                <router-link
                    to="/pending-approvals"
                    class="inline-flex items-center gap-2 text-[#0c8ce9] font-semibold hover:underline mb-4"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Pending Approvals
                </router-link>

                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ project.project_name }}</h1>
                        <p class="text-sm text-gray-600 mt-1">Review {{ formatType(approvalType) }}</p>
                    </div>
                    <div class="flex gap-3">
                        <button
                            @click="rejectApproval"
                            class="px-6 py-2.5 border-2 border-red-500 text-red-600 rounded-lg font-semibold hover:bg-red-50 transition-colors"
                        >
                            Reject
                        </button>
                        <button
                            @click="approveApproval"
                            class="px-6 py-2.5 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors"
                        >
                            Approve
                        </button>
                    </div>
                </div>
            </div>

            <!-- Info Banner -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 flex items-start gap-3">
                <svg class="w-5 h-5 text-yellow-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm font-semibold text-yellow-800">Pending Review</p>
                    <p class="text-xs text-yellow-700 mt-1">This {{ formatType(approvalType).toLowerCase() }} is awaiting your approval. Please review all details before making a decision.</p>
                </div>
            </div>

            <!-- Tabs -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50">
                    <div class="flex">
                        <button
                            v-for="tab in tabs"
                            :key="tab.key"
                            @click="activeTab = tab.key"
                            :class="[
                                'px-6 py-4 text-sm font-semibold transition-colors border-b-2',
                                activeTab === tab.key
                                    ? 'text-[#0c8ce9] border-[#0c8ce9] bg-white'
                                    : 'text-gray-600 border-transparent hover:text-gray-900 hover:bg-gray-100'
                            ]"
                        >
                            {{ tab.label }}
                        </button>
                    </div>
                </div>

                <div class="p-8 min-h-125">
                    <!-- Contract Tab -->
                    <div v-if="activeTab === 'contract'" class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Contract Information</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Contract Number</label>
                                <div class="px-4 py-2.5 border border-gray-300 bg-gray-50 rounded-lg text-sm text-gray-900">
                                    {{ setupData.contract?.number || 'CTR-2026-001' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Contract Date</label>
                                <div class="px-4 py-2.5 border border-gray-300 bg-gray-50 rounded-lg text-sm text-gray-900">
                                    {{ formatDate(setupData.contract?.date || new Date().toISOString().split('T')[0]) }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Contract Amount</label>
                                <div class="px-4 py-2.5 border border-gray-300 bg-gray-50 rounded-lg text-sm text-gray-900">
                                    ₱{{ formatCurrency(setupData.contract?.amount || project.total_budget) }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Contract Period (days)</label>
                                <div class="px-4 py-2.5 border border-gray-300 bg-gray-50 rounded-lg text-sm text-gray-900">
                                    {{ calculateDuration }}
                                </div>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Notice of Award</label>
                                <div class="px-4 py-2.5 border border-gray-300 bg-gray-50 rounded-lg text-sm text-gray-900">
                                    {{ setupData.contract?.notice_of_award || 'NOA-2026-001' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Project Setup Tab -->
                    <div v-if="activeTab === 'project-setup'" class="space-y-6">
                        <!-- Required Workforce -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Required Workforce</h3>
                            </div>
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Position</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Headcount</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-if="setupData.workers.length === 0">
                                            <td colspan="2" class="px-4 py-8 text-center text-sm text-gray-500">
                                                No workers assigned yet
                                            </td>
                                        </tr>
                                        <tr v-for="worker in setupData.workers" :key="worker.id">
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ worker.position }}</td>
                                            <td class="px-4 py-3 text-center text-sm text-gray-900">{{ worker.headcount }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Materials -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Materials</h3>
                            </div>
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Material Name</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Quantity</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Unit</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="material in setupData.materials" :key="material.id">
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ material.name }}</td>
                                            <td class="px-4 py-3 text-center text-sm text-gray-900">{{ material.quantity }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ material.unit }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Equipment -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Equipment</h3>
                            </div>
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Equipment Name</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Quantity</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Type</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="equipment in setupData.equipment" :key="equipment.id">
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ equipment.name }}</td>
                                            <td class="px-4 py-3 text-center text-sm text-gray-900">{{ equipment.quantity }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ equipment.type }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Tab -->
                    <div v-if="activeTab === 'schedule'" class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Project Schedule</h3>
                        
                        <div class="grid grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                                <div class="px-4 py-2.5 border border-gray-300 bg-gray-50 rounded-lg text-sm text-gray-900">
                                    {{ formatDate(setupData.schedule.start_date) }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                                <div class="px-4 py-2.5 border border-gray-300 bg-gray-50 rounded-lg text-sm text-gray-900">
                                    {{ formatDate(setupData.schedule.end_date) }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Duration</label>
                                <div class="px-4 py-2.5 border border-gray-300 bg-gray-50 rounded-lg text-sm text-gray-900">
                                    {{ calculateDuration }} days
                                </div>
                            </div>
                        </div>

                        <!-- Gantt Chart -->
                        <div class="border border-gray-200 rounded-lg p-6 bg-white">
                            <h4 class="text-sm font-bold text-gray-900 mb-4">Project Timeline - Gantt Chart</h4>
                            <div class="overflow-x-auto">
                                <div class="min-w-max">
                                    <!-- Timeline Header -->
                                    <div class="flex gap-4 mb-4">
                                        <div class="w-40 shrink-0">
                                            <p class="text-xs font-semibold text-gray-700">Milestone</p>
                                        </div>
                                        <div class="flex-1 flex gap-2">
                                            <div v-for="month in getMonthRange()" :key="month" class="flex-1 text-center text-xs font-semibold text-gray-600">
                                                {{ month }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Gantt Bars -->
                                    <div class="space-y-3">
                                        <!-- Project Duration Bar -->
                                        <div class="flex gap-4 items-center pb-4 border-b border-gray-200">
                                            <div class="w-40 shrink-0">
                                                <p class="text-sm font-semibold text-gray-900">Project Duration</p>
                                            </div>
                                            <div class="flex-1 relative h-8 bg-gray-100 rounded" :style="{ minWidth: '500px' }">
                                                <div class="absolute h-full bg-[#0c8ce9] rounded" :style="{ left: '0%', width: '100%' }"></div>
                                                <span class="absolute inset-0 flex items-center justify-center text-xs font-semibold text-white">
                                                    {{ calculateDuration }} days
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Milestone Bars -->
                                        <div v-for="milestone in setupData.schedule.milestones" :key="milestone.id" class="flex gap-4 items-center">
                                            <div class="w-40 shrink-0">
                                                <p class="text-sm font-semibold text-gray-900">{{ milestone.name }}</p>
                                                <p class="text-xs text-gray-600">{{ formatDate(milestone.start_date) }} - {{ formatDate(milestone.end_date) }}</p>
                                            </div>
                                            <div class="flex-1 relative h-8 bg-gray-100 rounded" :style="{ minWidth: '500px' }">
                                                <div class="absolute h-full bg-green-500 rounded" :style="{ left: getMilestonePosition(milestone.start_date) + '%', width: getMilestoneBarWidth(milestone.start_date, milestone.end_date) + '%' }"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Milestones List -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-bold text-gray-900">Milestones</h4>
                            </div>
                            <div class="space-y-3">
                                <div v-if="setupData.schedule.milestones.length === 0" class="text-center py-8 border border-gray-200 rounded-lg">
                                    <p class="text-sm text-gray-600">No milestones added</p>
                                </div>
                                <div v-for="milestone in setupData.schedule.milestones" :key="milestone.id" class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-900">{{ milestone.name }}</p>
                                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-600">
                                                <span>Start: {{ formatDate(milestone.start_date) }}</span>
                                                <span>End: {{ formatDate(milestone.end_date) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Tab -->
                    <div v-if="activeTab === 'financial'" class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Financial Planning</h3>
                        
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Total Budget</label>
                                <div class="px-4 py-2.5 border border-gray-300 bg-gray-50 rounded-lg text-sm text-gray-900">
                                    ₱{{ formatCurrency(setupData.financial.total_budget) }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Retention (%)</label>
                                <div class="px-4 py-2.5 border border-gray-300 bg-gray-50 rounded-lg text-sm text-gray-900">
                                    {{ setupData.financial.retention }}%
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-sm font-bold text-gray-900 mb-4">Cost Breakdown</h4>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <span class="text-sm text-gray-600">Materials Cost</span>
                                    <span class="text-sm font-semibold text-gray-900">₱{{ formatCurrency(setupData.financial.materials_cost) }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <span class="text-sm text-gray-600">Equipment Cost</span>
                                    <span class="text-sm font-semibold text-gray-900">₱{{ formatCurrency(setupData.financial.equipment_cost) }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <span class="text-sm text-gray-600">Labor Cost</span>
                                    <span class="text-sm font-semibold text-gray-900">₱{{ formatCurrency(setupData.financial.labor_cost) }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 border border-[#0c8ce9] bg-blue-50 rounded-lg">
                                    <p class="text-sm font-bold text-gray-900">Estimated Total</p>
                                    <p class="text-lg font-bold text-[#0c8ce9]">₱{{ formatCurrency(setupData.financial.estimated_total) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Tab -->
                    <div v-if="activeTab === 'documents'" class="space-y-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Required Documents</h3>
                        </div>
                        <div v-if="setupData.documents.length === 0" class="p-6 bg-gray-50 border border-gray-200 rounded-lg text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm font-semibold text-gray-700">No documents uploaded yet</p>
                        </div>
                        <div v-else class="grid grid-cols-1 gap-6">
                            <div v-for="doc in setupData.documents" :key="doc.id" class="border border-gray-300 rounded-lg p-4">
                                <div class="flex items-start gap-3 mb-3">
                                    <svg class="w-5 h-5 text-gray-600 shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900">{{ doc.name }}</p>
                                        <p class="text-xs text-gray-600">{{ doc.status }}</p>
                                    </div>
                                    <button
                                        v-if="doc.fileUrl"
                                        @click="openDocument(doc)"
                                        class="px-3 py-1 bg-[#0c8ce9] text-white rounded text-xs font-semibold hover:bg-blue-700 transition-colors"
                                    >
                                        View
                                    </button>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm text-green-600 font-semibold">{{ doc.status }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Tab -->
                    <div v-if="activeTab === 'summary'" class="space-y-6">
                        <!-- Project Overview -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Project Summary</h3>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold uppercase mb-1">Project</p>
                                    <p class="text-base font-semibold text-gray-900">{{ project.project_name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold uppercase mb-1">Client</p>
                                    <p class="text-base font-semibold text-gray-900">{{ project.client_name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold uppercase mb-1">Total Budget</p>
                                    <p class="text-base font-semibold text-gray-900">₱{{ formatCurrency(project.total_budget) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold uppercase mb-1">Duration</p>
                                    <p class="text-base font-semibold text-gray-900">{{ calculateDuration }} days</p>
                                </div>
                            </div>
                        </div>

                        <!-- Contract Summary -->
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h4 class="text-base font-bold text-gray-900 mb-4">Contract Details</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold uppercase mb-1">Contract Number</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ setupData.contract?.number || 'CTR-2026-001' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold uppercase mb-1">Contract Date</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ formatDate(setupData.contract?.date || new Date().toISOString().split('T')[0]) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold uppercase mb-1">Contract Amount</p>
                                    <p class="text-sm font-semibold text-gray-900">₱{{ formatCurrency(setupData.contract?.amount || project.total_budget) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold uppercase mb-1">Notice of Award</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ setupData.contract?.notice_of_award || 'NOA-2026-001' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Project Setup Summary -->
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h4 class="text-base font-bold text-gray-900 mb-4">Project Setup</h4>
                            <div class="grid grid-cols-3 gap-4 mb-6">
                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                    <p class="text-2xl font-bold text-[#0c8ce9]">{{ setupData.workers.length }}</p>
                                    <p class="text-xs text-gray-600 font-semibold mt-1">Workers Assigned</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                    <p class="text-2xl font-bold text-[#0c8ce9]">{{ setupData.materials.length }}</p>
                                    <p class="text-xs text-gray-600 font-semibold mt-1">Materials</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                    <p class="text-2xl font-bold text-[#0c8ce9]">{{ setupData.equipment.length }}</p>
                                    <p class="text-xs text-gray-600 font-semibold mt-1">Equipment</p>
                                </div>
                            </div>

                            <!-- Workers Detail -->
                            <div v-if="setupData.workers.length > 0" class="mb-4">
                                <p class="text-xs font-bold text-gray-700 uppercase mb-2">Workers</p>
                                <div class="space-y-1 bg-gray-50 p-3 rounded">
                                    <div v-for="worker in setupData.workers" :key="worker.id" class="text-sm text-gray-700">
                                        {{ worker.position }} - <span class="font-semibold">{{ worker.headcount }}</span> headcount
                                    </div>
                                </div>
                            </div>

                            <!-- Materials Detail -->
                            <div v-if="setupData.materials.length > 0" class="mb-4">
                                <p class="text-xs font-bold text-gray-700 uppercase mb-2">Materials</p>
                                <div class="space-y-1 bg-gray-50 p-3 rounded">
                                    <div v-for="material in setupData.materials" :key="material.id" class="text-sm text-gray-700">
                                        {{ material.name }} - <span class="font-semibold">{{ material.quantity }}</span> {{ material.unit }}
                                    </div>
                                </div>
                            </div>

                            <!-- Equipment Detail -->
                            <div v-if="setupData.equipment.length > 0">
                                <p class="text-xs font-bold text-gray-700 uppercase mb-2">Equipment</p>
                                <div class="space-y-1 bg-gray-50 p-3 rounded">
                                    <div v-for="equipment in setupData.equipment" :key="equipment.id" class="text-sm text-gray-700">
                                        {{ equipment.name }} - <span class="font-semibold">{{ equipment.quantity }}</span> ({{ equipment.type }})
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule Summary -->
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h4 class="text-base font-bold text-gray-900 mb-4">Schedule</h4>
                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold uppercase mb-1">Start Date</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ formatDate(setupData.schedule.start_date) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold uppercase mb-1">End Date</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ formatDate(setupData.schedule.end_date) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold uppercase mb-1">Duration</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ calculateDuration }} days</p>
                                </div>
                            </div>

                            <div v-if="setupData.schedule.milestones.length > 0">
                                <p class="text-xs font-bold text-gray-700 uppercase mb-2">Milestones</p>
                                <div class="space-y-2">
                                    <div v-for="milestone in setupData.schedule.milestones" :key="milestone.id" class="bg-gray-50 p-3 rounded">
                                        <p class="text-sm font-semibold text-gray-900">{{ milestone.name }}</p>
                                        <p class="text-xs text-gray-600">{{ formatDate(milestone.start_date) }} - {{ formatDate(milestone.end_date) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Summary -->
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h4 class="text-base font-bold text-gray-900 mb-4">Financial Details</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                    <span class="text-sm text-gray-600">Materials Cost</span>
                                    <span class="font-semibold text-gray-900">₱{{ formatCurrency(setupData.financial.materials_cost) }}</span>
                                </div>
                                <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                    <span class="text-sm text-gray-600">Equipment Cost</span>
                                    <span class="font-semibold text-gray-900">₱{{ formatCurrency(setupData.financial.equipment_cost) }}</span>
                                </div>
                                <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                    <span class="text-sm text-gray-600">Labor Cost</span>
                                    <span class="font-semibold text-gray-900">₱{{ formatCurrency(setupData.financial.labor_cost) }}</span>
                                </div>
                                <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                    <span class="text-sm text-gray-600">Retention</span>
                                    <span class="font-semibold text-gray-900">{{ setupData.financial.retention }}%</span>
                                </div>
                                <div class="flex justify-between items-center pt-2">
                                    <span class="text-sm font-bold text-gray-900">Estimated Total</span>
                                    <span class="text-lg font-bold text-[#0c8ce9]">₱{{ formatCurrency(setupData.financial.estimated_total) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Documents Summary -->
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h4 class="text-base font-bold text-gray-900 mb-4">Documents</h4>
                            <div class="grid grid-cols-1 gap-2">
                                <div v-if="setupData.documents.length === 0" class="text-center py-4">
                                    <p class="text-sm text-gray-600">No documents uploaded</p>
                                </div>
                                <div v-for="doc in setupData.documents" :key="doc.id" class="flex items-center justify-between bg-gray-50 p-3 rounded">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="text-sm font-semibold text-gray-900">{{ doc.name }}</span>
                                    </div>
                                    <span :class="['text-xs font-semibold px-2 py-1 rounded', doc.status === 'Uploaded' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700']">
                                        {{ doc.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Swal from 'sweetalert2'

const route = useRoute()
const router = useRouter()

const approvalId = ref(route.params.id)
const approvalType = ref(route.query.type || 'project-setup')
const activeTab = ref('contract')

// Mock project data - would come from API
const mockProjects = {
    1: {
        id: 1,
        project_name: 'Office Building Renovation',
        project_code: 'PRJ-2026-001',
        client_name: 'ABC Corporation',
        status: 'pending-setup',
        total_budget: 2500000,
        progress: 5,
        start_date: '2026-02-15',
        end_date: '2026-08-15',
        notice_of_award_file: 'noa_project_001.pdf'
    },
    2: {
        id: 2,
        project_name: 'Infrastructure Development',
        project_code: 'PRJ-2026-002',
        client_name: 'BuildTech Solutions',
        status: 'pending',
        total_budget: 5000000,
        start_date: '2026-03-01',
        end_date: '2026-12-31',
    },
    3: {
        id: 3,
        project_name: 'Commercial Complex Construction',
        project_code: 'PRJ-2026-003',
        client_name: 'Acme Corporation',
        status: 'pending',
        total_budget: 8500000,
        start_date: '2026-04-01',
        end_date: '2027-03-31',
    }
}

const project = ref(mockProjects[approvalId.value] || mockProjects[1])

const tabs = [
    { key: 'contract', label: 'Contract' },
    { key: 'project-setup', label: 'Project Setup' },
    { key: 'schedule', label: 'Schedule' },
    { key: 'financial', label: 'Financial' },
    { key: 'documents', label: 'Documents' },
    { key: 'summary', label: 'Summary' }
]

const setupData = ref({
    contract: {
        number: 'CTR-2026-001',
        date: '2026-02-01',
        amount: 2500000,
        notice_of_award: 'NOA-2026-001'
    },
    workers: [
        { id: 1, position: 'Site Engineer', headcount: 2, status: 'Assigned' },
        { id: 2, position: 'Safety Officer', headcount: 1, status: 'Assigned' },
        { id: 3, position: 'Foreman', headcount: 3, status: 'Assigned' }
    ],
    materials: [
        { id: 1, name: 'Cement', quantity: 500, unit: 'Bags' },
        { id: 2, name: 'Steel Bars', quantity: 100, unit: 'pcs' },
        { id: 3, name: 'Concrete', quantity: 50, unit: 'm³' }
    ],
    equipment: [
        { id: 1, name: 'Excavator', quantity: 2, type: 'Heavy' },
        { id: 2, name: 'Concrete Mixer', quantity: 1, type: 'Light' },
        { id: 3, name: 'Crane', quantity: 1, type: 'Heavy' }
    ],
    schedule: {
        start_date: project.value.start_date,
        end_date: project.value.end_date,
        duration: 180,
        milestones: [
            { id: 1, name: 'Foundation Complete', start_date: '2026-03-15', end_date: '2026-03-20' },
            { id: 2, name: 'Structural Work Complete', start_date: '2026-05-15', end_date: '2026-05-25' },
            { id: 3, name: 'Final Inspection', start_date: '2026-07-01', end_date: '2026-07-15' }
        ]
    },
    financial: {
        total_budget: project.value.total_budget,
        retention: 10,
        materials_cost: 800000,
        equipment_cost: 450000,
        labor_cost: 650000,
        estimated_total: 1900000
    },
    documents: [
        { id: 1, name: 'Technical Specifications', status: 'Uploaded', fileName: 'tech_specs.pdf', fileUrl: '#' },
        { id: 2, name: 'Safety Plan', status: 'Uploaded', fileName: 'safety_plan.pdf', fileUrl: '#' }
    ]
})

const calculateDuration = computed(() => {
    const start = new Date(setupData.value.schedule.start_date)
    const end = new Date(setupData.value.schedule.end_date)
    const diffTime = end - start
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatCurrency = (value) => {
    return parseFloat(value).toLocaleString('en-PH', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

const formatType = (type) => {
    const map = {
        'prebidding': 'Pre-Bidding',
        'project-setup': 'Project Setup'
    }
    return map[type] || type
}

const getMonthRange = () => {
    const months = []
    const startDate = new Date(setupData.value.schedule.start_date)
    const endDate = new Date(setupData.value.schedule.end_date)
    
    const start = new Date(startDate.getFullYear(), startDate.getMonth(), 1)
    const end = new Date(endDate.getFullYear(), endDate.getMonth() + 1, 0)
    
    let current = new Date(start)
    while (current <= end) {
        months.push(current.toLocaleDateString('en-US', { month: 'short', year: '2-digit' }))
        current.setMonth(current.getMonth() + 1)
    }
    
    return months
}

const getMilestonePosition = (milestoneDate) => {
    const startDate = new Date(setupData.value.schedule.start_date)
    const endDate = new Date(setupData.value.schedule.end_date)
    const milestone = new Date(milestoneDate)
    
    const totalDays = (endDate - startDate) / (1000 * 60 * 60 * 24)
    const daysFromStart = (milestone - startDate) / (1000 * 60 * 60 * 24)
    
    return (daysFromStart / totalDays) * 100
}

const getMilestoneBarWidth = (startDate, endDate) => {
    const projectStart = new Date(setupData.value.schedule.start_date)
    const projectEnd = new Date(setupData.value.schedule.end_date)
    const milestoneStart = new Date(startDate)
    const milestoneEnd = new Date(endDate)
    
    const totalDays = (projectEnd - projectStart) / (1000 * 60 * 60 * 24)
    const milestoneDays = (milestoneEnd - milestoneStart) / (1000 * 60 * 60 * 24)
    
    return (milestoneDays / totalDays) * 100
}

const openDocument = (doc) => {
    if (doc.fileUrl) {
        window.open(doc.fileUrl, '_blank')
    }
}

const approveApproval = () => {
    Swal.fire({
        title: 'Approve This Request?',
        text: `Are you sure you want to approve this ${formatType(approvalType.value).toLowerCase()}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Approve',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // API call would go here
            // await approveApprovalAPI(approvalId.value)
            
            Swal.fire({
                icon: 'success',
                title: 'Approved!',
                text: 'The request has been approved successfully.',
                confirmButtonColor: '#0c8ce9'
            }).then(() => {
                router.push('/pending-approvals')
            })
        }
    })
}

const rejectApproval = () => {
    Swal.fire({
        title: 'Reject This Request?',
        text: 'Please provide a reason for rejection:',
        input: 'textarea',
        inputPlaceholder: 'Enter rejection reason...',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Reject',
        cancelButtonText: 'Cancel',
        inputValidator: (value) => {
            if (!value) {
                return 'You need to provide a reason!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // API call would go here
            // await rejectApprovalAPI(approvalId.value, result.value)
            
            Swal.fire({
                icon: 'success',
                title: 'Rejected!',
                text: 'The request has been rejected.',
                confirmButtonColor: '#0c8ce9'
            }).then(() => {
                router.push('/pending-approvals')
            })
        }
    })
}

onMounted(() => {
    // Fetch approval details from API
    // const data = await fetchApprovalDetails(approvalId.value)
    // setupData.value = data
})
</script>
