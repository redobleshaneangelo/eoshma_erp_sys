<template>
    <div class="w-full bg-[#F3F4F6] min-h-screen">
        <div class="w-full">
            <!-- Tabs Navigation -->
            <div class="bg-white border-b border-gray-200 rounded-t-lg mb-0">
                <nav class="flex gap-0 px-6 py-0 overflow-x-auto">
                    <button
                        v-for="tab in tabs"
                        :key="tab.key"
                        @click="activeTab = tab.key"
                        class="flex items-center gap-2 px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap"
                        :class="activeTab === tab.key
                            ? 'border-[#0c8ce9] text-[#0c8ce9]'
                            : 'border-transparent text-gray-600 hover:text-gray-900'"
                    >
                        <span v-html="tab.icon"></span>
                        {{ tab.label }}
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="bg-white p-6">
                <!-- Employee Management Tab -->
                <div v-if="activeTab === 'employee-mgmt'" class="space-y-6">
                    <!-- Key Metrics -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-linear-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-blue-600 font-medium">Total Employees</p>
                                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ hrMetrics.totalEmployees }}</p>
                                </div>
                                <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <p class="text-xs text-blue-600 mt-3">+5 this month</p>
                        </div>
                        <div class="bg-linear-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-green-600 font-medium">Active Employees</p>
                                    <p class="text-3xl font-bold text-green-900 mt-2">{{ hrMetrics.activeEmployees }}</p>
                                </div>
                                <svg class="w-12 h-12 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-xs text-green-600 mt-3">98% utilization</p>
                        </div>
                        <div class="bg-linear-to-br from-yellow-50 to-yellow-100 border border-yellow-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-yellow-600 font-medium">Pending Reviews</p>
                                    <p class="text-3xl font-bold text-yellow-900 mt-2">{{ hrMetrics.pendingReviews }}</p>
                                </div>
                                <svg class="w-12 h-12 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-xs text-yellow-600 mt-3">Due this week</p>
                        </div>
                        <div class="bg-linear-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-purple-600 font-medium">Turnover Rate</p>
                                    <p class="text-3xl font-bold text-purple-900 mt-2">{{ hrMetrics.turnoverRate }}</p>
                                </div>
                                <svg class="w-12 h-12 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </div>
                            <p class="text-xs text-purple-600 mt-3">YTD</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Employee Trend</h3>
                                <span class="text-xs bg-blue-100 text-blue-800 px-3 py-1 rounded-full">6 Months</span>
                            </div>
                            <canvas id="employeeTrendChart" class="w-full" style="max-height: 280px;"></canvas>
                        </div>
                        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Department Distribution</h3>
                                <span class="text-xs bg-purple-100 text-purple-800 px-3 py-1 rounded-full">{{ hrMetrics.totalEmployees }} Total</span>
                            </div>
                            <canvas id="departmentChart" class="w-full" style="max-height: 280px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Leave & Absence Tab -->
                <div v-if="activeTab === 'leave-absence'" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-linear-to-br from-orange-50 to-orange-100 border border-orange-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-orange-600 font-medium">Leave Requests</p>
                                    <p class="text-3xl font-bold text-orange-900 mt-2">{{ leaveMetrics.totalRequests }}</p>
                                </div>
                                <svg class="w-12 h-12 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <p class="text-xs text-orange-600 mt-3">This month</p>
                        </div>
                        <div class="bg-linear-to-br from-red-50 to-red-100 border border-red-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-red-600 font-medium">Pending Approval</p>
                                    <p class="text-3xl font-bold text-red-900 mt-2">{{ leaveMetrics.pendingApproval }}</p>
                                </div>
                                <svg class="w-12 h-12 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <p class="text-xs text-red-600 mt-3">Require action</p>
                        </div>
                        <div class="bg-linear-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-blue-600 font-medium">Approved</p>
                                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ leaveMetrics.approved }}</p>
                                </div>
                                <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-xs text-blue-600 mt-3">This month</p>
                        </div>
                        <div class="bg-linear-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-green-600 font-medium">Avg Balance</p>
                                    <p class="text-3xl font-bold text-green-900 mt-2">{{ leaveMetrics.avgBalance }}</p>
                                </div>
                                <svg class="w-12 h-12 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </div>
                            <p class="text-xs text-green-600 mt-3">Per employee</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Leave Requests Trend</h3>
                            <span class="text-xs bg-orange-100 text-orange-800 px-3 py-1 rounded-full">This Month</span>
                        </div>
                        <canvas id="leaveChart" class="w-full" style="max-height: 280px;"></canvas>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pending Leave Requests</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-900">Employee</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-900">Leave Type</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-900">Dates</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-900">Days</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-900">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="req in pendingLeaveRequests" :key="req.id" class="border-t border-gray-100 hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ req.employee }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ req.type }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ req.dateRange }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ req.days }}</td>
                                        <td class="px-4 py-3">
                                            <button class="text-green-600 hover:text-green-800 font-medium text-xs mr-2">Approve</button>
                                            <button class="text-red-600 hover:text-red-800 font-medium text-xs">Reject</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Attendance Tab -->
                <div v-if="activeTab === 'attendance'" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-linear-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-green-600 font-medium">Present Today</p>
                                    <p class="text-3xl font-bold text-green-900 mt-2">{{ attendanceMetrics.presentToday }}</p>
                                </div>
                                <svg class="w-12 h-12 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-xs text-green-600 mt-3">{{ attendanceMetrics.presentPercentage }}%</p>
                        </div>
                        <div class="bg-linear-to-br from-red-50 to-red-100 border border-red-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-red-600 font-medium">Absent Today</p>
                                    <p class="text-3xl font-bold text-red-900 mt-2">{{ attendanceMetrics.absentToday }}</p>
                                </div>
                                <svg class="w-12 h-12 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-xs text-red-600 mt-3">{{ attendanceMetrics.absentPercentage }}%</p>
                        </div>
                        <div class="bg-linear-to-br from-yellow-50 to-yellow-100 border border-yellow-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-yellow-600 font-medium">Late Today</p>
                                    <p class="text-3xl font-bold text-yellow-900 mt-2">{{ attendanceMetrics.lateToday }}</p>
                                </div>
                                <svg class="w-12 h-12 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-xs text-yellow-600 mt-3">{{ attendanceMetrics.latePercentage }}%</p>
                        </div>
                        <div class="bg-linear-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-blue-600 font-medium">On Leave</p>
                                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ attendanceMetrics.onLeave }}</p>
                                </div>
                                <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <p class="text-xs text-blue-600 mt-3">Today</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Attendance Trend</h3>
                            <span class="text-xs bg-green-100 text-green-800 px-3 py-1 rounded-full">This Week</span>
                        </div>
                        <canvas id="attendanceChart" class="w-full" style="max-height: 280px;"></canvas>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Frequent Absentees</h3>
                            <div class="space-y-3">
                                <div v-for="emp in frequentAbsentees" :key="emp.id" class="flex items-center justify-between pb-3 border-b border-gray-100 last:border-0">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ emp.name }}</p>
                                        <p class="text-sm text-gray-600">{{ emp.department }}</p>
                                    </div>
                                    <span class="text-xs bg-red-100 text-red-800 px-3 py-1 rounded-full">{{ emp.absences }} days</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Late Arrivals (This Week)</h3>
                            <div class="space-y-3">
                                <div v-for="emp in lateArrivals" :key="emp.id" class="flex items-center justify-between pb-3 border-b border-gray-100 last:border-0">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ emp.name }}</p>
                                        <p class="text-sm text-gray-600">{{ emp.time }}</p>
                                    </div>
                                    <span class="text-xs bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full">{{ emp.minutes }} min</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payroll Tab -->
                <div v-if="activeTab === 'payroll'" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-linear-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-green-600 font-medium">Total Payroll</p>
                                    <p class="text-3xl font-bold text-green-900 mt-2">₱{{ formatNumber(payrollMetrics.totalPayroll) }}</p>
                                </div>
                                <svg class="w-12 h-12 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-xs text-green-600 mt-3">This month</p>
                        </div>
                        <div class="bg-linear-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-blue-600 font-medium">Deductions</p>
                                    <p class="text-3xl font-bold text-blue-900 mt-2">₱{{ formatNumber(payrollMetrics.deductions) }}</p>
                                </div>
                                <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                            </div>
                            <p class="text-xs text-blue-600 mt-3">Total deductions</p>
                        </div>
                        <div class="bg-linear-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-purple-600 font-medium">Bonuses</p>
                                    <p class="text-3xl font-bold text-purple-900 mt-2">₱{{ formatNumber(payrollMetrics.bonuses) }}</p>
                                </div>
                                <svg class="w-12 h-12 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path></svg>
                            </div>
                            <p class="text-xs text-purple-600 mt-3">Paid this month</p>
                        </div>
                        <div class="bg-linear-to-br from-orange-50 to-orange-100 border border-orange-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-orange-600 font-medium">Pending</p>
                                    <p class="text-3xl font-bold text-orange-900 mt-2">{{ payrollMetrics.pending }}</p>
                                </div>
                                <svg class="w-12 h-12 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-xs text-orange-600 mt-3">Approvals needed</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Payroll Trend</h3>
                                <span class="text-xs bg-green-100 text-green-800 px-3 py-1 rounded-full">6 Months</span>
                            </div>
                            <canvas id="payrollTrendChart" class="w-full" style="max-height: 280px;"></canvas>
                        </div>
                        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Salary Distribution</h3>
                                <span class="text-xs bg-purple-100 text-purple-800 px-3 py-1 rounded-full">{{ hrMetrics.totalEmployees }} Employees</span>
                            </div>
                            <canvas id="salaryChart" class="w-full" style="max-height: 280px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recruitment Tab -->
                <div v-if="activeTab === 'recruitment'" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-linear-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-blue-600 font-medium">Open Positions</p>
                                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ recruitmentMetrics.openPositions }}</p>
                                </div>
                                <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                            </div>
                            <p class="text-xs text-blue-600 mt-3">Active postings</p>
                        </div>
                        <div class="bg-linear-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-purple-600 font-medium">Total Candidates</p>
                                    <p class="text-3xl font-bold text-purple-900 mt-2">{{ recruitmentMetrics.totalCandidates }}</p>
                                </div>
                                <svg class="w-12 h-12 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <p class="text-xs text-purple-600 mt-3">In pipeline</p>
                        </div>
                        <div class="bg-linear-to-br from-orange-50 to-orange-100 border border-orange-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-orange-600 font-medium">Avg Time to Hire</p>
                                    <p class="text-3xl font-bold text-orange-900 mt-2">{{ recruitmentMetrics.avgTimeToHire }}</p>
                                </div>
                                <svg class="w-12 h-12 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-xs text-orange-600 mt-3">Days</p>
                        </div>
                        <div class="bg-linear-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-green-600 font-medium">Hired This Month</p>
                                    <p class="text-3xl font-bold text-green-900 mt-2">{{ recruitmentMetrics.hiredThisMonth }}</p>
                                </div>
                                <svg class="w-12 h-12 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-xs text-green-600 mt-3">New employees</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Recruitment Funnel</h3>
                                <span class="text-xs bg-blue-100 text-blue-800 px-3 py-1 rounded-full">Pipeline</span>
                            </div>
                            <canvas id="recruitmentFunnelChart" class="w-full" style="max-height: 280px;"></canvas>
                        </div>
                        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Hiring Trend</h3>
                                <span class="text-xs bg-green-100 text-green-800 px-3 py-1 rounded-full">6 Months</span>
                            </div>
                            <canvas id="hiringTrendChart" class="w-full" style="max-height: 280px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    /* =====================================================
    Core Imports
    ===================================================== */
    import { ref, onMounted, computed, watch, nextTick } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import axios from 'axios'
    import Swal from 'sweetalert2'

    /* =====================================================
    Chart Library
    ===================================================== */
    import Chart from 'chart.js/auto'

    /* =====================================================
    Stores & Layouts
    ===================================================== */
    import { useAuthStore } from '@/stores/auth'

    const auth = useAuthStore()

    /* =====================================================
    Router
    ===================================================== */
    const route = useRoute()
    const router = useRouter()

    /* =====================================================
    UI State
    ===================================================== */
    const loading = ref(false)
    const activeTab = ref('employee-mgmt')
    const chartInstances = ref({
        employeeTrend: null,
        department: null,
        leave: null,
        attendance: null,
        payrollTrend: null,
        salary: null,
        recruitmentFunnel: null,
        hiringTrend: null
    })

    /* =====================================================
    Tabs Configuration
    ===================================================== */
    const tabs = [
        { key: 'employee-mgmt', label: 'Employee Management', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>' },
        { key: 'leave-absence', label: 'Leave & Absence', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>' },
        { key: 'attendance', label: 'Attendance', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' },
        { key: 'payroll', label: 'Payroll', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' },
        { key: 'recruitment', label: 'Recruitment', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>' },
    ]

    /* =====================================================
    Dashboard Data - Employee Management
    ===================================================== */
    const hrMetrics = ref({
        totalEmployees: 245,
        activeEmployees: 240,
        pendingReviews: 12,
        turnoverRate: '2.4%'
    })

    const recentHires = ref([
        { id: 1, name: 'Maria Santos', position: 'Senior Developer', joinDate: '2025-01-15' },
        { id: 2, name: 'Juan Cruz', position: 'HR Specialist', joinDate: '2025-01-10' },
        { id: 3, name: 'Anna Reyes', position: 'Operations Manager', joinDate: '2025-01-08' }
    ])

    const upcomingExits = ref([
        { id: 1, name: 'Robert Garcia', position: 'Finance Manager', exitDate: '2025-02-15' },
        { id: 2, name: 'Sofia Lopez', position: 'QA Engineer', exitDate: '2025-03-01' }
    ])

    const employeeTrendData = ref({
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        totalEmployees: [220, 225, 230, 235, 240, 245],
        activeEmployees: [215, 220, 225, 230, 235, 240]
    })

    const departmentDistribution = ref({
        labels: ['IT', 'HR', 'Finance', 'Operations', 'Sales', 'Marketing'],
        data: [45, 28, 35, 52, 40, 35]
    })

    /* =====================================================
    Dashboard Data - Leave & Absence
    ===================================================== */
    const leaveMetrics = ref({
        totalRequests: 28,
        pendingApproval: 5,
        approved: 18,
        avgBalance: 8.5
    })

    const leaveBreakdown = ref([
        { type: 'Annual Leave', count: 18, percentage: '64.3%' },
        { type: 'Sick Leave', count: 5, percentage: '17.9%' },
        { type: 'Personal Leave', count: 3, percentage: '10.7%' },
        { type: 'Maternity', count: 1, percentage: '3.6%' },
        { type: 'Other', count: 1, percentage: '3.6%' }
    ])

    const pendingLeaveRequests = ref([
        { id: 1, employee: 'John Doe', type: 'Annual Leave', dateRange: '2025-02-03 to 2025-02-07', days: 5 },
        { id: 2, employee: 'Jane Smith', type: 'Sick Leave', dateRange: '2025-02-04', days: 1 },
        { id: 3, employee: 'Mike Johnson', type: 'Personal Leave', dateRange: '2025-02-10 to 2025-02-11', days: 2 },
        { id: 4, employee: 'Sarah Williams', type: 'Annual Leave', dateRange: '2025-02-17 to 2025-02-21', days: 5 },
        { id: 5, employee: 'Robert Brown', type: 'Sick Leave', dateRange: '2025-02-05', days: 1 }
    ])

    const leaveMonthlyData = ref({
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        approved: [4, 6, 5, 3],
        pending: [2, 1, 1, 1],
        rejected: [0, 1, 0, 0]
    })

    /* =====================================================
    Dashboard Data - Attendance
    ===================================================== */
    const attendanceMetrics = ref({
        presentToday: 238,
        presentPercentage: 97.1,
        absentToday: 5,
        absentPercentage: 2.0,
        lateToday: 2,
        latePercentage: 0.8,
        onLeave: 8
    })

    const frequentAbsentees = ref([
        { id: 1, name: 'David Martinez', department: 'Operations', absences: 12 },
        { id: 2, name: 'Lisa Anderson', department: 'IT', absences: 8 },
        { id: 3, name: 'Christopher Lee', department: 'Finance', absences: 6 }
    ])

    const lateArrivals = ref([
        { id: 1, name: 'James Wilson', time: 'Monday, 10:15 AM', minutes: 15 },
        { id: 2, name: 'Patricia Davis', time: 'Wednesday, 10:30 AM', minutes: 30 },
        { id: 3, name: 'Michael Brown', time: 'Thursday, 10:05 AM', minutes: 5 }
    ])

    const attendanceTrendData = ref({
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
        present: [240, 238, 242, 238, 235],
        absent: [3, 5, 2, 5, 8],
        late: [2, 2, 1, 2, 2]
    })

    /* =====================================================
    Dashboard Data - Payroll
    ===================================================== */
    const payrollMetrics = ref({
        totalPayroll: 5750000,
        deductions: 625000,
        bonuses: 450000,
        netPayroll: 5575000,
        pending: 3
    })

    const payrollRuns = ref([
        { id: 1, period: 'January 2025', date: '2025-02-01', status: 'Completed' },
        { id: 2, period: 'December 2024', date: '2025-01-02', status: 'Completed' },
        { id: 3, period: 'November 2024', date: '2024-12-01', status: 'Completed' },
        { id: 4, period: 'October 2024', date: '2024-11-01', status: 'Archived' }
    ])

    const payrollTrendData = ref({
        labels: ['Oct 2024', 'Nov 2024', 'Dec 2024', 'Jan 2025'],
        grossPayroll: [5500000, 5650000, 5700000, 5750000],
        netPayroll: [4850000, 5000000, 5050000, 5575000]
    })

    const salaryDistributionData = ref({
        labels: ['0-50K', '50-100K', '100-150K', '150-200K', '200K+'],
        data: [85, 95, 35, 20, 10]
    })

    /* =====================================================
    Dashboard Data - Recruitment
    ===================================================== */
    const recruitmentMetrics = ref({
        openPositions: 8,
        totalCandidates: 145,
        avgTimeToHire: 28,
        hiredThisMonth: 5
    })

    const openPositions = ref([
        { id: 1, title: 'Senior Developer', department: 'IT', candidates: 24 },
        { id: 2, title: 'HR Manager', department: 'HR', candidates: 12 },
        { id: 3, title: 'Sales Manager', department: 'Sales', candidates: 18 },
        { id: 4, title: 'Operations Analyst', department: 'Operations', candidates: 15 }
    ])

    const recentHiresRecruitment = ref([
        { id: 1, name: 'Maria Santos', position: 'Senior Developer', startDate: '2025-01-15' },
        { id: 2, name: 'Juan Cruz', position: 'HR Specialist', startDate: '2025-01-10' },
        { id: 3, name: 'Anna Reyes', position: 'Operations Manager', startDate: '2025-01-08' }
    ])

    const recruitmentFunnelData = ref({
        labels: ['Applied', 'Screened', 'Interviewed', 'Offered', 'Hired'],
        data: [145, 87, 42, 18, 5]
    })

    const hiringTrendData = ref({
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        hired: [8, 6, 7, 5, 4, 5]
    })

    /* =====================================================
    Helper Functions
    ===================================================== */
    const formatCurrency = (value) => {
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
        }).format(value)
    }

    const formatNumber = (value) => {
        return new Intl.NumberFormat('en-PH', {
            notation: 'compact',
            compactDisplay: 'short'
        }).format(value)
    }

    const showToast = ({ icon = 'info', title = '', html = '', position = 'top-end', timer = 3000 }) => {
        Swal.fire({
            toast: true,
            position,
            icon,
            title,
            html,
            showConfirmButton: false,
            timer,
            customClass: { popup: 'no-wrap-toast' }
        })
    }

    /* =====================================================
    Chart Initialization
    ===================================================== */
    const initEmployeeTrendChart = () => {
        const ctx = document.getElementById('employeeTrendChart')
        if (!ctx) {
            console.warn('employeeTrendChart canvas not found')
            return
        }
        
        try {
            if (chartInstances.value.employeeTrend) {
                chartInstances.value.employeeTrend.destroy()
                chartInstances.value.employeeTrend = null
            }
        } catch (e) {
            console.warn('Error destroying chart:', e)
        }
        
        console.log('Initializing Employee Trend Chart with data:', employeeTrendData.value)
        chartInstances.value.employeeTrend = new Chart(ctx, {
            type: 'line',
            data: {
                labels: employeeTrendData.value.labels,
                datasets: [
                    {
                        label: 'Total Employees',
                        data: employeeTrendData.value.totalEmployees,
                        borderColor: '#0c8ce9',
                        backgroundColor: 'rgba(12, 140, 233, 0.1)',
                        tension: 0.4,
                        borderWidth: 2,
                        fill: true
                    },
                    {
                        label: 'Active Employees',
                        data: employeeTrendData.value.activeEmployees,
                        borderColor: '#a855f7',
                        backgroundColor: 'rgba(168, 85, 247, 0.1)',
                        tension: 0.4,
                        borderWidth: 2,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#E5E7EB' } },
                    x: { grid: { color: '#E5E7EB' } }
                }
            }
        })
        console.log('Employee Trend Chart created:', chartInstances.value.employeeTrend)
    }

    const initDepartmentChart = () => {
        const ctx = document.getElementById('departmentChart')
        if (!ctx) return
        
        try {
            if (chartInstances.value.department) {
                chartInstances.value.department.destroy()
                chartInstances.value.department = null
            }
        } catch (e) {
            console.warn('Error destroying chart:', e)
        }
        
        chartInstances.value.department = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: departmentDistribution.value.labels,
                datasets: [{
                    label: 'Employees',
                    data: departmentDistribution.value.data,
                    backgroundColor: '#0c8ce9',
                    borderColor: '#0c8ce9',
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#E5E7EB' } },
                    y: { grid: { display: false } }
                }
            }
        })
    }

    const initLeaveChart = () => {
        const ctx = document.getElementById('leaveChart')
        if (!ctx) return
        
        try {
            if (chartInstances.value.leave) {
                chartInstances.value.leave.destroy()
                chartInstances.value.leave = null
            }
        } catch (e) {
            console.warn('Error destroying chart:', e)
        }
        
        chartInstances.value.leave = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: leaveMonthlyData.value.labels,
                datasets: [
                    {
                        label: 'Approved',
                        data: leaveMonthlyData.value.approved,
                        backgroundColor: '#10b981'
                    },
                    {
                        label: 'Pending',
                        data: leaveMonthlyData.value.pending,
                        backgroundColor: '#f59e0b'
                    },
                    {
                        label: 'Rejected',
                        data: leaveMonthlyData.value.rejected,
                        backgroundColor: '#ef4444'
                    }
                ]
            },
            options: {
                indexAxis: 'x',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true, grid: { color: '#E5E7EB' } },
                    y: { stacked: true, grid: { color: '#E5E7EB' } }
                },
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true } }
                }
            }
        })
    }

    const initAttendanceChart = () => {
        const ctx = document.getElementById('attendanceChart')
        if (!ctx) return
        
        try {
            if (chartInstances.value.attendance) {
                chartInstances.value.attendance.destroy()
                chartInstances.value.attendance = null
            }
        } catch (e) {
            console.warn('Error destroying chart:', e)
        }
        
        chartInstances.value.attendance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: attendanceTrendData.value.labels,
                datasets: [
                    {
                        label: 'Present',
                        data: attendanceTrendData.value.present,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        borderWidth: 2,
                        fill: true
                    },
                    {
                        label: 'Absent',
                        data: attendanceTrendData.value.absent,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        borderWidth: 2,
                        fill: true
                    },
                    {
                        label: 'Late',
                        data: attendanceTrendData.value.late,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4,
                        borderWidth: 2,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#E5E7EB' } },
                    x: { grid: { color: '#E5E7EB' } }
                }
            }
        })
    }

    const initPayrollTrendChart = () => {
        const ctx = document.getElementById('payrollTrendChart')
        if (!ctx) return
        
        try {
            if (chartInstances.value.payrollTrend) {
                chartInstances.value.payrollTrend.destroy()
                chartInstances.value.payrollTrend = null
            }
        } catch (e) {
            console.warn('Error destroying chart:', e)
        }
        
        chartInstances.value.payrollTrend = new Chart(ctx, {
            type: 'line',
            data: {
                labels: payrollTrendData.value.labels,
                datasets: [
                    {
                        label: 'Gross Payroll',
                        data: payrollTrendData.value.grossPayroll,
                        borderColor: '#0c8ce9',
                        backgroundColor: 'rgba(12, 140, 233, 0.1)',
                        tension: 0.4,
                        borderWidth: 2,
                        fill: true
                    },
                    {
                        label: 'Net Payroll',
                        data: payrollTrendData.value.netPayroll,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        borderWidth: 2,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#E5E7EB' }, ticks: { callback: (value) => '₱' + formatNumber(value) } },
                    x: { grid: { color: '#E5E7EB' } }
                }
            }
        })
    }

    const initSalaryChart = () => {
        const ctx = document.getElementById('salaryChart')
        if (!ctx) return
        
        try {
            if (chartInstances.value.salary) {
                chartInstances.value.salary.destroy()
                chartInstances.value.salary = null
            }
        } catch (e) {
            console.warn('Error destroying chart:', e)
        }
        
        chartInstances.value.salary = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: salaryDistributionData.value.labels,
                datasets: [{
                    data: salaryDistributionData.value.data,
                    backgroundColor: ['#0c8ce9', '#a855f7', '#10b981', '#f59e0b', '#ef4444'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 15 } }
                }
            }
        })
    }

    const initRecruitmentFunnelChart = () => {
        const ctx = document.getElementById('recruitmentFunnelChart')
        if (!ctx) return
        
        try {
            if (chartInstances.value.recruitmentFunnel) {
                chartInstances.value.recruitmentFunnel.destroy()
                chartInstances.value.recruitmentFunnel = null
            }
        } catch (e) {
            console.warn('Error destroying chart:', e)
        }
        
        chartInstances.value.recruitmentFunnel = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: recruitmentFunnelData.value.labels,
                datasets: [{
                    label: 'Candidates',
                    data: recruitmentFunnelData.value.data,
                    backgroundColor: '#0c8ce9',
                    borderColor: '#0c8ce9',
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#E5E7EB' } },
                    y: { grid: { display: false } }
                }
            }
        })
    }

    const initHiringTrendChart = () => {
        const ctx = document.getElementById('hiringTrendChart')
        if (!ctx) return
        
        try {
            if (chartInstances.value.hiringTrend) {
                chartInstances.value.hiringTrend.destroy()
                chartInstances.value.hiringTrend = null
            }
        } catch (e) {
            console.warn('Error destroying chart:', e)
        }
        
        chartInstances.value.hiringTrend = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: hiringTrendData.value.labels,
                datasets: [{
                    label: 'Hired',
                    data: hiringTrendData.value.hired,
                    backgroundColor: '#10b981',
                    borderColor: '#10b981',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#E5E7EB' } },
                    x: { grid: { color: '#E5E7EB' } }
                }
            }
        })
    }

    /* =====================================================
    API Methods (Ready for Backend Integration)
    ===================================================== */
    const fetchDashboardData = async () => {
        try {
            loading.value = true
            // API: GET /api/hr/dashboard/metrics
            // const response = await axios.get('/api/hr/dashboard/metrics')
            // hrMetrics.value = response.data.metrics
            // recentHires.value = response.data.recentHires
            // ... fetch other data
            await new Promise(resolve => setTimeout(resolve, 500))
        } catch (error) {
            showToast({ icon: 'error', title: 'Error loading dashboard data' })
        } finally {
            loading.value = false
        }
    }

    const approveLeaveRequest = async (requestId) => {
        try {
            // API: POST /api/hr/leave-requests/{id}/approve
            // await axios.post(`/api/hr/leave-requests/${requestId}/approve`)
            pendingLeaveRequests.value = pendingLeaveRequests.value.filter(r => r.id !== requestId)
            showToast({ icon: 'success', title: 'Leave request approved' })
        } catch (error) {
            showToast({ icon: 'error', title: 'Failed to approve request' })
        }
    }

    const rejectLeaveRequest = async (requestId) => {
        try {
            // API: POST /api/hr/leave-requests/{id}/reject
            // await axios.post(`/api/hr/leave-requests/${requestId}/reject`)
            pendingLeaveRequests.value = pendingLeaveRequests.value.filter(r => r.id !== requestId)
            showToast({ icon: 'success', title: 'Leave request rejected' })
        } catch (error) {
            showToast({ icon: 'error', title: 'Failed to reject request' })
        }
    }

    /* =====================================================
    Tab Change Handler with Chart Rendering
    ===================================================== */
    watch(activeTab, async (newTab) => {
        // Wait for DOM to update
        await nextTick()
        
        setTimeout(() => {
            switch(newTab) {
                case 'employee-mgmt':
                    try {
                        if (chartInstances.value.employeeTrend) chartInstances.value.employeeTrend.destroy()
                        if (chartInstances.value.department) chartInstances.value.department.destroy()
                    } catch (e) {
                        console.warn('Error destroying charts:', e)
                    }
                    initEmployeeTrendChart()
                    initDepartmentChart()
                    break
                case 'leave-absence':
                    try {
                        if (chartInstances.value.leave) chartInstances.value.leave.destroy()
                    } catch (e) {
                        console.warn('Error destroying chart:', e)
                    }
                    initLeaveChart()
                    break
                case 'attendance':
                    try {
                        if (chartInstances.value.attendance) chartInstances.value.attendance.destroy()
                    } catch (e) {
                        console.warn('Error destroying chart:', e)
                    }
                    initAttendanceChart()
                    break
                case 'payroll':
                    try {
                        if (chartInstances.value.payrollTrend) chartInstances.value.payrollTrend.destroy()
                        if (chartInstances.value.salary) chartInstances.value.salary.destroy()
                    } catch (e) {
                        console.warn('Error destroying charts:', e)
                    }
                    initPayrollTrendChart()
                    initSalaryChart()
                    break
                case 'recruitment':
                    try {
                        if (chartInstances.value.recruitmentFunnel) chartInstances.value.recruitmentFunnel.destroy()
                        if (chartInstances.value.hiringTrend) chartInstances.value.hiringTrend.destroy()
                    } catch (e) {
                        console.warn('Error destroying charts:', e)
                    }
                    initRecruitmentFunnelChart()
                    initHiringTrendChart()
                    break
            }
        }, 150)
    })

    /* =====================================================
    Lifecycle
    ===================================================== */
    onMounted(async () => {
        console.log('Dashboard mounted, initializing charts...')
        await fetchDashboardData()
        
        // Wait for DOM to be fully rendered
        await nextTick()
        
        // Initialize charts for the active tab
        setTimeout(() => {
            console.log('Calling chart init functions')
            initEmployeeTrendChart()
            initDepartmentChart()
            console.log('Chart instances after init:', chartInstances.value)
        }, 150)
    })
</script>
