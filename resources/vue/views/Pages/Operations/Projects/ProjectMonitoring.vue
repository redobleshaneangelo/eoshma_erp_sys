<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">
            <!-- Back Button & Header -->
            <div class="mb-8">
                <router-link
                    to="/projects"
                    class="inline-flex items-center gap-2 text-[#0c8ce9] font-semibold hover:underline mb-4"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Projects
                </router-link>

                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ project.project_name }}</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ project.project_code }} • Client: {{ project.client_name }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 border border-blue-300">
                            {{ project.progress }}% Complete
                        </span>
                    </div>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="grid grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <div class="mb-2">
                        <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide">Total Budget</p>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">₱{{ formatCurrency(project.total_budget) }}</p>
                    <p class="text-xs text-gray-500 mt-2">Allocated for project</p>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <div class="mb-2">
                        <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide">Duration</p>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ daysUntilCompletion }} days</p>
                    <p class="text-xs text-gray-500 mt-2">Remaining time</p>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <div class="mb-2">
                        <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide">Start Date</p>
                    </div>
                    <p class="text-xl font-bold text-gray-900">{{ formatDate(project.start_date) }}</p>
                    <p class="text-xs text-gray-500 mt-2">Project start</p>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <div class="mb-2">
                        <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide">End Date</p>
                    </div>
                    <p class="text-xl font-bold text-gray-900">{{ formatDate(project.end_date) }}</p>
                    <p class="text-xs text-gray-500 mt-2">Expected completion</p>
                </div>
            </div>

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

                <div class="p-8">
                    <!-- Overview Tab -->
                    <div v-if="activeTab === 'overview'" class="space-y-6">
                        <div class="space-y-6">
                            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                                <h2 class="text-lg font-semibold text-gray-900 mb-6">Project Progress</h2>
                                <div class="mb-6">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-semibold text-gray-700">Overall Progress</span>
                                        <span class="text-lg font-bold text-[#0c8ce9]">{{ project.progress }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-4">
                                        <div class="bg-[#0c8ce9] h-4 rounded-full transition-all duration-500" :style="{ width: project.progress + '%' }"></div>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Current Milestone</h3>
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <p class="text-lg font-bold text-gray-900">{{ project.current_milestone }}</p>
                                        <p class="text-xs text-gray-600 mt-1">Ongoing phase of the project</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                                <h2 class="text-lg font-semibold text-gray-900 mb-6">Project Information</h2>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-2">Project Name</label>
                                        <div class="px-3 py-2 border border-gray-300 bg-gray-50 rounded-lg text-sm text-gray-900">
                                            {{ project.project_name }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-2">Project Code</label>
                                        <div class="px-3 py-2 border border-gray-300 bg-gray-50 rounded-lg text-sm text-gray-900">
                                            {{ project.project_code }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-2">Project Type</label>
                                        <div class="px-3 py-2 border border-gray-300 bg-gray-50 rounded-lg text-sm text-gray-900">
                                            {{ project.project_type || 'N/A' }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-2">Client</label>
                                        <div class="px-3 py-2 border border-gray-300 bg-gray-50 rounded-lg text-sm text-gray-900">
                                            {{ project.client_name }}
                                        </div>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-semibold text-gray-700 mb-2">Site Address</label>
                                        <div class="px-3 py-2 border border-gray-300 bg-gray-50 rounded-lg text-sm text-gray-900">
                                            {{ project.address || 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-semibold text-gray-700 mb-2">Project Notes</label>
                                        <div class="px-3 py-2 border border-gray-300 bg-gray-50 rounded-lg text-sm text-gray-900">
                                            {{ project.notes || 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-6">Project Details</h2>
                            <div class="grid grid-cols-2 gap-6">
                                <div class="border-b border-gray-200 pb-4">
                                    <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide mb-2">Total Budget</p>
                                    <p class="text-base font-semibold text-gray-900">₱{{ formatCurrency(project.total_budget) }}</p>
                                </div>
                                <div class="border-b border-gray-200 pb-4">
                                    <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide mb-2">Project Duration</p>
                                    <p class="text-base font-semibold text-gray-900">{{ daysUntilCompletion }} days</p>
                                </div>
                            </div>

                            <div class="pt-6 border-t border-gray-200">
                                <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide mb-3">Notice of Award</p>
                                <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ project.notice_of_award_file }}</p>
                                        <p class="text-xs text-gray-600">Document available</p>
                                    </div>
                                    <button class="text-[#0c8ce9] hover:text-blue-700 font-semibold text-sm whitespace-nowrap">
                                        Download
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Project Setup Tab -->
                    <div v-if="activeTab === 'project_setup'" class="space-y-8">
                        <!-- Workforce -->
                        <div class="bg-white">
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
                                        @click="showRecruitmentModal = true"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700 rounded-lg transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Recruitment
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
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Allocated</th>
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Minimum</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="(worker, index) in monitoringSetup.workers" :key="index" class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ worker.position }}</td>
                                            <td class="px-6 py-4 text-center text-sm font-semibold text-gray-900">{{ worker.allocated }}</td>
                                            <td class="px-6 py-4 text-center text-sm font-semibold text-gray-900">{{ worker.minimum }}</td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" :class="getStockStatusClasses(getStockStatus(worker))">
                                                    {{ getStockStatus(worker) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr v-if="monitoringSetup.workers.length === 0">
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                                No workers added yet
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Materials -->
                        <div class="bg-white">
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
                                        @click="showProcurementRequestModal = true"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700 rounded-lg transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Request Procurement
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
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Allocated</th>
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Minimum</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Unit</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="(material, index) in monitoringSetup.materials" :key="index" class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ material.name }}</td>
                                            <td class="px-6 py-4 text-center text-sm text-gray-900">{{ material.allocated }}</td>
                                            <td class="px-6 py-4 text-center text-sm text-gray-900">{{ material.minimum }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ material.unit }}</td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" :class="getStockStatusClasses(getStockStatus(material))">
                                                    {{ getStockStatus(material) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr v-if="monitoringSetup.materials.length === 0">
                                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                                No materials added yet
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Equipment -->
                        <div class="bg-white">
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
                                        @click="showEquipmentRequestModal = true"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700 rounded-lg transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Request Procurement
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
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Allocated</th>
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Minimum</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="(equipment, index) in monitoringSetup.equipment" :key="index" class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ equipment.name }}</td>
                                            <td class="px-6 py-4 text-center text-sm text-gray-900">{{ equipment.allocated }}</td>
                                            <td class="px-6 py-4 text-center text-sm text-gray-900">{{ equipment.minimum }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ equipment.type }}</td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" :class="getStockStatusClasses(getStockStatus(equipment))">
                                                    {{ getStockStatus(equipment) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr v-if="monitoringSetup.equipment.length === 0">
                                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                                No equipment added yet
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Tab -->
                    <div v-if="activeTab === 'timeline'" class="space-y-6">
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-6">Project Timeline</h2>
                            <div class="space-y-4">
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-4 h-4 bg-[#0c8ce9] rounded-full"></div>
                                        <div class="w-0.5 h-16 bg-gray-300 my-2"></div>
                                    </div>
                                    <div class="pb-4">
                                        <p class="font-semibold text-gray-900">Project Started</p>
                                        <p class="text-sm text-gray-600">{{ formatDate(project.start_date) }}</p>
                                    </div>
                                </div>

                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-4 h-4 bg-[#0c8ce9] rounded-full"></div>
                                        <div class="w-0.5 h-16 bg-gray-300 my-2"></div>
                                    </div>
                                    <div class="pb-4">
                                        <p class="font-semibold text-gray-900">Currently in Progress</p>
                                        <p class="text-sm text-gray-600">{{ project.current_milestone }} - {{ project.progress }}% complete</p>
                                    </div>
                                </div>

                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-4 h-4 bg-gray-400 rounded-full"></div>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">Expected Completion</p>
                                        <p class="text-sm text-gray-600">{{ formatDate(project.end_date) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Tab -->
                    <div v-if="activeTab === 'schedule'" class="space-y-6">
                        <div class="grid grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                                <div class="px-4 py-2.5 border border-gray-300 bg-gray-50 rounded-lg text-sm text-gray-900">
                                    {{ formatDate(project.start_date) }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                                <div class="px-4 py-2.5 border border-gray-300 bg-gray-50 rounded-lg text-sm text-gray-900">
                                    {{ formatDate(project.end_date) }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Duration</label>
                                <div class="px-4 py-2.5 border border-gray-300 bg-gray-50 rounded-lg text-sm text-gray-900">
                                    {{ daysUntilCompletion }} days
                                </div>
                            </div>
                        </div>

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
                                                    {{ daysUntilCompletion }} days
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Milestone Bars -->
                                        <div v-for="milestone in milestones" :key="milestone.id" class="flex gap-4 items-center">
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

                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-bold text-gray-900">Milestones</h4>
                            </div>

                            <div class="grid grid-cols-3 gap-4 mb-6">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Milestone Name</label>
                                    <input v-model="newMilestone.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Enter milestone" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Start Date</label>
                                    <input v-model="newMilestone.start_date" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">End Date</label>
                                    <input v-model="newMilestone.end_date" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                                </div>
                            </div>
                            <button
                                @click="addMilestone"
                                class="px-4 py-2 bg-[#0c8ce9] text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors"
                            >
                                Add Milestone
                            </button>
                            <p v-if="milestoneError" class="text-xs text-red-600 mt-2">{{ milestoneError }}</p>

                            <div class="space-y-3 mt-6">
                                <div v-if="milestones.length === 0" class="text-center py-8 border border-gray-200 rounded-lg">
                                    <p class="text-sm text-gray-600">No milestones added</p>
                                </div>
                                <div v-for="milestone in milestones" :key="milestone.id" class="border border-gray-200 rounded-lg p-4">
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

                    <!-- Personnel Tab -->
                    <div v-if="activeTab === 'personnel'" class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">Assigned Personnel</h3>
                        </div>
                        <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Role</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Department</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Contact</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="person in personnelList" :key="person.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ person.name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ person.role }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ person.department }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ person.contact }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" :class="getPersonnelStatusClasses(person.status)">
                                                {{ person.status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="personnelList.length === 0">
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            No assigned personnel yet
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Progress Reports Tab -->
                    <div v-if="activeTab === 'progress_reports'" class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">Progress Reports</h3>
                            <button
                                @click="createProgressReport"
                                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700 rounded-lg transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create Report
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div
                                v-for="report in progressReports"
                                :key="report.id"
                                class="w-full bg-white border border-gray-200 rounded-lg p-5 shadow-sm"
                            >
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div>
                                        <p class="text-lg font-semibold text-gray-900">{{ report.title }}</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ formatDate(report.date) }} • {{ report.preparedBy }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Report Period: {{ formatDate(report.period_start) }} - {{ formatDate(report.period_end) }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" :class="getReportStatusClasses(report.status)">
                                        {{ report.status }}
                                    </span>
                                </div>
                                <div class="mt-4 flex items-center justify-end">
                                    <button
                                        @click="viewProgressReport(report)"
                                        class="px-4 py-2 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                                    >
                                        View Report
                                    </button>
                                </div>
                            </div>
                            <div v-if="progressReports.length === 0" class="text-center text-gray-500 py-8">
                                No progress reports yet
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Tab -->
                    <div v-if="activeTab === 'attendance'" class="space-y-6">
                        <div class="bg-white">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Attendance Overview</h3>
                            <div class="grid grid-cols-5 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide">Present Per Day</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ attendanceOverview.presentPerDay }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide">Absent Per Day</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ attendanceOverview.absentPerDay }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide">Late Total</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ attendanceOverview.lateTotal }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide">OT Total</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ attendanceOverview.otTotal }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide">Attendance Rate</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ attendanceOverview.attendanceRate }}%</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Daily Attendance</h3>
                                <button
                                    @click="recordAttendance"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700 rounded-lg transition-colors"
                                >
                                    Record Attendance +
                                </button>
                            </div>
                            <div class="space-y-3">
                                <div
                                    v-for="entry in attendanceDaily"
                                    :key="entry.id"
                                    class="flex items-center justify-between border border-gray-200 rounded-lg p-4"
                                >
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ entry.label }}</p>
                                        <p class="text-xs text-gray-500">{{ formatDate(entry.date) }}</p>
                                    </div>
                                    <button
                                        @click="openAttendanceDay(entry)"
                                        class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                                    >
                                        Open
                                    </button>
                                </div>
                                <div v-if="attendanceDaily.length === 0" class="text-center text-sm text-gray-500 py-6">
                                    No attendance records yet
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Tab -->
                    <div v-if="activeTab === 'financial'" class="space-y-6">
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Budget Overview</h3>
                                <button class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700 rounded-lg transition-colors">
                                    Budget Adjustment Request +
                                </button>
                            </div>
                            <p class="text-sm text-gray-700 mb-3">Total Budget: ₱{{ formatCurrency(financialSummary.totalBudget) }}</p>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-linear-to-r from-blue-700 via-sky-600 to-sky-400 h-2 rounded-full" :style="{ width: financialSummary.budgetProgress + '%' }"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">{{ financialSummary.budgetProgress }}% of Budget Set</p>

                            <div class="mt-6">
                                <h4 class="text-sm font-bold text-gray-900 mb-3">Category Breakdown</h4>
                                <div class="space-y-4">
                                    <div v-for="category in financialCategories" :key="category.name">
                                        <p class="text-xs font-semibold text-gray-700 mb-2">{{ category.name }}</p>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-linear-to-r from-blue-600 to-sky-500 h-2 rounded-full" :style="{ width: category.progress + '%' }"></div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">₱{{ formatCurrency(category.spent) }} out of ₱{{ formatCurrency(category.budget) }} Spent</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Budget Breakdown</h3>
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Category</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700">Approved Budget (₱)</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700">Committed Cost (₱)</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700">Actual Spent (₱)</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Description</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700">Remaining (₱)</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="row in budgetBreakdown" :key="row.category" class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ row.category }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700 text-right">{{ formatCurrency(row.approved) }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700 text-right">{{ formatCurrency(row.committed) }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700 text-right">{{ formatCurrency(row.spent) }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ row.description }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700 text-right">{{ row.remaining }}</td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" :class="getBudgetStatusClasses(row.status)">
                                                    {{ row.status }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr v-if="budgetBreakdown.length === 0">
                                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                                No budget breakdown available
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Tab -->
                    <div v-if="activeTab === 'documents'" class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">Project Documents</h3>
                            <button
                                @click="showMonitoringAddDocumentModal = true"
                                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700 rounded-lg transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Document
                            </button>
                        </div>

                        <div v-if="monitoringDocuments.length === 0" class="p-6 bg-gray-50 border border-gray-200 rounded-lg text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm font-semibold text-gray-700">No documents added yet</p>
                            <p class="text-xs text-gray-600 mt-1">Click "Add Document" to upload project documents</p>
                        </div>

                        <div v-else class="grid grid-cols-1 gap-4">
                            <div v-for="doc in monitoringDocuments" :key="doc.id" class="border border-gray-300 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-gray-600 shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900">{{ doc.name }}</p>
                                        <p class="text-xs text-gray-600">{{ doc.status }}</p>
                                    </div>
                                    <button
                                        v-if="doc.status === 'Uploaded'"
                                        @click="viewMonitoringDocument(doc)"
                                        class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                                    >
                                        View
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Scope Changes Tab -->
                    <div v-if="activeTab === 'scope_changes'" class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">Scope Changes</h3>
                        </div>
                        <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Change ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Impact</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="change in scopeChanges" :key="change.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ change.id }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ formatDate(change.date) }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ change.description }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">₱{{ formatCurrency(change.impact) }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" :class="getScopeStatusClasses(change.status)">
                                                {{ change.status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="scopeChanges.length === 0">
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            No scope changes recorded
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Request Procurement Modal -->
    <div v-if="showProcurementRequestModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 sticky top-0 bg-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Request Procurement</h3>
                    <button @click="closeProcurementRequestModal" class="text-gray-400 hover:text-gray-600">
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
                            v-model="procurementMaterial.name"
                            @change="updateProcurementUnit"
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

                    <div v-if="procurementMaterial.name === 'Others'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Custom Material</label>
                        <input
                            v-model="procurementMaterial.customName"
                            type="text"
                            placeholder="Enter custom material name"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                        <input
                            v-model.number="procurementMaterial.quantity"
                            type="number"
                            placeholder="0"
                            min="1"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>

                    <div v-if="procurementMaterial.name !== 'Others'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Unit</label>
                        <input
                            v-model="procurementMaterial.unit"
                            type="text"
                            readonly
                            class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 rounded-lg text-sm text-gray-600"
                        />
                    </div>

                    <div v-if="procurementMaterial.name === 'Others'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Unit</label>
                        <input
                            v-model="procurementMaterial.unit"
                            type="text"
                            placeholder="Enter unit"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>

                    <button
                        @click="addToProcurementList"
                        class="w-full px-4 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                    >
                        Add to List
                    </button>
                </div>

                <!-- Temporary List -->
                <div class="border-t pt-6">
                    <h4 class="text-sm font-bold text-gray-900 mb-3">Temporary Material List</h4>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Material</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Quantity</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Unit</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-if="procurementTempMaterials.length === 0">
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                                        No materials added yet. Add materials above to get started.
                                    </td>
                                </tr>
                                <tr v-for="(material, idx) in procurementTempMaterials" :key="idx" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ material.name }}</td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-900">{{ material.quantity }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ material.unit }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <button
                                            @click="removeFromProcurementList(idx)"
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
                    @click="closeProcurementRequestModal"
                    class="flex-1 px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="submitProcurementRequest"
                    :disabled="procurementTempMaterials.length === 0"
                    :class="[
                        'flex-1 px-4 py-2 rounded-lg font-semibold transition-colors',
                        procurementTempMaterials.length === 0
                            ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                            : 'bg-green-600 text-white hover:bg-green-700'
                    ]"
                >
                    Send Request
                </button>
            </div>
        </div>
    </div>

    <!-- Request Equipment Modal -->
    <div v-if="showEquipmentRequestModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 sticky top-0 bg-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Request Equipment</h3>
                    <button @click="closeEquipmentRequestModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Equipment Name</label>
                        <select
                            v-model="procurementEquipment.name"
                            @change="updateEquipmentRequestType"
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

                    <div v-if="procurementEquipment.name === 'Others'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Custom Equipment</label>
                        <input
                            v-model="procurementEquipment.customName"
                            type="text"
                            placeholder="Enter custom equipment name"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                        <input
                            v-model.number="procurementEquipment.quantity"
                            type="number"
                            placeholder="0"
                            min="1"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>

                    <div v-if="procurementEquipment.name !== 'Others'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                        <input
                            v-model="procurementEquipment.type"
                            type="text"
                            readonly
                            class="w-full px-4 py-2.5 border border-gray-300 bg-gray-100 rounded-lg text-sm text-gray-600"
                        />
                    </div>

                    <div v-if="procurementEquipment.name === 'Others'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                        <select
                            v-model="procurementEquipment.type"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="">Select type</option>
                            <option value="Heavy">Heavy</option>
                            <option value="Light">Light</option>
                            <option value="Specialized">Specialized</option>
                        </select>
                    </div>

                    <button
                        @click="addToEquipmentRequestList"
                        class="w-full px-4 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                    >
                        Add to List
                    </button>
                </div>

                <div class="border-t pt-6">
                    <h4 class="text-sm font-bold text-gray-900 mb-3">Temporary Equipment List</h4>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Equipment</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Quantity</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Type</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-if="procurementTempEquipment.length === 0">
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                                        No equipment added yet. Add equipment above to get started.
                                    </td>
                                </tr>
                                <tr v-for="(equipment, idx) in procurementTempEquipment" :key="idx" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ equipment.name }}</td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-900">{{ equipment.quantity }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ equipment.type }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <button
                                            @click="removeFromEquipmentRequestList(idx)"
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
                    @click="closeEquipmentRequestModal"
                    class="flex-1 px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="submitEquipmentRequest"
                    :disabled="procurementTempEquipment.length === 0"
                    :class="[
                        'flex-1 px-4 py-2 rounded-lg font-semibold transition-colors',
                        procurementTempEquipment.length === 0
                            ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                            : 'bg-green-600 text-white hover:bg-green-700'
                    ]"
                >
                    Send Request
                </button>
            </div>
        </div>
    </div>

    <!-- Add Workforce Modal -->
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

                    <div v-if="newWorkforce.role === 'Others'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Custom Role</label>
                        <input
                            v-model="newWorkforce.customRole"
                            type="text"
                            placeholder="Enter custom role"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Allocated</label>
                            <input
                                v-model.number="newWorkforce.allocated"
                                type="number"
                                min="0"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Minimum</label>
                            <input
                                v-model.number="newWorkforce.minimum"
                                type="number"
                                min="0"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                            />
                        </div>
                    </div>

                    <button
                        @click="addToWorkforceList"
                        class="w-full px-4 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                    >
                        Add to List
                    </button>
                </div>

                <div class="border-t pt-6">
                    <h4 class="text-sm font-bold text-gray-900 mb-3">Temporary Workforce List</h4>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Position</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Allocated</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Minimum</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-if="tempWorkforceList.length === 0">
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                                        No workers added yet. Add a worker above to get started.
                                    </td>
                                </tr>
                                <tr v-for="(worker, idx) in tempWorkforceList" :key="idx" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ worker.position }}</td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-900">{{ worker.allocated }}</td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-900">{{ worker.minimum }}</td>
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

    <!-- Recruitment Modal -->
    <div v-if="showRecruitmentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 sticky top-0 bg-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Recruitment Request</h3>
                    <button @click="closeRecruitmentModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Position/Role</label>
                        <select
                            v-model="newRecruitment.role"
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

                    <div v-if="newRecruitment.role === 'Others'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Custom Role</label>
                        <input
                            v-model="newRecruitment.customRole"
                            type="text"
                            placeholder="Enter custom role"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Needed</label>
                        <input
                            v-model.number="newRecruitment.needed"
                            type="number"
                            min="1"
                            placeholder="0"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>

                    <button
                        @click="addToRecruitmentList"
                        class="w-full px-4 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                    >
                        Add to List
                    </button>
                </div>

                <div class="border-t pt-6">
                    <h4 class="text-sm font-bold text-gray-900 mb-3">Temporary Recruitment List</h4>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Position</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Needed</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-if="tempRecruitmentList.length === 0">
                                    <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                                        No roles added yet. Add a role above to get started.
                                    </td>
                                </tr>
                                <tr v-for="(role, idx) in tempRecruitmentList" :key="idx" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ role.position }}</td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-900">{{ role.needed }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <button
                                            @click="removeFromRecruitmentList(idx)"
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
                    @click="closeRecruitmentModal"
                    class="flex-1 px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="submitRecruitmentRequest"
                    :disabled="tempRecruitmentList.length === 0"
                    :class="[
                        'flex-1 px-4 py-2 rounded-lg font-semibold transition-colors',
                        tempRecruitmentList.length === 0
                            ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                            : 'bg-green-600 text-white hover:bg-green-700'
                    ]"
                >
                    Send Request
                </button>
            </div>
        </div>
    </div>

    <!-- Add Document Modal -->
    <div v-if="showMonitoringAddDocumentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Add Document</h3>
                    <button @click="closeMonitoringDocumentModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Document Name</label>
                    <input
                        v-model="newMonitoringDocument.name"
                        type="text"
                        placeholder="Enter document name"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                    />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Upload File (PDF)</label>
                    <input
                        @change="handleMonitoringFileChange"
                        type="file"
                        accept=".pdf"
                        class="w-full text-sm"
                    />
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex gap-3">
                <button
                    @click="closeMonitoringDocumentModal"
                    class="flex-1 px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="saveMonitoringDocument"
                    class="flex-1 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                >
                    Add Document
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

                    <div v-if="newMaterial.name === 'Others'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Custom Material</label>
                        <input
                            v-model="newMaterial.customName"
                            type="text"
                            placeholder="Enter custom material name"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Allocated</label>
                            <input
                                v-model.number="newMaterial.allocated"
                                type="number"
                                min="0"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Minimum</label>
                            <input
                                v-model.number="newMaterial.minimum"
                                type="number"
                                min="0"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                            />
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

                <div class="border-t pt-6">
                    <h4 class="text-sm font-bold text-gray-900 mb-3">Temporary Material List</h4>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Material</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Allocated</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Minimum</th>
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
                                    <td class="px-4 py-3 text-center text-sm text-gray-900">{{ material.allocated }}</td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-900">{{ material.minimum }}</td>
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

                    <div v-if="newEquipment.name === 'Others'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Custom Equipment</label>
                        <input
                            v-model="newEquipment.customName"
                            type="text"
                            placeholder="Enter custom equipment name"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Allocated</label>
                            <input
                                v-model.number="newEquipment.allocated"
                                type="number"
                                min="0"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Minimum</label>
                            <input
                                v-model.number="newEquipment.minimum"
                                type="number"
                                min="0"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                            />
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

                <div class="border-t pt-6">
                    <h4 class="text-sm font-bold text-gray-900 mb-3">Temporary Equipment List</h4>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Equipment</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Allocated</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Minimum</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Type</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-if="tempEquipmentList.length === 0">
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                        No equipment added yet. Add equipment above to get started.
                                    </td>
                                </tr>
                                <tr v-for="(equipment, idx) in tempEquipmentList" :key="idx" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ equipment.name }}</td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-900">{{ equipment.allocated }}</td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-900">{{ equipment.minimum }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ equipment.type }}</td>
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
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Allocated</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Minimum</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-if="editWorkforceList.length === 0">
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                                    No workers available to edit.
                                </td>
                            </tr>
                            <tr v-for="(worker, idx) in editWorkforceList" :key="idx" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <input v-model="worker.position" type="text" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" />
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-900">
                                    <input v-model.number="worker.allocated" type="number" min="0" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm text-center" />
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-900">
                                    <input v-model.number="worker.minimum" type="number" min="0" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm text-center" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex gap-3 sticky bottom-0 bg-white">
                <button @click="closeEditWorkforceModal" class="flex-1 px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button @click="saveEditWorkforce" class="flex-1 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
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
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Allocated</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Minimum</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Unit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-if="editMaterialsList.length === 0">
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                                    No materials available to edit.
                                </td>
                            </tr>
                            <tr v-for="(material, idx) in editMaterialsList" :key="idx" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <input v-model="material.name" type="text" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" />
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-900">
                                    <input v-model.number="material.allocated" type="number" min="0" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm text-center" />
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-900">
                                    <input v-model.number="material.minimum" type="number" min="0" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm text-center" />
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <input v-model="material.unit" type="text" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex gap-3 sticky bottom-0 bg-white">
                <button @click="closeEditMaterialsModal" class="flex-1 px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button @click="saveEditMaterials" class="flex-1 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
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
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Allocated</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">Minimum</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Type</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-if="editEquipmentList.length === 0">
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                                    No equipment available to edit.
                                </td>
                            </tr>
                            <tr v-for="(equipment, idx) in editEquipmentList" :key="idx" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <input v-model="equipment.name" type="text" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" />
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-900">
                                    <input v-model.number="equipment.allocated" type="number" min="0" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm text-center" />
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-900">
                                    <input v-model.number="equipment.minimum" type="number" min="0" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm text-center" />
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <input v-model="equipment.type" type="text" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex gap-3 sticky bottom-0 bg-white">
                <button @click="closeEditEquipmentModal" class="flex-1 px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button @click="saveEditEquipment" class="flex-1 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AuthLayout from '@/views/Layouts/AuthLayout.vue'
