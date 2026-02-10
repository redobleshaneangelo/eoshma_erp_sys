<template>
    <auth-layout />
        <div class="ps-60 pe-4 bg-[#F3F4F6] min-h-screen">
            <div class="px-8 py-4">
                <div class="w-full pb-20">
                    <div class="flex justify-center pt-4 pb-4">
                        <div class="w-full max-w-7xl">
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <button
                                @click="addInvoiceModal = true"
                                class="flex items-center ms-auto gap-2 px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Invoice
                            </button>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-4 gap-4 mb-6">
                        <div class="bg-linear-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Billed</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">₱3,850,000</p>
                            <p class="text-xs text-gray-600 mt-1">All projects</p>
                        </div>
                        <div class="bg-linear-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Collected</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">₱2,950,000</p>
                            <p class="text-xs text-gray-600 mt-1">76% collected</p>
                        </div>
                        <div class="bg-linear-to-br from-orange-50 to-orange-100 rounded-lg p-4 border border-orange-200">
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Outstanding</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">₱900,000</p>
                            <p class="text-xs text-gray-600 mt-1">Pending collection</p>
                        </div>
                        <div class="bg-linear-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Progress Billings</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">12</p>
                            <p class="text-xs text-gray-600 mt-1">This month</p>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
                        <div class="grid grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Billing Type</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c8ce9]">
                                    <option>All Types</option>
                                    <option>Progress Billing</option>
                                    <option>Milestone Billing</option>
                                    <option>Final Billing</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c8ce9]">
                                    <option>All Status</option>
                                    <option>Paid</option>
                                    <option>Partially Paid</option>
                                    <option>Pending</option>
                                    <option>Overdue</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c8ce9]">
                                    <option>All Projects</option>
                                    <option>Project Alpha</option>
                                    <option>Project Beta</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search client, reference..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c8ce9]"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Receivables Table -->

                    <!-- Receivables Table -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Billing Date</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Invoice Ref</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Client</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Project</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Type</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Milestone</th>
                                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">Amount</th>
                                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">Collected</th>
                                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">Due Date</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="receivable in paginatedReceivables" :key="receivable.id" class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ formatDate(receivable.date) }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-[#0c8ce9]">{{ receivable.ref_no }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700 font-medium">{{ receivable.client_name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ receivable.project }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">{{ receivable.type }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ receivable.milestone }}</td>
                                        <td class="px-6 py-4 text-sm font-semibold text-right text-gray-900">₱{{ receivable.balance.toLocaleString() }}</td>
                                        <td class="px-6 py-4 text-sm text-right text-gray-700">₱{{ receivable.collected.toLocaleString() }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700 text-right whitespace-nowrap">{{ formatDate(receivable.due_date) }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span :class="statusStyles[receivable.status]" class="px-3 py-1 rounded-full text-xs font-bold border">
                                                {{ receivable.status }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="flex justify-between items-center px-6 py-4 bg-gray-50 border-t border-gray-200">
                            <p class="text-sm text-gray-600">Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredReceivables.length) }} of {{ filteredReceivables.length }} billings</p>
                            <div class="flex gap-2">
                                <button @click="currentPage--" :disabled="currentPage === 1" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">Previous</button>
                                <span class="px-4 py-2 text-sm font-medium">Page {{ currentPage }} of {{ totalPages }}</span>
                                <button @click="currentPage++" :disabled="currentPage === totalPages" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Invoice Modal -->
        <div v-if="addInvoiceModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-96 overflow-y-auto">
                <div class="sticky top-0 bg-linear-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white">Add New Invoice (Receivable)</h3>
                    <button @click="addInvoiceModal = false" class="text-white hover:bg-blue-800 p-1 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Client Name</label>
                            <input v-model="newInvoice.client_name" type="text" placeholder="Enter client name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reference No.</label>
                            <input v-model="newInvoice.ref_no" type="text" placeholder="RECV-7001" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Date</label>
                            <input v-model="newInvoice.date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                            <input v-model="newInvoice.due_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Billing Type</label>
                            <select v-model="newInvoice.type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option>Progress Billing</option>
                                <option>Milestone Billing</option>
                                <option>Final Billing</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                            <select v-model="newInvoice.project" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option>Project Alpha</option>
                                <option>Project Beta</option>
                                <option>Project Gamma</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Milestone</label>
                            <input v-model="newInvoice.milestone" type="text" placeholder="e.g., Phase 1, Foundation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount (₱)</label>
                            <input v-model="newInvoice.balance" type="number" placeholder="0.00" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                    <button @click="addInvoiceModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Cancel</button>
                    <button @click="submitInvoice" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">Add Invoice</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fixed bottom-0 right-0 p-2 z-50">
            <img :src="agnes" class="h-auto w-auto" v-if="loading" />
        </div>
    
