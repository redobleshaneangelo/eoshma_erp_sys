<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">
            <!-- Back Button & Header -->
            <div class="mb-8">
                <router-link
                    :to="`/projects/${projectId}`"
                    class="inline-flex items-center gap-2 text-[#0c8ce9] font-semibold hover:underline mb-4"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Project
                </router-link>
            </div>

            <!-- Stepper -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-8 mb-8">
                <div class="flex items-center justify-between">
                    <div v-for="(step, index) in steps" :key="index" class="flex items-center flex-1" :class="{ 'cursor-pointer': index <= maxStepReached }" @click="goToStep(index)">
                        <!-- Step Circle -->
                        <div :class="['w-12 h-12 rounded-full flex items-center justify-center transition-all', getStepCircleClass(index)]">
                            <component :is="getStepIcon(step, index)" :class="getIconClass(index)" />
                        </div>
                        
                        <!-- Step Label -->
                        <div class="ml-3">
                            <p :class="['text-xs font-semibold uppercase tracking-wide', getStepStatus(index) === 'completed' ? 'text-[#0c8ce9]' : getStepStatus(index) === 'current' ? 'text-[#0c8ce9]' : 'text-gray-400']">
                                {{ step.label }}
                            </p>
                        </div>

                        <!-- Divider -->
                        <div v-if="index < steps.length - 1" :class="['flex-1 h-1 mx-4 rounded', index < activeStep ? 'bg-[#0c8ce9]' : 'bg-gray-200']"></div>
                    </div>
                </div>
            </div>

            <!-- Budget Utilization Display -->
            <div class="bg-linear-to-r from-blue-50 to-cyan-50 border border-blue-200 rounded-lg p-6 mb-8">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-bold text-gray-900">Budget Utilization</h4>
                    <span class="text-lg font-bold text-[#0c8ce9]">{{ budgetUtilizationPercentage.toFixed(1) }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 mb-3">
                    <div
                        class="h-3 rounded-full transition-all duration-300"
                        :class="budgetUtilizationPercentage > 100 ? 'bg-red-500' : budgetUtilizationPercentage > 80 ? 'bg-yellow-500' : 'bg-green-500'"
                        :style="{ width: Math.min(budgetUtilizationPercentage, 100) + '%' }"
                    ></div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Total Budget</p>
                        <p class="text-sm font-bold text-gray-900">₱{{ formatCurrency(setupData.financial.total_budget) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Amount Utilized</p>
                        <p class="text-sm font-bold text-[#0c8ce9]">₱{{ formatCurrency(totalBudgetUtilized) }}</p>
                    </div>
                    <div v-if="setupData.financial.total_budget > 0">
                        <p class="text-xs text-gray-600 mb-1">Remaining Balance</p>
                        <p :class="setupData.financial.total_budget - totalBudgetUtilized < 0 ? 'text-red-600 font-bold' : 'text-green-600 font-bold'">₱{{ formatCurrency(setupData.financial.total_budget - totalBudgetUtilized) }}</p>
                    </div>
                </div>
            </div>

            <!-- Step Content -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-8 min-h-125">
                    <!-- Contract Step -->
                    <div v-if="activeStep === 0" class="space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Contract Number</label>
                                <input
                                    v-model="contract.contract_number"
                                    type="text"
                                    placeholder="e.g., CON-2026-001"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Contract Date</label>
                                <input
                                    v-model="contract.contract_date"
                                    type="date"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Contract Amount</label>
                                <input
                                    v-model="contract.contract_amount"
                                    type="number"
                                    placeholder="0.00"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Contract Period</label>
                                <div class="flex gap-2">
                                    <input
                                        v-model="contract.contract_period"
                                        type="number"
                                        min="1"
                                        placeholder="0"
                                        class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                    />
                                    <select
                                        v-model="contract.contract_period_unit"
                                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent bg-white"
                                    >
                                        <option value="days">Days</option>
                                        <option value="months">Months</option>
                                        <option value="years">Years</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Notice of Award -->
                        <div class="pt-4 border-t border-gray-200">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Notice of Award</label>
                            <div v-if="contract.notice_of_award_url" class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <svg class="w-6 h-6 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">{{ contract.notice_of_award_name }}</p>
                                    <p class="text-xs text-gray-600">File from Prebidding</p>
                                </div>
                                <a :href="contract.notice_of_award_url" target="_blank" class="text-green-600 hover:text-green-700 font-semibold text-sm">
                                    View
                                </a>
                            </div>
                            <div v-else class="p-4 bg-gray-50 border border-gray-200 rounded-lg text-center">
                                <p class="text-sm text-gray-600">No Notice of Award document found</p>
                            </div>
                        </div>
                    </div>

                    <!-- Project Setup Step -->
                    <div v-if="activeStep === 1" class="space-y-8">
                        <!-- Workers/HR Section -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Required Workforce</h3>
                                <div class="flex items-center gap-2">
                                    <button
                                        @click="showAddWorkforceModal = true"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700 rounded-lg transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Workforce
                                    </button>
                                    <button
                                        @click="openEditWorkforceModal"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1 0v2m0 10v2m-7-6h14" />
                                        </svg>
                                        Edit
                                    </button>
                                </div>
                            </div>
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Position</th>
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Headcount</th>
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="worker in setupData.workers" :key="worker.id" class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ worker.position }}</td>
                                            <td class="px-6 py-4 text-center text-sm font-semibold text-gray-900">{{ worker.headcount }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <button
                                                    @click="removeWorker(worker.id)"
                                                    :disabled="isRequiredWorkforceRole(worker.position)"
                                                    :class="[
                                                        'text-sm font-semibold',
                                                        isRequiredWorkforceRole(worker.position)
                                                            ? 'text-gray-400 cursor-not-allowed'
                                                            : 'text-red-600 hover:text-red-700'
                                                    ]"
                                                >
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="setupData.workers.length === 0">
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                                No workers added yet
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Materials Section -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Materials</h3>
                                <div class="flex items-center gap-2">
                                    <button
                                        @click="showAddMaterialModal = true"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700 rounded-lg transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Material
                                    </button>
                                    <button
                                        @click="openEditMaterialsModal"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1 0v2m0 10v2m-7-6h14" />
                                        </svg>
                                        Edit
                                    </button>
                                </div>
                            </div>
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Material</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Brand</th>
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Quantity</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Unit</th>
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="material in setupData.materials" :key="material.id" class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ material.name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ material.brand || 'N/A' }}</td>
                                            <td class="px-6 py-4 text-center text-sm text-gray-900">{{ material.quantity }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ material.unit }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <button
                                                    @click="removeMaterial(material.id)"
                                                    class="text-red-600 hover:text-red-700 text-sm font-semibold"
                                                >
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="setupData.materials.length === 0">
                                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                                No materials added yet
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Equipment Section -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Equipment</h3>
                                <div class="flex items-center gap-2">
                                    <button
                                        @click="showAddEquipmentModal = true"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700 rounded-lg transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Equipment
                                    </button>
                                    <button
                                        @click="openEditEquipmentModal"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1 0v2m0 10v2m-7-6h14" />
                                        </svg>
                                        Edit
                                    </button>
                                </div>
                            </div>
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Equipment</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Brand</th>
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Quantity</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Condition</th>
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="equipment in setupData.equipment" :key="equipment.id" class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ equipment.name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ equipment.brand || 'N/A' }}</td>
                                            <td class="px-6 py-4 text-center text-sm text-gray-900">{{ equipment.quantity }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ equipment.type }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ equipment.condition || 'N/A' }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <button
                                                    @click="removeEquipment(equipment.id)"
                                                    class="text-red-600 hover:text-red-700 text-sm font-semibold"
                                                >
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="setupData.equipment.length === 0">
                                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                                No equipment added yet
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Step (Gantt Chart) -->
                    <div v-if="activeStep === 2" class="space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Project Start Date</label>
                                <input
                                    v-model="setupData.schedule.start_date"
                                    @change="validateStartDate"
                                    :max="setupData.schedule.end_date"
                                    type="date"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Project End Date</label>
                                <input
                                    v-model="setupData.schedule.end_date"
                                    @change="validateEndDate"
                                    :min="setupData.schedule.start_date"
                                    type="date"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Duration (Days)</label>
                                <input
                                    :value="calculateDuration"
                                    type="number"
                                    readonly
                                    class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 rounded-lg text-sm text-gray-600"
                                />
                            </div>
                        </div>

                        <!-- Gantt Chart -->
                        <div class="pt-4 border-t border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Project Timeline - Gantt Chart</h3>
                            <div class="bg-white border border-gray-200 rounded-lg p-4 overflow-x-auto">
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

                        <!-- Major Milestones -->
                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Major Milestones</h3>
                                <button
                                    @click="openAddMilestoneModal"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700 rounded-lg transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Milestone
                                </button>
                            </div>
                            <div class="space-y-3">
                                <div v-for="milestone in setupData.schedule.milestones" :key="milestone.id" class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">{{ milestone.name }}</p>
                                        <p class="text-xs text-gray-600">{{ formatDate(milestone.start_date) }} - {{ formatDate(milestone.end_date) }}</p>
                                    </div>
                                    <button
                                        @click="removeMilestone(milestone.id)"
                                        class="text-red-600 hover:text-red-700 text-sm font-semibold"
                                    >
                                        Remove
                                    </button>
                                </div>
                                <div v-if="setupData.schedule.milestones.length === 0" class="p-4 text-center text-gray-500">
                                    No milestones added yet
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Step -->
                    <div v-if="activeStep === 3" class="space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Total Budget</label>
                                <input
                                    v-model="setupData.financial.total_budget"
                                    type="number"
                                    readonly
                                    placeholder="0"
                                    class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 rounded-lg text-sm"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Retention (%)</label>
                                <input
                                    v-model="setupData.financial.retention"
                                    type="number"
                                    placeholder="10"
                                    readonly
                                    class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 text-gray-600 rounded-lg text-sm cursor-not-allowed"
                                />
                            </div>
                        </div>

                        <!-- Budget Breakdown -->
                        <div class="pt-4 border-t border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Budget Breakdown</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm font-semibold text-gray-700">Materials</p>
                                    <p class="text-sm font-bold text-gray-900">₱{{ formatCurrency(materialsCost) }}</p>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm font-semibold text-gray-700">Equipment</p>
                                    <p class="text-sm font-bold text-gray-900">₱{{ formatCurrency(equipmentCost) }}</p>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm font-semibold text-gray-700">Labor</p>
                                    <p class="text-sm font-bold text-gray-900">₱{{ formatCurrency(setupData.financial.labor_cost) }}</p>
                                </div>
                                <div v-if="setupData.financial.subcontractor_cost > 0" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm font-semibold text-gray-700">Subcontractor</p>
                                    <p class="text-sm font-bold text-gray-900">₱{{ formatCurrency(setupData.financial.subcontractor_cost) }}</p>
                                </div>
                                <div v-if="setupData.financial.miscellaneous_cost > 0" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm font-semibold text-gray-700">Miscellaneous</p>
                                    <p class="text-sm font-bold text-gray-900">₱{{ formatCurrency(setupData.financial.miscellaneous_cost) }}</p>
                                </div>
                                <div class="flex items-center justify-between p-3 border border-[#0c8ce9] bg-blue-50 rounded-lg">
                                    <p class="text-sm font-bold text-gray-900">Total Utilized</p>
                                    <p class="text-lg font-bold text-[#0c8ce9]">₱{{ formatCurrency(estimatedTotal) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Step -->
                    <div v-if="activeStep === 4" class="space-y-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Project Documents</h3>
                            <button
                                @click="showAddDocumentModal = true"
                                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700 rounded-lg transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Document
                            </button>
                        </div>
                        <div v-if="setupData.documents.length === 0" class="p-6 bg-gray-50 border border-gray-200 rounded-lg text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm font-semibold text-gray-700">No documents added yet</p>
                            <p class="text-xs text-gray-600 mt-1">Click "Add Document" to upload project documents</p>
                        </div>
                        <div v-else class="grid grid-cols-1 gap-6">
                            <div v-for="doc in setupData.documents" :key="doc.id" class="border border-gray-300 rounded-lg p-4 cursor-pointer hover:shadow-lg transition-shadow" @click="doc.status === 'Pending' ? triggerFileInput(doc.id) : openDocument(doc)">
                                <div class="flex items-start gap-3 mb-3">
                                    <svg class="w-5 h-5 text-gray-600 shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900">{{ doc.name }}</p>
                                    </div>
                                    <button
                                        v-if="!doc.name?.startsWith('noa_')"
                                        @click.stop="removeDocument(doc.id)"
                                        class="text-red-600 hover:text-red-700 text-sm font-semibold shrink-0"
                                    >
                                        Remove
                                    </button>
                                </div>
                                <div v-if="doc.status === 'Pending'" class="relative">
                                    <input
                                        type="file"
                                        @change="uploadDocument(doc.id, $event)"
                                        class="hidden"
                                        :data-doc-id="doc.id"
                                        accept=".pdf"
                                    />
                                    <div class="px-3 py-2 border border-[#0c8ce9] bg-blue-50 text-[#0c8ce9] rounded-lg text-sm font-semibold text-center">
                                        Click to upload PDF
                                    </div>
                                </div>
                                <div v-else class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm text-green-600 font-semibold">Uploaded</span>
                                    <button
                                        @click.stop="openDocument(doc)"
                                        class="ml-auto px-3 py-1 bg-[#0c8ce9] text-white rounded text-xs font-semibold hover:bg-blue-700 transition-colors"
                                    >
                                        View
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Step -->
                    <div v-if="activeStep === 5" class="space-y-6">
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
                                    <p class="text-sm font-semibold text-gray-900">{{ contract.contract_number || 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold uppercase mb-1">Contract Date</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ formatDate(contract.contract_date) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold uppercase mb-1">Contract Amount</p>
                                    <p class="text-sm font-semibold text-gray-900">₱{{ formatCurrency(contract.contract_amount || 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold uppercase mb-1">Contract Period</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ contract.contract_period || 0 }} {{ contract.contract_period_unit || 'days' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold uppercase mb-1">Notice of Award</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ contract.notice_of_award_name || 'N/A' }}</p>
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
                                <div v-if="setupData.financial.subcontractor_cost > 0" class="flex justify-between items-center pb-3 border-b border-gray-200">
                                    <span class="text-sm text-gray-600">Subcontractor Cost</span>
                                    <span class="font-semibold text-gray-900">₱{{ formatCurrency(setupData.financial.subcontractor_cost) }}</span>
                                </div>
                                <div v-if="setupData.financial.miscellaneous_cost > 0" class="flex justify-between items-center pb-3 border-b border-gray-200">
                                    <span class="text-sm text-gray-600">Miscellaneous Cost</span>
                                    <span class="font-semibold text-gray-900">₱{{ formatCurrency(setupData.financial.miscellaneous_cost) }}</span>
                                </div>
                                <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                    <span class="text-sm text-gray-600">Retention</span>
                                    <span class="font-semibold text-gray-900">{{ setupData.financial.retention }}%</span>
                                </div>
                                <div class="flex justify-between items-center pt-2">
                                    <span class="text-sm font-bold text-gray-900">Total Utilized</span>
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

                        <!-- Approval Buttons -->
                        <div class="flex gap-4 pt-6 border-t border-gray-200">
                            <button
                                @click="showNoticeDrawer = true"
                                class="flex-1 px-6 py-3 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                            >
                                Send for Approval
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex gap-4 mt-6" v-if="activeStep !== 5">
                <button
                    @click="handlePreviousStep"
                    v-if="activeStep !== 0"
                    class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Previous
                </button>
                <button
                    @click="handleNextStep"
                    class="flex-1 px-6 py-3 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                >
                    Next
                </button>
            </div>
        </div>
    </div>

    <!-- Notice to Proceed Drawer -->
    <div v-if="showNoticeDrawer" class="fixed inset-0 z-50 overflow-hidden">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 transition-opacity" @click="showNoticeDrawer = false"></div>
        
        <!-- Drawer -->
        <div class="absolute inset-y-0 right-0 max-w-md w-full bg-white shadow-xl flex flex-col">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Send for Approval</h3>
                    <button @click="showNoticeDrawer = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <div class="space-y-6">
                    <!-- Project Information -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm font-semibold text-gray-700 mb-1">Project</p>
                        <p class="text-base font-bold text-gray-900">{{ project.project_name }}</p>
                        <p class="text-xs text-gray-600 mt-1">{{ project.project_code }}</p>
                    </div>

                    <!-- Notice to Proceed Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Notice to Proceed
                            <span class="text-red-500">*</span>
                        </label>
                        
                        <!-- File Upload Area -->
                        <div
                            @drop.prevent="handleNoticeDrop"
                            @dragover.prevent
                            class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-[#0c8ce9] transition-colors cursor-pointer"
                            @click="$refs.noticeFileInput.click()"
                        >
                            <div v-if="!noticeToProceedFile">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">
                                    Click to upload or drag and drop
                                </p>
                                <p class="mt-1 text-xs text-gray-500">PDF files only</p>
                            </div>
                            <div v-else class="flex items-center justify-center gap-3">
                                <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-gray-900">{{ noticeToProceedFile.name }}</p>
                                    <p class="text-xs text-gray-500">{{ (noticeToProceedFile.size / 1024).toFixed(2) }} KB</p>
                                </div>
                                <button
                                    @click.stop="noticeToProceedFile = null"
                                    class="ml-auto text-red-500 hover:text-red-700"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <input
                            ref="noticeFileInput"
                            type="file"
                            accept=".pdf,application/pdf"
                            class="hidden"
                            @change="handleNoticeUpload"
                        />
                    </div>

                    <!-- Info Message -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-green-800 mb-1">Ready to Proceed</p>
                                <p class="text-xs text-green-700">
                                    Upload the Notice to Proceed document to finalize the project setup and send it for approval.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex gap-3">
                    <button
                        @click="showNoticeDrawer = false"
                        class="flex-1 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        @click="submitForApproval"
                        :disabled="!noticeToProceedFile || submittingApproval"
                        class="flex-1 px-4 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="!submittingApproval">Submit</span>
                        <span v-else class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            Submitting...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Milestone Modal -->
    <div v-if="showAddMilestoneModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Add Milestone</h3>
                    <button @click="showAddMilestoneModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Milestone Name</label>
                    <input
                        v-model="newMilestone.name"
                        type="text"
                        placeholder="e.g., Foundation Complete"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                    />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                    <input
                        v-model="newMilestone.start_date"
                        type="date"
                        :min="setupData.schedule.start_date"
                        :max="setupData.schedule.end_date"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                    />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                    <input
                        v-model="newMilestone.end_date"
                        type="date"
                        :min="setupData.schedule.start_date"
                        :max="setupData.schedule.end_date"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                    />
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex gap-3">
                <button
                    @click="showAddMilestoneModal = false"
                    class="flex-1 px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="addMilestone"
                    class="flex-1 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                >
                    Add Milestone
                </button>
            </div>
        </div>
    </div>

    <div v-if="showAddWorkforceModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 sticky top-0 bg-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Add Workforce</h3>
                    <button @click="closeAddWorkforceModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Input Fields -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Position/Role</label>
                        <select
                            v-model="newWorkforce.role"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="">Select a role</option>
                            <option value="Site Engineer">Site Engineer</option>
                            <option value="Safety Officer">Safety Officer</option>
                            <option value="Foreman">Foreman</option>
                            <option value="Laborer">Laborer</option>
                            <option value="Electrician">Electrician</option>
                            <option value="Plumber">Plumber</option>
                            <option value="Carpenter">Carpenter</option>
                            <option value="Others">Others (Specify)</option>
                        </select>
                    </div>

                    <!-- Custom Role Input (appears when "Others" is selected) -->
                    <div v-if="newWorkforce.role === 'Others'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Custom Role</label>
                        <input
                            v-model="newWorkforce.customRole"
                            type="text"
                            placeholder="Enter custom role"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Headcount</label>
                        <input
                            v-model.number="newWorkforce.headcount"
                            type="number"
                            placeholder="1"
                            min="1"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>

                    <button
                        @click="addToWorkforceList"
                        class="w-full px-4 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                    >
                        Add to List
                    </button>
                </div>

                <!-- Temporary Workforce List -->
                <div class="border-t pt-6">
                    <h4 class="text-sm font-bold text-gray-900 mb-3">Temporary Workforce List</h4>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Position</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Headcount</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-if="tempWorkforceList.length === 0">
                                    <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                                        No workers added yet. Add a worker above to get started.
                                    </td>
                                </tr>
                                <tr v-for="(worker, idx) in tempWorkforceList" :key="idx" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ worker.role === 'Others' ? worker.customRole : worker.role }}</td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-900">{{ worker.headcount }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <button
                                            @click="removeFromWorkforceList(idx)"
                                            class="text-red-600 hover:text-red-700 text-sm font-semibold"
                                        >
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex gap-3 sticky bottom-0 bg-white">
                <button
                    @click="closeAddWorkforceModal"
                    class="flex-1 px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="submitWorkforceList"
                    :disabled="tempWorkforceList.length === 0"
                    :class="[
                        'flex-1 px-4 py-2 rounded-lg font-semibold transition-colors',
                        tempWorkforceList.length === 0
                            ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                            : 'bg-green-600 text-white hover:bg-green-700'
                    ]"
                >
                    Submit Workforce
                </button>
            </div>
        </div>
    </div>

    <!-- Add Material Modal -->
    <div v-if="showAddMaterialModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 sticky top-0 bg-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Add Material</h3>
                    <button @click="closeAddMaterialModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Input Fields -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Material Name</label>
                        <select
                            v-model="newMaterial.name"
                            @change="updateMaterialUnit"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="">Select material</option>
                            <option value="Cement">Cement</option>
                            <option value="Steel Bars">Steel Bars</option>
                            <option value="Bricks">Bricks</option>
                            <option value="Sand">Sand</option>
                            <option value="Gravel">Gravel</option>
                            <option value="Concrete">Concrete</option>
                            <option value="Paint">Paint</option>
                            <option value="Others">Others (Specify)</option>
                        </select>
                    </div>

                    <!-- Custom Material Input (appears when "Others" is selected) -->
                    <div v-if="newMaterial.name === 'Others'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Custom Material</label>
                        <input
                            v-model="newMaterial.customName"
                            type="text"
                            placeholder="Enter custom material name"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                        <input
                            v-model.number="newMaterial.quantity"
                            type="number"
                            placeholder="0"
                            min="1"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>

                    <!-- Brand Select (only appears when a material is selected and not Others) -->
                    <div v-if="selectedMaterial && selectedMaterialBrands.length > 0">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Brand</label>
                        <select
                            v-model="newMaterial.brand"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="">Select brand</option>
                            <option v-for="brand in selectedMaterialBrands" :key="brand.name" :value="brand.name">
                                {{ brand.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Brand Details Display -->
                    <div v-if="selectedMaterialBrand" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="text-sm font-bold text-gray-900 mb-2">Brand Details</h4>
                        <div class="space-y-1 text-sm">
                            <p><span class="font-semibold">Cost:</span> ₱{{ selectedMaterialBrand.cost }}</p>
                            <p><span class="font-semibold">Available:</span> {{ selectedMaterialBrand.available }}</p>
                        </div>
                    </div>

                    <div v-if="newMaterial.name !== 'Others'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Unit</label>
                        <input
                            v-model="newMaterial.unit"
                            type="text"
                            readonly
                            class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 rounded-lg text-sm text-gray-600"
                        />
                    </div>

                    <!-- Custom Unit Input (appears when "Others" is selected for material) -->
                    <div v-if="newMaterial.name === 'Others'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Unit</label>
                        <input
                            v-model="newMaterial.unit"
                            type="text"
                            placeholder="Enter unit"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>

                    <button
                        @click="addToMaterialList"
                        class="w-full px-4 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                    >
                        Add to List
                    </button>
                </div>

                <!-- Temporary Material List -->
                <div class="border-t pt-6">
                    <h4 class="text-sm font-bold text-gray-900 mb-3">Temporary Material List</h4>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Material</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Brand</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Quantity</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Unit</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-if="tempMaterialsList.length === 0">
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                        No materials added yet. Add a material above to get started.
                                    </td>
                                </tr>
                                <tr v-for="(material, idx) in tempMaterialsList" :key="idx" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ material.name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ material.brand || 'N/A' }}</td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-900">{{ material.quantity }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ material.unit }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <button
                                            @click="removeFromMaterialList(idx)"
                                            class="text-red-600 hover:text-red-700 text-sm font-semibold"
                                        >
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex gap-3 sticky bottom-0 bg-white">
                <button
                    @click="closeAddMaterialModal"
                    class="flex-1 px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="submitMaterialList"
                    :disabled="tempMaterialsList.length === 0"
                    :class="[
                        'flex-1 px-4 py-2 rounded-lg font-semibold transition-colors',
                        tempMaterialsList.length === 0
                            ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                            : 'bg-green-600 text-white hover:bg-green-700'
                    ]"
                >
                    Submit Materials
                </button>
            </div>
        </div>
    </div>

    <!-- Add Equipment Modal -->
    <div v-if="showAddEquipmentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 sticky top-0 bg-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Add Equipment</h3>
                    <button @click="closeAddEquipmentModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Input Fields -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Equipment Name</label>
                        <select
                            v-model="newEquipment.name"
                            @change="updateEquipmentType"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="">Select equipment</option>
                            <option value="Excavator">Excavator</option>
                            <option value="Concrete Mixer">Concrete Mixer</option>
                            <option value="Bulldozer">Bulldozer</option>
                            <option value="Crane">Crane</option>
                            <option value="Compactor">Compactor</option>
                            <option value="Scaffolding">Scaffolding</option>
                            <option value="Power Tools">Power Tools</option>
                            <option value="Others">Others (Specify)</option>
                        </select>
                    </div>

                    <!-- Custom Equipment Input (appears when "Others" is selected) -->
                    <div v-if="newEquipment.name === 'Others'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Custom Equipment</label>
                        <input
                            v-model="newEquipment.customName"
                            type="text"
                            placeholder="Enter custom equipment name"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                        <input
                            v-model.number="newEquipment.quantity"
                            type="number"
                            placeholder="0"
                            min="1"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>

                    <!-- Brand Select (only appears when equipment is selected and not Others) -->
                    <div v-if="selectedEquipment && selectedEquipmentBrands.length > 0">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Brand</label>
                        <select
                            v-model="newEquipment.brand"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="">Select brand</option>
                            <option v-for="brand in selectedEquipmentBrands" :key="brand.name" :value="brand.name">
                                {{ brand.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Brand Details Display -->
                    <div v-if="selectedEquipmentBrand" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="text-sm font-bold text-gray-900 mb-2">Brand Details</h4>
                        <div class="space-y-1 text-sm">
                            <p><span class="font-semibold">Cost:</span> ₱{{ selectedEquipmentBrand.cost }}</p>
                            <p><span class="font-semibold">Available:</span> {{ selectedEquipmentBrand.available }}</p>
                            <p><span class="font-semibold">Condition:</span> {{ selectedEquipmentBrand.condition }}</p>
                        </div>
                    </div>

                    <div v-if="newEquipment.name !== 'Others'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                        <input
                            v-model="newEquipment.type"
                            type="text"
                            readonly
                            class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 rounded-lg text-sm text-gray-600"
                        />
                    </div>

                    <!-- Type Select (appears when "Others" is selected for equipment) -->
                    <div v-if="newEquipment.name === 'Others'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                        <select
                            v-model="newEquipment.type"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="">Select type</option>
                            <option value="Heavy">Heavy</option>
                            <option value="Light">Light</option>
                            <option value="Specialized">Specialized</option>
                        </select>
                    </div>

                    <button
                        @click="addToEquipmentList"
                        class="w-full px-4 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                    >
                        Add to List
                    </button>
                </div>

                <!-- Temporary Equipment List -->
                <div class="border-t pt-6">
                    <h4 class="text-sm font-bold text-gray-900 mb-3">Temporary Equipment List</h4>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Equipment</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Brand</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Quantity</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Condition</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-if="tempEquipmentList.length === 0">
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
                                        No equipment added yet. Add equipment above to get started.
                                    </td>
                                </tr>
                                <tr v-for="(equipment, idx) in tempEquipmentList" :key="idx" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ equipment.name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ equipment.brand || 'N/A' }}</td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-900">{{ equipment.quantity }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ equipment.type }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ equipment.condition || 'N/A' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <button
                                            @click="removeFromEquipmentList(idx)"
                                            class="text-red-600 hover:text-red-700 text-sm font-semibold"
                                        >
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex gap-3 sticky bottom-0 bg-white">
                <button
                    @click="closeAddEquipmentModal"
                    class="flex-1 px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="submitEquipmentList"
                    :disabled="tempEquipmentList.length === 0"
                    :class="[
                        'flex-1 px-4 py-2 rounded-lg font-semibold transition-colors',
                        tempEquipmentList.length === 0
                            ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                            : 'bg-green-600 text-white hover:bg-green-700'
                    ]"
                >
                    Submit Equipment
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Workforce Modal -->
    <div v-if="showEditWorkforceModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 sticky top-0 bg-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Edit Workforce</h3>
                    <button @click="closeEditWorkforceModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Position</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Headcount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-if="editWorkforceList.length === 0">
                                <td colspan="2" class="px-4 py-8 text-center text-sm text-gray-500">
                                    No workers available to edit.
                                </td>
                            </tr>
                            <tr v-for="(worker, idx) in editWorkforceList" :key="idx" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <input
                                        v-model="worker.position"
                                        type="text"
                                        :readonly="isRequiredWorkforceRole(worker.position)"
                                        :class="[
                                            'w-full px-2 py-1 border border-gray-300 rounded text-sm',
                                            isRequiredWorkforceRole(worker.position)
                                                ? 'bg-gray-100 text-gray-600 cursor-not-allowed'
                                                : ''
                                        ]"
                                    />
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-900">
                                    <input
                                        v-model.number="worker.headcount"
                                        type="number"
                                        :min="isRequiredWorkforceRole(worker.position) ? 1 : 0"
                                        :disabled="isRequiredWorkforceRole(worker.position)"
                                        :class="[
                                            'w-24 px-2 py-1 border border-gray-300 rounded text-sm text-center',
                                            isRequiredWorkforceRole(worker.position)
                                                ? 'bg-gray-100 text-gray-600 cursor-not-allowed'
                                                : ''
                                        ]"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex gap-3 sticky bottom-0 bg-white">
                <button
                    @click="closeEditWorkforceModal"
                    class="flex-1 px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="saveEditWorkforce"
                    class="flex-1 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                >
                    Save Changes
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Materials Modal -->
    <div v-if="showEditMaterialsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 sticky top-0 bg-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Edit Materials</h3>
                    <button @click="closeEditMaterialsModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Material</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Unit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-if="editMaterialsList.length === 0">
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                                    No materials available to edit.
                                </td>
                            </tr>
                            <tr v-for="(material, idx) in editMaterialsList" :key="idx" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <input
                                        v-model="material.name"
                                        type="text"
                                        class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                    />
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-900">
                                    <input
                                        v-model.number="material.quantity"
                                        type="number"
                                        min="0"
                                        class="w-24 px-2 py-1 border border-gray-300 rounded text-sm text-center"
                                    />
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <input
                                        v-model="material.unit"
                                        type="text"
                                        readonly
                                        class="w-24 px-2 py-1 border border-gray-300 bg-gray-100 rounded text-sm text-gray-600"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex gap-3 sticky bottom-0 bg-white">
                <button
                    @click="closeEditMaterialsModal"
                    class="flex-1 px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="saveEditMaterials"
                    class="flex-1 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                >
                    Save Changes
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Equipment Modal -->
    <div v-if="showEditEquipmentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 sticky top-0 bg-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Edit Equipment</h3>
                    <button @click="closeEditEquipmentModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Equipment</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Type</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-if="editEquipmentList.length === 0">
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                                    No equipment available to edit.
                                </td>
                            </tr>
                            <tr v-for="(equipment, idx) in editEquipmentList" :key="idx" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <input
                                        v-model="equipment.name"
                                        type="text"
                                        class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                    />
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-900">
                                    <input
                                        v-model.number="equipment.quantity"
                                        type="number"
                                        min="0"
                                        class="w-24 px-2 py-1 border border-gray-300 rounded text-sm text-center"
                                    />
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <input
                                        v-model="equipment.type"
                                        type="text"
                                        readonly
                                        class="w-24 px-2 py-1 border border-gray-300 bg-gray-100 rounded text-sm text-gray-600"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex gap-3 sticky bottom-0 bg-white">
                <button
                    @click="closeEditEquipmentModal"
                    class="flex-1 px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="saveEditEquipment"
                    class="flex-1 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                >
                    Save Changes
                </button>
            </div>
        </div>
    </div>

    <!-- Add Document Modal -->
    <div v-if="showAddDocumentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 sticky top-0 bg-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Add Document</h3>
                    <button @click="closeAddDocumentModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Input Fields -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Document Name</label>
                        <input
                            v-model="newDocument.name"
                            type="text"
                            placeholder="Enter document name"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Upload File</label>
                        <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-[#0c8ce9] transition-colors cursor-pointer" @click="triggerDocumentFileInput" @drop="handleDocumentDrop" @dragover.prevent @dragenter.prevent>
                            <input
                                ref="documentFileInput"
                                type="file"
                                @change="handleDocumentFileUpload"
                                class="hidden"
                                accept=".pdf"
                            />
                            <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                            </svg>
                            <p class="text-sm font-semibold text-gray-700">Drag and drop your file here or click to browse</p>
                            <p class="text-xs text-gray-600 mt-1">PDF files only</p>
                            <div v-if="newDocument.file" class="mt-3">
                                <p class="text-sm text-green-600 font-semibold">✓ {{ newDocument.file.name }}</p>
                            </div>
                        </div>
                    </div>

                    <button
                        @click="addDocument"
                        class="w-full px-4 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                    >
                        Add Document
                    </button>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex gap-3 sticky bottom-0 bg-white">
                <button
                    @click="closeAddDocumentModal"
                    class="flex-1 px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, h } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'
import Swal from 'sweetalert2'

const auth = useAuthStore()
const route = useRoute()
const router = useRouter()

const projectId = ref(route.params.id)
const showHRRequestModal = ref(false)
const showAddWorkforceModal = ref(false)
const showAddMaterialModal = ref(false)
const showAddEquipmentModal = ref(false)
const showAddMilestoneModal = ref(false)
const showAddDocumentModal = ref(false)
const showNoticeDrawer = ref(false)
const noticeToProceedFile = ref(null)
const submittingApproval = ref(false)
const showEditWorkforceModal = ref(false)
const showEditMaterialsModal = ref(false)
const showEditEquipmentModal = ref(false)
const loading = ref(true)
const error = ref(null)

const documentFileInput = ref(null)

const editWorkforceList = ref([])
const editMaterialsList = ref([])
const editEquipmentList = ref([])

const newWorkforce = ref({
    role: '',
    customRole: '',
    headcount: 1
})

const tempWorkforceList = ref([])

const newMilestone = ref({
    name: '',
    start_date: '',
    end_date: ''
})

const newDocument = ref({
    name: '',
    file: null
})

const newMaterial = ref({
    name: '',
    customName: '',
    quantity: '',
    unit: '',
    brand: ''
})

const materialUnits = {
    'Cement': 'Bags',
    'Steel Bars': 'pcs',
    'Bricks': 'pcs',
    'Sand': 'kg',
    'Gravel': 'kg',
    'Concrete': 'm3',
    'Paint': 'L'
}

const materialBrandMap = {
    Cement: [
        { name: 'Holcim', cost: 52, available: 400 },
        { name: 'Republic Cement', cost: 50, available: 350 },
        { name: 'Eagle', cost: 48, available: 300 },
        { name: 'Cemex', cost: 55, available: 250 }
    ],
    'Steel Bars': [
        { name: 'SteelAsia', cost: 450, available: 200 },
        { name: 'PhilSteel', cost: 440, available: 180 },
        { name: 'MetalCraft', cost: 460, available: 150 }
    ],
    Bricks: [
        { name: 'RedBrick Co.', cost: 5, available: 5000 },
        { name: 'BrickMaster', cost: 6, available: 4000 },
        { name: 'Classic Bricks', cost: 5.5, available: 3500 }
    ],
    Sand: [
        { name: 'Riverbend', cost: 78, available: 200 },
        { name: 'QuarryPro', cost: 82, available: 180 },
        { name: 'StoneLine', cost: 75, available: 220 }
    ],
    Gravel: [
        { name: 'StoneWorks', cost: 85, available: 300 },
        { name: 'RockSupply', cost: 80, available: 250 },
        { name: 'GravelKing', cost: 90, available: 200 }
    ],
    Concrete: [
        { name: 'ConcreteMax', cost: 4500, available: 50 },
        { name: 'MixPro', cost: 4200, available: 60 },
        { name: 'StrongMix', cost: 4800, available: 40 }
    ],
    Paint: [
        { name: 'Boysen', cost: 550, available: 100 },
        { name: 'Davies', cost: 520, available: 120 },
        { name: 'Pacific Paint', cost: 500, available: 80 }
    ]
}

const tempMaterialsList = ref([])

const newEquipment = ref({
    name: '',
    customName: '',
    quantity: '',
    type: '',
    brand: ''
})

const equipmentTypes = {
    'Excavator': 'Heavy',
    'Concrete Mixer': 'Light',
    'Bulldozer': 'Heavy',
    'Crane': 'Heavy',
    'Compactor': 'Light',
    'Scaffolding': 'Light',
    'Power Tools': 'Light'
}

const equipmentBrandMap = {
    Excavator: [
        { name: 'Caterpillar', cost: 5200, available: 2, condition: 'Good' },
        { name: 'Komatsu', cost: 5100, available: 1, condition: 'Fair' },
        { name: 'Hitachi', cost: 5000, available: 1, condition: 'Good' },
        { name: 'Volvo', cost: 5400, available: 1, condition: 'Excellent' }
    ],
    'Concrete Mixer': [
        { name: 'Honda', cost: 2100, available: 4, condition: 'Good' },
        { name: 'Yanmar', cost: 2000, available: 3, condition: 'Fair' },
        { name: 'Belle', cost: 1900, available: 2, condition: 'Good' }
    ],
    Bulldozer: [
        { name: 'Caterpillar', cost: 8000, available: 1, condition: 'Excellent' },
        { name: 'Komatsu', cost: 7800, available: 2, condition: 'Good' },
        { name: 'John Deere', cost: 7500, available: 1, condition: 'Fair' }
    ],
    Crane: [
        { name: 'Liebherr', cost: 12000, available: 1, condition: 'Excellent' },
        { name: 'Terex', cost: 11500, available: 1, condition: 'Good' },
        { name: 'Manitowoc', cost: 11000, available: 1, condition: 'Good' }
    ],
    Compactor: [
        { name: 'Wacker Neuson', cost: 3500, available: 3, condition: 'Good' },
        { name: 'Bomag', cost: 3200, available: 2, condition: 'Fair' },
        { name: 'Dynapac', cost: 3800, available: 2, condition: 'Excellent' }
    ],
    Scaffolding: [
        { name: 'SafetyFirst', cost: 800, available: 10, condition: 'Good' },
        { name: 'ProScaffold', cost: 750, available: 8, condition: 'Fair' },
        { name: 'BuildRight', cost: 850, available: 6, condition: 'Excellent' }
    ],
    'Power Tools': [
        { name: 'Makita', cost: 350, available: 15, condition: 'Good' },
        { name: 'Bosch', cost: 380, available: 12, condition: 'Excellent' },
        { name: 'DeWalt', cost: 400, available: 10, condition: 'Good' }
    ]
}

const tempEquipmentList = ref([])

const requiredWorkforceRoles = ['supervisor', 'foreman']
const isRequiredWorkforceRole = (role) =>
    requiredWorkforceRoles.includes(role?.toString().trim().toLowerCase())

/* =====================================================
Step Icons
===================================================== */
const ContractIcon = {
    render() {
        return h('svg', { class: 'w-5 h-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' })
        ])
    }
}