import Swal from 'sweetalert2'

const route = useRoute()
const router = useRouter()

const activeTab = ref('overview')

const tabs = [
    { key: 'overview', label: 'Overview' },
    { key: 'project_setup', label: 'Project Setup' },
    { key: 'timeline', label: 'Timeline' },
    { key: 'schedule', label: 'Schedule' },
    { key: 'personnel', label: 'Personnel' },
    { key: 'progress_reports', label: 'Progress Reports' },
    { key: 'attendance', label: 'Attendance' },
    { key: 'financial', label: 'Financial' },
    { key: 'documents', label: 'Documents' },
    { key: 'scope_changes', label: 'Scope Changes' }
]

// Mock project data - in real app would be fetched via API
const projects = [
    {
        id: 1,
        project_name: 'Office Building Renovation',
        project_code: 'PRJ-2026-001',
        project_type: 'Renovation',
        client_name: 'ABC Corporation',
        status: 'pending-setup',
        total_budget: 2500000,
        progress: 5,
        start_date: '2026-02-15',
        end_date: '2026-08-15',
        address: 'Makati City, Metro Manila',
        notes: 'Initial scope includes facade upgrades and interior improvements.',
        notice_of_award_file: 'noa_project_001.pdf',
        current_milestone: 'Site Preparation'
    },
    {
        id: 2,
        project_name: 'Infrastructure Development Project',
        project_code: 'PRJ-2026-002',
        project_type: 'Infrastructure',
        client_name: 'XYZ Holdings',
        status: 'in-progress',
        total_budget: 5000000,
        progress: 45,
        start_date: '2026-01-15',
        end_date: '2026-12-31',
        address: 'Cebu City, Cebu',
        notes: 'Roadworks and drainage improvements across two phases.',
        notice_of_award_file: 'noa_project_002.pdf',
        current_milestone: 'Foundation Work'
    },
    {
        id: 3,
        project_name: 'Warehouse Automation System',
        project_code: 'PRJ-2026-003',
        project_type: 'Automation',
        client_name: 'Global Construction Inc',
        status: 'pending-setup',
        total_budget: 1200000,
        progress: 2,
        start_date: '2026-03-01',
        end_date: '2026-09-30',
        address: 'Clark Freeport Zone, Pampanga',
        notes: 'PLC integration and conveyor layout planning.',
        notice_of_award_file: 'noa_project_003.pdf',
        current_milestone: 'Planning Phase'
    },
    {
        id: 4,
        project_name: 'Corporate Training Center Construction',
        project_code: 'PRJ-2026-004',
        project_type: 'Construction',
        client_name: 'Metro Development Corp',
        status: 'in-progress',
        total_budget: 3500000,
        progress: 60,
        start_date: '2026-01-01',
        end_date: '2026-10-30',
        address: 'Bonifacio Global City, Taguig',
        notes: 'Structural framing underway, interior fit-out next.',
        notice_of_award_file: 'noa_project_004.pdf',
        current_milestone: 'Structural Framework'
    },
    {
        id: 5,
        project_name: 'Data Center Expansion',
        project_code: 'PRJ-2026-005',
        project_type: 'Expansion',
        client_name: 'ABC Corporation',
        status: 'completed',
        total_budget: 4200000,
        progress: 100,
        start_date: '2025-08-01',
        end_date: '2026-01-31',
        address: 'Quezon City, Metro Manila',
        notes: 'Completed commissioning and handover.',
        notice_of_award_file: 'noa_project_005.pdf',
        current_milestone: 'Completed'
    }
]

