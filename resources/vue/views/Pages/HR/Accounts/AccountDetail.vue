<template>
    <auth-layout />
        <div class="ps-60 bg-[#F3F4F6] min-h-screen">
            <div class="px-10 py-10">
                <!-- Back Button -->
                <div class="mb-6">
                    <button 
                        @click="router.push('/accounts')"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-[#0c8ce9] hover:text-blue-700 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Accounts
                    </button>
                </div>

                <!-- Header Card -->
                <div class="bg-white rounded-lg border border-gray-200 p-8 mb-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-6">
                            <!-- Avatar -->
                            <div class="w-20 h-20 rounded-full bg-linear-to-br from-[#0c8ce9] to-[#0a5fa3] flex items-center justify-center text-white shrink-0">
                                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                            
                            <!-- Employee Info -->
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h1 class="text-2xl font-bold text-gray-900">{{ employee.name }}</h1>
                                </div>
                                <div class="flex gap-3 text-sm">
                                    <div>
                                        <span class="text-gray-600">Employee ID:</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ employee.account_id }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="flex items-center gap-3">
                            <button 
                                @click="openPromoteModal"
                                class="px-6 py-2 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition-colors flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Promote
                            </button>
                            <button 
                                @click="openEditModal"
                                class="px-6 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Profile
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <div class="bg-white rounded-t-lg border-b border-gray-200 sticky top-0 z-10">
                    <div class="flex px-0 overflow-x-auto">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="[
                                'px-6 py-4 text-sm font-semibold border-b-2 transition-all duration-200 flex items-center gap-2 whitespace-nowrap',
                                activeTab === tab.id
                                    ? 'border-[#0c8ce9] text-[#0c8ce9]'
                                    : 'border-transparent text-gray-600 hover:text-gray-900'
                            ]"
                        >
                            <span v-html="tab.icon" class="w-4 h-4"></span>
                            {{ tab.label }}
                        </button>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="bg-white rounded-b-lg p-8">
                    <!-- Profile Tab -->
                    <div v-if="activeTab === 'profile'" class="space-y-8">
                        <!-- Personal Information -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <span class="w-1 h-6 bg-[#0c8ce9] rounded"></span>
                                Personal Information
                            </h3>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Full Name</label>
                                    <p class="text-gray-900 font-medium mt-1">{{ employee.name }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Email Address</label>
                                    <p class="text-gray-900 font-medium mt-1">{{ employee.email }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Date of Birth</label>
                                    <p class="text-gray-900 font-medium mt-1">{{ employee.date_of_birth || 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Gender</label>
                                    <p class="text-gray-900 font-medium mt-1">{{ employee.gender || 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Contact Number</label>
                                    <p class="text-gray-900 font-medium mt-1">{{ employee.phone || 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Address</label>
                                    <p class="text-gray-900 font-medium mt-1">{{ employee.address || 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Work Assignment Tab -->
                    <div v-if="activeTab === 'work'" class="space-y-8">
                        <!-- Employment Information -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <span class="w-1 h-6 bg-[#0c8ce9] rounded"></span>
                                Employment Information
                            </h3>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Date Hired</label>
                                    <p class="text-gray-900 font-medium mt-1">{{ formatDate(employee.created_at) }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Employment Type</label>
                                    <p class="text-gray-900 font-medium mt-1">{{ employee.employee_type || 'Full-time' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Department</label>
                                    <p class="text-gray-900 font-medium mt-1">{{ employee.department }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Job Title</label>
                                    <p class="text-gray-900 font-medium mt-1">{{ employee.role }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Employment Status</label>
                                    <p class="text-gray-900 font-medium mt-1">{{ employee.status === 1 ? 'Active' : 'Inactive' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Location</label>
                                    <p class="text-gray-900 font-medium mt-1">{{ employee.location || 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payroll Tab -->
                    <div v-if="activeTab === 'payroll'" class="space-y-8">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <span class="w-1 h-6 bg-[#0c8ce9] rounded"></span>
                                Payroll Information
                            </h3>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Payroll Group</label>
                                    <p class="text-gray-900 font-medium mt-1">{{ employee.payroll_group || 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Pay Type</label>
                                    <p class="text-gray-900 font-medium mt-1">{{ employee.pay_type || 'Monthly' }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <span class="w-1 h-6 bg-[#0c8ce9] rounded"></span>
                                Compensation Breakdown
                            </h3>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                                    <p class="text-xs font-semibold text-gray-600 uppercase">Basic Pay</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">$0.00</p>
                                </div>
                                <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                                    <p class="text-xs font-semibold text-gray-600 uppercase">Allowances</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">$0.00</p>
                                </div>
                                <div class="bg-red-50 rounded-lg p-4 border border-red-100">
                                    <p class="text-xs font-semibold text-gray-600 uppercase">Deductions</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">$0.00</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <span class="w-1 h-6 bg-[#0c8ce9] rounded"></span>
                                Payroll History
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gray-100 border-b border-gray-200">
                                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Period</th>
                                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Gross Pay</th>
                                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Deductions</th>
                                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Net Pay</th>
                                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Status</th>
                                            <th class="px-6 py-3 text-center font-semibold text-gray-700">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr class="hover:bg-gray-50">
                                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No payroll records found</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Records Tab -->
                    <div v-if="activeTab === 'records'" class="space-y-8">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <span class="w-1 h-6 bg-[#0c8ce9] rounded"></span>
                                HR Records
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gray-100 border-b border-gray-200">
                                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Record Type</th>
                                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Description</th>
                                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Date Added</th>
                                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Added By</th>
                                            <th class="px-6 py-3 text-center font-semibold text-gray-700">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr class="hover:bg-gray-50">
                                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No records found</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Tab -->
                    <div v-if="activeTab === 'attendance'" class="space-y-8">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <span class="w-1 h-6 bg-[#0c8ce9] rounded"></span>
                                Attendance Summary
                            </h3>
                            <div class="grid grid-cols-4 gap-4">
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                                    <p class="text-xs font-semibold text-gray-600 uppercase">Total Hours</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">0 hrs</p>
                                </div>
                                <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                                    <p class="text-xs font-semibold text-gray-600 uppercase">Overtime</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">0 hrs</p>
                                </div>
                                <div class="bg-red-50 rounded-lg p-4 border border-red-100">
                                    <p class="text-xs font-semibold text-gray-600 uppercase">Absences</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">0 days</p>
                                </div>
                                <div class="bg-orange-50 rounded-lg p-4 border border-orange-100">
                                    <p class="text-xs font-semibold text-gray-600 uppercase">Late Instances</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">0</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <span class="w-1 h-6 bg-[#0c8ce9] rounded"></span>
                                Attendance Records
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gray-100 border-b border-gray-200">
                                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Date</th>
                                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Time In</th>
                                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Time Out</th>
                                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Total Hours</th>
                                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Status</th>
                                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Source</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr class="hover:bg-gray-50">
                                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No attendance records found</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Tab -->
                    <div v-if="activeTab === 'documents'" class="space-y-8">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <span class="w-1 h-6 bg-[#0c8ce9] rounded"></span>
                                Upload Documents
                            </h3>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-12 text-center hover:border-[#0c8ce9] hover:bg-blue-50 transition-all cursor-pointer">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="text-gray-600 font-medium">Drag & drop your files or click to browse</p>
                                <p class="text-sm text-gray-500 mt-1">Accepted formats: PDF, DOCX, JPG</p>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <span class="w-1 h-6 bg-[#0c8ce9] rounded"></span>
                                Documents
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gray-100 border-b border-gray-200">
                                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Document Name</th>
                                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Category</th>
                                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Uploaded Date</th>
                                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Uploaded By</th>
                                            <th class="px-6 py-3 text-center font-semibold text-gray-700">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr class="hover:bg-gray-50">
                                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No documents uploaded</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div v-if="showEdit" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Edit Employee Profile</h2>
                        <button 
                            @click="showEdit = false"
                            class="text-gray-500 hover:text-gray-700 text-2xl leading-none"
                        >
                            ×
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input v-model="editForm.name" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input v-model="editForm.email" type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                <input v-model="editForm.phone" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                <input v-model="editForm.department" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                                <input v-model="editForm.role" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                                <input v-model="editForm.location" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Payroll Group</label>
                                <input v-model="editForm.payroll_group" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select v-model="editForm.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent">
                                    <option :value="1">Active</option>
                                    <option :value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
                        <button 
                            @click="showEdit = false"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button 
                            @click="saveChanges"
                            class="px-6 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                        >
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>

            <!-- Promote Employee Modal -->
            <PromoteEmployeeModal 
                v-if="showPromote"
                :employee="employee"
                :loading="loading"
                @close="showPromote = false"
                @promote="handlePromote"
            />
        </div>

        <div class="fixed bottom-0 right-0 p-2 z-50">
            <img :src="agnes" class="h-auto w-auto" v-if="loading" />
        </div>
    
</template>

<script setup>
    import { ref, onMounted } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import axios from 'axios'
    import Swal from 'sweetalert2'
    import { useAuthStore } from '@/stores/auth'
    import PromoteEmployeeModal from '@/components/HR/Accounts/PromoteEmployeeModal.vue'
    import agnes from '@/assets/agnes.gif'

    const router = useRouter()
    const route = useRoute()
    const auth = useAuthStore()

    const loading = ref(false)
    const showEdit = ref(false)
    const showPromote = ref(false)
    const activeTab = ref('profile')

    const employee = ref({
        account_id: 'EMP-2024-001',
        name: 'Juan Carlos Dela Cruz',
        email: 'juan.delacruz@eoshma.com',
        phone: '+63 917 123 4567',
        role: 'Senior Project Manager',
        department: 'Operations',
        status: 1,
        location: 'Manila Head Office',
        supervisor: 'Maria Santos',
        payroll_group: 'Monthly - Management',
        pay_type: 'Monthly',
        payment_method: 'Bank Transfer',
        tax_status: 'Married',
        employee_type: 'Full-time',
        work_schedule: 'Monday - Friday, 8:00 AM - 5:00 PM',
        gender: 'Male',
        date_of_birth: 'June 15, 1988',
        address: '123 Makati Avenue, Makati City, Metro Manila',
        username: 'jdelacruz',
        created_at: '2022-01-15T08:00:00Z'
    })

    const editForm = ref({ ...employee.value })

    const tabs = [
        { 
            id: 'profile', 
            label: 'Profile',
            icon: '<svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>'
        },
        { 
            id: 'work', 
            label: 'Work Assignment',
            icon: '<svg fill="currentColor" viewBox="0 0 24 24"><path d="M20 6h-2.18c.11-.89.35-1.75.8-2.5-.5-.41-1.07-.7-1.69-.7-1.9 0-3.4 1.9-3.4 4.4H13V5c0-.9-.9-2-2-2s-2 1.1-2 2v2H8.4c0-2.5-1.5-4.4-3.4-4.4-.62 0-1.19.29-1.69.7.45.75.69 1.61.8 2.5H4c-1.1 0-1.99.9-1.99 2L2 19c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/></svg>'
        },
        { 
            id: 'payroll', 
            label: 'Payroll',
            icon: '<svg fill="currentColor" viewBox="0 0 24 24"><path d="M17.45 4.75H6.55v15.5h10.9V4.75zM7.65 18h-1v-1h1v1zm0-2h-1v-1h1v1zm0-2h-1v-1h1v1zm0-2h-1v-1h1v1zm0-2h-1v-1h1v1zm7.7 8h-1v1h1v-1zm0-1h-1v-1h1v1zm0-2h-1v-1h1v1zm0-2h-1v-1h1v1zm0-2h-1v-1h1v1zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/></svg>'
        },
        { 
            id: 'records', 
            label: 'Records',
            icon: '<svg fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/></svg>'
        },
        { 
            id: 'attendance', 
            label: 'Attendance',
            icon: '<svg fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-2.16-2.66c-.44-.53-1.25-.58-1.78-.15-.53.44-.58 1.25-.15 1.78l3 3.72c.29.37.77.59 1.27.59.5 0 .98-.22 1.27-.59l3.99-4.99c.44-.53.39-1.34-.15-1.78-.53-.44-1.34-.39-1.78.15z"/></svg>'
        },
        { 
            id: 'documents', 
            label: 'Documents',
            icon: '<svg fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>'
        }
    ]

    const formatDate = (date) => {
        if (!date) return 'N/A'
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        })
    }

    const openEditModal = () => {
        editForm.value = { ...employee.value }
        showEdit.value = true
    }

    const openPromoteModal = () => {
        showPromote.value = true
    }

    const handlePromote = async (promotionData) => {
        loading.value = true
        try {
            // API: PUT /api/users/{accountId}/promote
            // Payload: { newRole, newDepartment, newSalary, effectiveDate, notes }
            console.log('Promoting employee:', {
                employeeId: employee.value.account_id,
                ...promotionData
            })

            // Update employee data with promotion details
            employee.value.role = promotionData.newRole
            employee.value.department = promotionData.newDepartment
            
            showPromote.value = false
            
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Employee promoted successfully'
            })
        } catch (error) {
            console.error('Error promoting employee:', error)
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to promote employee'
            })
        } finally {
            loading.value = false
        }
    }

    const saveChanges = async () => {
        loading.value = true
        try {
        //     await axios.put(
        //        `/api/users/${employee.value.account_id}`,
        //        editForm.value,
        //        { headers: { Authorization: `Bearer ${auth.token}` } }
        //    )
            
            employee.value = { ...editForm.value }
            showEdit.value = false
            
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Employee profile updated successfully'
            })
        } catch (error) {
            console.error('Error updating employee:', error)
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to update employee profile'
            })
        } finally {
            loading.value = false
        }
    }

    const fetchEmployee = async () => {
        loading.value = true
        try {
            const accountId = route.params.id
            // const response = await axios.get(
            //     `/api/users/${accountId}`,
            //     { headers: { Authorization: `Bearer ${auth.token}` } }
            // )

            // employee.value = response.data.data || response.data
            // auth.pageTitle = employee.value.name
        } catch (error) {
            console.error('Error fetching employee:', error)
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load employee details'
            })
        } finally {
            loading.value = false
        }
    }

    onMounted(() => {
        fetchEmployee()
    })
</script>