const SetupIcon = {
    render() {
        return h('svg', { class: 'w-5 h-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4' })
        ])
    }
}

const ScheduleIcon = {
    render() {
        return h('svg', { class: 'w-5 h-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' })
        ])
    }
}

const FinancialIcon = {
    render() {
        return h('svg', { class: 'w-5 h-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })
        ])
    }
}

const DocumentIcon = {
    render() {
        return h('svg', { class: 'w-5 h-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z' })
        ])
    }
}

const SummaryIcon = {
    render() {
        return h('svg', { class: 'w-5 h-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' })
        ])
    }
}

const steps = [
    { key: 'contract', label: 'Contract', icon: ContractIcon },
    { key: 'project-setup', label: 'Project Setup', icon: SetupIcon },
    { key: 'schedule', label: 'Schedule', icon: ScheduleIcon },
    { key: 'financial', label: 'Financial', icon: FinancialIcon },
    { key: 'documents', label: 'Documents', icon: DocumentIcon },
    { key: 'summary', label: 'Summary', icon: SummaryIcon }
]

const activeStep = ref(0)
const maxStepReached = ref(0)

const project = ref({
    project_name: '',
    project_code: '',
    project_address: '',
    project_type: '',
    client_name: '',
    total_budget: 0
})

const contract = ref({
    contract_number: '',
    contract_date: new Date().toISOString().split('T')[0],
    contract_amount: 0,
    contract_period: 0,
    contract_period_unit: 'days',
    terms_conditions: '',
    notice_of_award_url: '',
    notice_of_award_name: ''
})