const project = ref(projects.find(p => p.id === parseInt(route.params.id)) || projects[0])

const milestones = ref([
    { id: 1, name: 'Foundation Complete', start_date: '2026-03-15', end_date: '2026-03-20' },
    { id: 2, name: 'Structural Work Complete', start_date: '2026-05-15', end_date: '2026-05-25' },
    { id: 3, name: 'Final Inspection', start_date: '2026-07-01', end_date: '2026-07-15' }
])

const monitoringSetup = ref({
    workers: [
        { position: 'Project Manager', allocated: 1, minimum: 1, pendingProcurement: false },
        { position: 'Site Engineer', allocated: 2, minimum: 2, pendingProcurement: false },
        { position: 'Safety Officer', allocated: 1, minimum: 1, pendingProcurement: false }
    ],
    materials: [
        { name: 'Cement', allocated: 500, minimum: 400, unit: 'Bags', pendingProcurement: false },
        { name: 'Steel Bars', allocated: 100, minimum: 150, unit: 'pcs', pendingProcurement: false }
    ],
    equipment: [
        { name: 'Excavator', allocated: 2, minimum: 2, type: 'Heavy', pendingProcurement: false },
        { name: 'Concrete Mixer', allocated: 1, minimum: 2, type: 'Light', pendingProcurement: false }
    ]
})

