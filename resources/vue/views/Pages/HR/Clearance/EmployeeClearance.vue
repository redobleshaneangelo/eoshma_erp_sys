<template>
    <auth-layout />
        <div class="ps-60 bg-[#F3F4F6] min-h-screen">
            <div class="px-10 py-10">
                    <!-- Page Header -->
                    <div class="mb-8 flex items-center justify-between">
                        <div class="flex items-center gap-3 ms-auto">
                            <router-link
                                to="/employee-clearance/upcoming-exits"
                                class="px-4 py-2.5 bg-purple-600 text-white rounded-lg font-semibold text-sm hover:bg-purple-700 transition-colors flex items-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Upcoming Exits
                            </router-link>
                            <button
                                v-if="canInitiateExit"
                                @click="openInitiateExitModal"
                                class="px-6 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition-colors flex items-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Initiate Exit
                            </button>
                        </div>
                    </div>

                    <!-- Analytics Cards -->
                    <div class="grid grid-cols-5 gap-4 mb-8">
                        <div class="bg-white rounded-lg p-4 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Total Exiting</p>
                            <p class="text-2xl font-bold text-[#333333]">{{ allClearances.length }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Pending Finance</p>
                            <p class="text-2xl font-bold text-orange-600">{{ pendingFinance }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Pending HR</p>
                            <p class="text-2xl font-bold text-blue-600">{{ pendingHRClearance }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Offboarding</p>
                            <p class="text-2xl font-bold text-purple-600">{{ pendingOffboarding }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Cleared</p>
                            <p class="text-2xl font-bold text-green-600">{{ clearedCount }}</p>
                        </div>
                    </div>

                    <!-- Tab Navigation -->
                    <div class="bg-white border-b border-gray-200 rounded-t-lg mb-0">
                        <nav class="flex gap-0 px-6 py-0 overflow-x-auto">
                            <button
                                @click="activeTab = 'exiting'"
                                :class="activeTab === 'exiting' ? 'border-[#0c8ce9] text-[#0c8ce9]' : 'border-transparent text-gray-600 hover:text-gray-900'"
                                class="flex items-center gap-2 px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Exiting Employees
                            </button>
                            <button
                                v-if="canReviewFinance"
                                @click="activeTab = 'finance'"
                                :class="activeTab === 'finance' ? 'border-[#0c8ce9] text-[#0c8ce9]' : 'border-transparent text-gray-600 hover:text-gray-900'"
                                class="flex items-center gap-2 px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Finance Review
                            </button>
                            <button
                                v-if="canClearHR"
                                @click="activeTab = 'hr'"
                                :class="activeTab === 'hr' ? 'border-[#0c8ce9] text-[#0c8ce9]' : 'border-transparent text-gray-600 hover:text-gray-900'"
                                class="flex items-center gap-2 px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                                HR Clearance
                            </button>
                            <button
                                v-if="canOffboard"
                                @click="activeTab = 'offboarding'"
                                :class="activeTab === 'offboarding' ? 'border-[#0c8ce9] text-[#0c8ce9]' : 'border-transparent text-gray-600 hover:text-gray-900'"
                                class="flex items-center gap-2 px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                                Offboarding
                            </button>
                        </nav>
                    </div>

                    <!-- Tab 1: Exiting Employees -->
                    <div v-if="activeTab === 'exiting'" class="space-y-6">
                        <!-- Filters -->
                        <div class="bg-white pt-0 mb-0">
                            <div class="p-6">
                                <div class="grid grid-cols-5 gap-4">
                                <input
                                    v-model="filters.search"
                                    type="text"
                                    placeholder="Search by name/ID..."
                                    class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                                />
                                <select v-model="filters.department" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]">
                                    <option value="">All Departments</option>
                                    <option value="IT">IT</option>
                                    <option value="HR">HR</option>
                                    <option value="Finance">Finance</option>
                                    <option value="Operations">Operations</option>
                                </select>
                                <select v-model="filters.status" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]">
                                    <option value="">All Status</option>
                                    <option value="Exit Filed">Exit Filed</option>
                                    <option value="Attendance Review">Attendance Review</option>
                                    <option value="Final Pay">Final Pay</option>
                                    <option value="Finance Review">Finance Review</option>
                                    <option value="HR Clearance">HR Clearance</option>
                                    <option value="Cleared">Cleared</option>
                                </select>
                                <input
                                    v-model="filters.exitDateFrom"
                                    type="date"
                                    class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                                />
                                    <button @click="clearFilters" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-200">
                                        Clear
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Exiting Employees Table -->
                        <div class="bg-white rounded-b-lg overflow-hidden p-6">
                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Employee</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Department</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Exit Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Supervisor</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="clearance in filteredClearances" :key="clearance.id" class="border-b border-gray-200 hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm">
                                                <div class="font-semibold text-gray-900">{{ clearance.employeeName }}</div>
                                                <div class="text-xs text-gray-500">{{ clearance.employeeId }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-700">{{ clearance.department }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-700">{{ formatDate(clearance.exitDate) }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-700">{{ clearance.supervisor }}</td>
                                            <td class="px-6 py-4 text-sm">
                                                <span :class="getStatusBadgeClass(clearance.status)" class="px-2 py-1 rounded-full text-xs font-semibold">
                                                    {{ clearance.status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <button @click="viewClearance(clearance)" class="text-[#0c8ce9] hover:text-blue-700 font-medium text-xs">
                                                    View
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-if="filteredClearances.length === 0" class="px-6 py-8 text-center text-gray-500 text-sm">
                                No exiting employees found
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Finance Review -->
                    <div v-if="activeTab === 'finance'" class="space-y-6">
                        <div class="bg-white rounded-b-lg overflow-hidden p-6">
                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Employee</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Payroll Period</th>
                                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Total Payable</th>
                                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Deductions</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="fin in financeReviews" :key="fin.id" class="border-b border-gray-200 hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ fin.employeeName }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-700">{{ fin.payrollPeriod }}</td>
                                            <td class="px-6 py-4 text-sm text-right text-gray-900 font-semibold">₱ {{ formatCurrency(fin.totalPayable) }}</td>
                                            <td class="px-6 py-4 text-sm text-right text-gray-700">₱ {{ formatCurrency(fin.deductions) }}</td>
                                            <td class="px-6 py-4 text-sm">
                                                <span :class="getStatusBadgeClass(fin.status)" class="px-2 py-1 rounded-full text-xs font-semibold">
                                                    {{ fin.status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <button v-if="fin.status === 'Pending'" @click="approveFinance(fin)" class="text-green-600 hover:text-green-700 font-medium text-xs mr-2">
                                                    Approve
                                                </button>
                                                <button v-if="fin.status === 'Pending'" @click="rejectFinance(fin)" class="text-red-600 hover:text-red-700 font-medium text-xs">
                                                    Reject
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-if="financeReviews.length === 0" class="px-6 py-8 text-center text-gray-500 text-sm">
                                No pending finance reviews
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: HR Clearance -->
                    <div v-if="activeTab === 'hr'" class="space-y-6">
                        <div class="bg-white rounded-b-lg overflow-hidden p-6">
                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Employee</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Department</th>
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Checklist %</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Exit Interview</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="hr in hrClearances" :key="hr.id" class="border-b border-gray-200 hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ hr.employeeName }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-700">{{ hr.department }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="w-16 h-2 bg-gray-200 rounded-full mx-auto overflow-hidden">
                                                    <div class="h-full bg-green-500" :style="{ width: hr.checklistPercent + '%' }"></div>
                                                </div>
                                                <span class="text-xs text-gray-600">{{ hr.checklistPercent }}%</span>
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <span :class="hr.exitInterview ? 'text-green-600 font-semibold' : 'text-orange-600'">
                                                    <span v-if="hr.exitInterview" class="inline-flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                                                        Completed
                                                    </span>
                                                    <span v-else class="inline-flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                                                        Pending
                                                    </span>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <span :class="getStatusBadgeClass(hr.status)" class="px-2 py-1 rounded-full text-xs font-semibold">
                                                    {{ hr.status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <button @click="viewChecklistModal(hr)" class="text-[#0c8ce9] hover:text-blue-700 font-medium text-xs mr-2">
                                                    Checklist
                                                </button>
                                                <button
                                                    v-if="hr.status === 'HR Clearance' && hr.checklistPercent === 100 && hr.exitInterview"
                                                    @click="markCleared(hr)"
                                                    class="text-green-600 hover:text-green-700 font-medium text-xs"
                                                >
                                                    Clear
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-if="hrClearances.length === 0" class="px-6 py-8 text-center text-gray-500 text-sm">
                                No HR clearances pending
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: Offboarding -->
                    <div v-if="activeTab === 'offboarding'" class="space-y-6">
                        <div class="bg-white rounded-b-lg overflow-hidden p-6">
                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Employee</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Department</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Offboarding Date</th>
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Access Removed</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="offboard in offboardingList" :key="offboard.id" class="border-b border-gray-200 hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ offboard.employeeName }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-700">{{ offboard.department }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-700">{{ formatDate(offboard.offboardingDate) }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <span :class="offboard.accessRemoved ? 'text-green-600 font-semibold' : 'text-red-600'">
                                                    <span v-if="offboard.accessRemoved" class="inline-flex items-center gap-1 text-green-600 font-semibold">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                                                        Yes
                                                    </span>
                                                    <span v-else class="inline-flex items-center gap-1 text-red-600">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg>
                                                        No
                                                    </span>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <span :class="getStatusBadgeClass(offboard.status)" class="px-2 py-1 rounded-full text-xs font-semibold">
                                                    {{ offboard.status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <button
                                                    v-if="!offboard.accessRemoved"
                                                    @click="removeSystemAccess(offboard)"
                                                    class="text-red-600 hover:text-red-700 font-medium text-xs mr-2"
                                                >
                                                    Remove Access
                                                </button>
                                                <button @click="generateOffboardingReport(offboard)" class="text-[#0c8ce9] hover:text-blue-700 font-medium text-xs">
                                                    Report
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-if="offboardingList.length === 0" class="px-6 py-8 text-center text-gray-500 text-sm">
                                No offboarding items pending
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Initiate Exit Modal -->
            <div v-if="showInitiateModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                    <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-[#333333]">Initiate Employee Exit</h2>
                        <button @click="showInitiateModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Employee Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Employee *</label>
                            <select v-model="exitForm.employeeId" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]">
                                <option value="">Select Employee</option>
                                <option value="EMP-001">John Smith (EMP-001)</option>
                                <option value="EMP-002">Sarah Johnson (EMP-002)</option>
                                <option value="EMP-003">Michael Chen (EMP-003)</option>
                                <option value="EMP-004">Emily Davis (EMP-004)</option>
                                <option value="EMP-005">Robert Wilson (EMP-005)</option>
                            </select>
                        </div>

                        <!-- Exit Reason -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Exit Reason *</label>
                            <select v-model="exitForm.exitReason" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]">
                                <option value="">Select Reason</option>
                                <option value="Resignation">Resignation</option>
                                <option value="Retirement">Retirement</option>
                                <option value="Termination">Termination</option>
                                <option value="Contract End">Contract End</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <!-- Last Working Day -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Last Working Day *</label>
                            <input v-model="exitForm.exitDate" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]" />
                        </div>

                        <!-- Pending Obligations -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pending Obligations / Notes</label>
                            <textarea v-model="exitForm.notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]" placeholder="List any pending tasks or notes..."></textarea>
                        </div>
                    </div>

                    <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
                        <button @click="showInitiateModal = false" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-200">
                            Cancel
                        </button>
                        <button @click="submitInitiateExit" class="px-6 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-medium text-sm hover:bg-blue-700">
                            Initiate Exit
                        </button>
                    </div>
                </div>
            </div>

            <!-- Checklist Modal -->
            <div v-if="showChecklistModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                    <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-[#333333]">HR Clearance Checklist</h2>
                        <button @click="showChecklistModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div v-for="(item, idx) in selectedChecklist" :key="idx" class="flex items-start gap-4 pb-4 border-b border-gray-200">
                            <input
                                v-model="item.completed"
                                type="checkbox"
                                @change="updateChecklistItem(idx)"
                                class="mt-1 w-5 h-5 text-green-600 rounded focus:ring-2 focus:ring-green-500 cursor-pointer"
                            />
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">{{ item.name }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ item.description }}</p>
                            </div>
                            <span v-if="item.completed" class="inline-flex items-center gap-1 text-green-600 text-sm font-semibold">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                                Done
                            </span>
                        </div>
                    </div>

                    <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
                        <button @click="showChecklistModal = false" class="px-6 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-medium text-sm hover:bg-blue-700">
                            Close
                        </button>
                    </div>
                </div>
            </div>

            <div class="fixed bottom-0 right-0 p-2 z-50">
                <img :src="agnes" class="h-auto w-auto" v-if="loading" />
            </div>
    
</template>

<script setup>
    import { ref, computed, onMounted } from 'vue'
    import { useRouter } from 'vue-router'
    import Swal from 'sweetalert2'
    import { useAuthStore } from '@/stores/auth'
    import agnes from '@/assets/agnes.gif'

    const auth = useAuthStore()
    const router = useRouter()

    const loading = ref(false)
    const activeTab = ref('exiting')
    const showInitiateModal = ref(false)
    const showChecklistModal = ref(false)

    // Role-based permissions
    const canInitiateExit = computed(() => ['hr_manager', 'manager'].includes(auth.role))
    const canReviewFinance = computed(() => ['hr_manager', 'finance'].includes(auth.role))
    const canClearHR = computed(() => ['hr_manager'].includes(auth.role))
    const canOffboard = computed(() => ['hr_manager', 'it_admin'].includes(auth.role))

    // Filters
    const filters = ref({
        search: '',
        department: '',
        status: '',
        exitDateFrom: ''
    })

    // Form data
    const exitForm = ref({
        employeeId: '',
        exitReason: '',
        exitDate: new Date().toISOString().split('T')[0],
        notes: ''
    })

    const selectedChecklist = ref([])

    // Mock data - API endpoints
    // GET /api/employee-clearance - fetch all clearances
    // POST /api/employee-clearance - initiate exit
    // GET /api/employee-clearance/{id} - get clearance details
    // PATCH /api/employee-clearance/{id}/progress - progress clearance stage
    // GET /api/employee-clearance/finance-review - get finance reviews
    // PATCH /api/employee-clearance/{id}/finance/approve - approve final pay
    // PATCH /api/employee-clearance/{id}/finance/reject - reject final pay
    // GET /api/employee-clearance/hr-clearance - get hr clearances
    // PATCH /api/employee-clearance/{id}/hr-checklist - update checklist
    // PATCH /api/employee-clearance/{id}/hr/clear - mark as cleared
    // GET /api/employee-clearance/offboarding - get offboarding list
    // PATCH /api/employee-clearance/{id}/offboarding/remove-access - remove system access
    // GET /api/employee-clearance/{id}/offboarding/report - generate report

    const allClearances = ref([
        {
            id: 'CLR-001',
            employeeId: 'EMP-001',
            employeeName: 'John Smith',
            department: 'IT',
            position: 'Senior Developer',
            supervisor: 'Sarah Johnson',
            exitDate: '2026-02-15',
            exitReason: 'Resignation',
            status: 'Finance Review',
            initiatedBy: 'Sarah Johnson',
            initiatedDate: '2026-01-20',
            timeline: [
                { name: 'Exit Filed', completed: true, date: '2026-01-20' },
                { name: 'Attendance Review', completed: true, date: '2026-01-22' },
                { name: 'Final Pay', completed: true, date: '2026-01-25' },
                { name: 'Finance Review', completed: false },
                { name: 'HR Clearance', completed: false },
                { name: 'Offboarding', completed: false }
            ]
        },
        {
            id: 'CLR-002',
            employeeId: 'EMP-002',
            employeeName: 'Sarah Johnson',
            department: 'HR',
            position: 'HR Manager',
            supervisor: 'Michael Chen',
            exitDate: '2026-02-28',
            exitReason: 'Retirement',
            status: 'HR Clearance',
            initiatedBy: 'Michael Chen',
            initiatedDate: '2026-01-15',
            timeline: [
                { name: 'Exit Filed', completed: true, date: '2026-01-15' },
                { name: 'Attendance Review', completed: true, date: '2026-01-17' },
                { name: 'Final Pay', completed: true, date: '2026-01-20' },
                { name: 'Finance Review', completed: true, date: '2026-01-25' },
                { name: 'HR Clearance', completed: false },
                { name: 'Offboarding', completed: false }
            ]
        },
        {
            id: 'CLR-003',
            employeeId: 'EMP-003',
            employeeName: 'Michael Chen',
            department: 'Finance',
            position: 'Finance Officer',
            supervisor: 'Emily Davis',
            exitDate: '2026-03-10',
            exitReason: 'Termination',
            status: 'Exit Filed',
            initiatedBy: 'Emily Davis',
            initiatedDate: '2026-01-28',
            timeline: [
                { name: 'Exit Filed', completed: false },
                { name: 'Attendance Review', completed: false },
                { name: 'Final Pay', completed: false },
                { name: 'Finance Review', completed: false },
                { name: 'HR Clearance', completed: false },
                { name: 'Offboarding', completed: false }
            ]
        }
    ])

    const financeReviews = ref([
        {
            id: 'FIN-001',
            employeeId: 'EMP-001',
            employeeName: 'John Smith',
            payrollPeriod: 'January 2026',
            totalPayable: 85000,
            deductions: 12500,
            status: 'Pending',
            attachments: []
        },
        {
            id: 'FIN-002',
            employeeId: 'EMP-004',
            employeeName: 'Emily Davis',
            payrollPeriod: 'January 2026',
            totalPayable: 95000,
            deductions: 15000,
            status: 'Approved',
            attachments: []
        }
    ])

    const hrClearances = ref([
        {
            id: 'HRC-001',
            employeeId: 'EMP-002',
            employeeName: 'Sarah Johnson',
            department: 'HR',
            position: 'HR Manager',
            status: 'HR Clearance',
            checklistPercent: 75,
            exitInterview: false
        }
    ])

    const offboardingList = ref([
        {
            id: 'OFB-001',
            employeeId: 'EMP-005',
            employeeName: 'Robert Wilson',
            department: 'Operations',
            position: 'Operations Manager',
            offboardingDate: '2026-02-20',
            accessRemoved: false,
            status: 'Pending'
        }
    ])

    // Computed properties
    const filteredClearances = computed(() => {
        return allClearances.value.filter(c => {
            const matchesSearch = filters.value.search === '' ||
                c.employeeName.toLowerCase().includes(filters.value.search.toLowerCase()) ||
                c.employeeId.toLowerCase().includes(filters.value.search.toLowerCase())
            const matchesDept = filters.value.department === '' || c.department === filters.value.department
            const matchesStatus = filters.value.status === '' || c.status === filters.value.status
            const matchesDate = filters.value.exitDateFrom === '' || new Date(c.exitDate) >= new Date(filters.value.exitDateFrom)

            return matchesSearch && matchesDept && matchesStatus && matchesDate
        })
    })

    const pendingFinance = computed(() => financeReviews.value.filter(f => f.status === 'Pending').length)
    const pendingHRClearance = computed(() => hrClearances.value.filter(h => h.status === 'HR Clearance').length)
    const pendingOffboarding = computed(() => offboardingList.value.filter(o => o.status === 'Pending').length)
    const clearedCount = computed(() => allClearances.value.filter(c => c.status === 'Cleared').length)
    const highSeverityComplaints = computed(() => 3)

    // Methods
    const formatDate = (date) => {
        return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
    }

    const formatCurrency = (amount) => {
        return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
    }

    const getStatusBadgeClass = (status) => {
        const classes = {
            'Exit Filed': 'bg-blue-100 text-blue-800',
            'Attendance Review': 'bg-purple-100 text-purple-800',
            'Final Pay': 'bg-yellow-100 text-yellow-800',
            'Finance Review': 'bg-orange-100 text-orange-800',
            'HR Clearance': 'bg-indigo-100 text-indigo-800',
            'Pending': 'bg-yellow-100 text-yellow-800',
            'Approved': 'bg-green-100 text-green-800',
            'Rejected': 'bg-red-100 text-red-800',
            'Cleared': 'bg-green-100 text-green-800',
            'Completed': 'bg-green-100 text-green-800'
        }
        return classes[status] || 'bg-gray-100 text-gray-800'
    }

    const openInitiateExitModal = () => {
        exitForm.value = {
            employeeId: '',
            exitReason: '',
            exitDate: new Date().toISOString().split('T')[0],
            notes: ''
        }
        showInitiateModal.value = true
    }

    const submitInitiateExit = () => {
        if (!exitForm.value.employeeId || !exitForm.value.exitReason || !exitForm.value.exitDate) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill in all required fields',
                confirmButtonColor: '#0c8ce9'
            })
            return
        }

        // API: POST /api/employee-clearance
        const newClearance = {
            id: `CLR-${String(allClearances.value.length + 1).padStart(3, '0')}`,
            employeeId: exitForm.value.employeeId,
            employeeName: 'New Employee',
            department: 'Department',
            exitDate: exitForm.value.exitDate,
            exitReason: exitForm.value.exitReason,
            status: 'Exit Filed',
            initiatedBy: auth.userName || 'Current User',
            initiatedDate: new Date().toISOString().split('T')[0],
            timeline: [
                { name: 'Exit Filed', completed: true, date: new Date().toISOString().split('T')[0] },
                { name: 'Attendance Review', completed: false },
                { name: 'Final Pay', completed: false },
                { name: 'Finance Review', completed: false },
                { name: 'HR Clearance', completed: false },
                { name: 'Offboarding', completed: false }
            ]
        }

        allClearances.value.push(newClearance)

        Swal.fire({
            icon: 'success',
            title: 'Exit Initiated',
            text: `Clearance process initiated for employee. ID: ${newClearance.id}`,
            confirmButtonColor: '#0c8ce9'
        })
        showInitiateModal.value = false
    }

    const viewClearance = (clearance) => {
        router.push(`/employee-clearance/${clearance.id}`)
    }

    const progressClearance = (clearance) => {
        // API: PATCH /api/employee-clearance/{id}/progress
        const idx = allClearances.value.findIndex(c => c.id === clearance.id)
        if (idx !== -1) {
            const nextStatuses = {
                'Exit Filed': 'Attendance Review',
                'Attendance Review': 'Final Pay',
                'Final Pay': 'Finance Review',
                'Finance Review': 'HR Clearance',
                'HR Clearance': 'Cleared'
            }
            allClearances.value[idx].status = nextStatuses[clearance.status] || clearance.status
        }

        Swal.fire({
            icon: 'success',
            title: 'Progress Updated',
            text: 'Clearance has been moved to next stage',
            confirmButtonColor: '#0c8ce9'
        })
    }

    const approveFinance = async (finance) => {
        // API: PATCH /api/employee-clearance/{id}/finance/approve
        const { value: approvalNotes } = await Swal.fire({
            title: 'Approve Final Pay?',
            input: 'textarea',
            inputLabel: 'Approval Notes',
            inputPlaceholder: 'Add notes...',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            confirmButtonText: 'Approve'
        })

        if (approvalNotes !== undefined) {
            const idx = financeReviews.value.findIndex(f => f.id === finance.id)
            if (idx !== -1) {
                financeReviews.value[idx].status = 'Approved'
            }

            Swal.fire({
                icon: 'success',
                title: 'Approved',
                text: 'Final pay approved successfully',
                confirmButtonColor: '#0c8ce9'
            })
        }
    }

    const rejectFinance = async (finance) => {
        // API: PATCH /api/employee-clearance/{id}/finance/reject
        const { value: rejectionReason } = await Swal.fire({
            title: 'Reject Final Pay?',
            input: 'textarea',
            inputLabel: 'Reason for Rejection',
            inputPlaceholder: 'Provide reason...',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Reject',
            inputValidator: (value) => {
                if (!value) return 'Please provide a reason'
            }
        })

        if (rejectionReason) {
            const idx = financeReviews.value.findIndex(f => f.id === finance.id)
            if (idx !== -1) {
                financeReviews.value[idx].status = 'Rejected'
            }

            Swal.fire({
                icon: 'success',
                title: 'Rejected',
                text: 'Final pay rejected. Employee will be notified.',
                confirmButtonColor: '#0c8ce9'
            })
        }
    }

    const viewChecklistModal = (hr) => {
        selectedChecklist.value = [
            { name: 'Return Company ID Card', description: 'Employee returns all identification documents', completed: true },
            { name: 'Return Laptop/Equipment', description: 'Company laptop, phone, and other devices returned', completed: true },
            { name: 'Return Tools & Assets', description: 'All tools and company property returned', completed: false },
            { name: 'Exit Interview Completed', description: 'Formal exit interview conducted with HR', completed: false }
        ]
        showChecklistModal.value = true
    }

    const updateChecklistItem = (idx) => {
        const completedCount = selectedChecklist.value.filter(item => item.completed).length
        const totalItems = selectedChecklist.value.length
        // Update in HR clearance
        const hrIdx = hrClearances.value.findIndex(h => h.id === 'HRC-001')
        if (hrIdx !== -1) {
            hrClearances.value[hrIdx].checklistPercent = Math.round((completedCount / totalItems) * 100)
        }
    }

    const markCleared = (hr) => {
        // API: PATCH /api/employee-clearance/{id}/hr/clear
        Swal.fire({
            title: 'Mark as Cleared?',
            text: 'This will move the employee to offboarding stage',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            confirmButtonText: 'Mark Cleared'
        }).then((result) => {
            if (result.isConfirmed) {
                const idx = hrClearances.value.findIndex(h => h.id === hr.id)
                if (idx !== -1) {
                    hrClearances.value[idx].status = 'Cleared'
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Cleared',
                    text: 'Employee moved to offboarding',
                    confirmButtonColor: '#0c8ce9'
                })
            }
        })
    }

    const removeSystemAccess = async (offboard) => {
        // API: PATCH /api/employee-clearance/{id}/offboarding/remove-access
        const { isConfirmed } = await Swal.fire({
            title: 'Remove System Access?',
            html: 'This will remove:<br>• ERP Login<br>• Email Account<br>• Software Licenses<br>• Physical Access',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Remove Access'
        })

        if (isConfirmed) {
            const idx = offboardingList.value.findIndex(o => o.id === offboard.id)
            if (idx !== -1) {
                offboardingList.value[idx].accessRemoved = true
                offboardingList.value[idx].status = 'Completed'
            }

            Swal.fire({
                icon: 'success',
                title: 'Access Removed',
                text: 'All system access has been revoked',
                confirmButtonColor: '#0c8ce9'
            })
        }
    }

    const generateOffboardingReport = (offboard) => {
        // API: GET /api/employee-clearance/{id}/offboarding/report
        Swal.fire({
            icon: 'info',
            title: 'Generating Report',
            text: `Offboarding report for ${offboard.employeeName} is being generated...`,
            confirmButtonColor: '#0c8ce9'
        })
    }

    const clearFilters = () => {
        filters.value = {
            search: '',
            department: '',
            status: '',
            exitDateFrom: ''
        }
    }

    onMounted(() => {
        // Initialize clearances
        // API: GET /api/employee-clearance
        auth.pageTitle = 'Employee Clearance'
    })
</script>