const setupData = ref({
    workers: [],
    materials: [],
    equipment: [],
    schedule: {
        start_date: '',
        end_date: '',
        duration: 0,
        milestones: []
    },
    financial: {
        total_budget: 0,
        retention: 0,
        materials_cost: 0,
        equipment_cost: 0,
        labor_cost: 0,
        subcontractor_cost: 0,
        miscellaneous_cost: 0,
        estimated_total: 0
    },
    documents: []
})

const availableHRRequests = ref([
    { id: 1, position: 'Site Engineer', headcount: 2, status: 'Filled', requestNumber: 'HR-REC-001' },
    { id: 2, position: 'Safety Officer', headcount: 1, status: 'Filled', requestNumber: 'HR-REC-002' },
    { id: 3, position: 'Foreman', headcount: 3, status: 'In Progress', requestNumber: 'HR-REC-003' },
    { id: 4, position: 'Laborers', headcount: 20, status: 'In Progress', requestNumber: 'HR-REC-004' }
])

const calculateDuration = computed(() => {
    const start = new Date(setupData.value.schedule.start_date)
    const end = new Date(setupData.value.schedule.end_date)
    const diffTime = end - start
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays
})

const contractPeriodInDays = computed(() => {
    const period = contract.value.contract_period || 0
    const unit = contract.value.contract_period_unit || 'days'
    
    switch (unit) {
        case 'months':
            return period * 30
        case 'years':
            return period * 365
        case 'days':
        default:
            return period
    }
})