const showAddWorkforceModal = ref(false)
const showAddMaterialModal = ref(false)
const showAddEquipmentModal = ref(false)
const showRecruitmentModal = ref(false)

const showEditWorkforceModal = ref(false)
const showEditMaterialsModal = ref(false)
const showEditEquipmentModal = ref(false)

const editWorkforceList = ref([])
const editMaterialsList = ref([])
const editEquipmentList = ref([])

const newWorkforce = ref({
    role: '',
    customRole: '',
    allocated: 1,
    minimum: 1
})

const newRecruitment = ref({
    role: '',
    customRole: '',
    needed: 1
})

const newMaterial = ref({
    name: '',
    customName: '',
    allocated: '',
    minimum: '',
    unit: ''
})

const newEquipment = ref({
    name: '',
    customName: '',
    allocated: '',
    minimum: '',
    type: ''
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

const equipmentTypes = {
    'Excavator': 'Heavy',
    'Concrete Mixer': 'Light',
    'Bulldozer': 'Heavy',
    'Crane': 'Heavy',
    'Compactor': 'Light',
    'Scaffolding': 'Light',
    'Power Tools': 'Light'
}

const tempWorkforceList = ref([])
const tempMaterialsList = ref([])
const tempEquipmentList = ref([])
const tempRecruitmentList = ref([])

const personnelList = ref([
    { id: 1, name: 'John Davis', role: 'Project Manager', department: 'Operations', contact: '0917-123-4567', status: 'Active' },
    { id: 2, name: 'Sarah Anderson', role: 'Lead Engineer', department: 'Engineering', contact: '0918-234-5678', status: 'Active' },
    { id: 3, name: 'Michael Chen', role: 'QA Lead', department: 'Quality', contact: '0919-345-6789', status: 'On Site' },
    { id: 4, name: 'Anna Reyes', role: 'Safety Officer', department: 'Safety', contact: '0920-456-7890', status: 'Active' }
])

const progressReports = ref([
    {
        id: 1,
        title: 'Weekly Progress Report',
        date: '2026-02-03',
        period_start: '2026-01-27',
        period_end: '2026-02-02',
        preparedBy: 'John Davis',
        status: 'Submitted'
    },
    {
        id: 2,
        title: 'Site Inspection Summary',
        date: '2026-01-27',
        period_start: '2026-01-20',
        period_end: '2026-01-26',
        preparedBy: 'Sarah Anderson',
        status: 'Draft'
    }
])

const attendanceOverview = ref({
    presentPerDay: 2.2,
    absentPerDay: 0,
    lateTotal: 13,
    otTotal: 5,
    attendanceRate: 100
})

const attendanceDaily = ref([
    { id: 1, label: 'Day 2 | ID-002', date: '2026-01-13' },
    { id: 2, label: 'Day 1 | ID-001', date: '2026-01-12' }
])

const financialSummary = ref({
    totalBudget: 2000000,
    budgetProgress: 50
})

const financialCategories = ref([
    { name: 'Labor', budget: 300000, spent: 300000, progress: 100 },
    { name: 'Materials', budget: 400000, spent: 0, progress: 5 },
    { name: 'Subcontractors', budget: 200000, spent: 150000, progress: 75 },
    { name: 'Equipment', budget: 50000, spent: 48000, progress: 96 },
    { name: 'Miscellaneous', budget: 50000, spent: 48000, progress: 96 }
])

const budgetBreakdown = ref([
    {
        category: 'Labor',
        approved: 300000,
        committed: 100000,
        spent: 300000,
        description: 'Manpower',
        remaining: 80000,
        status: 'Over Budget'
    },
    {
        category: 'Materials',
        approved: 400000,
        committed: 50000,
        spent: 0,
        description: 'Concrete, steel, etc.',
        remaining: 80000,
        status: 'Approaching Limit'
    },
    {
        category: 'Sub Contractors',
        approved: 200000,
        committed: 80000,
        spent: 150000,
        description: 'Plumbing, electrical',
        remaining: '-',
        status: 'On Track'
    }
])

const monitoringDocuments = ref([
    { id: 1, name: 'Construction Permit', status: 'Uploaded' },
    { id: 2, name: 'Safety Plan', status: 'Pending' }
])

const scopeChanges = ref([
    { id: 'SC-001', date: '2026-02-01', description: 'Additional electrical outlets per floor', impact: 50000, status: 'Approved' },
    { id: 'SC-002', date: '2026-02-03', description: 'Change of flooring material to premium tiles', impact: 120000, status: 'Pending' }
])

const showProcurementRequestModal = ref(false)
const showEquipmentRequestModal = ref(false)
const showMonitoringAddDocumentModal = ref(false)
const procurementMaterial = ref({
    name: '',
    customName: '',
    quantity: '',
    unit: ''
})
const procurementEquipment = ref({
    name: '',
    customName: '',
    quantity: '',
    type: ''
})
const procurementMaterialUnits = {
    'Cement': 'Bags',
    'Steel Bars': 'pcs',
    'Bricks': 'pcs',
    'Sand': 'kg',
    'Gravel': 'kg',
    'Concrete': 'm3',
    'Paint': 'L'
}
const procurementEquipmentTypes = {
    'Excavator': 'Heavy',
    'Concrete Mixer': 'Light',
    'Bulldozer': 'Heavy',
    'Crane': 'Heavy',
    'Compactor': 'Light',
    'Scaffolding': 'Light',
    'Power Tools': 'Light'
}
const procurementTempMaterials = ref([])
const procurementTempEquipment = ref([])

const newMonitoringDocument = ref({
    name: '',
    fileName: ''
})

const milestoneColors = [
    '#5B8FF9',
    '#61DDAA',
    '#65789B',
    '#F6BD16',
    '#F6903D',
    '#9C8ADE',
    '#F08BB4'
]

const newMilestone = ref({
    name: '',
    start_date: '',
    end_date: ''
})

const milestoneError = ref('')

const openEditWorkforceModal = () => {
    editWorkforceList.value = JSON.parse(JSON.stringify(monitoringSetup.value.workers))
    showEditWorkforceModal.value = true
}

const closeEditWorkforceModal = () => {
    showEditWorkforceModal.value = false
    editWorkforceList.value = []
}

const saveEditWorkforce = () => {
    monitoringSetup.value.workers = JSON.parse(JSON.stringify(editWorkforceList.value))
    closeEditWorkforceModal()
}

const openEditMaterialsModal = () => {
    editMaterialsList.value = JSON.parse(JSON.stringify(monitoringSetup.value.materials))
    showEditMaterialsModal.value = true
}

const closeEditMaterialsModal = () => {
    showEditMaterialsModal.value = false
    editMaterialsList.value = []
}

const saveEditMaterials = () => {
    monitoringSetup.value.materials = JSON.parse(JSON.stringify(editMaterialsList.value))
    closeEditMaterialsModal()
}

const openEditEquipmentModal = () => {
    editEquipmentList.value = JSON.parse(JSON.stringify(monitoringSetup.value.equipment))
    showEditEquipmentModal.value = true
}

const closeEditEquipmentModal = () => {
    showEditEquipmentModal.value = false
    editEquipmentList.value = []
}

const saveEditEquipment = () => {
    monitoringSetup.value.equipment = JSON.parse(JSON.stringify(editEquipmentList.value))
    closeEditEquipmentModal()
}

const getPersonnelStatusClasses = (status) => {
    const map = {
        Active: 'bg-green-100 text-green-800',
        'On Site': 'bg-blue-100 text-blue-800',
        Inactive: 'bg-gray-200 text-gray-700'
    }
    return map[status] || 'bg-gray-100 text-gray-700'
}

const getReportStatusClasses = (status) => {
    const map = {
        Submitted: 'bg-green-100 text-green-800',
        Draft: 'bg-yellow-100 text-yellow-800',
        Reviewed: 'bg-blue-100 text-blue-800'
    }
    return map[status] || 'bg-gray-100 text-gray-700'
}

const getBudgetStatusClasses = (status) => {
    const map = {
        'Over Budget': 'bg-red-100 text-red-800',
        'Approaching Limit': 'bg-yellow-100 text-yellow-800',
        'On Track': 'bg-green-100 text-green-800'
    }
    return map[status] || 'bg-gray-100 text-gray-700'
}

const getScopeStatusClasses = (status) => {
    const map = {
        Approved: 'bg-green-100 text-green-800',
        Pending: 'bg-yellow-100 text-yellow-800',
        Rejected: 'bg-red-100 text-red-800'
    }
    return map[status] || 'bg-gray-100 text-gray-700'
}

const createProgressReport = () => {
    Swal.fire({
        toast: true,
        icon: 'info',
        title: 'Create Report clicked',
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000
    })
}

const viewProgressReport = (report) => {
    router.push({
        name: 'project_progress_report',
        params: { id: project.value.id, reportId: report.id }
    })
}

const recordAttendance = () => {
    router.push({ name: 'project_attendance', params: { id: project.value.id } })
}

const openAttendanceDay = (entry) => {
    router.push({
        name: 'project_attendance_day',
        params: { id: project.value.id, dayId: entry.id }
    })
}

const handleMonitoringFileChange = (event) => {
    const file = event.target.files?.[0]
    newMonitoringDocument.value.fileName = file ? file.name : ''
}

const saveMonitoringDocument = () => {
    if (!newMonitoringDocument.value.name) {
        Swal.fire({
            toast: true,
            icon: 'warning',
            title: 'Missing Document Name',
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        })
        return
    }

    const nextId = Math.max(0, ...monitoringDocuments.value.map(doc => doc.id || 0)) + 1
    monitoringDocuments.value.push({
        id: nextId,
        name: newMonitoringDocument.value.name,
        status: newMonitoringDocument.value.fileName ? 'Uploaded' : 'Pending'
    })

    Swal.fire({
        toast: true,
        icon: 'success',
        title: 'Document added',
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000
    })

    closeMonitoringDocumentModal()
}

const closeMonitoringDocumentModal = () => {
    showMonitoringAddDocumentModal.value = false
    newMonitoringDocument.value = {
        name: '',
        fileName: ''
    }
}

const viewMonitoringDocument = (doc) => {
    Swal.fire({
        icon: 'info',
        title: doc.name,
        text: 'Opening document preview...'
    })
}

const addToWorkforceList = () => {
    if (!newWorkforce.value.role || !newWorkforce.value.allocated || !newWorkforce.value.minimum) {
        Swal.fire({
            toast: true,
            icon: 'warning',
            title: 'Missing Fields',
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        })
        return
    }

    if (newWorkforce.value.role === 'Others' && !newWorkforce.value.customRole) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Custom Role',
            text: 'Please specify a custom role.'
        })
        return
    }

    tempWorkforceList.value.push({
        position: newWorkforce.value.role === 'Others' ? newWorkforce.value.customRole : newWorkforce.value.role,
        allocated: newWorkforce.value.allocated,
        minimum: newWorkforce.value.minimum,
        pendingProcurement: false
    })

    newWorkforce.value = {
        role: '',
        customRole: '',
        allocated: 1,
        minimum: 1
    }

    Swal.fire({
        toast: true,
        icon: 'success',
        title: 'Added to List',
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000
    })
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
        allocated: 1,
        minimum: 1
    }
}

