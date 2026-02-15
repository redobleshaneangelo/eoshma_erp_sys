<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-600">My Attendance</p>
                <p class="text-xs text-gray-500">Records for {{ userLabel }}</p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    @click="openOvertimeModal"
                    class="px-5 py-2.5 bg-amber-500 text-white rounded-lg font-semibold text-sm hover:bg-amber-600 transition-colors flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Request OT
                </button>
                <button
                    @click="openQRModal"
                    class="px-5 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition-colors flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z" />
                    </svg>
                    My QR for Today
                </button>
                <button
                    @click="openQRScanner('in')"
                    class="px-5 py-2.5 bg-green-600 text-white rounded-lg font-semibold text-sm hover:bg-green-700 transition-colors flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Time In
                </button>
                <button
                    @click="openQRScanner('out')"
                    class="px-5 py-2.5 bg-red-600 text-white rounded-lg font-semibold text-sm hover:bg-red-700 transition-colors flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2m11-11a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Time Out
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Total Days</p>
                <p class="text-3xl font-bold text-[#333333]">{{ records.length }}</p>
                <p class="text-xs text-gray-500 mt-2">Logged Records</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Time In Only</p>
                <p class="text-3xl font-bold text-blue-600">{{ timeInOnlyCount }}</p>
                <p class="text-xs text-gray-500 mt-2">Needs Time Out</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Clocked Out</p>
                <p class="text-3xl font-bold text-green-600">{{ clockedOutCount }}</p>
                <p class="text-xs text-gray-500 mt-2">Complete</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Absent</p>
                <p class="text-3xl font-bold text-red-600">{{ absentCount }}</p>
                <p class="text-xs text-gray-500 mt-2">No Time In/Out</p>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Search by Date or Day</label>
                    <div class="relative">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search attendance..."
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                        />
                        <svg
                            class="absolute right-3 top-3 w-5 h-5 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date</label>
                    <input
                        v-model="selectedDate"
                        type="date"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select
                        v-model="selectedStatus"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    >
                        <option value="">All Status</option>
                        <option value="time-in">Time In Only</option>
                        <option value="clocked-out">Clocked Out</option>
                        <option value="absent">Absent</option>
                    </select>
                </div>
            </div>

            <div v-if="hasActiveFilters" class="mt-3 pt-3 border-t border-gray-200">
                <button
                    @click="clearFilters"
                    class="text-sm text-[#0c8ce9] font-semibold hover:underline"
                >
                    Clear All Filters
                </button>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700">
                    Attendance Records
                    <span class="text-gray-500">{{ filteredRecords.length }} records</span>
                </h3>
                <div class="flex items-center gap-3">
                    <select
                        v-model="pageSize"
                        class="px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    >
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200 sticky top-0">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <button
                                    @click="toggleSort('date')"
                                    class="text-xs font-semibold text-gray-700 uppercase tracking-wide hover:text-[#0c8ce9] transition-colors flex items-center gap-1"
                                >
                                    Date
                                    <svg
                                        v-if="sortField === 'date'"
                                        class="w-3 h-3"
                                        :class="sortDirection === 'asc' ? 'rotate-180' : ''"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Day
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Time In
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Time In Location
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Time Out
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Time Out Location
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Status
                            </th>
                            <!-- Actions column removed -->
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="record in paginatedRecords"
                            :key="record.date"
                            class="border-b border-gray-200 hover:bg-gray-50 transition-colors"
                        >
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ record.date }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ record.dayOfWeek }}</td>
                            <td class="px-6 py-4 text-sm font-semibold">
                                <div class="flex items-center gap-2">
                                    <span
                                        v-if="record.timeIn"
                                        class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold"
                                    >
                                        {{ record.timeIn }}
                                    </span>
                                    <span v-else class="text-gray-400 text-xs">—</span>
                                    <button
                                        v-if="record.timeInPhoto"
                                        @click="openPhotoPreview(record, 'in')"
                                        class="text-xs text-[#0c8ce9] font-semibold hover:underline"
                                    >
                                        View
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div v-if="record.timeInGeo?.label" class="font-medium">{{ record.timeInGeo.label }}</div>
                                <div v-else class="text-gray-400 text-xs">—</div>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold">
                                <div class="flex items-center gap-2">
                                    <span
                                        v-if="record.timeOut"
                                        class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold"
                                    >
                                        {{ record.timeOut }}
                                    </span>
                                    <span v-else class="text-gray-400 text-xs">—</span>
                                    <button
                                        v-if="record.timeOutPhoto"
                                        @click="openPhotoPreview(record, 'out')"
                                        class="text-xs text-[#0c8ce9] font-semibold hover:underline"
                                    >
                                        View
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div v-if="record.timeOutGeo?.label" class="font-medium">{{ record.timeOutGeo.label }}</div>
                                <div v-else class="text-gray-400 text-xs">—</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span
                                    :class="getStatusBadgeClass(record.status)"
                                    class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                                >
                                    {{ formatStatus(record.status) }}
                                </span>
                            </td>
                            <!-- Actions column removed -->
                        </tr>
                    </tbody>
                </table>

                <div
                    v-if="filteredRecords.length === 0"
                    class="flex flex-col items-center justify-center py-16 px-6"
                >
                    <svg
                        class="w-16 h-16 text-gray-300 mb-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <p class="text-gray-600 font-semibold">No attendance records found</p>
                    <p class="text-gray-400 text-sm mt-1">Try adjusting your filters</p>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="filteredRecords.length > 0" class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="text-sm text-gray-600">
                    Showing {{ (currentPage - 1) * pageSize + 1 }} to
                    {{ Math.min(currentPage * pageSize, filteredRecords.length) }} of
                    {{ filteredRecords.length }}
                </div>
                <div class="flex items-center gap-2">
                    <button
                        @click="previousPage"
                        :disabled="currentPage === 1"
                        class="px-3 py-1.5 border border-gray-300 rounded text-sm font-semibold text-gray-700 hover:bg-gray-100 disabled:bg-gray-100 disabled:cursor-not-allowed disabled:text-gray-400 transition-colors"
                    >
                        Previous
                    </button>
                    <div class="flex gap-1">
                        <button
                            v-for="page in visiblePages"
                            :key="page"
                            @click="goToPage(page)"
                            :class="currentPage === page ? 'bg-[#0c8ce9] text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-100'"
                            class="px-3 py-1.5 rounded text-sm font-semibold transition-colors"
                        >
                            {{ page }}
                        </button>
                    </div>
                    <button
                        @click="nextPage"
                        :disabled="currentPage === totalPages"
                        class="px-3 py-1.5 border border-gray-300 rounded text-sm font-semibold text-gray-700 hover:bg-gray-100 disabled:bg-gray-100 disabled:cursor-not-allowed disabled:text-gray-400 transition-colors"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>

        <div
            v-if="photoPreview"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            @click="closePhotoPreview"
        >
            <div
                class="bg-white rounded-lg shadow-xl max-w-xl w-full"
                @click.stop
            >
                <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-800">{{ photoPreviewTitle }}</h2>
                    <button
                        @click="closePhotoPreview"
                        class="text-gray-500 hover:text-gray-700 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-6 space-y-4">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-700">
                        <div><strong>Date:</strong> {{ photoPreviewRecord.date }}</div>
                        <div><strong>Day:</strong> {{ photoPreviewRecord.dayOfWeek }}</div>
                        <div><strong>Time In:</strong> {{ photoPreviewRecord.timeIn || '—' }}</div>
                        <div><strong>Time Out:</strong> {{ photoPreviewRecord.timeOut || '—' }}</div>
                        <div><strong>Time In Location:</strong> {{ photoPreviewRecord.timeInGeo?.label || '—' }}</div>
                        <div><strong>Time Out Location:</strong> {{ photoPreviewRecord.timeOutGeo?.label || '—' }}</div>
                        <div><strong>Time In Coordinates:</strong> {{ geoSummary(photoPreviewRecord.timeInGeo) }}</div>
                        <div><strong>Time Out Coordinates:</strong> {{ geoSummary(photoPreviewRecord.timeOutGeo) }}</div>
                        <div><strong>Status:</strong> {{ formatStatus(photoPreviewRecord.status) }}</div>
                    </div>
                    <div class="flex items-center justify-center">
                        <img
                            v-if="photoPreviewUrl"
                            :src="photoPreviewUrl"
                            alt="Attendance photo"
                            class="max-h-96 w-full object-contain rounded-lg border border-gray-200"
                        />
                        <div v-else class="text-sm text-gray-500">No photo available.</div>
                    </div>
                </div>
                <div class="border-t border-gray-200 px-6 py-4 flex justify-end">
                    <button
                        @click="closePhotoPreview"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>

        <div v-if="showQRModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-800">My QR Code</h2>
                    <button
                        @click="closeQRModal"
                        class="text-gray-500 hover:text-gray-700 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-6">
                    <div class="text-sm text-gray-600 mb-4">
                        Show this QR to the scanner for today.
                    </div>
                    <div class="flex items-center justify-center">
                        <div id="qrcode" class="w-50 h-50 flex items-center justify-center border border-gray-200 rounded"></div>
                    </div>
                    <div v-if="qrError" class="mt-4 text-sm text-red-600">{{ qrError }}</div>
                    <div v-if="qrLoading" class="mt-4 text-sm text-gray-500">Generating QR code...</div>
                </div>
                <div class="border-t border-gray-200 px-6 py-4 flex justify-end">
                    <button
                        @click="closeQRModal"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>

        <div v-if="showOvertimeModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
                <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-800">Overtime Request</h2>
                    <button
                        @click="closeOvertimeModal"
                        class="text-gray-500 hover:text-gray-700 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date</label>
                        <input
                            v-model="overtimeForm.request_date"
                            type="date"
                            :min="minOvertimeDate"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                        />
                        <p class="text-xs text-gray-500 mt-1">Requests must be filed at least one day ahead.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Hours</label>
                        <input
                            v-model="overtimeForm.hours"
                            type="number"
                            step="0.5"
                            min="0.5"
                            max="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                        />
                        <p class="text-xs text-gray-500 mt-1">Maximum of 4 hours per request.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Start Time</label>
                        <input
                            v-model="overtimeForm.start_time"
                            type="time"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                        />
                        <p class="text-xs text-gray-500 mt-1">Choose when overtime begins after your shift.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Day Type</label>
                        <select
                            v-model="overtimeForm.day_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                        >
                            <option value="" disabled>Select day type</option>
                            <option v-for="rate in overtimeRates" :key="rate.day_type" :value="rate.day_type">
                                {{ rate.label }} (x{{ rate.multiplier }})
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Reason</label>
                        <textarea
                            v-model="overtimeForm.reason"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                            placeholder="Optional reason"
                        ></textarea>
                    </div>
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <h3 class="text-sm font-semibold text-gray-700">Pending Requests</h3>
                            <button
                                type="button"
                                @click="openOvertimeHistoryModal"
                                class="text-xs font-semibold text-[#0c8ce9] hover:underline"
                            >
                                View Approved/Rejected
                            </button>
                        </div>
                        <div v-if="pendingOvertimeRequests.length" class="space-y-2 max-h-40 overflow-y-auto">
                            <div
                                v-for="item in pendingOvertimeRequests"
                                :key="item.id"
                                class="flex items-center justify-between text-sm text-gray-700"
                            >
                                <div>
                                    {{ item.date }} • {{ item.dayTypeLabel }} • {{ item.startTime || '--' }} • {{ item.hours }}h
                                </div>
                                <span class="text-xs font-semibold" :class="item.status === 'Approved' ? 'text-green-600' : item.status === 'Rejected' ? 'text-red-600' : 'text-amber-600'">
                                    {{ item.status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-200 px-6 py-4 flex justify-end gap-2">
                    <button
                        @click="closeOvertimeModal"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200"
                    >
                        Cancel
                    </button>
                    <button
                        @click="submitOvertimeRequest"
                        class="px-4 py-2 bg-amber-500 text-white rounded-lg text-sm font-semibold hover:bg-amber-600"
                    >
                        Submit
                    </button>
                </div>
            </div>
        </div>

        <div v-if="showOvertimeHistoryModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
                <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-800">Approved & Rejected Requests</h2>
                    <button
                        @click="closeOvertimeHistoryModal"
                        class="text-gray-500 hover:text-gray-700 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-6">
                    <div v-if="historyOvertimeRequests.length" class="space-y-2 max-h-64 overflow-y-auto">
                        <div
                            v-for="item in historyOvertimeRequests"
                            :key="item.id"
                            class="flex items-center justify-between text-sm text-gray-700"
                        >
                            <div>
                                {{ item.date }} • {{ item.dayTypeLabel }} • {{ item.startTime || '--' }} • {{ item.hours }}h
                            </div>
                            <span class="text-xs font-semibold" :class="item.status === 'Approved' ? 'text-green-600' : 'text-red-600'">
                                {{ item.status }}
                            </span>
                        </div>
                    </div>
                    <div v-else class="text-sm text-gray-500">No approved or rejected requests yet.</div>
                </div>
                <div class="border-t border-gray-200 px-6 py-4 flex justify-end">
                    <button
                        @click="closeOvertimeHistoryModal"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200"
                    >
                        Close
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
    import { useRouter } from 'vue-router'
    import { useAuthStore } from '@/stores/auth'

    const auth = useAuthStore()
    const router = useRouter()
    const records = ref([])
    const employeeId = ref(null)
    const searchQuery = ref('')
    const selectedStatus = ref('')
    const selectedDate = ref('')
    const sortField = ref('date')
    const sortDirection = ref('desc')
    const currentPage = ref(1)
    const pageSize = ref(10)
    const showQRModal = ref(false)
    const showOvertimeModal = ref(false)
    const showOvertimeHistoryModal = ref(false)
    const overtimeRates = ref([])
    const overtimeRequests = ref([])
    const pendingOvertimeRequests = computed(() => {
        return overtimeRequests.value.filter(item => item.status === 'Pending')
    })

    const historyOvertimeRequests = computed(() => {
        return overtimeRequests.value.filter(item => item.status === 'Approved' || item.status === 'Rejected')
    })

    const overtimeForm = ref({
        request_date: '',
        hours: '',
        day_type: '',
        start_time: '',
        reason: ''
    })
    const qrPayload = ref('')
    const qrError = ref('')
    const qrLoading = ref(false)
    const photoPreview = ref(null)

    const userLabel = computed(() => {
        const user = auth.user
        if (!user) return 'Unknown User'
        const last = user.last_name || ''
        const first = user.first_name || ''
        const middle = user.middle_name ? `${user.middle_name.charAt(0)}.` : ''
        return `${last}, ${first} ${middle}`.trim()
    })

    const currentUser = computed(() => {
        const user = auth.user
        return {
            name: userLabel.value,
            id: user?.id || null,
            employeeId: employeeId.value,
            position: user?.role || 'Employee'
        }
    })

    const filteredRecords = computed(() => {
        let filtered = records.value.filter(record => {
            const search = searchQuery.value.toLowerCase()
            const matchSearch =
                record.date.toLowerCase().includes(search) ||
                record.dayOfWeek.toLowerCase().includes(search)

            const matchStatus = !selectedStatus.value || record.status === selectedStatus.value
            const matchDate = !selectedDate.value || record.date === selectedDate.value

            return matchSearch && matchStatus && matchDate
        })

        filtered.sort((a, b) => {
            const aVal = a[sortField.value]
            const bVal = b[sortField.value]

            if (aVal < bVal) return sortDirection.value === 'asc' ? -1 : 1
            if (aVal > bVal) return sortDirection.value === 'asc' ? 1 : -1
            return 0
        })

        return filtered
    })

    const timeInOnlyCount = computed(() => {
        return records.value.filter(record => record.status === 'time-in').length
    })

    const absentCount = computed(() => {
        return records.value.filter(record => record.status === 'absent').length
    })

    const clockedOutCount = computed(() => {
        return records.value.filter(record => record.status === 'clocked-out').length
    })

    const hasActiveFilters = computed(() => {
        return searchQuery.value || selectedStatus.value || selectedDate.value
    })

    const totalPages = computed(() => {
        return Math.ceil(filteredRecords.value.length / pageSize.value)
    })

    const paginatedRecords = computed(() => {
        const start = (currentPage.value - 1) * pageSize.value
        const end = start + pageSize.value
        return filteredRecords.value.slice(start, end)
    })

    const visiblePages = computed(() => {
        const pages = []
        const maxVisible = 5
        let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
        let end = Math.min(totalPages.value, start + maxVisible - 1)

        if (end - start + 1 < maxVisible) {
            start = Math.max(1, end - maxVisible + 1)
        }

        for (let i = start; i <= end; i++) {
            pages.push(i)
        }
        return pages
    })

    const toggleSort = (field) => {
        if (sortField.value === field) {
            sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
        } else {
            sortField.value = field
            sortDirection.value = 'asc'
        }
    }

    const openPhotoPreview = (record, type) => {
        photoPreview.value = { record, type }
    }

    const closePhotoPreview = () => {
        photoPreview.value = null
    }

    const photoPreviewTitle = computed(() => {
        if (!photoPreview.value) return ''
        return photoPreview.value.type === 'out' ? 'Time Out Photo' : 'Time In Photo'
    })

    const photoPreviewRecord = computed(() => {
        return photoPreview.value?.record || {}
    })

    const photoPreviewUrl = computed(() => {
        if (!photoPreview.value) return ''
        return photoPreview.value.type === 'out'
            ? photoPreview.value.record.timeOutPhoto
            : photoPreview.value.record.timeInPhoto
    })

    const openQRScanner = (mode) => {
        router.push({ name: 'attendance_scan', query: { mode } })
    }

    const minOvertimeDate = computed(() => {
        const d = new Date()
        d.setDate(d.getDate() + 1)
        return d.toLocaleDateString('en-CA')
    })

    const openOvertimeModal = async () => {
        showOvertimeModal.value = true
        overtimeForm.value = {
            request_date: minOvertimeDate.value,
            hours: '',
            day_type: '',
            start_time: '',
            reason: ''
        }
        await fetchOvertimeRates()
        await fetchOvertimeRequests()
    }

    const closeOvertimeModal = () => {
        showOvertimeModal.value = false
    }

    const openOvertimeHistoryModal = () => {
        showOvertimeHistoryModal.value = true
    }

    const closeOvertimeHistoryModal = () => {
        showOvertimeHistoryModal.value = false
    }

    const getLocalDate = () => {
        return new Date().toLocaleDateString('en-CA')
    }

    const openQRModal = async () => {
        showQRModal.value = true
        qrError.value = ''
        qrLoading.value = true
        qrPayload.value = ''
        const today = getLocalDate()

        try {
            const response = await axios.get('/api/attendance/qr', {
                params: { date: today }
            })
            qrPayload.value = response.data?.data?.payload || ''
            generateQRCode()
        } catch (error) {
            console.error('Failed to load QR payload', error)
            qrError.value = 'Failed to generate QR code.'
        } finally {
            qrLoading.value = false
        }
    }

    const closeQRModal = () => {
        showQRModal.value = false
    }

    const fetchOvertimeRates = async () => {
        try {
            const response = await axios.get('/api/overtime-rates')
            const rows = response.data?.data || []
            overtimeRates.value = rows.filter(rate => rate.is_active)
        } catch (error) {
            console.error('Failed to load overtime rates', error)
        }
    }

    const fetchOvertimeRequests = async () => {
        try {
            const response = await axios.get('/api/overtime-requests')
            overtimeRequests.value = response.data?.data || []
        } catch (error) {
            console.error('Failed to load overtime requests', error)
        }
    }

    const submitOvertimeRequest = async () => {
        try {
            const hours = Number(overtimeForm.value.hours)
            if (Number.isFinite(hours) && hours > 4) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Overtime limit',
                    text: 'Overtime requests are limited to 4 hours.',
                    confirmButtonColor: '#0c8ce9'
                })
                return
            }
            await axios.post('/api/overtime-requests', overtimeForm.value)
            await fetchOvertimeRequests()
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Overtime request submitted',
                showConfirmButton: false,
                timer: 2000
            })
            closeOvertimeModal()
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to submit overtime request.'
            Swal.fire({
                icon: 'error',
                title: 'Submission failed',
                text: message,
                confirmButtonColor: '#ef4444'
            })
        }
    }

    const generateQRCode = () => {
        if (!qrPayload.value) {
            qrError.value = 'QR payload not available.'
            return
        }

        setTimeout(() => {
            try {
                const qrData = qrPayload.value
                const qrContainer = document.getElementById('qrcode')
                if (!qrContainer) {
                    return
                }

                qrContainer.innerHTML = ''

                const tryQRServices = [
                    `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(qrData)}`,
                    `https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=${encodeURIComponent(qrData)}`,
                    `https://api.qr-code-generator.com/v1/create?access-token=demo&data=${encodeURIComponent(qrData)}&size=200`
                ]

                let currentServiceIndex = 0

                const tryNextService = () => {
                    if (currentServiceIndex >= tryQRServices.length) {
                        qrContainer.innerHTML = `
                            <div style="width: 200px; height: 200px; border: 2px solid #ccc; display: flex; align-items: center; justify-content: center; background: #f9f9f9;">
                                <div style="text-align: center; padding: 10px;">
                                    <div style="font-size: 12px; margin-bottom: 10px;">QR Code Data:</div>
                                    <div style="font-size: 10px; word-break: break-all;">${qrData}</div>
                                    <div style="font-size: 11px; margin-top: 10px; color: #666;">
                                        Copy this data to any online QR generator
                                    </div>
                                </div>
                            </div>
                        `
                        return
                    }

                    const qrUrl = tryQRServices[currentServiceIndex]

                    const img = document.createElement('img')
                    img.src = qrUrl
                    img.alt = 'QR Code'
                    img.style.width = '200px'
                    img.style.height = '200px'

                    img.onerror = () => {
                        currentServiceIndex++
                        tryNextService()
                    }

                    qrContainer.innerHTML = ''
                    qrContainer.appendChild(img)
                }

                tryNextService()
            } catch (error) {
                console.error('Error in QR code generation:', error)
                qrError.value = 'Error generating QR code.'
            }
        }, 100)
    }

    const handleAttendanceUpdate = (data) => {
        updateAttendance(
            data.date,
            data.timeIn || null,
            data.timeOut || null,
            data.timeInPhoto || null,
            data.timeOutPhoto || null,
            data.qrPayload || null
        )
    }

    const getStatusBadgeClass = (status) => {
        const classes = {
            present: 'bg-green-100 text-green-700',
            'time-in': 'bg-blue-100 text-blue-700',
            'clocked-out': 'bg-purple-100 text-purple-700',
            absent: 'bg-red-100 text-red-700'
        }
        return classes[status] || 'bg-gray-100 text-gray-700'
    }

    const formatStatus = (status) => {
        const formats = {
            present: 'Present',
            'time-in': 'Time In Only',
            'clocked-out': 'Clocked Out',
            absent: 'Not Scanned'
        }
        return formats[status] || status
    }

    const clearFilters = () => {
        searchQuery.value = ''
        selectedStatus.value = ''
        selectedDate.value = ''
        currentPage.value = 1
    }

    const geoSummary = (geo) => {
        if (!geo || geo.latitude == null || geo.longitude == null) {
            return '—'
        }

        const lat = Number(geo.latitude).toFixed(6)
        const lng = Number(geo.longitude).toFixed(6)
        const accuracy = geo.accuracy != null ? ` (±${Number(geo.accuracy).toFixed(0)}m)` : ''
        return `${lat}, ${lng}${accuracy}`
    }

    const editRecord = (record) => {
        Swal.fire({
            title: `Edit Attendance - ${record.date}`,
            html: `
                <div style="text-align: left;">
                    <label style="display: block; margin: 12px 0 6px 0; font-weight: 600; font-size: 14px;">Time In</label>
                    <input id="timeIn" type="time" value="${record.timeIn || ''}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                    
                    <label style="display: block; margin: 12px 0 6px 0; font-weight: 600; font-size: 14px;">Time Out</label>
                    <input id="timeOut" type="time" value="${record.timeOut || ''}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                </div>
            `,
            confirmButtonText: 'Save',
            confirmButtonColor: '#0c8ce9',
            showCancelButton: true,
            preConfirm: () => {
                const timeIn = document.getElementById('timeIn').value
                const timeOut = document.getElementById('timeOut').value
                return { timeIn, timeOut }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                updateAttendance(record.date, result.value.timeIn || null, result.value.timeOut || null)
            }
        })
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

    const goToPage = (page) => {
        currentPage.value = page
    }

    const fetchRecords = async () => {
        try {
            const response = await axios.get('/api/attendance/records', {
                params: selectedDate.value ? { date: selectedDate.value } : {}
            })
            records.value = response.data?.data || []
            employeeId.value = response.data?.meta?.employee?.id || null
            currentPage.value = 1
        } catch (error) {
            console.error('Failed to load attendance records', error)
        }
    }

    const updateAttendance = async (date, timeIn, timeOut, timeInPhoto, timeOutPhoto, qrPayload) => {
        try {
            await axios.patch(`/api/attendance/records/${date}`, {
                time_in: timeIn,
                time_out: timeOut,
                time_in_photo: timeInPhoto,
                time_out_photo: timeOutPhoto,
                qr_payload: qrPayload
            })
            await fetchRecords()
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Attendance updated',
                showConfirmButton: false,
                timer: 2000
            })
        } catch (error) {
            console.error('Failed to update attendance', error)
            Swal.fire({
                icon: 'error',
                title: 'Update failed',
                text: 'Please try again.',
                confirmButtonColor: '#ef4444'
            })
        }
    }

    const todayRecord = computed(() => {
        const today = new Date().toISOString().split('T')[0]
        return records.value.find(record => record.date === today) || null
    })

    onMounted(() => {
        fetchRecords()
    })

    watch([selectedDate], () => {
        fetchRecords()
    })
</script>