const documentsComplete = computed(() => {
    return setupData.value.documents.every(doc => doc.status === 'Uploaded')
})

/* =====================================================
Computed Properties for Brand Selection
===================================================== */
const selectedMaterial = computed(() => {
    if (!newMaterial.value.name || newMaterial.value.name === 'Others') return null
    return newMaterial.value.name
})

const selectedMaterialBrands = computed(() => {
    if (!selectedMaterial.value) return []
    return materialBrandMap[selectedMaterial.value] || []
})

const selectedMaterialBrand = computed(() => {
    return selectedMaterialBrands.value.find(
        brand => brand.name === newMaterial.value.brand
    ) || null
})

const selectedEquipment = computed(() => {
    if (!newEquipment.value.name || newEquipment.value.name === 'Others') return null
    return newEquipment.value.name
})

const selectedEquipmentBrands = computed(() => {
    if (!selectedEquipment.value) return []
    return equipmentBrandMap[selectedEquipment.value] || []
})

const selectedEquipmentBrand = computed(() => {
    return selectedEquipmentBrands.value.find(
        brand => brand.name === newEquipment.value.brand
    ) || null
})

/* =====================================================
Computed Properties for Financial Breakdown
===================================================== */
const materialsCost = computed(() => {
    return setupData.value.materials.reduce((total, material) => {
        return total + (material.cost || 0) * (material.quantity || 0)
    }, 0)
})