const addToRecruitmentList = () => {
    if (!newRecruitment.value.role || !newRecruitment.value.needed) {
        Swal.fire({
            toast: true,
            icon: 'warning',
            title: 'Missing Fields',
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        })
        return
    }

    if (newRecruitment.value.role === 'Others' && !newRecruitment.value.customRole) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Custom Role',
            text: 'Please specify a custom role.'
        })
        return
    }

    tempRecruitmentList.value.push({
        position: newRecruitment.value.role === 'Others' ? newRecruitment.value.customRole : newRecruitment.value.role,
        needed: newRecruitment.value.needed
    })

    newRecruitment.value = {
        role: '',
        customRole: '',
        needed: 1
    }

    Swal.fire({
        toast: true,
        icon: 'success',
        title: 'Added to List',
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000
    })
}

const removeFromRecruitmentList = (index) => {
    tempRecruitmentList.value.splice(index, 1)
}

const closeRecruitmentModal = () => {
    showRecruitmentModal.value = false
    tempRecruitmentList.value = []
    newRecruitment.value = {
        role: '',
        customRole: '',
        needed: 1
    }
}

const submitRecruitmentRequest = () => {
    if (tempRecruitmentList.value.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Roles',
            text: 'Please add at least one role to request.'
        })
        return
    }

    Swal.fire({
        title: 'Send Recruitment Request?',
        text: 'Do you want to send this request to Recruitment?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0c8ce9',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Send'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                toast: true,
                icon: 'success',
                title: 'Sent to recruitment',
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500
            })
            closeRecruitmentModal()
        }
    })
}

