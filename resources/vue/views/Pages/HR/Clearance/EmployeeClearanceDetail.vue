<template>
    <auth-layout />
        <div class="ps-60 bg-[#F3F4F6] min-h-screen">
            <div class="px-10 py-10 space-y-6">
                <button
                    @click="goBack"
                    class="flex items-center text-[#0c8ce9] hover:text-blue-700 font-semibold transition-colors"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Employee Clearance
                </button>

                <div class="bg-white rounded-lg border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Clearance ID</p>
                            <h1 class="text-2xl font-bold text-[#333333] mt-1">{{ clearanceData.id }}</h1>
                            <p class="text-sm text-gray-600 mt-1">{{ clearanceData.employeeName }} - {{ clearanceData.position }}</p>
                        </div>
                        <span :class="getStatusBadgeClass(clearanceData.status)" class="px-3 py-1 rounded-full text-xs font-semibold">
                            {{ clearanceData.status }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg p-4 border border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Exit Date</p>
                        <p class="text-sm font-semibold text-gray-900">{{ formatDate(clearanceData.exitDate) }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Department</p>
                        <p class="text-sm font-semibold text-gray-900">{{ clearanceData.department }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Supervisor</p>
                        <p class="text-sm font-semibold text-gray-900">{{ clearanceData.supervisor }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Exit Reason</p>
                        <p class="text-sm font-semibold text-gray-900">{{ clearanceData.exitReason }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Clearance Timeline</h3>
                    <div class="flex items-start gap-0">
                        <div v-for="(step, idx) in clearanceData.timeline" :key="idx" class="flex-1 flex flex-col items-center">
                            <div
                                class="flex items-center justify-center w-9 h-9 rounded-full text-xs font-semibold text-white"
                                :class="step.completed ? 'bg-green-500' : 'bg-gray-300'"
                            >
                                <svg v-if="step.completed" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                                </svg>
                                <span v-else>{{ idx + 1 }}</span>
                            </div>
                            <p class="text-xs font-semibold text-gray-900 mt-3 text-center">{{ step.name }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ step.completed ? formatDate(step.date) : 'Pending' }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg border border-gray-100 p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Finance Summary</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Payroll Period</span>
                                <span class="font-semibold text-gray-900">{{ financeSummary.payrollPeriod }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Total Payable</span>
                                <span class="font-semibold text-gray-900">P {{ formatCurrency(financeSummary.totalPayable) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Deductions</span>
                                <span class="font-semibold text-gray-900">P {{ formatCurrency(financeSummary.deductions) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Status</span>
                                <span :class="getStatusBadgeClass(financeSummary.status)" class="px-2 py-1 rounded-full text-xs font-semibold">
                                    {{ financeSummary.status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-100 p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">HR Checklist</h3>
                        <div class="space-y-3">
                            <div v-for="(item, idx) in hrChecklist" :key="idx" class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center" :class="item.completed ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-500'">
                                    <svg v-if="item.completed" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                                    </svg>
                                    <span v-else class="text-xs font-semibold">{{ idx + 1 }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ item.name }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ item.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Actions</h3>
                    <div class="flex items-center gap-3">
                        <button class="px-5 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition-colors">
                            Progress Stage
                        </button>
                        <button class="px-5 py-2.5 bg-green-600 text-white rounded-lg font-semibold text-sm hover:bg-green-700 transition-colors">
                            Mark Cleared
                        </button>
                        <button class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-200 transition-colors">
                            Export Summary
                        </button>
                    </div>
                </div>
            </div>
        </div>
</template>

<script setup>
    import { computed, onMounted, ref } from 'vue'
    import { useRoute, useRouter } from 'vue-router'
    import { useAuthStore } from '@/stores/auth'

    const auth = useAuthStore()
    const router = useRouter()
    const route = useRoute()

    const clearances = ref([
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
            timeline: [
                { name: 'Exit Filed', completed: true, date: '2026-01-15' },
                { name: 'Attendance Review', completed: true, date: '2026-01-17' },
                { name: 'Final Pay', completed: true, date: '2026-01-20' },
                { name: 'Finance Review', completed: true, date: '2026-01-25' },
                { name: 'HR Clearance', completed: false },
                { name: 'Offboarding', completed: false }
            ]
        }
    ])

    const clearanceData = computed(() => {
        return clearances.value.find(item => item.id === route.params.id) || {
            id: 'CLR-000',
            employeeId: 'EMP-000',
            employeeName: 'Employee Name',
            department: 'Department',
            position: 'Position',
            supervisor: 'Supervisor',
            exitDate: '2026-02-01',
            exitReason: 'Reason',
            status: 'Exit Filed',
            timeline: [
                { name: 'Exit Filed', completed: false },
                { name: 'Attendance Review', completed: false },
                { name: 'Final Pay', completed: false },
                { name: 'Finance Review', completed: false },
                { name: 'HR Clearance', completed: false },
                { name: 'Offboarding', completed: false }
            ]
        }
    })

    const financeSummary = computed(() => {
        return {
            payrollPeriod: 'January 2026',
            totalPayable: 85000,
            deductions: 12500,
            status: clearanceData.value.status === 'Finance Review' ? 'Pending' : 'Approved'
        }
    })

    const hrChecklist = computed(() => [
        { name: 'Return Company ID Card', description: 'ID and access badges submitted', completed: true },
        { name: 'Return Laptop/Equipment', description: 'All issued devices returned', completed: true },
        { name: 'Return Tools and Assets', description: 'Assigned tools returned', completed: false },
        { name: 'Exit Interview Completed', description: 'HR exit interview done', completed: false }
    ])

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

    const goBack = () => {
        router.back()
    }

    onMounted(() => {
        auth.pageTitle = 'Employee Clearance Detail'
    })
</script>