const equipmentCost = computed(() => {
    return setupData.value.equipment.reduce((total, equipment) => {
        return total + (equipment.cost || 0) * (equipment.quantity || 0)
    }, 0)
})

const estimatedTotal = computed(() => {
    const total = 
        Number(materialsCost.value || 0) +
        Number(equipmentCost.value || 0) +
        Number(setupData.value.financial.labor_cost || 0) +
        Number(setupData.value.financial.subcontractor_cost || 0) +
        Number(setupData.value.financial.miscellaneous_cost || 0)
    return total
})

const totalBudgetUtilized = computed(() => {
    return estimatedTotal.value
})

const budgetUtilizationPercentage = computed(() => {
    if (setupData.value.financial.total_budget === 0) return 0
    return (totalBudgetUtilized.value / setupData.value.financial.total_budget) * 100
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

const showToast = ({
    icon = 'info',
    title = '',
    html = '',
    position = 'top-end',
    timer = 3000
}) => {
    Swal.fire({
        toast: true,
        position,
        icon,
        title,
        html,
        showConfirmButton: false,
        timer
    })
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

const validateEndDate = () => {
    const startDate = new Date(setupData.value.schedule.start_date)
    const endDate = new Date(setupData.value.schedule.end_date)
    
    if (endDate < startDate) {
        showToast({ icon: 'warning', html: '<b>End date</b> must be after <b>start date</b>.' })
        setupData.value.schedule.end_date = setupData.value.schedule.start_date
    }
}

const validateStartDate = () => {
    const startDate = new Date(setupData.value.schedule.start_date)
    const endDate = new Date(setupData.value.schedule.end_date)
    
    if (startDate > endDate) {
        showToast({ icon: 'warning', html: '<b>Start date</b> must be before <b>end date</b>.' })
        setupData.value.schedule.start_date = setupData.value.schedule.end_date
    }
}

const goToStep = (index) => {
    if (index < activeStep.value) {
        activeStep.value = index
        return
    }

    if (index === activeStep.value) return

    if (index <= maxStepReached.value) {
        activeStep.value = index
    }
}

const nextStep = () => {
    if (activeStep.value < steps.length - 1) {
        activeStep.value++
        if (activeStep.value > maxStepReached.value) {
            maxStepReached.value = activeStep.value
        }
    }
}

const previousStep = () => {
    if (activeStep.value > 0) {
        activeStep.value--
    }
}

const getStepStatus = (index) => {
    if (index < maxStepReached.value) return 'completed'
    if (index === activeStep.value) return 'current'
    return 'upcoming'
}

const getStepIcon = (step, index) => {
    return getStepStatus(index) === 'completed'
        ? h('svg', { class: 'w-5 h-5', fill: 'currentColor', viewBox: '0 0 20 20' }, [h('path', { 'fill-rule': 'evenodd', d: 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z', 'clip-rule': 'evenodd' })])
        : h(step.icon)
}

const getIconClass = (index) => {
    return getStepStatus(index) === 'upcoming'
        ? 'text-gray-400'
        : 'text-white'
}

const getStepCircleClass = (index) => {
    if (index < activeStep.value) {
        return 'bg-gradient-to-r from-[#0C8CE9] to-[#0EA5E9] text-white shadow-md scale-105'
    } else if (index === activeStep.value) {
        return 'bg-white border-4 border-[#0C8CE9] text-[#0C8CE9] shadow-lg scale-110'
    } else {
        return 'bg-gray-200 text-gray-400'
    }
}

const checkStepRequirements = (stepIndex) => {
    // Contract: require contract number, amount, and period
    if (stepIndex === 0) {
        if (!contract.value.contract_number?.toString().trim()) {
            showToast({ icon: 'warning', html: 'Please enter a <b>contract number</b>.' })
            return false
        }
        if (!contract.value.contract_amount) {
            showToast({ icon: 'warning', html: 'Please enter a <b>contract amount</b>.' })
            return false
        }
        if (!contract.value.contract_period || contract.value.contract_period <= 0) {
            showToast({ icon: 'warning', html: 'Please enter a <b>contract period</b>.' })
            return false
        }
    }

    // Project Setup: require workers, materials, equipment
    if (stepIndex === 1) {
        if (setupData.value.workers.length === 0) {
            showToast({ icon: 'warning', html: 'Please add at least one <b>worker</b> to the project.' })
            return false
        }
        if (setupData.value.materials.length === 0) {
            showToast({ icon: 'warning', html: 'Please add at least one <b>material</b> to the project.' })
            return false
        }
        if (setupData.value.equipment.length === 0) {
            showToast({ icon: 'warning', html: 'Please add at least one <b>equipment</b> to the project.' })
            return false
        }
    }

    // Schedule: require milestones
    if (stepIndex === 2) {
        if (setupData.value.schedule.milestones.length === 0) {
            showToast({ icon: 'warning', html: 'Please add at least one <b>milestone</b> to the schedule.' })
            return false
        }
    }

    // Documents: require all documents to be uploaded
    if (stepIndex === 4) {
        if (!documentsComplete.value) {
            showToast({ icon: 'warning', html: 'Please upload all <b>required documents</b>.' })
            return false
        }
    }

    return true
}

const handleNextStep = () => {
    if (checkStepRequirements(activeStep.value)) {
        nextStep()
        window.scrollTo(0, 0)
    }
}

const handlePreviousStep = () => {
    previousStep()
    window.scrollTo(0, 0)
}

const addWorkerToProject = (worker) => {
    const exists = setupData.value.workers.some(w => w.id === worker.id)
    if (!exists) {
        setupData.value.workers.push({
            id: worker.id,
            position: worker.position,
            headcount: worker.headcount,
            status: worker.status
        })
    }
    showHRRequestModal.value = false
}

const removeWorker = (id) => {
    const worker = setupData.value.workers.find(w => w.id === id)
    if (worker && isRequiredWorkforceRole(worker.position)) {
        showToast({ icon: 'warning', html: '<b>Supervisor</b> and <b>Foreman</b> are required roles and cannot be removed.' })
        return
    }

    setupData.value.workers = setupData.value.workers.filter(w => w.id !== id)
}

const removeMaterial = (id) => {
    setupData.value.materials = setupData.value.materials.filter(m => m.id !== id)
}

const removeEquipment = (id) => {
    setupData.value.equipment = setupData.value.equipment.filter(e => e.id !== id)
}

const openEditWorkforceModal = () => {
    editWorkforceList.value = JSON.parse(JSON.stringify(setupData.value.workers))
    showEditWorkforceModal.value = true
}

const closeEditWorkforceModal = () => {
    showEditWorkforceModal.value = false
    editWorkforceList.value = []
}

const saveEditWorkforce = () => {
    const invalidRequired = editWorkforceList.value.find(
        worker => isRequiredWorkforceRole(worker.position) && Number(worker.headcount) <= 0
    )

    if (invalidRequired) {
        showToast({ icon: 'warning', html: '<b>Supervisor</b> and <b>Foreman</b> must have a headcount of at least <b>1</b>.' })
        return
    }

    setupData.value.workers = JSON.parse(JSON.stringify(editWorkforceList.value))
    closeEditWorkforceModal()
}

const openEditMaterialsModal = () => {
    editMaterialsList.value = JSON.parse(JSON.stringify(setupData.value.materials))
    showEditMaterialsModal.value = true
}

const closeEditMaterialsModal = () => {
    showEditMaterialsModal.value = false
    editMaterialsList.value = []
}

const saveEditMaterials = () => {
    setupData.value.materials = JSON.parse(JSON.stringify(editMaterialsList.value))
    closeEditMaterialsModal()
}

const openEditEquipmentModal = () => {
    editEquipmentList.value = JSON.parse(JSON.stringify(setupData.value.equipment))
    showEditEquipmentModal.value = true
}

const closeEditEquipmentModal = () => {
    showEditEquipmentModal.value = false
    editEquipmentList.value = []
}

const saveEditEquipment = () => {
    setupData.value.equipment = JSON.parse(JSON.stringify(editEquipmentList.value))
    closeEditEquipmentModal()
}

const removeMilestone = (id) => {
    setupData.value.schedule.milestones = setupData.value.schedule.milestones.filter(m => m.id !== id)
}

const triggerFileInput = (docId) => {
    const input = document.querySelector(`input[data-doc-id="${docId}"]`)
    if (input) {
        input.click()
    }
}

const uploadDocument = (docId, event) => {
    const file = event.target.files[0]
    if (file) {
        const doc = setupData.value.documents.find(d => d.id === docId)
        if (doc) {
            doc.file = file
            doc.status = 'Uploaded'
        }
    }
}

const approveAndSend = () => {
    showToast({ icon: 'success', title: 'Success!', html: 'Project setup sent for approval. The project will be assigned to the operations team.', timer: 2000 })
    setTimeout(() => {
        router.push('/projects')
    }, 2000)
}

const generateNoticeToProceeedFileName = () => {
    const now = new Date()
    const year = now.getFullYear()
    const month = String(now.getMonth() + 1).padStart(2, '0')
    const day = String(now.getDate()).padStart(2, '0')
    const hours = String(now.getHours()).padStart(2, '0')
    const minutes = String(now.getMinutes()).padStart(2, '0')
    const seconds = String(now.getSeconds()).padStart(2, '0')
    return `ntp_${year}${month}${day}${hours}${minutes}${seconds}`
}

const renameFile = (file, newName) => {
    return new File([file], newName, { type: file.type })
}

const handleNoticeUpload = (event) => {
    const file = event.target.files?.[0]
    if (!file) return

    if (file.type !== 'application/pdf' && !file.name.endsWith('.pdf')) {
        showToast({ icon: 'error', html: 'Please upload a PDF file only.' })
        event.target.value = ''
        return
    }

    const newFileName = generateNoticeToProceeedFileName()
    const renamedFile = renameFile(file, newFileName)
    noticeToProceedFile.value = renamedFile
}

const handleNoticeDrop = (event) => {
    const files = event.dataTransfer.files
    if (files.length === 0) return

    const file = files[0]
    if (file.type !== 'application/pdf' && !file.name.endsWith('.pdf')) {
        showToast({ icon: 'error', html: 'Please upload a PDF file only.' })
        return
    }

    const newFileName = generateNoticeToProceeedFileName()
    const renamedFile = renameFile(file, newFileName)
    noticeToProceedFile.value = renamedFile
}

/**
 * Compile project data for database insertion
 */
const compileProjectData = () => {
    return {
        // Main project table
        project: {
            project_name: project.value.project_name,
            project_code: project.value.project_code,
            project_type: project.value.project_type,
            project_address: project.value.project_address || null,
            project_start_date: setupData.value.schedule.start_date,
            project_end_date: setupData.value.schedule.end_date,
            contract_number: contract.value.contract_number,
            contract_date: contract.value.contract_date,
            contract_amount: parseFloat(contract.value.contract_amount || 0),
            contract_period: contractPeriodInDays.value,
            total_budget: parseFloat(setupData.value.financial.total_budget || 0),
            total_utilized: totalBudgetUtilized.value,
            retention: parseFloat(setupData.value.financial.retention || 0)
        },

        // project_has_workforces table
        workforces: setupData.value.workers.map(worker => ({
            project_id: projectId.value,
            position: worker.position,
            name: worker.name || '',
            headcount: parseInt(worker.headcount || 0),
            status: worker.status || 'Assigned',
            cost: parseFloat(worker.cost || 0)
        })),

        // project_has_materials table
        materials: setupData.value.materials.map(material => ({
            project_id: projectId.value,
            name: material.name,
            brand: material.brand || null,
            quantity: parseInt(material.quantity || 0),
            unit: material.unit,
            cost_per_unit: parseFloat(material.cost || 0),
            available: parseInt(material.available || 0),
            minimum_stock: parseInt(material.minimum || 0)
        })),

        // project_has_equipment table
        equipment: setupData.value.equipment.map(equip => ({
            project_id: projectId.value,
            name: equip.name,
            brand: equip.brand || null,
            type: equip.type,
            quantity: parseInt(equip.quantity || 0),
            cost_per_unit: parseFloat(equip.cost || 0),
            condition: equip.condition || null,
            available: parseInt(equip.available || 0)
        })),

        // project_has_milestones table
        milestones: setupData.value.schedule.milestones.map(milestone => ({
            project_id: projectId.value,
            name: milestone.name,
            start_date: milestone.start_date,
            end_date: milestone.end_date,
            status: 'pending',
            completion_percentage: 0
        })),

        // project_has_documents table
        documents: setupData.value.documents.map(doc => ({
            project_id: projectId.value,
            document_name: doc.name,
            status: doc.status || 'Uploaded',
            uploaded_at: new Date().toISOString(),
            // Existing documents from prebidding (e.g. NOA) have fileUrl
            file_path: doc.fileUrl || null
        })),

        // Financial breakdown
        financial: {
            project_id: projectId.value,
            total_budget: parseFloat(setupData.value.financial.total_budget || 0),
            materials_cost: materialsCost.value,
            equipment_cost: equipmentCost.value,
            labor_cost: parseFloat(setupData.value.financial.labor_cost || 0),
            subcontractor_cost: parseFloat(setupData.value.financial.subcontractor_cost || 0),
            miscellaneous_cost: parseFloat(setupData.value.financial.miscellaneous_cost || 0),
        },
    }
}

const submitForApproval = async () => {
    if (!noticeToProceedFile.value) {
        showToast({ icon: 'warning', title: 'Missing Document', html: 'Please upload the Notice to Proceed document.' })
        return
    }

    submittingApproval.value = true

    try {
        // Compile all project data
        const projectData = compileProjectData()

        const mergedDocuments = [
            ...setupData.value.documents,
            {
                name: noticeToProceedFile.value.name,
                file: noticeToProceedFile.value,
                status: 'Uploaded'
            }
        ]

        projectData.documents = mergedDocuments.map(doc => ({
            project_id: projectId.value,
            document_name: doc.name,
            status: doc.status || 'Uploaded',
            uploaded_at: new Date().toISOString(),
            // Preserve existing file path for prebidding documents (e.g. NOA)
            file_path: doc.fileUrl || null
        }))

        // Create FormData for file uploads
        const formData = new FormData()

        // Add document files
        mergedDocuments.forEach((doc, index) => {
            if (doc.file) {
                formData.append(`documents[${index}]`, doc.file)
            }
        })

        // Add compiled project data as JSON
        formData.append('project_data', JSON.stringify(projectData))

        // API call to save project setup
        const response = await axios.post(`/api/projects/${projectId.value}/setup/submit`, formData, {
            headers: { 
                'Authorization': `Bearer ${auth.token}`,
                'Content-Type': 'multipart/form-data'
            }
        })

        showToast({ icon: 'success', title: 'Submitted for Approval', html: 'Your Notice to Proceed has been submitted successfully.', timer: 2000 })
        setTimeout(() => {
            showNoticeDrawer.value = false
            noticeToProceedFile.value = null
            router.push('/projects')
        }, 2000)
    } catch (error) {
        console.error('Submission error:', error)
        showToast({ icon: 'error', title: 'Submission Failed', html: 'There was an error submitting your document. Please try again.' })
    } finally {
        submittingApproval.value = false
    }
}

const openAddMilestoneModal = () => {
    if (!setupData.value.schedule.start_date || !setupData.value.schedule.end_date) {
        showToast({ 
            icon: 'error', 
            title: 'Project Dates Required', 
            html: 'Please set the project start and end dates before adding milestones.' 
        })
        return
    }
    showAddMilestoneModal.value = true
}

const addMilestone = () => {
    if (!newMilestone.value.name || !newMilestone.value.start_date || !newMilestone.value.end_date) {
        showToast({ icon: 'warning', title: 'Missing Fields', html: 'Please fill in all milestone fields.' })
        return
    }

    const startDate = new Date(newMilestone.value.start_date)
    const endDate = new Date(newMilestone.value.end_date)
    const projectStart = new Date(setupData.value.schedule.start_date)
    const projectEnd = new Date(setupData.value.schedule.end_date)
    
    if (startDate > endDate) {
        showToast({ icon: 'error', title: 'Invalid Dates', html: 'Start date must be before end date.' })
        return
    }

    if (startDate < projectStart || endDate > projectEnd) {
        showToast({ 
            icon: 'error', 
            title: 'Invalid Date Range', 
            html: 'Milestone dates must be within the project start and end dates.' 
        })
        return
    }

    const milestoneId = Math.max(...setupData.value.schedule.milestones.map(m => m.id), 0) + 1
    setupData.value.schedule.milestones.push({
        id: milestoneId,
        name: newMilestone.value.name,
        start_date: newMilestone.value.start_date,
        end_date: newMilestone.value.end_date
    })

    showAddMilestoneModal.value = false
    newMilestone.value = { name: '', start_date: '', end_date: '' }
    
    showToast({ icon: 'success', title: 'Milestone Added', html: 'The milestone has been added to the project timeline.', timer: 1500 })
}

// Add Workforce Modal Methods
const addToWorkforceList = () => {
    if (!newWorkforce.value.role || !newWorkforce.value.headcount) {
        showToast({ icon: 'warning', title: 'Missing Fields', timer: 3000 })
        return
    }

    if (newWorkforce.value.role === 'Others' && !newWorkforce.value.customRole) {
        showToast({ icon: 'warning', title: 'Missing Custom Role', html: 'Please specify a custom role.' })
        return
    }

    const roleName = newWorkforce.value.role === 'Others'
        ? newWorkforce.value.customRole
        : newWorkforce.value.role
    const normalizedRole = roleName?.toString().trim().toLowerCase()
    const existingIndex = tempWorkforceList.value.findIndex(item => {
        const itemRoleName = item.role === 'Others' ? item.customRole : item.role
        return itemRoleName?.toString().trim().toLowerCase() === normalizedRole
    })

    if (existingIndex >= 0) {
        tempWorkforceList.value[existingIndex].headcount =
            Number(tempWorkforceList.value[existingIndex].headcount || 0) +
            Number(newWorkforce.value.headcount || 0)
    } else {
        tempWorkforceList.value.push({
            role: newWorkforce.value.role,
            customRole: newWorkforce.value.customRole,
            headcount: newWorkforce.value.headcount
        })
    }

    // Reset form
    newWorkforce.value = {
        role: '',
        customRole: '',
        headcount: 1
    }

    showToast({ icon: 'success', title: 'Added to List', timer: 2000 })
}

const removeFromWorkforceList = (index) => {
    tempWorkforceList.value.splice(index, 1)
}

const closeAddWorkforceModal = () => {
    showAddWorkforceModal.value = false
    tempWorkforceList.value = []
    newWorkforce.value = {
        role: '',
        customRole: '',
        headcount: 1
    }
}

const submitWorkforceList = () => {
    if (tempWorkforceList.value.length === 0) {
        showToast({ icon: 'warning', title: 'No Workers', html: 'Please add at least one worker to submit.' })
        return
    }

    // Add all temp workers to the main workers list
    tempWorkforceList.value.forEach((worker) => {
        const position = worker.role === 'Others' ? worker.customRole : worker.role
        const normalizedPosition = position?.toString().trim().toLowerCase()
        const existing = setupData.value.workers.find(w =>
            w.position?.toString().trim().toLowerCase() === normalizedPosition
        )

        if (existing) {
            existing.headcount =
                Number(existing.headcount || 0) + Number(worker.headcount || 0)
        } else {
            const workerId = Math.max(...setupData.value.workers.map(w => w.id || 0), 0) + 1
            setupData.value.workers.push({
                id: workerId,
                position,
                name: '',
                headcount: worker.headcount,
                cost: 0,
                status: 'Assigned'
            })
        }
    })

    showToast({ icon: 'success', title: `${tempWorkforceList.value.length} worker(s) added to workforce`, timer: 2000 })

    closeAddWorkforceModal()
}

// Add Material Modal Methods
const addToMaterialList = () => {
    const materialName = newMaterial.value.name === 'Others' ? newMaterial.value.customName : newMaterial.value.name
    
    if (!materialName || !newMaterial.value.quantity || !newMaterial.value.unit) {
        showToast({ icon: 'warning', title: 'Missing Fields', timer: 3000 })
        return
    }

    // Require brand for non-custom materials
    if (newMaterial.value.name !== 'Others' && !newMaterial.value.brand) {
        showToast({ icon: 'warning', title: 'Please select a brand', timer: 3000 })
        return
    }

    tempMaterialsList.value.push({
        name: materialName,
        brand: newMaterial.value.brand || null,
        quantity: newMaterial.value.quantity,
        unit: newMaterial.value.unit,
        cost: selectedMaterialBrand.value?.cost || 0,
        available: selectedMaterialBrand.value?.available || 0
    })

    showToast({ 
        icon: 'success', 
        html: `<b>${materialName}</b>${newMaterial.value.brand ? ` (${newMaterial.value.brand})` : ''} added to list`,
        timer: 2000 
    })

    // Reset form
    newMaterial.value = {
        name: '',
        customName: '',
        quantity: '',
        unit: '',
        brand: ''
    }
}

const updateMaterialUnit = () => {
    if (newMaterial.value.name !== 'Others') {
        newMaterial.value.unit = materialUnits[newMaterial.value.name] || ''
    } else {
        newMaterial.value.unit = ''
    }
    // Reset brand when material changes
    newMaterial.value.brand = ''
}

const removeFromMaterialList = (index) => {
    tempMaterialsList.value.splice(index, 1)
}

const closeAddMaterialModal = () => {
    showAddMaterialModal.value = false
    tempMaterialsList.value = []
    newMaterial.value = {
        name: '',
        customName: '',
        quantity: '',
        unit: '',
        brand: ''
    }
}

const submitMaterialList = () => {
    if (tempMaterialsList.value.length === 0) {
        showToast({ icon: 'warning', title: 'No Materials', html: 'Please add at least one material to submit.' })
        return
    }

    // Add all temp materials to the main materials list
    tempMaterialsList.value.forEach((material, index) => {
        const materialId = Math.max(...setupData.value.materials.map(m => m.id || 0), 0) + index + 1
        setupData.value.materials.push({
            id: materialId,
            name: material.name,
            brand: material.brand,
            quantity: material.quantity,
            unit: material.unit,
            cost: material.cost,
            available: material.available
        })
    })

    showToast({ icon: 'success', title: `${tempMaterialsList.value.length} material(s) added`, timer: 2000 })

    closeAddMaterialModal()
}

// Add Equipment Modal Methods
const addToEquipmentList = () => {
    const equipmentName = newEquipment.value.name === 'Others' ? newEquipment.value.customName : newEquipment.value.name
    
    if (!equipmentName || !newEquipment.value.quantity || !newEquipment.value.type) {
        showToast({ icon: 'warning', title: 'Missing Fields', timer: 3000 })
        return
    }

    // Require brand for non-custom equipment
    if (newEquipment.value.name !== 'Others' && !newEquipment.value.brand) {
        showToast({ icon: 'warning', title: 'Please select a brand', timer: 3000 })
        return
    }

    tempEquipmentList.value.push({
        name: equipmentName,
        brand: newEquipment.value.brand || null,
        quantity: newEquipment.value.quantity,
        type: newEquipment.value.type,
        cost: selectedEquipmentBrand.value?.cost || 0,
        available: selectedEquipmentBrand.value?.available || 0,
        condition: selectedEquipmentBrand.value?.condition || null
    })

    showToast({ 
        icon: 'success', 
        html: `<b>${equipmentName}</b>${newEquipment.value.brand ? ` (${newEquipment.value.brand})` : ''} added to list`,
        timer: 2000 
    })

    // Reset form
    newEquipment.value = {
        name: '',
        customName: '',
        quantity: '',
        type: '',
        brand: ''
    }
}

const updateEquipmentType = () => {
    if (newEquipment.value.name !== 'Others') {
        newEquipment.value.type = equipmentTypes[newEquipment.value.name] || ''
    } else {
        newEquipment.value.type = ''
    }
    // Reset brand when equipment changes
    newEquipment.value.brand = ''
}

const removeFromEquipmentList = (index) => {
    tempEquipmentList.value.splice(index, 1)
}

const closeAddEquipmentModal = () => {
    showAddEquipmentModal.value = false
    tempEquipmentList.value = []
    newEquipment.value = {
        name: '',
        customName: '',
        quantity: '',
        type: '',
        brand: ''
    }
}

const submitEquipmentList = () => {
    if (tempEquipmentList.value.length === 0) {
        showToast({ icon: 'warning', title: 'No Equipment', html: 'Please add at least one equipment to submit.' })
        return
    }

    // Add all temp equipment to the main equipment list
    tempEquipmentList.value.forEach((equipment, index) => {
        const equipmentId = Math.max(...setupData.value.equipment.map(e => e.id || 0), 0) + index + 1
        setupData.value.equipment.push({
            id: equipmentId,
            name: equipment.name,
            brand: equipment.brand,
            quantity: equipment.quantity,
            type: equipment.type,
            cost: equipment.cost,
            available: equipment.available,
            condition: equipment.condition
        })
    })

    showToast({ icon: 'success', title: `${tempEquipmentList.value.length} equipment item(s) added`, timer: 2000 })

    closeAddEquipmentModal()
}

// Add Document Modal Methods
const triggerDocumentFileInput = () => {
    documentFileInput.value?.click()
}

const handleDocumentFileUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        newDocument.value.file = file
    }
}

const handleDocumentDrop = (event) => {
    const files = event.dataTransfer.files
    if (files.length > 0) {
        newDocument.value.file = files[0]
    }
}

const addDocument = () => {
    if (!newDocument.value.name || !newDocument.value.file) {
        showToast({ icon: 'warning', title: 'Missing Fields', html: 'Please enter document name and upload a file', timer: 3000 })
        return
    }

    const docId = Math.max(...setupData.value.documents.map(d => d.id || 0), 0) + 1
    const fileUrl = URL.createObjectURL(newDocument.value.file)
    setupData.value.documents.push({
        id: docId,
        name: newDocument.value.name,
        status: 'Uploaded',
        fileName: newDocument.value.file.name,
        fileUrl: fileUrl,
        // Keep the actual File object so it can be uploaded with the rest
        file: newDocument.value.file
    })

    showToast({ icon: 'success', title: 'Document Added', timer: 2000 })

    closeAddDocumentModal()
}

const closeAddDocumentModal = () => {
    showAddDocumentModal.value = false
    newDocument.value = {
        name: '',
        file: null
    }
}

const removeDocument = (docId) => {
    setupData.value.documents = setupData.value.documents.filter(d => d.id !== docId)
}

const openDocument = (doc) => {
    if (doc.fileUrl) {
        window.open(doc.fileUrl, '_blank')
    }
}

const fetchProjectData = async () => {
    try {
        const response = await axios.get(`/api/prebiddings/${projectId.value}`, {
            headers: {
                Authorization: `Bearer ${auth.token}`
            }
        })

        const prebidding = response.data.prebidding
        const documents = response.data.documents
        
        project.value = {
            project_name: prebidding.project_name || '',
            project_code: prebidding.project_code || '',
            project_address: prebidding.address || '',
            project_type: prebidding.project_type || '',
            client_name: prebidding.client_name || '',
            total_budget: prebidding.financial?.total_budget || 0
        }

        /* ---------------- SCHEDULE (not in API yet) ---------------- */
        setupData.value.schedule.start_date = ''
        setupData.value.schedule.end_date = ''
        setupData.value.schedule.milestones = []

        /* ---------------- WORKERS (from labor planning) ---------------- */
        setupData.value.workers = []

        console.log('Labor data from API:', response.data)

        if (prebidding.team && typeof prebidding.team === 'object') {
            // Extract team members with their names and costs
            setupData.value.workers = Object.entries(prebidding.team)
                .filter(([role, value]) => value) // Filter out empty roles
                .map(([role, value], idx) => ({
                    id: idx + 1,
                    position: role
                        .replace(/_/g, ' ')
                        .replace(/\b\w/g, l => l.toUpperCase()),
                    name: value.name || '',
                    headcount: 1,
                    cost: parseFloat(value.cost || 0),
                    status: 'Assigned'
                }))
        }

        /* ---------------- MATERIALS ---------------- */
        if (Array.isArray(prebidding.materials)) {
            setupData.value.materials = prebidding.materials.map((item, idx) => {
                const matchedBrand = materialBrandMap[item.name]?.find(b => b.name === item.brand);
                return {
                    id: idx + 1,
                    name: item.name,
                    brand: item.brand,
                    quantity: item.quantity,
                    unit: item.unit,
                    cost: item.cost || matchedBrand?.cost || 0,
                    available: item.available || matchedBrand?.available || 0
                }
            })
        }

        /* ---------------- EQUIPMENT ---------------- */
        if (Array.isArray(prebidding.equipment)) {
            setupData.value.equipment = prebidding.equipment.map((item, idx) => {
                const matchedBrand = equipmentBrandMap[item.name]?.find(b => b.name === item.brand);
                return {
                    id: idx + 1,
                    name: item.name,
                    brand: item.brand,
                    condition: item.condition,
                    quantity: item.quantity,
                    type: item.type,
                    cost: item.cost || matchedBrand?.cost || 0,
                    available: item.available || matchedBrand?.available || 0
                }
            })
        }

        /* ---------------- FINANCIAL ---------------- */
        if (prebidding.financial) {
            setupData.value.financial.total_budget =
                prebidding.financial.total_budget || 0

            setupData.value.financial.retention =
                prebidding.financial.retention || 0

            setupData.value.financial.materials_cost =
                prebidding.financial.materials_cost || 0
            setupData.value.financial.equipment_cost =
                prebidding.financial.equipment_cost || 0
            setupData.value.financial.labor_cost =
                prebidding.financial.labor_cost || 0
            setupData.value.financial.subcontractor_cost =
                prebidding.financial.subcontractor_cost || 0
            setupData.value.financial.miscellaneous_cost =
                prebidding.financial.miscellaneous_cost || 0

            const totalUtilized =
                setupData.value.financial.materials_cost +
                setupData.value.financial.equipment_cost +
                setupData.value.financial.labor_cost +
                setupData.value.financial.subcontractor_cost +
                setupData.value.financial.miscellaneous_cost

            setupData.value.financial.estimated_total =
                prebidding.financial.total_budget_utilized || totalUtilized
        }

        /* ---------------- SUBCONTRACTORS ---------------- */
        if (Array.isArray(prebidding.subcontractors)) {
            setupData.value.financial.subcontractor_cost =
                prebidding.subcontractors.reduce(
                    (sum, item) => sum + (item.cost || 0),
                    0
                )
        }

        /* ---------------- NOTICE OF AWARD ---------------- */
        if (documents?.uploaded) {
            const noaDoc = documents.uploaded.find(doc =>
                doc.name?.startsWith('noa_')
            )

            if (noaDoc) {
                contract.value.notice_of_award_url = noaDoc.fileUrl
                contract.value.notice_of_award_name = noaDoc.name
            }
        }

        /* ---------------- DOCUMENTS ---------------- */
        if (documents?.uploaded) {
            setupData.value.documents = documents.uploaded.map((doc, idx) => ({
                id: idx + 1,
                name: doc.name,
                status: 'Uploaded',
                fileName: doc.name,
                fileUrl: doc.fileUrl
            }))
        }

        loading.value = false
    } catch (err) {
        loading.value = false
        error.value = err.message || 'Failed to load project data'
        showToast({ icon: 'error', title: 'Error', html: error.value })
    }
}


onMounted(() => {
    auth.pageTitle = 'Project Setup & Contract'
    fetchProjectData()
})
</script>
