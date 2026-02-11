<template>
    <div class="space-y-6">
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <div class="grid grid-cols-4 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search by request ID, date, day type, or reason..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Day Type</label>
                    <select
                        v-model="filters.dayType"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                    >
                        <option value="">All Day Types</option>
                        <option v-for="option in dayTypeOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select
                        v-model="filters.status"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                    >
                        <option value="">All Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button
                        @click="clearFilters"
                        class="w-full px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-300 transition-colors"
                    >
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-700">Overtime Requests</h2>
                        <p class="text-xs text-gray-500">View or cancel your submitted overtime requests.</p>
                    </div>
                    <button
                        @click="fetchRequests"
                        class="px-3 py-1.5 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                    >
                        Refresh
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-4">
                <div v-if="isLoading" class="py-10 text-center text-sm text-gray-500">
                    Loading requests...
                </div>

                <div v-else-if="paginatedRequests.length === 0" class="py-10 text-center">
                    <svg class="w-14 h-14 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-base font-semibold text-gray-900 mb-1">No overtime requests</h3>
                    <p class="text-sm text-gray-600">Try adjusting your filters or submit a new request.</p>
                </div>

                <div v-else class="space-y-4">
                    <div
                        v-for="request in paginatedRequests"
                        :key="request.id"
                        class="relative overflow-hidden border border-gray-200 rounded-xl p-5 bg-white shadow-sm hover:shadow-md transition-shadow"
                    >
                        <div class="absolute inset-y-0 left-0 w-1 bg-[#0c8ce9]"></div>
                        <div class="flex flex-wrap items-start justify-between gap-4 pl-2">
                            <div>
                                <div class="text-xs uppercase tracking-wide text-gray-500">Request</div>
                                <div class="text-lg font-semibold text-gray-900">OT-{{ request.id }}</div>
                                <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600 mt-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">
                                        {{ formatDate(request.date) }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-slate-50 text-slate-700 text-xs font-semibold">
                                        {{ formatHours(request.hours) }}
                                    </span>
                                </div>
                            </div>
                            <span :class="statusPillClass(request.status)">
                                {{ request.status || 'Pending' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5 text-sm pl-2">
                            <div>
                                <div class="text-gray-500">Start Time</div>
                                <div class="font-semibold text-gray-900">{{ request.startTime || '--' }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Day Type</div>
                                <div class="font-semibold text-gray-900">{{ request.dayTypeLabel || request.dayType }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Submitted</div>
                                <div class="font-semibold text-gray-900">{{ formatDateTime(request.requestedAt) }}</div>
                            </div>
                        </div>

                        <div class="mt-4 text-sm text-gray-600 pl-2">
                            <span class="font-semibold text-gray-700">Reason:</span>
                            {{ request.reason || 'No reason provided.' }}
                        </div>

                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 pl-2">
                            <div class="text-xs text-gray-500">
                                Approved Hours: {{ formatValue(request.approvedHours) }}
                            </div>
                            <div class="flex items-center gap-3">
                                <button
                                    @click="cancelRequest(request)"
                                    :disabled="request.status !== 'Pending'"
                                    class="px-3 py-1.5 text-xs font-semibold rounded border transition-colors"
                                    :class="request.status === 'Pending'
                                        ? 'border-red-200 text-red-600 hover:bg-red-50'
                                        : 'border-gray-200 text-gray-400 cursor-not-allowed'"
                                >
                                    Cancel Request
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-wrap items-center justify-between gap-3">
                <span class="text-sm font-semibold text-gray-800">
                    Total Requests: <span class="text-[#0c8ce9]">{{ filteredRequests.length }}</span>
                </span>
                <div class="flex items-center gap-2">
                    <button
                        @click="previousPage"
                        :disabled="currentPage === 1"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        Previous
                    </button>
                    <span class="text-sm text-gray-600">Page {{ currentPage }} of {{ totalPages }}</span>
                    <button
                        @click="nextPage"
                        :disabled="currentPage === totalPages"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed, onMounted, watch } from 'vue'
    import axios from 'axios'
    import Swal from 'sweetalert2'
    import { useAuthStore } from '@/stores/auth'

    const auth = useAuthStore()
    const searchQuery = ref('')
    const currentPage = ref(1)
    const itemsPerPage = 6
    const isLoading = ref(false)

    const filters = ref({
        dayType: '',
        status: ''
    })

    const pendingRequests = ref([])

    const dayTypeOptions = computed(() => {
        const map = new Map()
        pendingRequests.value.forEach(item => {
            if (item.dayType) {
                map.set(item.dayType, item.dayTypeLabel || item.dayType)
            }
        })
        return Array.from(map.entries()).map(([value, label]) => ({ value, label }))
    })

    const filteredRequests = computed(() => {
        return pendingRequests.value.filter(req => {
            const haystack = [
                String(req.id ?? ''),
                req.date,
                req.dayTypeLabel,
                req.dayType,
                req.status,
                req.reason
            ]
                .filter(Boolean)
                .join(' ')
                .toLowerCase()

            const matchSearch = !searchQuery.value || haystack.includes(searchQuery.value.toLowerCase())
            const matchDayType = !filters.value.dayType || req.dayType === filters.value.dayType
            const matchStatus = !filters.value.status || req.status === filters.value.status

            return matchSearch && matchDayType && matchStatus
        })
    })

    const totalPages = computed(() => {
        return Math.max(1, Math.ceil(filteredRequests.value.length / itemsPerPage))
    })

    const paginatedRequests = computed(() => {
        const start = (currentPage.value - 1) * itemsPerPage
        const end = start + itemsPerPage
        return filteredRequests.value.slice(start, end)
    })

    const fetchRequests = async () => {
        isLoading.value = true
        try {
            const response = await axios.get('/api/overtime-requests')
            pendingRequests.value = response.data?.data || []
        } catch (error) {
            console.error('Failed to load overtime requests', error)
            Swal.fire({
                icon: 'error',
                title: 'Load failed',
                text: 'Unable to load overtime requests right now.',
                confirmButtonColor: '#ef4444'
            })
        } finally {
            isLoading.value = false
        }
    }

    const formatValue = (value) => {
        if (value === null || value === undefined || value === '') return '--'
        return value
    }

    const formatDate = (date) => {
        if (!date) return '--'
        return new Date(date).toLocaleDateString('en-PH', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        })
    }

    const formatDateTime = (date) => {
        if (!date) return '--'
        return new Date(date).toLocaleString('en-PH', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        })
    }

    const formatHours = (hours) => {
        if (hours === null || hours === undefined) return '--'
        return `${Number(hours).toFixed(2)} hour(s)`
    }

    const statusPillClass = (status) => {
        switch (status) {
            case 'Approved':
                return 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700'
            case 'Rejected':
                return 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700'
            case 'Cancelled':
                return 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600'
            default:
                return 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800'
        }
    }

    const cancelRequest = async (request) => {
        if (request.status !== 'Pending') {
            return
        }

        const { isConfirmed } = await Swal.fire({
            title: 'Cancel Request?',
            text: `Cancel overtime request OT-${request.id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Cancel Request'
        })

        if (!isConfirmed) {
            return
        }

        try {
            await axios.put(`/api/overtime-requests/${request.id}/cancel`)
            await fetchRequests()
            Swal.fire({
                icon: 'success',
                title: 'Cancelled',
                text: 'Your request has been cancelled.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
        } catch (error) {
            console.error('Failed to cancel overtime request', error)
            const message = error.response?.data?.message || 'Failed to cancel the request.'
            Swal.fire({
                icon: 'error',
                title: 'Cancel failed',
                text: message,
                confirmButtonColor: '#ef4444'
            })
        }
    }

    const clearFilters = () => {
        searchQuery.value = ''
        filters.value = {
            dayType: '',
            status: ''
        }
        currentPage.value = 1
    }

    const previousPage = () => {
        if (currentPage.value > 1) {
            currentPage.value--
        }
    }

    const nextPage = () => {
        if (currentPage.value < totalPages.value) {
            currentPage.value++
        }
    }

    watch([searchQuery, filters], () => {
        currentPage.value = 1
    }, { deep: true })

    onMounted(() => {
        auth.pageTitle = 'Pending Requests'
        fetchRequests()
    })
</script>