const submitWorkforceList = () => {
    if (tempWorkforceList.value.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Workers',
            text: 'Please add at least one worker to submit.'
        })
        return
    }

    tempWorkforceList.value.forEach((worker) => {
        monitoringSetup.value.workers.push({ ...worker })
    })

    Swal.fire({
        toast: true,
        icon: 'success',
        title: `${tempWorkforceList.value.length} worker(s) added`,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000
    })

    closeAddWorkforceModal()
}

const updateMaterialUnit = () => {
    if (newMaterial.value.name !== 'Others') {
        newMaterial.value.unit = materialUnits[newMaterial.value.name] || ''
    } else {
        newMaterial.value.unit = ''
    }
}

const addToMaterialList = () => {
    const materialName = newMaterial.value.name === 'Others' ? newMaterial.value.customName : newMaterial.value.name

    if (!materialName || !newMaterial.value.allocated || !newMaterial.value.minimum || !newMaterial.value.unit) {
        Swal.fire({
            toast: true,
            icon: 'warning',
            title: 'Missing Fields',
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        })
        return
    }

    tempMaterialsList.value.push({
        name: materialName,
        allocated: newMaterial.value.allocated,
        minimum: newMaterial.value.minimum,
        unit: newMaterial.value.unit,
        pendingProcurement: false
    })

    newMaterial.value = {
        name: '',
        customName: '',
        allocated: '',
        minimum: '',
        unit: ''
    }

    Swal.fire({
        toast: true,
        icon: 'success',
        title: 'Added to List',
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000
    })
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
        allocated: '',
        minimum: '',
        unit: ''
    }
}

