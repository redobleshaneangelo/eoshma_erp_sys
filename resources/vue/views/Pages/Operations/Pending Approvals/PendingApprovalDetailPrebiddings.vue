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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Pending Approvals
                </router-link>

                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ prebidding.project_name }}</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ prebidding.project_code }} • Submitted by {{ prebidding.submitted_by }}</p>
                    </div>
                    <!-- Status removed as per requirements -->
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="grid grid-cols-5 gap-4 mb-8">
                <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                    <div class="text-gray-600 text-xs font-semibold uppercase">Total Budget</div>
                    <div class="text-xl font-bold text-gray-900 mt-2">₱{{ formatCurrency(prebidding.total_budget) }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                    <div class="text-gray-600 text-xs font-semibold uppercase">Materials Cost</div>
                    <div class="text-xl font-bold text-blue-600 mt-2">₱{{ formatCurrency(prebidding.materials_cost) }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                    <div class="text-gray-600 text-xs font-semibold uppercase">Labor Cost</div>
                    <div class="text-xl font-bold text-green-600 mt-2">₱{{ formatCurrency(prebidding.labor_cost) }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                    <div class="text-gray-600 text-xs font-semibold uppercase">Equipment Cost</div>
                    <div class="text-xl font-bold text-purple-600 mt-2">₱{{ formatCurrency(prebidding.equipment_cost) }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                    <div class="text-gray-600 text-xs font-semibold uppercase">Days to Deadline</div>
                    <div :class="[
                        'text-xl font-bold mt-2',
                        daysUntilDeadline <= 7 ? 'text-red-600' : 'text-gray-900'
                    ]">
                        {{ daysUntilDeadline }} days
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
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
                        <!-- Project Information -->
                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Project Information</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Client Name</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ prebidding.client_name }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Project Code</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ prebidding.project_code }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Project Type</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ prebidding.project_type }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Submitted By</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ prebidding.submitted_by }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Submission Date</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ formatDate(prebidding.submitted_at) }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Bidding Deadline</span>
                                        <span :class="[
                                            'text-sm font-semibold',
                                            daysUntilDeadline <= 7 ? 'text-red-600' : 'text-gray-900'
                                        ]">
                                            {{ formatDate(prebidding.deadline) }}
                                        </span>
                                    </div>

                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Project Description</h3>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ prebidding.description }}</p>
                            </div>
                        </div>

                        <!-- Budget Breakdown Chart -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Budget Breakdown</h3>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="space-y-4">
                                    <!-- Materials -->
                                    <div>
                                        <div class="flex justify-between mb-2">
                                            <span class="text-sm font-semibold text-gray-900">Materials</span>
                                            <span class="text-sm font-bold text-gray-900">₱{{ formatCurrency(prebidding.materials_cost) }} ({{ getPercentage(prebidding.materials_cost) }}%)</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3">
                                            <div 
                                                class="bg-blue-600 h-3 rounded-full transition-all"
                                                :style="{ width: getPercentage(prebidding.materials_cost) + '%' }"
                                            ></div>
                                        </div>
                                    </div>

                                    <!-- Equipment -->
                                    <div>
                                        <div class="flex justify-between mb-2">
                                            <span class="text-sm font-semibold text-gray-900">Equipment</span>
                                            <span class="text-sm font-bold text-gray-900">₱{{ formatCurrency(prebidding.equipment_cost) }} ({{ getPercentage(prebidding.equipment_cost) }}%)</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3">
                                            <div 
                                                class="bg-purple-600 h-3 rounded-full transition-all"
                                                :style="{ width: getPercentage(prebidding.equipment_cost) + '%' }"
                                            ></div>
                                        </div>
                                    </div>

                                    <!-- Labor -->
                                    <div>
                                        <div class="flex justify-between mb-2">
                                            <span class="text-sm font-semibold text-gray-900">Labor</span>
                                            <span class="text-sm font-bold text-gray-900">₱{{ formatCurrency(prebidding.labor_cost) }} ({{ getPercentage(prebidding.labor_cost) }}%)</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3">
                                            <div 
                                                class="bg-green-600 h-3 rounded-full transition-all"
                                                :style="{ width: getPercentage(prebidding.labor_cost) + '%' }"
                                            ></div>
                                        </div>
                                    </div>

                                    <!-- Subcontractor -->
                                    <div>
                                        <div class="flex justify-between mb-2">
                                            <span class="text-sm font-semibold text-gray-900">Subcontractor</span>
                                            <span class="text-sm font-bold text-gray-900">₱{{ formatCurrency(prebidding.subcontractor_cost) }} ({{ getPercentage(prebidding.subcontractor_cost) }}%)</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3">
                                            <div 
                                                class="bg-orange-600 h-3 rounded-full transition-all"
                                                :style="{ width: getPercentage(prebidding.subcontractor_cost) + '%' }"
                                            ></div>
                                        </div>
                                    </div>

                                    <!-- Miscellaneous -->
                                    <div>
                                        <div class="flex justify-between mb-2">
                                            <span class="text-sm font-semibold text-gray-900">Miscellaneous</span>
                                            <span class="text-sm font-bold text-gray-900">₱{{ formatCurrency(prebidding.miscellaneous_cost) }} ({{ getPercentage(prebidding.miscellaneous_cost) }}%)</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3">
                                            <div 
                                                class="bg-gray-600 h-3 rounded-full transition-all"
                                                :style="{ width: getPercentage(prebidding.miscellaneous_cost) + '%' }"
                                            ></div>
                                        </div>
                                    </div>

                                    <!-- Total -->
                                    <div class="border-t-2 border-gray-300 pt-4 mt-2">
                                        <div class="flex justify-between">
                                            <span class="text-base font-bold text-gray-900">Total Budget</span>
                                            <span class="text-base font-bold text-[#0c8ce9]">₱{{ formatCurrency(prebidding.total_budget) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Materials Tab -->
                    <div v-if="activeTab === 'materials'">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Materials Planning</h3>
                        <div v-if="materials.length === 0" class="bg-gray-50 rounded-lg p-8 text-center border border-gray-200">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-gray-600 font-semibold">No materials data available</p>
                        </div>
                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Material Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Brand</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Unit</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Quantity</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Unit Cost</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Total Cost</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Source</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="material in materials" :key="material.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ material.name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ material.brand || 'N/A' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ material.unit }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 font-semibold">{{ material.quantity }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">₱{{ formatCurrency(material.unit_cost) }}</td>
                                        <td class="px-4 py-3 text-sm font-bold text-blue-600">₱{{ formatCurrency(calculateItemTotal(material.unit_cost, material.quantity)) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ material.source }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="5" class="px-4 py-3 text-sm font-bold text-gray-900 text-right">Total Materials Cost:</td>
                                        <td colspan="2" class="px-4 py-3 text-sm font-bold text-blue-600">₱{{ formatCurrency(prebidding.materials_cost) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Equipment Tab -->
                    <div v-if="activeTab === 'equipment'">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Equipment Planning</h3>
                        <div v-if="equipment.length === 0" class="bg-gray-50 rounded-lg p-8 text-center border border-gray-200">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2m6-2v2M9 7a2 2 0 012-2h6a2 2 0 012 2H9z" />
                            </svg>
                            <p class="text-gray-600 font-semibold">No equipment data available</p>
                        </div>
                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Equipment Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Brand</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Condition</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Quantity</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Unit Cost</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Total Cost</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="eq in equipment" :key="eq.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ eq.name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ eq.brand || 'N/A' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ eq.type }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ eq.condition || 'N/A' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 font-semibold">{{ eq.quantity }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">₱{{ formatCurrency(eq.unit_cost) }}</td>
                                        <td class="px-4 py-3 text-sm font-bold text-purple-600">₱{{ formatCurrency(calculateItemTotal(eq.unit_cost, eq.quantity)) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="6" class="px-4 py-3 text-sm font-bold text-gray-900 text-right">Total Equipment Cost:</td>
                                        <td class="px-4 py-3 text-sm font-bold text-purple-600">₱{{ formatCurrency(prebidding.equipment_cost) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Labor Tab -->
                    <div v-if="activeTab === 'labor'">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Labor Cost Planning</h3>
                        <div v-if="labor.length === 0" class="bg-gray-50 rounded-lg p-8 text-center border border-gray-200">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 8.646 4 4 0 010-8.646M9 12H3m13.5-5.5h5" />
                            </svg>
                            <p class="text-gray-600 font-semibold">No labor data available</p>
                        </div>
                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Role</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Worker Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Total Cost</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="item in labor" :key="item.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ item.role }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ item.personnel }}</td>
                                        <td class="px-4 py-3 text-sm font-bold text-green-600">₱{{ formatCurrency(item.cost) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="2" class="px-4 py-3 text-sm font-bold text-gray-900 text-right">Total Labor Cost:</td>
                                        <td class="px-4 py-3 text-sm font-bold text-green-600">₱{{ formatCurrency(prebidding.labor_cost) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Subcontractors Tab -->
                    <div v-if="activeTab === 'subcontractors'">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Subcontractor Planning</h3>
                        <div v-if="subcontractors.length === 0" class="bg-gray-50 rounded-lg p-8 text-center border border-gray-200">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-gray-600 font-semibold">No subcontractor data available</p>
                        </div>
                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Subcontractor</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Scope of Work</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Planned Cost</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="sub in subcontractors" :key="sub.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ sub.name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ sub.scope }}</td>
                                        <td class="px-4 py-3 text-sm font-bold text-orange-600">₱{{ formatCurrency(sub.cost) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="2" class="px-4 py-3 text-sm font-bold text-gray-900 text-right">Total Subcontractor Cost:</td>
                                        <td class="px-4 py-3 text-sm font-bold text-orange-600">₱{{ formatCurrency(prebidding.subcontractor_cost) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Documents Tab -->
                    <div v-if="activeTab === 'documents'">
                        <!-- Document Checklist -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h4 class="text-base font-bold text-gray-900 mb-4">Required Document Checklist</h4>
                            <div v-if="documentChecklist.length === 0" class="bg-gray-50 rounded-lg p-6 text-center border border-gray-200">
                                <p class="text-gray-600">No documents in checklist</p>
                            </div>
                            <div v-else class="space-y-2">
                                <div 
                                    v-for="doc in documentChecklist" 
                                    :key="doc.id"
                                    class="flex items-center justify-between py-3 px-4 border border-gray-200 rounded-lg"
                                >
                                    <div class="flex items-center gap-3">
                                        <div :class="[
                                            'rounded-full p-1',
                                            doc.completed ? 'bg-green-100' : 'bg-gray-100'
                                        ]">
                                            <svg v-if="doc.completed" class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <svg v-else class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p :class="[
                                            'text-sm',
                                            doc.completed ? 'text-gray-900 font-semibold' : 'text-gray-600'
                                        ]">{{ doc.name }}</p>
                                    </div>
                                    <span :class="[
                                        'text-xs px-3 py-1 rounded-full font-semibold',
                                        doc.completed ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'
                                    ]">
                                        {{ doc.completed ? 'Completed' : 'Pending' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 mt-6">Attached Documents</h3>
                        <div v-if="documents.length === 0" class="bg-gray-50 rounded-lg p-8 text-center border border-gray-200">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-gray-600 font-semibold">No documents attached</p>
                        </div>
                        <div v-else class="grid grid-cols-3 gap-4">
                            <div 
                                v-for="doc in documents" 
                                :key="doc.id"
                                @click="viewDocument(doc)"
                                class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all cursor-pointer"
                            >
                                <div class="flex items-start gap-3">
                                    <div class="bg-red-100 rounded-lg p-3">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-gray-900 mb-1">{{ doc.name }}</h4>
                                        <p class="text-xs text-gray-600">{{ doc.type }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Uploaded {{ formatDate(doc.uploaded_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>  
                    </div>

                    <!-- Bid Details Tab -->
                    <div v-if="activeTab === 'bid_details'">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Bidding Information</h3>
                        <div class="space-y-6">
                            <!-- Bidding Schedule -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h4 class="text-base font-bold text-gray-900 mb-4">Bidding Schedule</h4>
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700 uppercase">Submission Deadline</label>
                                        <p class="text-sm text-gray-900 mt-1 font-semibold">{{ formatDate(prebidding.deadline) }}</p>                                    
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700 uppercase">Opening Date</label>
                                        <p class="text-sm text-gray-900 mt-1 font-semibold">{{ formatDate(bidDetails.opening_date) }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700 uppercase">Bid Type</label>
                                        <p class="text-sm text-gray-900 mt-1">{{ bidDetails.bid_type }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700 uppercase">Bond Type</label>
                                        <p class="text-sm text-gray-900 mt-1">{{ bidDetails.bond_type }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700 uppercase">Bid Security Amount</label>
                                        <p class="text-sm text-gray-900 mt-1">₱{{ formatCurrency(securityAmount) }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Contract Information -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h4 class="text-base font-bold text-gray-900 mb-4">Contract Information</h4>
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700 uppercase">Contract Amount</label>
                                        <p class="text-sm text-gray-900 mt-1 font-semibold">₱{{ formatCurrency(prebidding.contract_amount) }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700 uppercase">Billing Type</label>
                                        <p class="text-sm text-gray-900 mt-1">{{ prebidding.billing_type || 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700 uppercase">Retention</label>
                                        <p class="text-sm text-gray-900 mt-1 font-semibold">₱{{ formatCurrency(retentionAmount) }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Bidding Activities -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h4 class="text-base font-bold text-gray-900 mb-4">Bidding Activities</h4>
                                <div v-if="bidActivities.length === 0" class="bg-gray-50 rounded-lg p-6 text-center border border-gray-200">
                                    <p class="text-gray-600">No bidding activities available</p>
                                </div>
                                <div v-else class="space-y-3">
                                    <div 
                                        v-for="activity in bidActivities" 
                                        :key="activity.id"
                                        class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all"
                                    >
                                        <div class="flex items-center gap-3">
                                            <div class="bg-[#0c8ce9] rounded-full p-2">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ activity.name }}</p>
                                                <p class="text-xs text-gray-600">{{ activity.description }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs font-semibold text-gray-700">{{ formatDate(activity.date) }}</p>
                                            <p class="text-xs text-gray-500">{{ activity.time }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex justify-end gap-3">
                <button 
                    @click="openRejectDrawer"
                    class="px-6 py-3 bg-white border-2 border-red-500 text-red-600 rounded-lg font-semibold hover:bg-red-50 transition-all"
                >
                    Reject Pre-Bidding
                </button>
                <button 
                    @click="approvePrebidding"
                    class="px-6 py-3 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-all"
                >
                    Approve Pre-Bidding
                </button>
            </div>
        </div>
    </div>

    <!-- Rejection Drawer -->
    <transition name="drawer">
        <div 
            v-if="showRejectDrawer" 
            class="fixed inset-0 z-50 overflow-hidden"
            @click="closeRejectDrawer"
        >
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/50 transition-opacity"></div>
            
            <!-- Drawer Panel -->
            <div class="absolute inset-y-0 right-0 max-w-xl w-full flex">
                <div 
                    class="relative w-full bg-white shadow-2xl"
                    @click.stop
                >
                    <!-- Drawer Header -->
                    <div class="bg-linear-to-r from-red-600 to-red-700 px-6 py-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="bg-white bg-opacity-20 rounded-lg p-2">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">Reject Pre-Bidding</h3>
                                    <p class="text-sm text-red-100">{{ prebidding.project_code }}</p>
                                </div>
                            </div>
                            <button 
                                @click="closeRejectDrawer"
                                class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition-all"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Drawer Content -->
                    <div class="p-6 overflow-y-auto h-[calc(100vh-180px)]">
                        <!-- Project Info Summary -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-200">
                            <h4 class="text-sm font-bold text-gray-900 mb-3">Pre-Bidding Summary</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Project Name:</span>
                                    <span class="font-semibold text-gray-900">{{ prebidding.project_name }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Client:</span>
                                    <span class="font-semibold text-gray-900">{{ prebidding.client_name }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Total Budget:</span>
                                    <span class="font-semibold text-[#0c8ce9]">₱{{ formatCurrency(prebidding.total_budget) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Submitted By:</span>
                                    <span class="font-semibold text-gray-900">{{ prebidding.submitted_by }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Rejection Reason Form -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                Reason for Rejection <span class="text-red-600">*</span>
                            </label>
                            <textarea 
                                v-model="rejectionReason"
                                rows="6"
                                placeholder="Please provide a detailed reason for rejecting this pre-bidding request..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                            ></textarea>
                            <p class="text-xs text-gray-500 mt-2">{{ rejectionReason.length }} characters</p>
                            <p v-if="rejectionError" class="text-xs text-red-600 mt-1">{{ rejectionError }}</p>
                        </div>

                        <!-- Important Notice -->
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <h5 class="text-sm font-bold text-red-900 mb-1">Important Notice</h5>
                                    <p class="text-xs text-red-800 leading-relaxed">
                                        Once rejected, the submitter will be notified immediately via email. This action cannot be undone. Please ensure your rejection reason is clear and constructive.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Drawer Footer -->
                    <div class="absolute bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-6 py-4">
                        <div class="flex gap-3">
                            <button 
                                @click="closeRejectDrawer"
                                class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition-all"
                            >
                                Cancel
                            </button>
                            <button 
                                @click="confirmRejectPrebidding"
                                class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-all"
                            >
                                Confirm Rejection
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

const activeTab = ref('overview')
const showRejectDrawer = ref(false)
const rejectionReason = ref('')
const rejectionError = ref('')

const tabs = [
    { key: 'overview', label: 'Overview' },
    { key: 'materials', label: 'Materials' },
    { key: 'equipment', label: 'Equipment' },
    { key: 'labor', label: 'Labor' },
    { key: 'subcontractors', label: 'Subcontractors' },
    { key: 'documents', label: 'Documents' },
    { key: 'bid_details', label: 'Bid Details' }
]

const statusStyles = {
    'Pending Approval': 'bg-yellow-100 text-yellow-800 border border-yellow-300',
    'Approved': 'bg-green-100 text-green-800 border border-green-300',
    'Rejected': 'bg-red-100 text-red-800 border border-red-300'
}


// Reactive data
const prebidding = ref({
    id: null,
    project_name: '',
    project_code: '',
    project_type: '',
    client_name: '',
    submitted_by: '',
    deadline: '',
    total_budget: 0,
    contract_amount: 0,
    billing_type: '',
    retention: 0,
    materials_cost: 0,
    equipment_cost: 0,
    labor_cost: 0,
    subcontractor_cost: 0,
    miscellaneous_cost: 0,
    status: '',
    description: '',
    submitted_at: '',
    document_count: 0
})

const materials = ref([])
const equipment = ref([])
const labor = ref([])
const subcontractors = ref([])
const documents = ref([])
const bidDetails = ref({})
const bidActivities = ref([])
const documentChecklist = ref([])

// Computed
const daysUntilDeadline = computed(() => {
    if (!prebidding.value.deadline) return 0
    const today = new Date()
    const deadline = new Date(prebidding.value.deadline)
    const diffTime = deadline - today
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return isNaN(diffDays) ? 0 : diffDays
})

const securityAmount = computed(() => {
    const total = prebidding.value.total_budget || 0
    const percentage = bidDetails.value.security_amount || 0
    return (percentage / 100) * total
})

const retentionAmount = computed(() => {
    const contract = prebidding.value.contract_amount || 0
    const percentage = prebidding.value.retention || 0
    return (percentage / 100) * contract
})

// Methods
const formatCurrency = (value) => {
    const num = parseFloat(value)
    if (isNaN(num) || num === null || num === undefined) {
        return '0.00'
    }
    return num.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const formatDate = (date) => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const getPercentage = (amount) => {
    const total = prebidding.value.total_budget || 0
    if (total === 0) return '0.0'
    const value = parseFloat(amount) || 0
    const percentage = (value / total) * 100
    return percentage.toFixed(1)
}

const calculateItemTotal = (unitCost, quantity) => {
    const cost = parseFloat(unitCost) || 0
    const qty = parseFloat(quantity) || 0
    return cost * qty
}

const viewDocument = (doc) => {
    console.log('Viewing document:', doc.file_path)
    if (doc.file_path) {
        window.open(doc.file_path, '_blank')
    }
}

const approvePrebidding = async () => {
    const result = await Swal.fire({
        title: 'Approve Pre-Bidding?',
        html: `
            <div class="text-left">
                <p class="text-sm text-gray-700 mb-3">You are about to approve this pre-bidding request:</p>
                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Project:</span>
                        <span class="font-semibold text-gray-900">${prebidding.value.project_name}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total Budget:</span>
                        <span class="font-semibold text-blue-600">₱${formatCurrency(prebidding.value.total_budget)}</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-3">Once approved, this will be sent to the Finance department for further processing.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0c8ce9',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Approve',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'rounded-lg',
            confirmButton: 'px-6 py-2 rounded-lg font-semibold',
            cancelButton: 'px-6 py-2 rounded-lg font-semibold'
        }
    })

    if (result.isConfirmed) {
        // TODO: API call to approve
        await axios.put(`/api/pending-approvals/operations/prebiddings/${route.params.id}/approve`)
        
        await Swal.fire({
            title: 'Sent to Finance!',
            text: 'Pre-bidding has been approved and forwarded to the Finance department.',
            icon: 'success',
            confirmButtonColor: '#0c8ce9',
            confirmButtonText: 'OK',
            customClass: {
                popup: 'rounded-lg',
                confirmButton: 'px-6 py-2 rounded-lg font-semibold'
            }
        })

        router.push('/pending-approvals')
    }
}

const openRejectDrawer = () => {
    showRejectDrawer.value = true
    rejectionReason.value = ''
    rejectionError.value = ''
}

const closeRejectDrawer = () => {
    showRejectDrawer.value = false
    rejectionReason.value = ''
    rejectionError.value = ''
}

const confirmRejectPrebidding = async () => {
    // Validate rejection reason
    if (!rejectionReason.value.trim()) {
        rejectionError.value = 'Please provide a reason for rejection'
        return
    }

    if (rejectionReason.value.trim().length < 10) {
        rejectionError.value = 'Rejection reason must be at least 10 characters'
        return
    }

    // TODO: API call to reject
    // await axios.post(`/api/pending-approvals/operations/prebiddings/${route.params.id}/reject`, {
    //     reason: rejectionReason.value
    // })

    closeRejectDrawer()

    await Swal.fire({
        title: 'Pre-Bidding Rejected',
        text: 'The submitter has been notified of the rejection.',
        icon: 'success',
        confirmButtonColor: '#0c8ce9',
        confirmButtonText: 'OK',
        customClass: {
            popup: 'rounded-lg',
            confirmButton: 'px-6 py-2 rounded-lg font-semibold'
        }
    })

    // router.push('/pending-approvals')
}

onMounted(() => {
    fetchPrebiddingDetails()
})

const fetchPrebiddingDetails = async () => {
    try {
        const id = route.params.id
        if (!id) {
            console.error('No prebidding ID provided')
            return
        }

        const response = await axios.get(`/api/pending-approvals/operations/prebiddings/${id}`, {
            headers: { Authorization: `Bearer ${auth.token}` }
        })

        const data = response.data.prebidding

        console.log('Fetched Prebidding Details:', response.data)
        
        // Map main prebidding data
        prebidding.value = {
            id: data.id,
            project_name: data.project_name || '',
            project_code: data.project_code || '',
            project_type: data.project_type || '',
            client_name: data.client_name || '',
            submitted_by: data.submitted_by || '',
            deadline: data.deadline || '',
            total_budget: data.total_budget || 0,
            contract_amount: data.contract_amount || 0,
            billing_type: data.billing_type || '',
            retention: data.retention || 0,
            materials_cost: data.materials_cost || 0,
            equipment_cost: data.equipment_cost || 0,
            labor_cost: data.labor_cost || 0,
            subcontractor_cost: data.subcontractor_cost || 0,
            miscellaneous_cost: data.miscellaneous_cost || 0,
            status: data.status || 'Pending Approval',
            description: data.description || '',
            submitted_at: data.submitted_at || '',
            document_count: data.document_count || 0
        }

        // Map materials
        if (response.data.materials && Array.isArray(response.data.materials)) {
            materials.value = response.data.materials
        }

        // Map equipment
        if (response.data.equipment && Array.isArray(response.data.equipment)) {
            equipment.value = response.data.equipment
        }

        // Map labor
        if (response.data.labor && Array.isArray(response.data.labor)) {
            labor.value = response.data.labor
        }

        // Map subcontractors
        if (response.data.subcontractors && Array.isArray(response.data.subcontractors)) {
            subcontractors.value = response.data.subcontractors
        }

        // Map documents
        if (response.data.documents) {
            documentChecklist.value = response.data.documents.checklist || []
            documents.value = response.data.documents.uploaded || []
        }

        // Map bid details
        if (response.data.bid_details) {
            bidDetails.value = response.data.bid_details
        }

        // Map bid activities
        if (response.data.bid_activities && Array.isArray(response.data.bid_activities)) {
            bidActivities.value = response.data.bid_activities
        }

    } catch (error) {
        console.error('Error fetching prebidding details:', error)
        if (error.response?.status === 404) {
            await Swal.fire({
                title: 'Not Found',
                text: 'The prebidding you are looking for does not exist.',
                icon: 'error',
                confirmButtonColor: '#0c8ce9',
                confirmButtonText: 'OK'
            })
            router.push('/pending-approvals')
        } else {
            await Swal.fire({
                title: 'Error',
                text: 'Failed to load prebidding details. Please try again.',
                icon: 'error',
                confirmButtonColor: '#0c8ce9',
                confirmButtonText: 'OK'
            })
        }
    }
}
</script>

<style scoped>
/* Drawer transition animations */
.drawer-enter-active,
.drawer-leave-active {
    transition: opacity 300ms ease;
}

.drawer-enter-from,
.drawer-leave-to {
    opacity: 0;
}

.drawer-enter-active .absolute.inset-y-0,
.drawer-leave-active .absolute.inset-y-0 {
    transition: transform 300ms ease;
}

.drawer-enter-from .absolute.inset-y-0,
.drawer-leave-to .absolute.inset-y-0 {
    transform: translateX(100%);
}
</style>
