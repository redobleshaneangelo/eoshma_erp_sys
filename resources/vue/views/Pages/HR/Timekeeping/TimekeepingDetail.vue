<template>
    <auth-layout />
        <div class="ps-60 bg-[#F3F4F6] min-h-screen">
            <div class="px-10 py-10">
                    <!-- Back Button -->
                    <button
                        @click="goBack"
                        class="flex items-center text-[#0c8ce9] hover:text-blue-700 font-semibold mb-4 transition-colors"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Timekeeping
                    </button>

                    <!-- Header Card -->
                    <div class="bg-white rounded-lg border border-gray-100 p-6 mb-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold text-[#333333]">{{ dateTitle }}</h1>
                                <p class="text-sm text-gray-600 mt-1">Timekeeping Records</p>
                            </div>
                            <div class="flex gap-3">
                                <button
                                    @click="exportToPDF"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-100 transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Export
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-4 gap-4 mb-8">
                        <div class="bg-white rounded-lg p-6 border border-gray-100 shadow-sm">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Total Records</p>
                            <p class="text-4xl font-bold text-[#0c8ce9]">{{ totalRecords }}</p>
                            <p class="text-xs text-gray-500 mt-2">Employees</p>
                        </div>
                        <div class="bg-white rounded-lg p-6 border border-gray-100 shadow-sm">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Present</p>
                            <p class="text-4xl font-bold text-green-500">{{ presentCount }}</p>
                            <p class="text-xs text-gray-500 mt-2">On Time</p>
                        </div>
                        <div class="bg-white rounded-lg p-6 border border-gray-100 shadow-sm">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Late</p>
                            <p class="text-4xl font-bold text-yellow-500">{{ lateCount }}</p>
                            <p class="text-xs text-gray-500 mt-2">Delayed</p>
                        </div>
                        <div class="bg-white rounded-lg p-6 border border-gray-100 shadow-sm">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">AWOL</p>
                            <p class="text-4xl font-bold text-red-500">{{ awolCount }}</p>
                            <p class="text-xs text-gray-500 mt-2">Absent</p>
                        </div>
                    </div>

                    <!-- Filter and Search -->
                    <div class="bg-white rounded-lg p-6 border border-gray-100 mb-8">
                        <div class="grid grid-cols-2 gap-4">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search by employee name or ID..."
                                class="px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                            />
                            <select
                                v-model="statusFilter"
                                class="px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                            >
                                <option value="">All Status</option>
                                <option value="present">Present</option>
                                <option value="late">Late</option>
                                <option value="awol">AWOL</option>
                            </select>
                        </div>
                    </div>

                    <!-- Records Table -->
                    <div class="bg-white rounded-lg border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                            Employee ID
                                        </th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                            Name
                                        </th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                            Time In
                                        </th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                            Time Out
                                        </th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                            Work Hours
                                        </th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                            Status
                                        </th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr
                                        v-for="employee in employees"
                                        :key="employee.id"
                                        class="hover:bg-gray-50 transition-colors"
                                    >
                                        <td class="px-6 py-4">
                                            <span class="font-semibold text-gray-900">#{{ String(employee.id).padStart(4, '0') }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-gray-900 font-medium">{{ employee.name }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span v-if="employee.timeIn" class="text-gray-700">{{ employee.timeIn }}</span>
                                            <span v-else class="text-gray-400 italic">—</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span v-if="employee.timeOut" class="text-gray-700">{{ employee.timeOut }}</span>
                                            <span v-else class="text-gray-400 italic">—</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span v-if="employee.timeIn && employee.timeOut" class="text-gray-700">
                                                {{ calculateWorkHours(employee.timeIn, employee.timeOut) }}
                                            </span>
                                            <span v-else class="text-gray-400 italic">—</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                :class="getStatusBadgeClass(employee.status)"
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold"
                                            >
                                                <svg v-if="employee.status === 'present'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                                                <svg v-else-if="employee.status === 'late'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                                                <svg v-else-if="employee.status === 'awol'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg>
                                                {{ capitalizeStatus(employee.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <button
                                                @click="editRecord(employee)"
                                                class="text-[#0c8ce9] hover:text-blue-700 font-semibold text-sm transition-colors"
                                            >
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div v-if="employees.length === 0" class="p-12 text-center">
                            <div class="text-gray-400 mb-3">
                                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="text-gray-600 font-medium">No records found</p>
                            <p class="text-sm text-gray-500 mt-1">Try adjusting your filters</p>
                        </div>
                    </div>
                    <div v-if="meta.total > 0" class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50">
                        <div class="text-sm text-gray-600">
                            Showing {{ (meta.current_page - 1) * meta.per_page + 1 }} to
                            {{ Math.min(meta.current_page * meta.per_page, meta.total) }} of
                            {{ meta.total }}
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                @click="previousPage"
                                :disabled="meta.current_page === 1"
                                class="px-3 py-1.5 border border-gray-300 rounded text-sm font-semibold text-gray-700 hover:bg-gray-100 disabled:bg-gray-100 disabled:cursor-not-allowed disabled:text-gray-400 transition-colors"
                            >
                                Previous
                            </button>
                            <div class="flex gap-1">
                                <button
                                    v-for="page in visiblePages"
                                    :key="page"
                                    @click="goToPage(page)"
                                    :class="meta.current_page === page ? 'bg-[#0c8ce9] text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-100'"
                                    class="px-3 py-1.5 rounded text-sm font-semibold transition-colors"
                                >
                                    {{ page }}
                                </button>
                            </div>
                            <button
                                @click="nextPage"
                                :disabled="meta.current_page === meta.last_page"
                                class="px-3 py-1.5 border border-gray-300 rounded text-sm font-semibold text-gray-700 hover:bg-gray-100 disabled:bg-gray-100 disabled:cursor-not-allowed disabled:text-gray-400 transition-colors"
                            >
                                Next
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
    import { ref, computed, onMounted, watch } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import axios from 'axios'
    import { useAuthStore } from '@/stores/auth'
    import AuthLayout from '@/views/Layouts/AuthLayout.vue'
    import agnes from '@/assets/agnes.gif'
    import Swal from 'sweetalert2'

    const auth = useAuthStore()
    const router = useRouter()
    const route = useRoute()

    const loading = ref(false)
    const searchQuery = ref('')
    const statusFilter = ref('')
    const employees = ref([])
    const meta = ref({
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0
    })
    const dayData = ref({
        date: '',
        dayOfWeek: ''
    })
    const summary = ref({
        presentCount: 0,
        lateCount: 0,
        awolCount: 0
    })

    const dateTitle = computed(() => {
        const routeTitle = route.query.dateTitle
        if (routeTitle) return routeTitle
        if (dayData.value.date && dayData.value.dayOfWeek) {
            return `${dayData.value.dayOfWeek} - ${dayData.value.date}`
        }
        return 'Timekeeping Detail'
    })

    const presentCount = computed(() => summary.value.presentCount)
    const lateCount = computed(() => summary.value.lateCount)
    const awolCount = computed(() => summary.value.awolCount)
    const totalRecords = computed(() => presentCount.value + lateCount.value + awolCount.value)

    const visiblePages = computed(() => {
        const pages = []
        const maxVisible = 5
        let start = Math.max(1, meta.value.current_page - Math.floor(maxVisible / 2))
        let end = Math.min(meta.value.last_page, start + maxVisible - 1)

        if (end - start + 1 < maxVisible) {
            start = Math.max(1, end - maxVisible + 1)
        }

        for (let i = start; i <= end; i++) {
            pages.push(i)
        }
        return pages
    })

    // Helper: Calculate work hours
    const calculateWorkHours = (timeIn, timeOut) => {
        if (!timeIn || !timeOut) return '00:00:00'
        const [inH, inM, inS = '0'] = timeIn.split(':')
        const [outH, outM, outS = '0'] = timeOut.split(':')
        const inSeconds = Number(inH) * 3600 + Number(inM) * 60 + Number(inS)
        const outSeconds = Number(outH) * 3600 + Number(outM) * 60 + Number(outS)
        const diffSeconds = Math.max(outSeconds - inSeconds, 0)
        const hours = Math.floor(diffSeconds / 3600)
        const minutes = Math.floor((diffSeconds % 3600) / 60)
        const seconds = diffSeconds % 60
        return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
    }

    // Helper: Get status badge class
    const getStatusBadgeClass = (status) => {
        const classes = {
            present: 'bg-green-50 text-green-700',
            late: 'bg-yellow-50 text-yellow-700',
            awol: 'bg-red-50 text-red-700'
        }
        return classes[status] || 'bg-gray-50 text-gray-700'
    }

    // Helper: Capitalize status
    const capitalizeStatus = (status) => {
        return status.charAt(0).toUpperCase() + status.slice(1)
    }

    // Actions
    const goBack = () => {
        router.back()
    }

    const editRecord = (employee) => {
        Swal.fire({
            title: `Edit Record - ${employee.name}`,
            html: `
                <div class="text-left">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Time In</label>
                        <input type="time" id="timeIn" value="${employee.timeIn || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Time Out</label>
                        <input type="time" id="timeOut" value="${employee.timeOut || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
                    </div>
                </div>
            `,
            confirmButtonText: 'Save Changes',
            confirmButtonColor: '#0c8ce9',
            cancelButtonText: 'Cancel',
            showCancelButton: true,
            didOpen: () => {
                document.getElementById('timeIn').focus()
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const timeIn = document.getElementById('timeIn').value
                const timeOut = document.getElementById('timeOut').value
                axios.patch(`/api/timekeeping/days/${dayData.value.date}/employees/${employee.id}`, {
                    time_in: timeIn || null,
                    time_out: timeOut || null
                }).then(() => {
                    fetchDetail()
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Record updated',
                        showConfirmButton: false,
                        timer: 2000
                    })
                }).catch((error) => {
                    console.error('Failed to update record', error)
                    Swal.fire({
                        icon: 'error',
                        title: 'Update failed',
                        text: 'Please try again.',
                        confirmButtonColor: '#ef4444'
                    })
                })
            }
        })
    }

    const exportToPDF = async () => {
        try {
            loading.value = true
            
            // Create print window with all data
            const printWindow = window.open('', '', 'height=800,width=1000')
            
            let htmlContent = `
                <html>
                    <head>
                        <title>Timekeeping Report - ${dayData.value.date}</title>
                        <style>
                            * { margin: 0; padding: 0; box-sizing: border-box; }
                            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; padding: 20px; }
                            h1 { font-size: 28px; font-weight: bold; margin-bottom: 10px; color: #1f2937; }
                            .date-info { color: #666; margin-bottom: 20px; font-size: 14px; }
                            .summary-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 30px; }
                            .summary-card { border: 1px solid #ddd; padding: 15px; border-radius: 5px; text-align: center; background: #f9fafb; }
                            .summary-card-label { font-size: 12px; color: #999; margin-bottom: 10px; text-transform: uppercase; font-weight: bold; }
                            .summary-card-value { font-size: 32px; font-weight: bold; }
                            .table-section { margin-top: 30px; }
                            .table-title { font-size: 18px; font-weight: bold; margin-bottom: 15px; color: #1f2937; }
                            table { width: 100%; border-collapse: collapse; }
                            th { background-color: #f3f4f6; border-bottom: 2px solid #d1d5db; padding: 12px; text-align: left; font-size: 12px; font-weight: bold; color: #374151; }
                            td { padding: 12px; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
                            tr:nth-child(even) { background-color: #f9fafb; }
                            .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 11px; color: #9ca3af; text-align: right; }
                            @media print {
                                body { margin: 0; padding: 0; }
                                .table-section { page-break-inside: avoid; }
                            }
                        </style>
                    </head>
                    <body>
                        <h1>${dateTitle.value}</h1>
                        <div class="date-info">Date: ${dayData.value.date} (${dayData.value.dayOfWeek})</div>
                        
                        <div class="summary-cards">
                            <div class="summary-card">
                                <div class="summary-card-label">Total Records</div>
                                <div class="summary-card-value" style="color: #0c8ce9;">${totalRecords.value}</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-card-label">Present</div>
                                <div class="summary-card-value" style="color: #10b981;">${presentCount.value}</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-card-label">Late</div>
                                <div class="summary-card-value" style="color: #f59e0b;">${lateCount.value}</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-card-label">AWOL</div>
                                <div class="summary-card-value" style="color: #ef4444;">${awolCount.value}</div>
                            </div>
                        </div>
                        
                        <div class="table-section">
                            <div class="table-title">Timekeeping Records</div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Time In</th>
                                        <th>Time Out</th>
                                        <th>Work Hours</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${employees.value.map(emp => `
                                        <tr>
                                            <td>#${String(emp.id).padStart(4, '0')}</td>
                                            <td>${emp.name}</td>
                                            <td>${emp.timeIn || '—'}</td>
                                            <td>${emp.timeOut || '—'}</td>
                                            <td>${(emp.timeIn && emp.timeOut) ? calculateWorkHours(emp.timeIn, emp.timeOut) : '—'}</td>
                                            <td>${emp.status.charAt(0).toUpperCase() + emp.status.slice(1)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="footer">Generated on ${new Date().toLocaleString()}</div>
                    </body>
                </html>
            `
            
            printWindow.document.write(htmlContent)
            printWindow.document.close()
            
            // Trigger download dialog
            setTimeout(() => {
                printWindow.focus()
                printWindow.print()
                // Close after print
                setTimeout(() => printWindow.close(), 1000)
            }, 250)
            
            Swal.fire({
                icon: 'success',
                title: 'PDF Ready',
                text: `Timekeeping report for ${dayData.value.date} is ready for download.`,
                confirmButtonColor: '#0c8ce9'
            })
        } catch (error) {
            console.error('Error exporting PDF:', error)
            Swal.fire({
                icon: 'error',
                title: 'Export Failed',
                text: 'Failed to export PDF. Please try again.',
                confirmButtonColor: '#ef4444'
            })
        } finally {
            loading.value = false
        }
    }

    const printRecords = () => {
        try {
            // Create a new window for printing
            const printWindow = window.open('', '', 'height=800,width=1000')
            
            let htmlContent = `
                <html>
                    <head>
                        <title>Timekeeping Report - ${dayData.value.date}</title>
                        <style>
                            * { margin: 0; padding: 0; box-sizing: border-box; }
                            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; padding: 20px; }
                            h1 { font-size: 28px; font-weight: bold; margin-bottom: 10px; color: #1f2937; }
                            .date-info { color: #666; margin-bottom: 20px; font-size: 14px; }
                            .summary-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 30px; }
                            .summary-card { border: 1px solid #ddd; padding: 15px; border-radius: 5px; text-align: center; background: #f9fafb; }
                            .summary-card-label { font-size: 12px; color: #999; margin-bottom: 10px; text-transform: uppercase; font-weight: bold; }
                            .summary-card-value { font-size: 32px; font-weight: bold; }
                            .table-section { margin-top: 30px; }
                            .table-title { font-size: 18px; font-weight: bold; margin-bottom: 15px; color: #1f2937; }
                            table { width: 100%; border-collapse: collapse; }
                            th { background-color: #f3f4f6; border-bottom: 2px solid #d1d5db; padding: 12px; text-align: left; font-size: 12px; font-weight: bold; color: #374151; }
                            td { padding: 12px; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
                            tr:nth-child(even) { background-color: #f9fafb; }
                            .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 11px; color: #9ca3af; text-align: right; }
                            @media print {
                                body { margin: 0; padding: 20px; }
                                .table-section { page-break-inside: avoid; }
                            }
                        </style>
                    </head>
                    <body>
                        <h1>${dateTitle.value}</h1>
                        <div class="date-info">Date: ${dayData.value.date} (${dayData.value.dayOfWeek})</div>
                        
                        <div class="summary-cards">
                            <div class="summary-card">
                                <div class="summary-card-label">Total Records</div>
                                <div class="summary-card-value" style="color: #0c8ce9;">${totalRecords.value}</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-card-label">Present</div>
                                <div class="summary-card-value" style="color: #10b981;">${presentCount.value}</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-card-label">Late</div>
                                <div class="summary-card-value" style="color: #f59e0b;">${lateCount.value}</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-card-label">AWOL</div>
                                <div class="summary-card-value" style="color: #ef4444;">${awolCount.value}</div>
                            </div>
                        </div>
                        
                        <div class="table-section">
                            <div class="table-title">Timekeeping Records</div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Time In</th>
                                        <th>Time Out</th>
                                        <th>Work Hours</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${employees.value.map(emp => `
                                        <tr>
                                            <td>#${String(emp.id).padStart(4, '0')}</td>
                                            <td>${emp.name}</td>
                                            <td>${emp.timeIn || '—'}</td>
                                            <td>${emp.timeOut || '—'}</td>
                                            <td>${(emp.timeIn && emp.timeOut) ? calculateWorkHours(emp.timeIn, emp.timeOut) : '—'}</td>
                                            <td>${emp.status.charAt(0).toUpperCase() + emp.status.slice(1)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="footer">Generated on ${new Date().toLocaleString()}</div>
                    </body>
                </html>
            `
            
            printWindow.document.write(htmlContent)
            printWindow.document.close()
            
            setTimeout(() => {
                printWindow.focus()
                printWindow.print()
            }, 250)
        } catch (error) {
            console.error('Error printing:', error)
            Swal.fire({
                icon: 'error',
                title: 'Print Failed',
                text: 'Failed to print records. Please try again.',
                confirmButtonColor: '#ef4444'
            })
        }
    }

    const fetchDetail = async () => {
        const date = route.params.date
        if (!date) return
        try {
            loading.value = true
            const response = await axios.get(`/api/timekeeping/days/${date}`, {
                params: {
                    page: meta.value.current_page,
                    per_page: meta.value.per_page,
                    search: searchQuery.value,
                    status: statusFilter.value
                }
            })
            employees.value = response.data?.data || []
            meta.value = response.data?.meta || meta.value
            summary.value = response.data?.summary || summary.value
            dayData.value = {
                date: summary.value.date || date,
                dayOfWeek: summary.value.dayOfWeek || ''
            }
        } catch (error) {
            console.error('Failed to load timekeeping detail', error)
        } finally {
            loading.value = false
        }
    }

    const previousPage = () => {
        if (meta.value.current_page > 1) {
            meta.value.current_page -= 1
            fetchDetail()
        }
    }

    const nextPage = () => {
        if (meta.value.current_page < meta.value.last_page) {
            meta.value.current_page += 1
            fetchDetail()
        }
    }

    const goToPage = (page) => {
        if (page !== meta.value.current_page) {
            meta.value.current_page = page
            fetchDetail()
        }
    }

    watch([searchQuery, statusFilter], () => {
        meta.value.current_page = 1
        fetchDetail()
    })

    watch(() => route.params.date, () => {
        meta.value.current_page = 1
        fetchDetail()
    })

    onMounted(() => {
        fetchDetail()
    })
</script>

<style scoped>
    @media print {
        .ps-56\.25 {
            padding-left: 0;
        }
    }
</style>