const submitMaterialList = () => {
    if (tempMaterialsList.value.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Materials',
            text: 'Please add at least one material to submit.'
        })
        return
    }

    tempMaterialsList.value.forEach((material) => {
        monitoringSetup.value.materials.push({ ...material })
    })

    Swal.fire({
        toast: true,
        icon: 'success',
        title: `${tempMaterialsList.value.length} material(s) added`,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000
    })

    closeAddMaterialModal()
}

const updateEquipmentType = () => {
    if (newEquipment.value.name !== 'Others') {
        newEquipment.value.type = equipmentTypes[newEquipment.value.name] || ''
    } else {
        newEquipment.value.type = ''
    }
}

const addToEquipmentList = () => {
    const equipmentName = newEquipment.value.name === 'Others' ? newEquipment.value.customName : newEquipment.value.name

    if (!equipmentName || !newEquipment.value.allocated || !newEquipment.value.minimum || !newEquipment.value.type) {
        Swal.fire({
            toast: true,
            icon: 'warning',
            title: 'Missing Fields',
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        })
        return
    }

    tempEquipmentList.value.push({
        name: equipmentName,
        allocated: newEquipment.value.allocated,
        minimum: newEquipment.value.minimum,
        type: newEquipment.value.type,
        pendingProcurement: false
    })

    newEquipment.value = {
        name: '',
        customName: '',
        allocated: '',
        minimum: '',
        type: ''
    }

    Swal.fire({
        toast: true,
        icon: 'success',
        title: 'Added to List',
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000
    })
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
        allocated: '',
        minimum: '',
        type: ''
    }
}

const submitEquipmentList = () => {
    if (tempEquipmentList.value.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Equipment',
            text: 'Please add at least one equipment to submit.'
        })
        return
    }

    tempEquipmentList.value.forEach((equipment) => {
        monitoringSetup.value.equipment.push({ ...equipment })
    })

    Swal.fire({
        toast: true,
        icon: 'success',
        title: `${tempEquipmentList.value.length} equipment item(s) added`,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000
    })

    closeAddEquipmentModal()
}

const getStockStatus = (item) => {
    if (item.pendingProcurement) return 'Pending Procurement'
    const allocated = Number(item.allocated || 0)
    const minimum = Number(item.minimum || 0)
    if (allocated === 0) return 'No Stocks'
    if (allocated < minimum) return 'Insufficient'
    if (allocated <= minimum * 1.2) return 'Low Stock'
    return 'Sufficient'
}