</template>

<script setup>
    import { ref, computed, watch, onMounted } from 'vue'
    import { useAuthStore } from '@/stores/auth'
    import agnes from '@/assets/agnes.gif'

    const auth = useAuthStore()
    const loading = ref(false)

    /* =====================================================
    Modal State
    ===================================================== */
    const addInvoiceModal = ref(false)
    const newInvoice = ref({
        date: new Date().toISOString().split('T')[0],
        ref_no: '',
        client_name: '',
        type: 'Progress Billing',
        project: 'Project Alpha',
        milestone: '',
        balance: 0,
        collected: 0,
        due_date: '',
        status: 'Pending'
    })

    const submitInvoice = () => {
        if (!newInvoice.value.client_name || !newInvoice.value.ref_no || !newInvoice.value.balance) {
            alert('Please fill in required fields')
            return
        }

        receivables.value.unshift({
            id: Math.max(...receivables.value.map(r => r.id), 0) + 1,
            ...newInvoice.value
        })

        // Reset form
        newInvoice.value = {
            date: new Date().toISOString().split('T')[0],
            ref_no: '',
            client_name: '',
            type: 'Progress Billing',
            project: 'Project Alpha',
            milestone: '',
            balance: 0,
            collected: 0,
            due_date: '',
            status: 'Pending'
        }
        addInvoiceModal.value = false
    }

    /* =====================================================
    Pagination & Search
    ===================================================== */
    const searchQuery = ref('')
    const currentPage = ref(1)
    const itemsPerPage = 10

    watch(searchQuery, () => {
        currentPage.value = 1
    })

    /* =====================================================
    Receivables Data
    ===================================================== */
    const receivables = ref([
        {
            id: 201,
            date: '2026-01-08',
            ref_no: 'INV-B1001',
            client_name: 'Metro Development Corp',
            project: 'Project Alpha',
            type: 'Progress Billing',
            milestone: 'Foundation Complete',
            balance: 450000,
            collected: 450000,
            due_date: '2026-01-15',
            status: 'Paid'
        },
        {
            id: 202,
            date: '2026-01-15',
            ref_no: 'INV-B1002',
            client_name: 'Metro Development Corp',
            project: 'Project Alpha',
            type: 'Progress Billing',
            milestone: 'Structural Work 25%',
            balance: 550000,
            collected: 275000,
            due_date: '2026-02-15',
            status: 'Partially Paid'
        },
        {
            id: 203,
            date: '2025-12-28',
            ref_no: 'INV-B2001',
            client_name: 'Urban Construction Ltd',
            project: 'Project Beta',
            type: 'Milestone Billing',
            milestone: 'Site Preparation Done',
            balance: 280000,
            collected: 0,
            due_date: '2026-01-20',
            status: 'Pending'
        },
        {
            id: 204,
            date: '2025-12-10',
            ref_no: 'INV-B3001',
            client_name: 'City Complex Developers',
            project: 'Project Gamma',
            type: 'Progress Billing',
            milestone: 'Excavation Complete',
            balance: 320000,
            collected: 0,
            due_date: '2026-01-10',
            status: 'Overdue'
        }
    ])

    const filteredReceivables = computed(() => {
        if (!searchQuery.value) return receivables.value
        const query = searchQuery.value.toLowerCase()
        return receivables.value.filter(r =>
            r.client_name.toLowerCase().includes(query) ||
            r.project?.toLowerCase().includes(query) ||
            r.ref_no.toLowerCase().includes(query) ||
            r.description?.toLowerCase().includes(query)
        )
    })

    const totalPages = computed(() =>
        Math.ceil(filteredReceivables.value.length / itemsPerPage) || 1
    )

    const paginatedReceivables = computed(() => {
        const start = (currentPage.value - 1) * itemsPerPage
        return filteredReceivables.value.slice(start, start + itemsPerPage)
    })

    /* =====================================================
    Helpers
    ===================================================== */
    const formatDate = (date) => {
        const d = new Date(date)
        const months = [
            'January','February','March','April','May','June',
            'July','August','September','October','November','December'
        ]
        return `${months[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`
    }

    const statusStyles = {
        'Completed': 'bg-[#dbfce7] text-[#00a63e] border-[#bbf8d1]',
        'Partially Paid': 'bg-[#fefce8] text-[#d08700] border-[#fff085]',
        'Unpaid': 'bg-[#ffe2e2] text-[#651A1A] border-[#ffcccc]'
    }

    /* =====================================================
    Lifecycle
    ===================================================== */
    onMounted(() => {
        // init logic here
        auth.pageTitle = 'Receivables'
    })
</script>
