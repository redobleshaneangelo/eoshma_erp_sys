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

                    <!-- Filter & Summary Section -->
                    <div class="grid grid-cols-4 gap-4 mb-6">
                        <div class="bg-linear-to-br from-red-50 to-red-100 rounded-lg p-4 border border-red-200">
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Due</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">₱1,250,000</p>
                            <p class="text-xs text-gray-600 mt-1">Across all vendors</p>
                        </div>
                        <div class="bg-linear-to-br from-orange-50 to-orange-100 rounded-lg p-4 border border-orange-200">
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Overdue</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">₱450,000</p>
                            <p class="text-xs text-gray-600 mt-1">From 5 invoices</p>
                        </div>
                        <div class="bg-linear-to-br from-yellow-50 to-yellow-100 rounded-lg p-4 border border-yellow-200">
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Due This Month</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">₱380,000</p>
                            <p class="text-xs text-gray-600 mt-1">8 upcoming payments</p>
                        </div>
                        <div class="bg-linear-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Pending Invoices</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">23</p>
                            <p class="text-xs text-gray-600 mt-1">Awaiting approval</p>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
                        <div class="grid grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Vendor Type</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c8ce9]">
                                    <option>All Types</option>
                                    <option>Contractors</option>
                                    <option>Material Suppliers</option>
                                    <option>Labor Service</option>
                                    <option>Equipment Rental</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c8ce9]">
                                    <option>All Status</option>
                                    <option>Unpaid</option>
                                    <option>Partially Paid</option>
                                    <option>Paid</option>
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
                                    placeholder="Search vendor, reference..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c8ce9]"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Payables Table -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Invoice Date</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Invoice Ref</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Vendor</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Type</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Project</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Description</th>
                                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">Amount</th>
                                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">Due Date</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide">Status</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="payable in paginatedPayables" :key="payable.id" class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ formatDate(payable.date) }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-[#0c8ce9]">{{ payable.ref_no }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ payable.vendor }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">{{ payable.type }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ payable.project }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ payable.description }}</td>
                                        <td class="px-6 py-4 text-sm font-semibold text-right text-gray-900">₱{{ payable.amount.toLocaleString() }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700 text-right whitespace-nowrap">{{ formatDate(payable.due_date) }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span :class="statusStyles[payable.status]" class="px-3 py-1 rounded-full text-xs font-bold border">
                                                {{ payable.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button class="text-[#0c8ce9] hover:text-[#0a6fb3] font-medium text-sm">Record Payment</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="flex justify-between items-center px-6 py-4 bg-gray-50 border-t border-gray-200">
                            <p class="text-sm text-gray-600">Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredPayables.length) }} of {{ filteredPayables.length }} invoices</p>
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
                <div class="sticky top-0 bg-linear-to-r from-green-600 to-green-700 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white">Add New Invoice (Payable)</h3>
                    <button @click="addInvoiceModal = false" class="text-white hover:bg-green-800 p-1 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vendor Name</label>
                            <input v-model="newInvoice.vendor" type="text" placeholder="Enter vendor name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reference No.</label>
                            <input v-model="newInvoice.ref_no" type="text" placeholder="INV-5005" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Date</label>
                            <input v-model="newInvoice.date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                            <input v-model="newInvoice.due_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vendor Type</label>
                            <select v-model="newInvoice.type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option>Contractors</option>
                                <option>Material Suppliers</option>
                                <option>Labor Service</option>
                                <option>Equipment Rental</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                            <select v-model="newInvoice.project" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option>Project Alpha</option>
                                <option>Project Beta</option>
                                <option>Project Gamma</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea v-model="newInvoice.description" placeholder="Enter invoice description" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount (₱)</label>
                            <input v-model="newInvoice.amount" type="number" placeholder="0.00" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                    <button @click="addInvoiceModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Cancel</button>
                    <button @click="submitInvoice" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">Add Invoice</button>
                        </div>
                    </div>
                </div>
            </div>
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
        vendor: '',
        type: 'Contractors',
        project: 'Project Alpha',
        description: '',
        amount: 0,
        due_date: '',
        status: 'Unpaid'
    })

    const submitInvoice = () => {
        if (!newInvoice.value.vendor || !newInvoice.value.ref_no || !newInvoice.value.amount) {
            alert('Please fill in required fields')
            return
        }

        payables.value.unshift({
            id: Math.max(...payables.value.map(p => p.id), 0) + 1,
            ...newInvoice.value
        })

        // Reset form
        newInvoice.value = {
            date: new Date().toISOString().split('T')[0],
            ref_no: '',
            vendor: '',
            type: 'Contractors',
            project: 'Project Alpha',
            description: '',
            amount: 0,
            due_date: '',
            status: 'Unpaid'
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
    Payables Data
    ===================================================== */
    const payables = ref([
        {
            id: 101,
            date: '2026-01-05',
            ref_no: 'INV-5001',
            vendor: 'BuildCo Contractors',
            type: 'Contractors',
            project: 'Project Alpha',
            description: 'Concrete Foundation Work - Phase 1',
            amount: 450000,
            due_date: '2026-01-25',
            status: 'Unpaid'
        },
        {
            id: 102,
            date: '2026-01-08',
            ref_no: 'INV-5002',
            vendor: 'Steel Supplies Ltd',
            type: 'Material Suppliers',
            project: 'Project Alpha',
            description: 'Structural Steel Beams & Plates',
            amount: 280000,
            due_date: '2026-02-08',
            status: 'Unpaid'
        },
        {
            id: 103,
            date: '2025-12-28',
            ref_no: 'INV-5003',
            vendor: 'Elite Labor Solutions',
            type: 'Labor Service',
            project: 'Project Beta',
            description: 'Labor - Formwork Installation',
            amount: 125000,
            due_date: '2026-01-15',
            status: 'Overdue'
        },
        {
            id: 104,
            date: '2026-01-15',
            ref_no: 'INV-5004',
            vendor: 'Heavy Equipment Rental',
            type: 'Equipment Rental',
            project: 'Project Alpha',
            description: 'Crane Rental - 20 days',
            amount: 95000,
            due_date: '2026-02-15',
            status: 'Partially Paid'
        }
    ])

    const filteredPayables = computed(() => {
        if (!searchQuery.value) return payables.value
        const query = searchQuery.value.toLowerCase()
        return payables.value.filter(p =>
            p.vendor.toLowerCase().includes(query) ||
            p.project?.toLowerCase().includes(query) ||
            p.ref_no.toLowerCase().includes(query) ||
            p.description?.toLowerCase().includes(query)
        )
    })

    const totalPages = computed(() =>
        Math.ceil(filteredPayables.value.length / itemsPerPage) || 1
    )

    const paginatedPayables = computed(() => {
        const start = (currentPage.value - 1) * itemsPerPage
        return filteredPayables.value.slice(start, start + itemsPerPage)
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
        auth.pageTitle = 'Payables'
    })
</script>