const getStockStatusClasses = (status) => {
    const map = {
        Sufficient: 'bg-green-100 text-green-800',
        'Low Stock': 'bg-yellow-100 text-yellow-800',
        Insufficient: 'bg-red-100 text-red-800',
        'No Stocks': 'bg-gray-200 text-gray-700',
        'Pending Procurement': 'bg-blue-100 text-blue-800'
    }
    return map[status] || 'bg-gray-100 text-gray-700'
}

const updateProcurementUnit = () => {
    if (procurementMaterial.value.name !== 'Others') {
        procurementMaterial.value.unit = procurementMaterialUnits[procurementMaterial.value.name] || ''
    } else {
        procurementMaterial.value.unit = ''
    }
}

const addToProcurementList = () => {
    const materialName = procurementMaterial.value.name === 'Others'
        ? procurementMaterial.value.customName
        : procurementMaterial.value.name

    if (!materialName || !procurementMaterial.value.quantity || !procurementMaterial.value.unit) {
        Swal.fire({
            toast: true,
            icon: 'warning',
            title: 'Missing Fields',
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        })
        return
    }

    procurementTempMaterials.value.push({
        name: materialName,
        quantity: procurementMaterial.value.quantity,
        unit: procurementMaterial.value.unit
    })

    procurementMaterial.value = {
        name: '',
        customName: '',
        quantity: '',
        unit: ''
    }

    Swal.fire({
        toast: true,
        icon: 'success',
        title: 'Added to List',
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000
    })
}

const removeFromProcurementList = (index) => {
    procurementTempMaterials.value.splice(index, 1)
}

const closeProcurementRequestModal = () => {
    showProcurementRequestModal.value = false
    procurementTempMaterials.value = []
    procurementMaterial.value = {
        name: '',
        customName: '',
        quantity: '',
        unit: ''
    }
}

const submitProcurementRequest = () => {
    if (procurementTempMaterials.value.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Materials',
            text: 'Please add at least one material to request.'
        })
        return
    }

    Swal.fire({
        title: 'Send Procurement Request?',
        text: 'Do you want to send this request to Procurement?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0c8ce9',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Send'
    }).then((result) => {
        if (result.isConfirmed) {
            procurementTempMaterials.value.forEach((material) => {
                const match = monitoringSetup.value.materials.find(item => item.name === material.name)
                if (match) {
                    match.pendingProcurement = true
                }
            })
            Swal.fire({
                toast: true,
                icon: 'success',
                title: 'Procurement request sent successfully.',
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500
            })
            closeProcurementRequestModal()
        }
    })
}

const updateEquipmentRequestType = () => {
    if (procurementEquipment.value.name !== 'Others') {
        procurementEquipment.value.type = procurementEquipmentTypes[procurementEquipment.value.name] || ''
    } else {
        procurementEquipment.value.type = ''
    }
}

const addToEquipmentRequestList = () => {
    const equipmentName = procurementEquipment.value.name === 'Others'
        ? procurementEquipment.value.customName
        : procurementEquipment.value.name

    if (!equipmentName || !procurementEquipment.value.quantity || !procurementEquipment.value.type) {
        Swal.fire({
            toast: true,
            icon: 'warning',
            title: 'Missing Fields',
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        })
        return
    }

    procurementTempEquipment.value.push({
        name: equipmentName,
        quantity: procurementEquipment.value.quantity,
        type: procurementEquipment.value.type
    })

    procurementEquipment.value = {
        name: '',
        customName: '',
        quantity: '',
        type: ''
    }

    Swal.fire({
        toast: true,
        icon: 'success',
        title: 'Added to List',
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000
    })
}

const removeFromEquipmentRequestList = (index) => {
    procurementTempEquipment.value.splice(index, 1)
}

const closeEquipmentRequestModal = () => {
    showEquipmentRequestModal.value = false
    procurementTempEquipment.value = []
    procurementEquipment.value = {
        name: '',
        customName: '',
        quantity: '',
        type: ''
    }
}

const submitEquipmentRequest = () => {
    if (procurementTempEquipment.value.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Equipment',
            text: 'Please add at least one equipment to request.'
        })
        return
    }

    Swal.fire({
        title: 'Send Equipment Request?',
        text: 'Do you want to send this equipment request to Procurement?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0c8ce9',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Send'
    }).then((result) => {
        if (result.isConfirmed) {
            procurementTempEquipment.value.forEach((equipment) => {
                const match = monitoringSetup.value.equipment.find(item => item.name === equipment.name)
                if (match) {
                    match.pendingProcurement = true
                }
            })
            Swal.fire({
                toast: true,
                icon: 'success',
                title: 'Equipment request sent successfully.',
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500
            })
            closeEquipmentRequestModal()
        }
    })
}

const daysUntilCompletion = computed(() => {
    const start = new Date(project.value.start_date)
    const end = new Date(project.value.end_date)
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

const toRgba = (hex, alpha) => {
    const normalized = hex.replace('#', '')
    const bigint = parseInt(normalized, 16)
    const r = (bigint >> 16) & 255
    const g = (bigint >> 8) & 255
    const b = bigint & 255
    return `rgba(${r}, ${g}, ${b}, ${alpha})`
}

const getMilestoneColor = (index) => {
    return milestoneColors[index % milestoneColors.length]
}

const getMonthRange = () => {
    const months = []
    const startDate = new Date(project.value.start_date)
    const endDate = new Date(project.value.end_date)

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
    const startDate = new Date(project.value.start_date)
    const endDate = new Date(project.value.end_date)
    const milestone = new Date(milestoneDate)

    const totalDays = (endDate - startDate) / (1000 * 60 * 60 * 24)
    const daysFromStart = (milestone - startDate) / (1000 * 60 * 60 * 24)

    return (daysFromStart / totalDays) * 100
}

const getMilestoneBarWidth = (startDate, endDate) => {
    const projectStart = new Date(project.value.start_date)
    const projectEnd = new Date(project.value.end_date)
    const milestoneStart = new Date(startDate)
    const milestoneEnd = new Date(endDate)

    const totalDays = (projectEnd - projectStart) / (1000 * 60 * 60 * 24)
    const milestoneDays = (milestoneEnd - milestoneStart) / (1000 * 60 * 60 * 24)

    return (milestoneDays / totalDays) * 100
}

const addMilestone = () => {
    const name = newMilestone.value.name.trim()
    if (!name || !newMilestone.value.start_date || !newMilestone.value.end_date) {
        milestoneError.value = 'Please complete the milestone name and dates.'
        return
    }

    if (new Date(newMilestone.value.end_date) < new Date(newMilestone.value.start_date)) {
        milestoneError.value = 'End date must be on or after the start date.'
        return
    }

    milestones.value.push({
        id: Date.now(),
        name,
        start_date: newMilestone.value.start_date,
        end_date: newMilestone.value.end_date
    })

    newMilestone.value = { name: '', start_date: '', end_date: '' }
    milestoneError.value = ''
}
</script>
