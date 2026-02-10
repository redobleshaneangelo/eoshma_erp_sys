<template>
    <auth-layout />
        <div class="ps-60 bg-[#F3F4F6] min-h-screen">
            <div class="px-10 py-10">

                    <!-- Analytics Cards -->
                    <div class="grid grid-cols-5 gap-4 mb-8">
                        <div class="bg-white rounded-lg p-4 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Total Nearing Exit</p>
                            <p class="text-2xl font-bold text-[#333333]">{{ allUpcomingExits.length }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Within 15 Days</p>
                            <p class="text-2xl font-bold text-red-600">{{ within15Days }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">16-30 Days</p>
                            <p class="text-2xl font-bold text-orange-600">{{ within30Days }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Pending Initiation</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ pendingInitiation }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Clearance Started</p>
                            <p class="text-2xl font-bold text-blue-600">{{ clearanceStarted }}</p>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="bg-white rounded-lg p-6 border border-gray-100 mb-8">
                        <div class="grid grid-cols-6 gap-4">
                            <input
                                v-model="filters.search"
                                type="text"
                                placeholder="Search name/ID..."
                                class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                            />
                            <select v-model="filters.department" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]">
                                <option value="">All Departments</option>
                                <option value="IT">IT</option>
                                <option value="HR">HR</option>
                                <option value="Finance">Finance</option>
                                <option value="Operations">Operations</option>
                            </select>
                            <select v-model="filters.position" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]">
                                <option value="">All Positions</option>
                                <option value="Manager">Manager</option>
                                <option value="Senior Developer">Senior Developer</option>
                                <option value="Developer">Developer</option>
                                <option value="Officer">Officer</option>
                            </select>
                            <select v-model="filters.daysRange" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]">
                                <option value="">All Ranges</option>
                                <option value="15">Within 15 days</option>
                                <option value="30">16-30 days</option>
                                <option value="60">31-60 days</option>
                                <option value="90">61-90 days</option>
                            </select>
                            <select v-model="filters.status" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]">
                                <option value="">All Status</option>
                                <option value="Pending Initiation">Pending Initiation</option>
                                <option value="Exit Filed">Exit Filed</option>
                                <option value="Clearance Started">Clearance Started</option>
                            </select>
                            <button @click="clearFilters" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-200">
                                Clear
                            </button>
                        </div>
                    </div>

                    <!-- Upcoming Exits Table -->
                    <div class="bg-white rounded-lg border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase w-12">Alert</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase cursor-pointer hover:bg-gray-100" @click="toggleSort('employeeId')">
                                            Employee ID
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Department</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Position</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase cursor-pointer hover:bg-gray-100" @click="toggleSort('contractEndDate')">
                                            Contract End Date
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Days Remaining</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="exit in paginatedExits" :key="exit.id" class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="px-4 py-4 text-center">
                                            <div :title="getAlertTooltip(exit.daysRemaining)" class="flex justify-center">
                                                <svg v-if="exit.daysRemaining <= 15" class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" />
                                                </svg>
                                                <svg v-else-if="exit.daysRemaining <= 30" class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                <svg v-else class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ exit.employeeId }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ exit.name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ exit.department }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ exit.position }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ formatDate(exit.contractEndDate) }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span :class="getDaysRemainingClass(exit.daysRemaining)" class="px-3 py-1 rounded-full text-sm font-bold">
                                                {{ exit.daysRemaining }} days
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <span :class="getStatusBadgeClass(exit.status)" class="px-2 py-1 rounded-full text-xs font-semibold">
                                                {{ exit.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm space-x-2">
                                            <button @click="viewClearance(exit)" class="text-[#0c8ce9] hover:text-blue-700 font-medium text-xs">
                                                View
                                            </button>
                                            <button @click="sendReminder(exit)" class="text-purple-600 hover:text-purple-700 font-medium text-xs">
                                                Remind
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-if="filteredExits.length === 0" class="px-6 py-8 text-center text-gray-500 text-sm">
                            No upcoming exits found
                        </div>

                        <!-- Pagination -->
                        <div v-if="filteredExits.length > 0" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between bg-gray-50">
                            <div class="text-sm text-gray-600">
                                Showing {{ (currentPage - 1) * pageSize + 1 }} to {{ Math.min(currentPage * pageSize, filteredExits.length) }} of {{ filteredExits.length }}
                            </div>
                            <div class="flex gap-2">
                                <button
                                    @click="currentPage = Math.max(1, currentPage - 1)"
                                    :disabled="currentPage === 1"
                                    class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-100 disabled:opacity-50"
                                >
                                    Previous
                                </button>
                                <div class="flex gap-1">
                                    <button
                                        v-for="page in totalPages"
                                        :key="page"
                                        @click="currentPage = page"
                                        :class="currentPage === page ? 'bg-[#0c8ce9] text-white' : 'bg-white border border-gray-300 hover:bg-gray-100'"
                                        class="px-3 py-1 rounded-lg text-sm font-medium"
                                    >
                                        {{ page }}
                                    </button>
                                </div>
                                <button
                                    @click="currentPage = Math.min(totalPages, currentPage + 1)"
                                    :disabled="currentPage === totalPages"
                                    class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-100 disabled:opacity-50"
                                >
                                    Next
                                </button>
                            </div>
                            <button
                                @click="exportToFormat('pdf')"
                                class="px-4 py-1.5 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 font-medium flex items-center gap-2 ml-auto"
                            >
                                📄 PDF
                            </button>
                            <button
                                @click="exportToFormat('excel')"
                                class="px-4 py-1.5 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 font-medium flex items-center gap-2"
                            >
                                📊 Excel
                            </button>
                        </div>
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
    const currentPage = ref(1)
    const pageSize = ref(10)
    const sortBy = ref('contractEndDate')
    const sortOrder = ref('asc')

    // Role-based permissions
    const canViewAll = computed(() => ['hr_manager'].includes(auth.role))

    // Filters
    const filters = ref({
        search: '',
        department: '',
        position: '',
        daysRange: '',
        status: ''
    })

    // API endpoints documented
    // GET /api/employee-clearance/upcoming-exits - fetch all upcoming exits
    // GET /api/employee-clearance/upcoming-exits/{id} - get employee details
    // POST /api/employee-clearance/upcoming-exits/{id}/send-reminder - send reminder notification
    // PATCH /api/employee-clearance/upcoming-exits/{id}/initiate-clearance - initiate clearance workflow
    // GET /api/employee-clearance/upcoming-exits/export/pdf - export to PDF
    // GET /api/employee-clearance/upcoming-exits/export/excel - export to Excel

    // Mock data - 12 employees with various exit dates
    const allUpcomingExits = ref([
        {
            id: 'UPC-001',
            employeeId: 'EMP-010',
            name: 'James Wilson',
            department: 'IT',
            position: 'Senior Developer',
            contractEndDate: new Date(new Date().setDate(new Date().getDate() + 8)).toISOString().split('T')[0],
            daysRemaining: 8,
            status: 'Pending Initiation',
            supervisorEmail: 'supervisor@company.com'
        },
        {
            id: 'UPC-002',
            employeeId: 'EMP-011',
            name: 'Maria Garcia',
            department: 'Finance',
            position: 'Finance Officer',
            contractEndDate: new Date(new Date().setDate(new Date().getDate() + 12)).toISOString().split('T')[0],
            daysRemaining: 12,
            status: 'Pending Initiation',
            supervisorEmail: 'supervisor@company.com'
        },
        {
            id: 'UPC-003',
            employeeId: 'EMP-012',
            name: 'David Brown',
            department: 'HR',
            position: 'HR Manager',
            contractEndDate: new Date(new Date().setDate(new Date().getDate() + 22)).toISOString().split('T')[0],
            daysRemaining: 22,
            status: 'Exit Filed',
            supervisorEmail: 'supervisor@company.com'
        },
        {
            id: 'UPC-004',
            employeeId: 'EMP-013',
            name: 'Lisa Anderson',
            department: 'Operations',
            position: 'Operations Manager',
            contractEndDate: new Date(new Date().setDate(new Date().getDate() + 28)).toISOString().split('T')[0],
            daysRemaining: 28,
            status: 'Exit Filed',
            supervisorEmail: 'supervisor@company.com'
        },
        {
            id: 'UPC-005',
            employeeId: 'EMP-014',
            name: 'Robert Martinez',
            department: 'IT',
            position: 'Developer',
            contractEndDate: new Date(new Date().setDate(new Date().getDate() + 35)).toISOString().split('T')[0],
            daysRemaining: 35,
            status: 'Clearance Started',
            supervisorEmail: 'supervisor@company.com'
        },
        {
            id: 'UPC-006',
            employeeId: 'EMP-015',
            name: 'Jennifer Lee',
            department: 'Finance',
            position: 'Accountant',
            contractEndDate: new Date(new Date().setDate(new Date().getDate() + 45)).toISOString().split('T')[0],
            daysRemaining: 45,
            status: 'Pending Initiation',
            supervisorEmail: 'supervisor@company.com'
        },
        {
            id: 'UPC-007',
            employeeId: 'EMP-016',
            name: 'Thomas Clark',
            department: 'Operations',
            position: 'Officer',
            contractEndDate: new Date(new Date().setDate(new Date().getDate() + 55)).toISOString().split('T')[0],
            daysRemaining: 55,
            status: 'Exit Filed',
            supervisorEmail: 'supervisor@company.com'
        },
        {
            id: 'UPC-008',
            employeeId: 'EMP-017',
            name: 'Patricia White',
            department: 'HR',
            position: 'HR Officer',
            contractEndDate: new Date(new Date().setDate(new Date().getDate() + 65)).toISOString().split('T')[0],
            daysRemaining: 65,
            status: 'Pending Initiation',
            supervisorEmail: 'supervisor@company.com'
        },
        {
            id: 'UPC-009',
            employeeId: 'EMP-018',
            name: 'Christopher Davis',
            department: 'IT',
            position: 'IT Support',
            contractEndDate: new Date(new Date().setDate(new Date().getDate() + 75)).toISOString().split('T')[0],
            daysRemaining: 75,
            status: 'Clearance Started',
            supervisorEmail: 'supervisor@company.com'
        },
        {
            id: 'UPC-010',
            employeeId: 'EMP-019',
            name: 'Michelle Thompson',
            department: 'Finance',
            position: 'Finance Manager',
            contractEndDate: new Date(new Date().setDate(new Date().getDate() + 85)).toISOString().split('T')[0],
            daysRemaining: 85,
            status: 'Pending Initiation',
            supervisorEmail: 'supervisor@company.com'
        },
        {
            id: 'UPC-011',
            employeeId: 'EMP-020',
            name: 'Kevin Harris',
            department: 'Operations',
            position: 'Manager',
            contractEndDate: new Date(new Date().setDate(new Date().getDate() + 5)).toISOString().split('T')[0],
            daysRemaining: 5,
            status: 'Exit Filed',
            supervisorEmail: 'supervisor@company.com'
        },
        {
            id: 'UPC-012',
            employeeId: 'EMP-021',
            name: 'Nancy Jones',
            department: 'HR',
            position: 'HR Officer',
            contractEndDate: new Date(new Date().setDate(new Date().getDate() + 18)).toISOString().split('T')[0],
            daysRemaining: 18,
            status: 'Pending Initiation',
            supervisorEmail: 'supervisor@company.com'
        }
    ])

    // Computed properties
    const filteredExits = computed(() => {
        let filtered = allUpcomingExits.value.filter(exit => {
            const matchesSearch = filters.value.search === '' ||
                exit.name.toLowerCase().includes(filters.value.search.toLowerCase()) ||
                exit.employeeId.toLowerCase().includes(filters.value.search.toLowerCase())
            const matchesDept = filters.value.department === '' || exit.department === filters.value.department
            const matchesPosition = filters.value.position === '' || exit.position === filters.value.position
            const matchesStatus = filters.value.status === '' || exit.status === filters.value.status

            let matchesDays = true
            if (filters.value.daysRange) {
                const daysLimit = parseInt(filters.value.daysRange)
                if (filters.value.daysRange === '15') {
                    matchesDays = exit.daysRemaining <= 15
                } else if (filters.value.daysRange === '30') {
                    matchesDays = exit.daysRemaining > 15 && exit.daysRemaining <= 30
                } else if (filters.value.daysRange === '60') {
                    matchesDays = exit.daysRemaining > 30 && exit.daysRemaining <= 60
                } else if (filters.value.daysRange === '90') {
                    matchesDays = exit.daysRemaining > 60 && exit.daysRemaining <= 90
                }
            }

            return matchesSearch && matchesDept && matchesPosition && matchesStatus && matchesDays
        })

        // Sorting
        filtered.sort((a, b) => {
            let compareA = a[sortBy.value]
            let compareB = b[sortBy.value]

            if (sortBy.value === 'contractEndDate') {
                compareA = new Date(compareA)
                compareB = new Date(compareB)
            }

            if (sortOrder.value === 'asc') {
                return compareA < compareB ? -1 : 1
            } else {
                return compareA > compareB ? -1 : 1
            }
        })

        return filtered
    })

    const paginatedExits = computed(() => {
        const start = (currentPage.value - 1) * pageSize.value
        const end = start + pageSize.value
        return filteredExits.value.slice(start, end)
    })

    const totalPages = computed(() => Math.ceil(filteredExits.value.length / pageSize.value))

    const within15Days = computed(() => allUpcomingExits.value.filter(e => e.daysRemaining <= 15).length)
    const within30Days = computed(() => allUpcomingExits.value.filter(e => e.daysRemaining > 15 && e.daysRemaining <= 30).length)
    const pendingInitiation = computed(() => allUpcomingExits.value.filter(e => e.status === 'Pending Initiation').length)
    const clearanceStarted = computed(() => allUpcomingExits.value.filter(e => e.status !== 'Pending Initiation').length)

    // Methods
    const formatDate = (date) => {
        return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
    }

    const getDaysRemainingClass = (days) => {
        if (days <= 15) return 'bg-red-100 text-red-800'
        if (days <= 30) return 'bg-orange-100 text-orange-800'
        if (days <= 60) return 'bg-yellow-100 text-yellow-800'
        return 'bg-blue-100 text-blue-800'
    }

    const getAlertTooltip = (days) => {
        if (days <= 15) return 'Critical: Exit within 15 days'
        if (days <= 30) return 'Warning: Exit within 30 days'
        return 'Upcoming: Exit within 60-90 days'
    }

    const getStatusBadgeClass = (status) => {
        const classes = {
            'Pending Initiation': 'bg-yellow-100 text-yellow-800',
            'Exit Filed': 'bg-blue-100 text-blue-800',
            'Clearance Started': 'bg-purple-100 text-purple-800'
        }
        return classes[status] || 'bg-gray-100 text-gray-800'
    }

    const toggleSort = (column) => {
        if (sortBy.value === column) {
            sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
        } else {
            sortBy.value = column
            sortOrder.value = 'asc'
        }
        currentPage.value = 1
    }

    const viewClearance = (exit) => {
        // Navigate to clearance details
        router.push({
            name: 'employee_clearance',
            query: { employeeId: exit.employeeId }
        })
    }


    const sendReminder = async (exit) => {
        // API: POST /api/employee-clearance/upcoming-exits/{id}/send-reminder
        const { isConfirmed } = await Swal.fire({
            title: 'Send Reminder?',
            html: `Send exit reminder to <strong>${exit.name}</strong>?<br><br>Reminder will be sent to:<br>• Employee<br>• Supervisor<br>• HR Department`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0c8ce9',
            confirmButtonText: 'Send Reminder'
        })

        if (isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Reminder Sent',
                text: `Reminder sent to ${exit.name} and their supervisor`,
                confirmButtonColor: '#0c8ce9'
            })
        }
    }

    const clearFilters = () => {
        filters.value = {
            search: '',
            department: '',
            position: '',
            daysRange: '',
            status: ''
        }
        currentPage.value = 1
    }

    const exportToFormat = (format) => {
        const data = filteredExits.value.map(exit => ({
            'Employee ID': exit.employeeId,
            'Name': exit.name,
            'Department': exit.department,
            'Position': exit.position,
            'Contract End Date': formatDate(exit.contractEndDate),
            'Days Remaining': exit.daysRemaining,
            'Status': exit.status
        }))

        if (format === 'pdf') {
            // API: GET /api/employee-clearance/upcoming-exits/export/pdf
            Swal.fire({
                icon: 'info',
                title: 'Exporting to PDF',
                text: 'Your PDF is being generated...',
                confirmButtonColor: '#0c8ce9',
                didClose: () => {
                    // Simulate download
                    const link = document.createElement('a')
                    link.href = '#'
                    link.download = 'upcoming-exits.pdf'
                }
            })
        } else if (format === 'excel') {
            // API: GET /api/employee-clearance/upcoming-exits/export/excel
            Swal.fire({
                icon: 'info',
                title: 'Exporting to Excel',
                text: 'Your Excel file is being generated...',
                confirmButtonColor: '#0c8ce9',
                didClose: () => {
                    // Simulate download
                    const link = document.createElement('a')
                    link.href = '#'
                    link.download = 'upcoming-exits.xlsx'
                }
            })
        }
    }

    onMounted(() => {
        // Initialize upcoming exits
        // API: GET /api/employee-clearance/upcoming-exits
    })
</script>
