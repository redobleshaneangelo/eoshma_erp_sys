<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10 space-y-6">
            <div>
                <router-link
                    to="/payroll"
                    class="inline-flex items-center gap-2 text-[#0c8ce9] font-semibold hover:underline mb-4"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Payroll
                </router-link>

                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ run.name }}</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ run.frequency }} • {{ run.group }} group</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" :class="getStatusClasses(run.status)">
                        {{ run.status }}
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Payroll Period</p>
                        <p class="text-sm font-semibold text-gray-900 mt-2">{{ formatDate(run.startDate) }} - {{ formatDate(run.endDate) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Pay Date</p>
                        <p class="text-sm font-semibold text-gray-900 mt-2">{{ formatDate(run.payDate) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Description</p>
                        <p class="text-sm text-gray-700 mt-2">{{ run.description }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50">
                    <div class="flex flex-wrap">
                        <button
                            v-for="tab in availableTabs"
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

                <div class="p-6">
                    <div v-if="activeTab === 'attendance'" class="space-y-4">
                        <h2 class="text-lg font-semibold text-gray-900">Attendance Summary</h2>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Employee</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Rate</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Days Logged</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Total Hours</th>
                                        <th v-if="isPending && isApprovalMode" class="px-4 py-3 text-left text-xs font-semibold text-gray-700">
                                            <div class="flex items-center gap-2">
                                                Eligible
                                                <input
                                                    type="checkbox"
                                                    :checked="allEligible"
                                                    :disabled="isEligibilityReadOnly"
                                                    @change="toggleAllEligible($event.target.checked)"
                                                    class="h-4 w-4 rounded border-gray-300 text-[#0c8ce9]"
                                                />
                                            </div>
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="employee in attendanceSummary" :key="employee.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <p class="font-semibold">{{ employee.name }}</p>
                                            <p class="text-xs text-gray-500">{{ employee.role }}</p>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">₱{{ formatNumber(employee.rate) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ employee.daysLogged }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ employee.totalHours }}</td>
                                        <td v-if="isPending && isApprovalMode" class="px-4 py-3 text-sm text-gray-700">
                                            <input
                                                type="checkbox"
                                                :checked="employee.isEligible"
                                                :disabled="isEligibilityReadOnly"
                                                @change="toggleEligibility(employee, $event.target.checked)"
                                                class="h-4 w-4 rounded border-gray-300 text-[#0c8ce9]"
                                            />
                                        </td>
                                        <td class="px-4 py-3">
                                            <button
                                                @click="openEmployeeAttendance(employee)"
                                                class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                                            >
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="attendanceSummary.length === 0">
                                        <td :colspan="isPending && isApprovalMode ? 6 : 5" class="px-4 py-8 text-center text-sm text-gray-500">No attendance summary available.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-if="activeTab === 'allowances'" class="space-y-4">
                        <h2 class="text-lg font-semibold text-gray-900">Allowances</h2>
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Employee</label>
                                <select v-model="newAllowance.employeeId" :disabled="isReadOnly" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm disabled:bg-gray-100 disabled:text-gray-500">
                                    <option value="">Select employee</option>
                                    <option v-for="employee in attendanceSummary" :key="employee.id" :value="employee.id">
                                        {{ employee.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Allowance Type</label>
                                <input v-model="newAllowance.type" :disabled="isReadOnly" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm disabled:bg-gray-100 disabled:text-gray-500" placeholder="Meal, transport" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Amount</label>
                                <input v-model.number="newAllowance.amount" :disabled="isReadOnly" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm disabled:bg-gray-100 disabled:text-gray-500" placeholder="0" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Notes</label>
                                <input v-model="newAllowance.notes" :disabled="isReadOnly" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm disabled:bg-gray-100 disabled:text-gray-500" placeholder="Optional" />
                            </div>
                            <div class="flex items-end">
                                <button
                                    @click="addAllowance"
                                    :disabled="isReadOnly"
                                    class="w-full px-4 py-2 text-sm font-semibold text-white bg-[#0c8ce9] rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:text-gray-500"
                                >
                                    Assign Allowance
                                </button>
                            </div>
                        </div>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Employee</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Amount</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Notes</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="item in allowanceAssignments" :key="item.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ item.employeeName }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ item.type }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">₱{{ formatNumber(item.amount) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ item.notes }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            <div class="flex items-center gap-2">
                                                <button
                                                    @click="editAllowance(item)"
                                                    :disabled="isReadOnly"
                                                    class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700 disabled:bg-gray-300 disabled:text-gray-500"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    @click="removeAllowance(item)"
                                                    :disabled="isReadOnly"
                                                    class="px-3 py-1 text-xs font-semibold text-red-700 border border-red-200 rounded hover:bg-red-50 disabled:text-gray-400 disabled:border-gray-200 disabled:bg-gray-50"
                                                >
                                                    Remove
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="allowanceAssignments.length === 0">
                                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No allowances assigned.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-if="activeTab === 'deductions'" class="space-y-4">
                        <h2 class="text-lg font-semibold text-gray-900">Deductions</h2>
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Employee</label>
                                <select v-model="newDeduction.employeeId" :disabled="isReadOnly" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm disabled:bg-gray-100 disabled:text-gray-500">
                                    <option value="">Select employee</option>
                                    <option v-for="employee in attendanceSummary" :key="employee.id" :value="employee.id">
                                        {{ employee.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Deduction Type</label>
                                <input v-model="newDeduction.type" :disabled="isReadOnly" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm disabled:bg-gray-100 disabled:text-gray-500" placeholder="SSS, Loan" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Amount</label>
                                <input v-model.number="newDeduction.amount" :disabled="isReadOnly" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm disabled:bg-gray-100 disabled:text-gray-500" placeholder="0" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Notes</label>
                                <input v-model="newDeduction.notes" :disabled="isReadOnly" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm disabled:bg-gray-100 disabled:text-gray-500" placeholder="Optional" />
                            </div>
                            <div class="flex items-end">
                                <button
                                    @click="addDeduction"
                                    :disabled="isReadOnly"
                                    class="w-full px-4 py-2 text-sm font-semibold text-white bg-[#0c8ce9] rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:text-gray-500"
                                >
                                    Assign Deduction
                                </button>
                            </div>
                        </div>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Employee</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Amount</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Notes</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="item in deductionAssignments" :key="item.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ item.employeeName }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ item.type }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">₱{{ formatNumber(item.amount) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ item.notes }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            <div class="flex items-center gap-2">
                                                <button
                                                    @click="editDeduction(item)"
                                                    :disabled="isReadOnly"
                                                    class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700 disabled:bg-gray-300 disabled:text-gray-500"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    @click="removeDeduction(item)"
                                                    :disabled="isReadOnly"
                                                    class="px-3 py-1 text-xs font-semibold text-red-700 border border-red-200 rounded hover:bg-red-50 disabled:text-gray-400 disabled:border-gray-200 disabled:bg-gray-50"
                                                >
                                                    Remove
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="deductionAssignments.length === 0">
                                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No deductions assigned.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-if="activeTab === 'statutory'" class="space-y-4">
                        <h2 class="text-lg font-semibold text-gray-900">Statutory Compliance</h2>
                        <div class="text-sm text-gray-600 space-y-2">
                            <p>Review statutory computations and compliance requirements for this payroll period.</p>
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Basis</span>
                                <select
                                    v-model="statutoryBasis"
                                    @change="fetchStatutoryCompliance"
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                >
                                    <option value="monthly">Monthly-equivalent</option>
                                    <option value="period">Per-period</option>
                                </select>
                                <span class="text-xs text-gray-500">
                                    {{ statutoryBasis === 'monthly'
                                        ? 'Scaled to monthly based on frequency.'
                                        : 'Uses only this payroll period.'
                                    }}
                                </span>
                            </div>
                        </div>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Contribution</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Coverage Period</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Employee Share</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Employer Share</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Total Amount</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Due Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="item in statutoryCompliance" :key="item.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ item.name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ item.coverage }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">₱{{ formatNumber(item.employeeShare) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">₱{{ formatNumber(item.employerShare) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">₱{{ formatNumber(item.total) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ formatDate(item.dueDate) }}</td>
                                    </tr>
                                    <tr v-if="statutoryCompliance.length === 0">
                                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">No statutory compliance data yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-if="activeTab === 'computed'" class="space-y-4">
                        <h2 class="text-lg font-semibold text-gray-900">Computed Payroll</h2>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Employee</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Basic Pay</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Allowance</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Deductions</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Net Pay</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="row in computedPayrollRows" :key="row.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <p class="font-semibold">{{ row.employee }}</p>
                                            <p class="text-xs text-gray-500">{{ row.role }}</p>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">₱{{ formatNumber(row.basicPay) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">₱{{ formatNumber(row.allowance) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">₱{{ formatNumber(row.deductions) }}</td>
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">₱{{ formatNumber(row.netPay) }}</td>
                                    </tr>
                                    <tr v-if="computedPayrollRows.length === 0">
                                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No computed payroll data.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-3" v-if="showPrimaryAction || (isPending && isApprovalMode && !isViewOnly)">
                <button
                    v-if="isPending && isApprovalMode && !isViewOnly"
                    @click="approvePayroll"
                    class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors"
                >
                    Approve Payroll
                </button>
                <button
                    v-if="showPrimaryAction"
                    @click="handlePrimaryAction"
                    class="px-4 py-2 text-sm font-semibold text-white bg-[#0c8ce9] rounded-lg hover:bg-blue-700 transition-colors"
                >
                    {{ primaryActionLabel }}
                </button>
            </div>
        </div>
    </div>

    <!-- Employee Attendance Modal -->
    <div v-if="showEmployeeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ selectedEmployee.name }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ selectedEmployee.role }}</p>
                    </div>
                    <button @click="closeEmployeeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="border-b border-gray-200 bg-gray-50">
                <div class="flex flex-wrap">
                    <button
                        v-for="tab in employeeTabs"
                        :key="tab.key"
                        @click="employeeModalTab = tab.key"
                        :class="[
                            'px-6 py-4 text-sm font-semibold transition-colors border-b-2',
                            employeeModalTab === tab.key
                                ? 'text-[#0c8ce9] border-[#0c8ce9] bg-white'
                                : 'text-gray-600 border-transparent hover:text-gray-900 hover:bg-gray-100'
                        ]"
                    >
                        {{ tab.label }}
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div v-if="employeeModalTab === 'attendance'" class="space-y-4">
                    <h4 class="text-sm font-semibold text-gray-900">Attendance Records</h4>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Time In</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Time Out</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Payroll Start</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Payroll End</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Payroll Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Payroll Frequency</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="record in selectedEmployee.records" :key="record.date" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ formatDate(record.date) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ record.timeIn || '--' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ record.timeOut || '--' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ formatDate(record.payrollStart) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ formatDate(record.payrollEnd) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ record.payrollType }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ record.payrollFrequency }}</td>
                                    <td class="px-4 py-3">
                                        <button
                                            @click="openAttendanceRecord(record)"
                                            class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                                        >
                                            View
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="selectedEmployee.records.length === 0">
                                    <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">No attendance records.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div v-if="employeeModalTab === 'incidents'" class="space-y-4">
                    <h4 class="text-sm font-semibold text-gray-900">Incident Reports</h4>
                    <div class="grid grid-cols-1 gap-4">
                        <div v-for="incident in selectedEmployee.incidents" :key="incident.id" class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <p class="text-sm font-semibold text-gray-900">{{ incident.name }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ formatDate(incident.date) }}</p>
                        </div>
                        <div v-if="selectedEmployee.incidents.length === 0" class="text-sm text-gray-500">No incident reports.</div>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200">
                <button
                    @click="closeEmployeeModal"
                    class="w-full px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Attendance Record Modal -->
    <div v-if="showAttendanceRecordModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Attendance Record</h3>
                    <button @click="closeAttendanceRecord" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Time In</p>
                        <p class="text-sm text-gray-900 mt-1">{{ selectedAttendanceRecord.timeIn || '--' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Time Out</p>
                        <p class="text-sm text-gray-900 mt-1">{{ selectedAttendanceRecord.timeOut || '--' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Time In Photo</p>
                        <div class="w-full h-36 border border-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-500">
                            {{ selectedAttendanceRecord.timeInPhoto || 'No photo' }}
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Time Out Photo</p>
                        <div class="w-full h-36 border border-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-500">
                            {{ selectedAttendanceRecord.timeOutPhoto || 'No photo' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200">
                <button
                    @click="closeAttendanceRecord"
                    class="w-full px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AuthLayout from '@/views/Layouts/AuthLayout.vue'
import Swal from 'sweetalert2'
import axios from 'axios'

const auth = useAuthStore()
const route = useRoute()
const router = useRouter()
const runId = Number(route.params.id)

onMounted(() => {
    auth.pageTitle = 'Payroll Run'
    applyQueryRun()
    fetchRun()
    fetchAttendanceSummary()
    fetchAllowances()
    fetchDeductions()
    fetchStatutoryCompliance()
    fetchComputedPayroll()
})

const tabs = [
    { key: 'attendance', label: 'Attendance Summary' },
    { key: 'allowances', label: 'Allowances' },
    { key: 'deductions', label: 'Deductions' },
    { key: 'statutory', label: 'Statutory Compliance' },
    { key: 'computed', label: 'Computed Payroll' }
]

const activeTab = ref('attendance')

const attendanceSummary = ref([])

const employeeTabs = [
    { key: 'attendance', label: 'Attendance' },
    { key: 'incidents', label: 'Incident Reports' }
]

const showEmployeeModal = ref(false)
const employeeModalTab = ref('attendance')
const selectedEmployee = ref({ records: [], incidents: [] })
const showAttendanceRecordModal = ref(false)
const selectedAttendanceRecord = ref({})

const openEmployeeAttendance = (employee) => {
    selectedEmployee.value = employee
    employeeModalTab.value = 'attendance'
    showEmployeeModal.value = true
}

const closeEmployeeModal = () => {
    showEmployeeModal.value = false
    selectedEmployee.value = { records: [], incidents: [] }
}

const openAttendanceRecord = (record) => {
    selectedAttendanceRecord.value = record
    showAttendanceRecordModal.value = true
}

const closeAttendanceRecord = () => {
    showAttendanceRecordModal.value = false
    selectedAttendanceRecord.value = {}
}

const goToComputedPayroll = () => {
    router.push({ name: 'computed_payroll', params: { id: runId } })
}

const handlePrimaryAction = () => {
    if (isApproved.value) {
        Swal.fire({
            title: 'Release payslip?',
            text: 'This will release payslips for the approved payroll.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0c8ce9',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, release'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post(`/api/payroll-runs/${runId}/release-payslip`)
                    .then((response) => {
                        const payload = response.data?.data
                        if (payload) {
                            run.value = normalizeRun(payload)
                        }
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Payslips released',
                            showConfirmButton: false,
                            timer: 2000
                        })
                        router.push({ name: 'payroll', query: { tab: 'payroll_runs' } })
                    })
                    .catch((error) => {
                        console.error('Failed to release payslips', error)
                    })
            }
        })
        return
    }
    goToComputedPayroll()
}

const approvePayroll = () => {
    Swal.fire({
        title: 'Approve payroll? ',
        text: 'This will mark the payroll run as approved.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, approve'
    }).then((result) => {
        if (!result.isConfirmed) return
        axios.post(`/api/payroll-runs/${runId}/approve`)
            .then((response) => {
                const payload = response.data?.data
                if (payload) {
                    run.value = normalizeRun(payload)
                }
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Payroll approved',
                    showConfirmButton: false,
                    timer: 2000
                })
            })
            .catch((error) => {
                console.error('Failed to approve payroll', error)
            })
    })
}

const newAllowance = ref({
    employeeId: '',
    type: '',
    amount: 0,
    notes: ''
})

const allowanceAssignments = ref([])

const addAllowance = async () => {
    if (isReadOnly.value) return
    const employeeId = Number(newAllowance.value.employeeId)
    const employee = attendanceSummary.value.find(item => item.id === employeeId)
    if (!employee || !newAllowance.value.type || !newAllowance.value.amount) return
    try {
        const response = await axios.post(`/api/payroll-runs/${runId}/allowances`, {
            employee_id: employeeId,
            type: newAllowance.value.type,
            amount: newAllowance.value.amount,
            notes: newAllowance.value.notes
        })
        const saved = response.data?.data
        if (saved) {
            allowanceAssignments.value.unshift({
                id: saved.id,
                employeeName: saved.employeeName || employee.name,
                type: saved.type,
                amount: saved.amount,
                notes: saved.notes || '-'
            })
        }
        newAllowance.value = { employeeId: '', type: '', amount: 0, notes: '' }
    } catch (error) {
        console.error('Failed to add allowance', error)
    }
}

const newDeduction = ref({
    employeeId: '',
    type: '',
    amount: 0,
    notes: ''
})

const deductionAssignments = ref([])

const addDeduction = async () => {
    if (isReadOnly.value) return
    const employeeId = Number(newDeduction.value.employeeId)
    const employee = attendanceSummary.value.find(item => item.id === employeeId)
    if (!employee || !newDeduction.value.type || !newDeduction.value.amount) return
    try {
        const response = await axios.post(`/api/payroll-runs/${runId}/deductions`, {
            employee_id: employeeId,
            type: newDeduction.value.type,
            amount: newDeduction.value.amount,
            notes: newDeduction.value.notes
        })
        const saved = response.data?.data
        if (saved) {
            deductionAssignments.value.unshift({
                id: saved.id,
                employeeName: saved.employeeName || employee.name,
                type: saved.type,
                amount: saved.amount,
                notes: saved.notes || '-'
            })
        }
        newDeduction.value = { employeeId: '', type: '', amount: 0, notes: '' }
    } catch (error) {
        console.error('Failed to add deduction', error)
    }
}

const statutoryCompliance = ref([])
const statutoryBasis = ref('monthly')

const defaultRun = () => ({
    id: runId || 0,
    name: 'Payroll Run',
    frequency: '--',
    startDate: '',
    endDate: '',
    group: 'fixed',
    status: 'Draft',
    payDate: '',
    description: 'Payroll run details.'
})

const run = ref(defaultRun())

const normalizeRun = (payload) => {
    if (!payload) return defaultRun()
    return {
        id: payload.id || runId || 0,
        name: payload.name || 'Payroll Run',
        frequency: payload.frequency || '--',
        startDate: payload.start_date || payload.startDate || '',
        endDate: payload.end_date || payload.endDate || '',
        group: payload.group || 'fixed',
        status: payload.status || 'Draft',
        payDate: payload.pay_date || payload.payDate || '',
        description: payload.description || 'Payroll run details.'
    }
}

const applyQueryRun = () => {
    const query = route.query
    if (!query || !query.name) return
    run.value = normalizeRun(query)
}

const fetchRun = async () => {
    if (!runId) return
    try {
        const response = await axios.get(`/api/payroll-runs/${runId}`)
        const payload = response.data?.data
        if (payload) {
            run.value = normalizeRun(payload)
        }
    } catch (error) {
        console.error('Failed to load payroll run', error)
    }
}

const mapAttendance = (payload) => ({
    id: payload.id,
    name: payload.name,
    role: payload.role,
    rate: payload.rate,
    daysLogged: payload.daysLogged,
    totalHours: payload.totalHours,
    isEligible: payload.isEligible,
    records: payload.records || [],
    incidents: payload.incidents || []
})

const fetchAttendanceSummary = async () => {
    if (!runId) return
    try {
        const response = await axios.get(`/api/payroll-runs/${runId}/attendance-summary`)
        const rows = response.data?.data || []
        attendanceSummary.value = rows.map(mapAttendance)
    } catch (error) {
        console.error('Failed to load attendance summary', error)
    }
}

const allEligible = computed(() => {
    if (!attendanceSummary.value.length) return false
    return attendanceSummary.value.every(employee => employee.isEligible)
})

const toggleEligibility = async (employee, checked) => {
    if (isEligibilityReadOnly.value) return
    try {
        const response = await axios.post(`/api/payroll-runs/${runId}/eligibilities`, {
            employee_id: employee.id,
            is_eligible: checked
        })
        const data = response.data?.data
        if (data) {
            attendanceSummary.value = attendanceSummary.value.map(item =>
                item.id === employee.id ? { ...item, isEligible: data.isEligible } : item
            )
            fetchComputedPayroll()
            fetchStatutoryCompliance()
        }
    } catch (error) {
        console.error('Failed to update eligibility', error)
    }
}

const toggleAllEligible = async (checked) => {
    if (isEligibilityReadOnly.value) return
    try {
        const employeeIds = attendanceSummary.value.map(employee => employee.id)
        await axios.post(`/api/payroll-runs/${runId}/eligibilities/bulk`, {
            employee_ids: employeeIds,
            is_eligible: checked
        })
        attendanceSummary.value = attendanceSummary.value.map(employee => ({
            ...employee,
            isEligible: checked
        }))
        fetchComputedPayroll()
        fetchStatutoryCompliance()
    } catch (error) {
        console.error('Failed to update eligibility', error)
    }
}

const fetchAllowances = async () => {
    if (!runId) return
    try {
        const response = await axios.get(`/api/payroll-runs/${runId}/allowances`)
        const rows = response.data?.data || []
        allowanceAssignments.value = rows.map(item => ({
            id: item.id,
            employeeName: item.employeeName,
            type: item.type,
            amount: item.amount,
            notes: item.notes || '-'
        }))
    } catch (error) {
        console.error('Failed to load allowances', error)
    }
}

const editAllowance = async (item) => {
    const result = await Swal.fire({
        title: 'Edit allowance',
        html: `
            <div class="text-left">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Type</label>
                <input id="allowance-type" class="swal2-input" value="${item.type}">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Amount</label>
                <input id="allowance-amount" class="swal2-input" type="number" min="0" value="${item.amount}">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Notes</label>
                <input id="allowance-notes" class="swal2-input" value="${item.notes}">
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        preConfirm: () => {
            const type = document.getElementById('allowance-type').value.trim()
            const amount = Number(document.getElementById('allowance-amount').value)
            const notes = document.getElementById('allowance-notes').value.trim()
            if (!type || Number.isNaN(amount)) {
                Swal.showValidationMessage('Type and amount are required')
                return null
            }
            return { type, amount, notes }
        }
    })

    if (!result.isConfirmed || !result.value) return
    try {
        const response = await axios.patch(`/api/payroll-runs/${runId}/allowances/${item.id}`, {
            type: result.value.type,
            amount: result.value.amount,
            notes: result.value.notes
        })
        const saved = response.data?.data
        if (saved) {
            allowanceAssignments.value = allowanceAssignments.value.map(entry =>
                entry.id === item.id
                    ? { ...entry, type: saved.type, amount: saved.amount, notes: saved.notes || '-' }
                    : entry
            )
        }
    } catch (error) {
        console.error('Failed to update allowance', error)
    }
}

const removeAllowance = async (item) => {
    const result = await Swal.fire({
        title: 'Remove allowance?',
        text: `Remove ${item.type} for ${item.employeeName}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0c8ce9',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, remove'
    })

    if (!result.isConfirmed) return
    try {
        await axios.delete(`/api/payroll-runs/${runId}/allowances/${item.id}`)
        allowanceAssignments.value = allowanceAssignments.value.filter(entry => entry.id !== item.id)
    } catch (error) {
        console.error('Failed to remove allowance', error)
    }
}

const fetchDeductions = async () => {
    if (!runId) return
    try {
        const response = await axios.get(`/api/payroll-runs/${runId}/deductions`)
        const rows = response.data?.data || []
        deductionAssignments.value = rows.map(item => ({
            id: item.id,
            employeeName: item.employeeName,
            type: item.type,
            amount: item.amount,
            notes: item.notes || '-'
        }))
    } catch (error) {
        console.error('Failed to load deductions', error)
    }
}

const editDeduction = async (item) => {
    const result = await Swal.fire({
        title: 'Edit deduction',
        html: `
            <div class="text-left">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Type</label>
                <input id="deduction-type" class="swal2-input" value="${item.type}">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Amount</label>
                <input id="deduction-amount" class="swal2-input" type="number" min="0" value="${item.amount}">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Notes</label>
                <input id="deduction-notes" class="swal2-input" value="${item.notes}">
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        preConfirm: () => {
            const type = document.getElementById('deduction-type').value.trim()
            const amount = Number(document.getElementById('deduction-amount').value)
            const notes = document.getElementById('deduction-notes').value.trim()
            if (!type || Number.isNaN(amount)) {
                Swal.showValidationMessage('Type and amount are required')
                return null
            }
            return { type, amount, notes }
        }
    })

    if (!result.isConfirmed || !result.value) return
    try {
        const response = await axios.patch(`/api/payroll-runs/${runId}/deductions/${item.id}`, {
            type: result.value.type,
            amount: result.value.amount,
            notes: result.value.notes
        })
        const saved = response.data?.data
        if (saved) {
            deductionAssignments.value = deductionAssignments.value.map(entry =>
                entry.id === item.id
                    ? { ...entry, type: saved.type, amount: saved.amount, notes: saved.notes || '-' }
                    : entry
            )
        }
    } catch (error) {
        console.error('Failed to update deduction', error)
    }
}

const removeDeduction = async (item) => {
    const result = await Swal.fire({
        title: 'Remove deduction?',
        text: `Remove ${item.type} for ${item.employeeName}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0c8ce9',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, remove'
    })

    if (!result.isConfirmed) return
    try {
        await axios.delete(`/api/payroll-runs/${runId}/deductions/${item.id}`)
        deductionAssignments.value = deductionAssignments.value.filter(entry => entry.id !== item.id)
    } catch (error) {
        console.error('Failed to remove deduction', error)
    }
}

const fetchStatutoryCompliance = async () => {
    if (!runId) return
    try {
        const response = await axios.get(`/api/payroll-runs/${runId}/statutory-compliance`, {
            params: { basis: statutoryBasis.value }
        })
        const rows = response.data?.data || []
        statutoryCompliance.value = rows
    } catch (error) {
        console.error('Failed to load statutory compliance', error)
    }
}

const isViewOnly = computed(() => route.name === 'payroll_run_view')
const isApprovalMode = computed(() => route.query?.mode === 'approval')
const isApproved = computed(() => run.value.status === 'Approved')
const isPending = computed(() => run.value.status === 'Pending')
const isCompleted = computed(() => run.value.status === 'Completed')
const isReadOnly = computed(() => isPending.value || isApproved.value || isCompleted.value || isViewOnly.value)
const isEligibilityReadOnly = computed(() => isApproved.value || isCompleted.value || isViewOnly.value || (isPending.value && !isApprovalMode.value))

const availableTabs = computed(() => {
    if (isApproved.value || isPending.value || isCompleted.value || isViewOnly.value) {
        return tabs
    }
    return tabs.filter(tab => tab.key !== 'computed')
})

const showPrimaryAction = computed(() => !isPending.value && !isCompleted.value && !isViewOnly.value)

const primaryActionLabel = computed(() => {
    return isApproved.value ? 'Release Payslip' : 'Compute Payroll'
})

const computedPayrollRows = ref([])

const fetchComputedPayroll = async () => {
    if (!runId) return
    try {
        const response = await axios.get(`/api/payroll-runs/${runId}/computed-payroll`)
        const rows = response.data?.data || []
        computedPayrollRows.value = rows
    } catch (error) {
        console.error('Failed to load computed payroll', error)
    }
}

const getStatusClasses = (status) => {
    const map = {
        Draft: 'bg-gray-200 text-gray-700',
        Pending: 'bg-yellow-100 text-yellow-800',
        Approved: 'bg-green-100 text-green-800',
        Completed: 'bg-blue-100 text-blue-800'
    }
    return map[status] || 'bg-gray-100 text-gray-700'
}

const formatDate = (date) => {
    if (!date) return '--'
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatNumber = (value) => {
    return Number(value || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}
</script>
