<template>
    <auth-layout />
        <div class="ps-60 bg-[#F3F4F6] min-h-screen">
            <div class="px-10 py-10">
                    <!-- Tab Navigation -->
                    <div class="bg-white border-b border-gray-200 rounded-t-lg mb-0">
                        <nav class="flex gap-0 px-6 py-0 overflow-x-auto">
                            <button
                                v-for="tab in tabs"
                                :key="tab.id"
                                @click="activeTab = tab.id"
                                class="flex items-center gap-2 px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap"
                                :class="activeTab === tab.id
                                    ? 'border-[#0c8ce9] text-[#0c8ce9]'
                                    : 'border-transparent text-gray-600 hover:text-gray-900'"
                            >
                                <span v-html="tab.icon"></span>
                                {{ tab.label }}
                                <span v-if="tab.count" class="bg-gray-200 text-gray-700 px-2 py-0 rounded-full text-xs font-semibold">
                                    {{ tab.count }}
                                </span>
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="bg-white rounded-b-lg">
                        <!-- Overview Tab -->
                        <div v-show="activeTab === 'overview'" class="p-6">
                            <div class="space-y-8">
                        <div class="mb-8">
                            <div class="grid grid-cols-5 gap-4">
                                <div class="bg-white rounded-lg p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Present Today</p>
                                    <p class="text-4xl font-bold text-[#0c8ce9]">{{ stats.presentToday }}</p>
                                    <p class="text-xs text-gray-500 mt-2">Employees</p>
                                </div>
                                <div class="bg-white rounded-lg p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Late Today</p>
                                    <p class="text-4xl font-bold text-yellow-500">{{ stats.lateToday }}</p>
                                    <p class="text-xs text-gray-500 mt-2">Employees</p>
                                </div>
                                <div class="bg-white rounded-lg p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">AWOL Today</p>
                                    <p class="text-4xl font-bold text-red-500">{{ stats.awolToday }}</p>
                                    <p class="text-xs text-gray-500 mt-2">Employees</p>
                                </div>
                                <div class="bg-white rounded-lg p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Missing Time-Out</p>
                                    <p class="text-4xl font-bold text-orange-500">{{ stats.missingTimeOut }}%</p>
                                    <p class="text-xs text-gray-500 mt-2">Missing Records</p>
                                </div>
                                <div class="bg-white rounded-lg p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Overtime Hours</p>
                                    <p class="text-4xl font-bold text-green-500">{{ stats.overtimeHours }}</p>
                                    <p class="text-xs text-gray-500 mt-2">Total Hours</p>
                                </div>
                            </div>
                        </div>

                        <!-- Filters and Search Section -->
                        <div class="bg-white rounded-lg p-6 border border-gray-100 mb-8">
                            <div class="grid grid-cols-4 gap-4">
                                <input 
                                    v-model="filters.search"
                                    type="text"
                                    placeholder="Search by date (e.g., Feb 5, 2026)"
                                    class="px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                />
                                <select 
                                    v-model="filters.month"
                                    class="px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                >
                                    <option value="01">January</option>
                                    <option value="02">February</option>
                                    <option value="03">March</option>
                                    <option value="04">April</option>
                                    <option value="05">May</option>
                                    <option value="06">June</option>
                                    <option value="07">July</option>
                                    <option value="08">August</option>
                                    <option value="09">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                                <select 
                                    v-model="filters.year"
                                    class="px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                >
                                    <option value="2026">2026</option>
                                    <option value="2025">2025</option>
                                    <option value="2024">2024</option>
                                </select>
                                <select 
                                    v-model="filters.status"
                                    class="px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                >
                                    <option value="">All Status</option>
                                    <option value="present">Present</option>
                                    <option value="late">Late</option>
                                    <option value="awol">AWOL</option>
                                </select>
                            </div>
                        </div>

                        <!-- Days List Section -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-semibold text-[#333333]">Days ({{ filteredDays.length }})</h3>
                                <p class="text-xs text-gray-500">Showing records for {{ filters.month }}/{{ filters.year }}</p>
                            </div>

                            <div v-if="filteredDays.length > 0" class="space-y-3">
                                <div 
                                    v-for="day in filteredDays"
                                    :key="day.id"
                                    class="bg-white rounded-lg border border-gray-100 overflow-hidden hover:shadow-md transition-shadow"
                                >
                                    <div class="flex items-center justify-between p-6">
                                        <div class="flex items-center gap-4 flex-1">
                                            <!-- Status Indicator Bar -->
                                            <div 
                                                class="w-1.5 h-20 rounded-full"
                                                :class="getStatusColor(day.status)"
                                            ></div>

                                            <!-- Day Information -->
                                            <div class="flex-1">
                                                <p class="font-semibold text-[#333333] text-base">{{ formatDate(day.date) }}</p>
                                                <p class="text-sm text-gray-600 mt-1">{{ day.dayOfWeek }} • {{ day.recordCount }} Records</p>
                                                <div class="flex gap-3 mt-2">
                                                    <span 
                                                        v-if="day.presentCount > 0"
                                                        class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-green-50 text-green-700"
                                                    >
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                                                        Present {{ day.presentCount }}
                                                    </span>
                                                    <span 
                                                        v-if="day.lateCount > 0"
                                                        class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-yellow-50 text-yellow-700"
                                                    >
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                                                        Late {{ day.lateCount }}
                                                    </span>
                                                    <span 
                                                        v-if="day.awolCount > 0"
                                                        class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-red-50 text-red-700"
                                                    >
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg>
                                                        AWOL {{ day.awolCount }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action Button -->
                                        <button 
                                            @click="handleViewDay(day)"
                                            class="px-6 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition-colors ml-4 shrink-0"
                                        >
                                            View
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Empty State -->
                            <div v-else class="bg-white rounded-lg border border-gray-100 p-12 text-center">
                                <div class="text-gray-400 mb-3">
                                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <p class="text-gray-600 font-medium">No timekeeping records found</p>
                                <p class="text-sm text-gray-500 mt-1">Try adjusting your filters</p>
                            </div>
                        </div>
                            </div>
                        </div>

                    </div>
            </div>

            <div class="fixed bottom-0 right-0 p-2 z-50">
                <img :src="agnes" class="h-auto w-auto" v-if="loading" />
            </div>
        </div>
    
</template>

<script setup>
    import { ref, computed, onMounted, watch } from 'vue'
    import { useRouter } from 'vue-router'
    import axios from 'axios'
    import { useAuthStore } from '@/stores/auth'
    import AuthLayout from '@/views/Layouts/AuthLayout.vue'
    import agnes from '@/assets/agnes.gif'
    import Swal from 'sweetalert2'

    const auth = useAuthStore()
    const router = useRouter()

    const loading = ref(false)
    const activeTab = ref('overview')

    auth.pageTitle = 'Timekeeping'

    // Statistics
    const stats = ref({
        presentToday: 0,
        lateToday: 0,
        awolToday: 0,
        missingTimeOut: 0,
        overtimeHours: 0
    })

    // Filter state
    const filters = ref({
        search: '',
        month: '01',
        year: '2026',
        status: ''
    })

    const days = ref([])

    // Tabs configuration
    const tabs = computed(() => [
        {
            id: 'overview',
            label: 'Overview',
            count: filteredDays.value.length,
            icon: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h1m0-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>'
        }
    ])

    // Computed: Filter days based on search and status
    const filteredDays = computed(() => days.value)

    // Helper: Get status color
    const getStatusColor = () => 'bg-blue-500'

    // Helper: Format date to readable format
    const formatDate = (dateString) => {
        const options = { year: 'numeric', month: 'long', day: 'numeric' }
        return new Date(dateString + 'T00:00:00').toLocaleDateString('en-US', options)
    }

    // Handler: View day details
    const handleViewDay = (day) => {
        router.push({
            name: 'timekeeping_detail',
            params: { date: day.date },
            query: { dateTitle: formatDate(day.date) }
        })
    }

    const fetchDays = async () => {
        try {
            loading.value = true
            const response = await axios.get('/api/timekeeping/days', {
                params: {
                    search: filters.value.search,
                    month: filters.value.month,
                    year: filters.value.year,
                    status: filters.value.status
                }
            })
            days.value = response.data?.data || []
            updateStatsFromDays()
        } catch (error) {
            console.error('Failed to load timekeeping days', error)
        } finally {
            loading.value = false
        }
    }

    const updateStatsFromDays = () => {
        if (!days.value.length) {
            stats.value = {
                presentToday: 0,
                lateToday: 0,
                awolToday: 0,
                missingTimeOut: 0,
                overtimeHours: 0
            }
            return
        }

        const totals = days.value.reduce(
            (acc, day) => {
                acc.present += day.presentCount || 0
                acc.late += day.lateCount || 0
                acc.awol += day.awolCount || 0
                return acc
            },
            { present: 0, late: 0, awol: 0 }
        )

        stats.value = {
            presentToday: totals.present,
            lateToday: totals.late,
            awolToday: totals.awol,
            missingTimeOut: 0,
            overtimeHours: 0
        }
    }

    // Handler: Record attendance
    const handleRecordAttendance = () => {
        Swal.fire({
            title: 'Record Attendance',
            html: `
                <div class="text-left">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Select Employee</label>
                        <input type="text" id="employeeSearch" placeholder="Search employee..." class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date *</label>
                        <input type="date" id="attendanceDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Time In *</label>
                        <input type="time" id="timeIn" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Time Out</label>
                        <input type="time" id="timeOut" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
                        <select id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="present">Present</option>
                            <option value="late">Late</option>
                            <option value="awol">AWOL</option>
                        </select>
                    </div>
                </div>
            `,
            confirmButtonText: 'Record Attendance',
            confirmButtonColor: '#0c8ce9',
            cancelButtonText: 'Cancel',
            showCancelButton: true,
            didOpen: () => {
                // Set today's date as default
                const today = new Date().toISOString().split('T')[0]
                document.getElementById('attendanceDate').value = today
                document.getElementById('employeeSearch').focus()
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const employeeSearch = document.getElementById('employeeSearch').value
                const attendanceDate = document.getElementById('attendanceDate').value
                const timeIn = document.getElementById('timeIn').value
                const timeOut = document.getElementById('timeOut').value
                const status = document.getElementById('status').value

                // Validate required fields
                if (!attendanceDate || !timeIn || !status) {
                    Swal.fire('Error', 'Please fill in all required fields', 'error')
                    return
                }

                // TODO: Send to API
                // axios.post('/api/timekeeping/record', { employeeSearch, attendanceDate, timeIn, timeOut, status })

                Swal.fire({
                    icon: 'success',
                    title: 'Attendance Recorded',
                    text: 'Employee attendance has been recorded successfully.',
                    confirmButtonColor: '#0c8ce9'
                })
            }
        })
    }

    onMounted(() => {
        fetchDays()
    })

    watch(filters, () => {
        fetchDays()
    }, { deep: true })
</script>
