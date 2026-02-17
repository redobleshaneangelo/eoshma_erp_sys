<template>
    <auth-layout />
    <div class="ps-60 pe-4 bg-[#F3F4F6] min-h-screen">
        <div class="px-8 py-6">
            <div class="w-full max-w-7xl mx-auto space-y-6">
                <div class="flex items-center justify-end">
                    <button
                        @click="openAddInvoiceModal"
                        class="flex items-center gap-2 px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Invoice
                    </button>
                </div>

                <div class="grid grid-cols-4 gap-4">
                    <div class="bg-linear-to-br from-red-50 to-red-100 rounded-lg p-4 border border-red-200">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Amount</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">₱{{ totals.total.toLocaleString() }}</p>
                        <p class="text-xs text-gray-600 mt-1">All invoices</p>
                    </div>
                    <div class="bg-linear-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Posted</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ totals.postedCount }}</p>
                        <p class="text-xs text-gray-600 mt-1">Invoices</p>
                    </div>
                    <div class="bg-linear-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Paid</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ totals.paidCount }}</p>
                        <p class="text-xs text-gray-600 mt-1">Invoices</p>
                    </div>
                    <div class="bg-linear-to-br from-gray-50 to-gray-100 rounded-lg p-4 border border-gray-200">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Cancelled</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ totals.cancelledCount }}</p>
                        <p class="text-xs text-gray-600 mt-1">Invoices</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <div class="grid grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input
                                v-model="filters.search"
                                type="text"
                                placeholder="Invoice no, vendor, notes..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c8ce9]"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Type</label>
                            <select v-model="filters.invoiceType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c8ce9]">
                                <option value="">All Types</option>
                                <option value="Sales Invoice">Sales Invoice</option>
                                <option value="Purchase Invoice">Purchase Invoice</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select v-model="filters.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c8ce9]">
                                <option value="">All Status</option>
                                <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vendor / Client</label>
                            <select v-model="filters.counterpartyId" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c8ce9]">
                                <option value="">All</option>
                                <option v-for="party in counterparties" :key="party.key" :value="party.id">{{ party.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rows</label>
                            <select v-model.number="pagination.pageSize" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c8ce9]">
                                <option :value="10">10</option>
                                <option :value="25">25</option>
                                <option :value="50">50</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Invoice No.</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Vendor / Client</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Invoice Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Due Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">Total Amount</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">Net Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Notes</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="invoice in paginatedInvoices" :key="invoice.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-sm font-semibold text-[#0c8ce9]">{{ invoice.invoiceNo }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ invoice.invoiceType }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ invoice.counterpartyName }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ formatDate(invoice.invoiceDate) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ formatDate(invoice.dueDate) }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span :class="statusStyles[invoice.status]" class="px-2.5 py-1 rounded-full text-xs font-semibold">
                                            {{ invoice.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">₱{{ Number(invoice.totalAmount || 0).toLocaleString() }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">₱{{ Number(invoice.netAmount || 0).toLocaleString() }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ invoice.description || '--' }}</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="openInvoiceView(invoice)" class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700">
                                                View
                                            </button>
                                            <button
                                                v-if="canViewHistory(invoice)"
                                                @click="openPaymentHistory(invoice)"
                                                class="px-3 py-1 text-xs font-semibold text-white bg-purple-600 rounded hover:bg-purple-700"
                                            >
                                                Payment History
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="paginatedInvoices.length === 0">
                                    <td colspan="10" class="px-4 py-10 text-center text-gray-500 text-sm">No invoices found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between items-center px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <p class="text-sm text-gray-600">
                            Showing {{ filteredInvoices.length ? ((pagination.page - 1) * pagination.pageSize + 1) : 0 }}
                            to {{ Math.min(pagination.page * pagination.pageSize, filteredInvoices.length) }}
                            of {{ filteredInvoices.length }} invoices
                        </p>
                        <div class="flex gap-2">
                            <button
                                @click="pagination.page = Math.max(1, pagination.page - 1)"
                                :disabled="pagination.page === 1"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Previous
                            </button>
                            <span class="px-4 py-2 text-sm font-medium">Page {{ pagination.page }} of {{ totalPages }}</span>
                            <button
                                @click="pagination.page = Math.min(totalPages, pagination.page + 1)"
                                :disabled="pagination.page === totalPages"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showInvoiceModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-linear-to-r from-green-600 to-green-700 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white">Add Invoice</h3>
                    <button @click="showInvoiceModal = false" class="text-white hover:bg-green-800 p-1 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Invoice No.</label>
                            <input type="text" value="Auto-generated" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Type</label>
                            <select v-model="newInvoice.invoiceType" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option>Purchase Invoice</option>
                                <option>Sales Invoice</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vendor / Client</label>
                            <select v-model.number="newInvoice.counterpartyId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Select...</option>
                                <option v-for="party in counterparties" :key="party.key" :value="party.id">{{ party.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select v-model="newInvoice.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Date</label>
                            <input v-model="newInvoice.invoiceDate" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                            <input v-model="newInvoice.dueDate" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Amount</label>
                            <input :value="formatMoney(newInvoice.totalAmount)" type="text" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Net Amount</label>
                            <input :value="formatMoney(newInvoice.netAmount)" type="text" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description / Notes</label>
                        <textarea v-model="newInvoice.description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachments</label>
                        <input type="file" multiple accept=".pdf,image/*" @change="handleAttachments" class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                        <p class="text-xs text-gray-500 mt-1" v-if="newInvoice.attachments.length">{{ newInvoice.attachments.length }} file(s) attached</p>
                    </div>

                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-800">Invoice Line Items</h4>
                            <button @click="addLineItem" class="px-3 py-1 text-xs font-semibold bg-green-600 text-white rounded hover:bg-green-700">Add Item</button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-100 border-b border-gray-200">
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Item / Description</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Qty</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Unit</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Unit Price</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Line Total</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Account Search</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Account</th>
                                        <th class="px-3 py-2 text-center font-semibold text-gray-700">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="(line, index) in newInvoice.lineItems" :key="line.key">
                                        <td class="px-3 py-2">
                                            <input v-model="line.description" type="text" class="w-full px-2 py-1 border border-gray-300 rounded" />
                                        </td>
                                        <td class="px-3 py-2">
                                            <input v-model.number="line.quantity" type="number" min="1" class="w-20 px-2 py-1 border border-gray-300 rounded" @input="recomputeLine(index)" />
                                        </td>
                                        <td class="px-3 py-2">
                                            <input v-model="line.unit" type="text" class="w-24 px-2 py-1 border border-gray-300 rounded" />
                                        </td>
                                        <td class="px-3 py-2">
                                            <input v-model.number="line.unitPrice" type="number" min="0" step="0.01" class="w-28 px-2 py-1 border border-gray-300 rounded" @input="recomputeLine(index)" />
                                        </td>
                                        <td class="px-3 py-2 font-semibold">₱{{ Number(line.lineTotal || 0).toLocaleString() }}</td>
                                        <td class="px-3 py-2">
                                            <input v-model="line.accountSearch" type="text" placeholder="Search code/name" class="w-36 px-2 py-1 border border-gray-300 rounded" />
                                        </td>
                                        <td class="px-3 py-2">
                                            <select v-model.number="line.accountId" class="w-52 px-2 py-1 border border-gray-300 rounded" @change="syncLineAccount(index)">
                                                <option :value="null">Select account</option>
                                                <option v-for="account in filteredAccounts('expense', line.accountSearch)" :key="account.key" :value="account.id">
                                                    {{ account.code }} - {{ account.name }}
                                                </option>
                                            </select>
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <button @click="removeLineItem(index)" class="text-red-600 hover:text-red-800 text-xs font-semibold" :disabled="newInvoice.lineItems.length === 1">Remove</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                    <button @click="showInvoiceModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Cancel</button>
                    <button @click="submitInvoice" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">Save Invoice</button>
                </div>
            </div>
        </div>

        <div v-if="showInvoiceViewModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900">Invoice View</h3>
                    <button @click="showInvoiceViewModal = false" class="text-gray-600 hover:text-gray-900">✕</button>
                </div>

                <div v-if="selectedInvoice" class="p-6 space-y-6">
                    <div class="grid grid-cols-4 gap-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Invoice No.</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ selectedInvoice.invoiceNo }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Vendor / Client</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ selectedInvoice.counterpartyName }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Invoice Date</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ formatDate(selectedInvoice.invoiceDate) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Due Date</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ formatDate(selectedInvoice.dueDate) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ selectedInvoice.status }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Amount</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">₱{{ Number(selectedInvoice.totalAmount || 0).toLocaleString() }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Net Amount</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">₱{{ Number(selectedInvoice.netAmount || 0).toLocaleString() }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Latest Pay Date</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ formatDate(latestPayDate(selectedInvoice)) }}</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Items</h4>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-100 border-b border-gray-200">
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Description</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Qty</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Unit</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Unit Price</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Line Total</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Account</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="line in selectedInvoice.lineItems" :key="line.key">
                                        <td class="px-3 py-2">{{ line.description || '--' }}</td>
                                        <td class="px-3 py-2">{{ line.quantity }}</td>
                                        <td class="px-3 py-2">{{ line.unit }}</td>
                                        <td class="px-3 py-2">₱{{ Number(line.unitPrice || 0).toLocaleString() }}</td>
                                        <td class="px-3 py-2">₱{{ Number(line.lineTotal || 0).toLocaleString() }}</td>
                                        <td class="px-3 py-2">{{ line.accountName || '--' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-semibold text-gray-800">Payment History</h4>
                            <button
                                v-if="canViewHistory(selectedInvoice)"
                                @click="openPaymentHistory(selectedInvoice)"
                                class="px-3 py-1 text-xs font-semibold text-white bg-purple-600 rounded hover:bg-purple-700"
                            >
                                Open in New Tab
                            </button>
                        </div>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-100 border-b border-gray-200">
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Pay Date</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Amount</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Reference</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Attachment</th>
                                        <th class="px-3 py-2 text-center font-semibold text-gray-700">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="payment in selectedInvoice.paymentHistory || []" :key="payment.id">
                                        <td class="px-3 py-2">{{ formatDate(payment.payDate) }}</td>
                                        <td class="px-3 py-2">₱{{ Number(payment.amount || 0).toLocaleString() }}</td>
                                        <td class="px-3 py-2">{{ payment.referenceNo || '--' }}</td>
                                        <td class="px-3 py-2">{{ payment.attachmentName || '--' }}</td>
                                        <td class="px-3 py-2 text-center">
                                            <button @click="viewPayment(payment)" class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700">View</button>
                                        </td>
                                    </tr>
                                    <tr v-if="!(selectedInvoice.paymentHistory || []).length">
                                        <td colspan="5" class="px-3 py-6 text-center text-gray-500">No payment history found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-200 flex justify-end gap-2">
                    <button
                        v-if="selectedInvoice && canRecordPayment(selectedInvoice)"
                        @click="openPaymentModal(selectedInvoice)"
                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700"
                    >
                        Record Payment
                    </button>
                    <button @click="showInvoiceViewModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold">Close</button>
                </div>
            </div>
        </div>

        <div v-if="showPaymentModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-xl w-full">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Record Payment</h3>
                    <button @click="showPaymentModal = false" class="text-gray-600 hover:text-gray-900">✕</button>
                </div>

                <div class="p-6 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Pay Date</label>
                        <input v-model="paymentDraft.payDate" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Amount</label>
                        <input v-model.number="paymentDraft.amount" type="number" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Reference</label>
                        <input v-model="paymentDraft.referenceNo" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Attachment</label>
                        <input id="payables-payment-attachment-input" type="file" accept=".pdf,image/*" @change="handlePaymentAttachment" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        <p class="text-xs text-gray-500 mt-1" v-if="paymentDraft.attachmentName">{{ paymentDraft.attachmentName }}</p>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-200 flex justify-end gap-2">
                    <button @click="showPaymentModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold">Cancel</button>
                    <button @click="savePayment" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700">Save Payment</button>
                </div>
            </div>
        </div>

        <div v-if="showPaymentViewModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Payment View</h3>
                    <button @click="showPaymentViewModal = false" class="text-gray-600 hover:text-gray-900">✕</button>
                </div>
                <div class="p-6 space-y-3 text-sm">
                    <p><span class="font-semibold text-gray-700">Pay Date:</span> {{ formatDate(selectedPayment?.payDate) }}</p>
                    <p><span class="font-semibold text-gray-700">Amount:</span> ₱{{ Number(selectedPayment?.amount || 0).toLocaleString() }}</p>
                    <p><span class="font-semibold text-gray-700">Reference:</span> {{ selectedPayment?.referenceNo || '--' }}</p>
                    <p>
                        <span class="font-semibold text-gray-700">Attachment:</span>
                        <a
                            v-if="selectedPayment?.attachmentUrl"
                            :href="selectedPayment.attachmentUrl"
                            target="_blank"
                            class="text-[#0c8ce9] hover:underline"
                        >
                            {{ selectedPayment?.attachmentName || 'Open attachment' }}
                        </a>
                        <span v-else>{{ selectedPayment?.attachmentName || '--' }}</span>
                    </p>
                </div>
                <div class="p-6 border-t border-gray-200 flex justify-end">
                    <button @click="showPaymentViewModal = false" class="px-4 py-2 bg-[#0c8ce9] text-white rounded-lg text-sm font-semibold">Close</button>
                </div>
            </div>
        </div>

        <div class="fixed bottom-0 right-0 p-2 z-50">
            <img :src="agnes" class="h-auto w-auto" v-if="loading" />
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import agnes from '@/assets/agnes.gif'

const auth = useAuthStore()
const router = useRouter()
const loading = ref(false)

const moduleKey = 'payables'
const statuses = ['Posted', 'Paid', 'Cancelled']

const counterparties = ref([])
const chartAccounts = ref([])

const filters = ref({
    search: '',
    invoiceType: '',
    status: '',
    counterpartyId: ''
})

const pagination = ref({
    page: 1,
    pageSize: 10
})

const invoices = ref([])

const showInvoiceModal = ref(false)
const newInvoice = ref(createInvoiceDraft())

const showInvoiceViewModal = ref(false)
const selectedInvoice = ref(null)

const showPaymentModal = ref(false)
const paymentDraft = ref(createPaymentDraft())

const showPaymentViewModal = ref(false)
const selectedPayment = ref(null)

const statusStyles = {
    Posted: 'bg-blue-100 text-blue-800',
    Paid: 'bg-green-100 text-green-800',
    Cancelled: 'bg-red-100 text-red-800'
}

const totals = computed(() => ({
    total: invoices.value.reduce((sum, invoice) => sum + Number(invoice.totalAmount || 0), 0),
    postedCount: invoices.value.filter((invoice) => invoice.status === 'Posted').length,
    paidCount: invoices.value.filter((invoice) => invoice.status === 'Paid').length,
    cancelledCount: invoices.value.filter((invoice) => invoice.status === 'Cancelled').length
}))

const filteredInvoices = computed(() => {
    const keyword = String(filters.value.search || '').trim().toLowerCase()

    return invoices.value.filter((invoice) => {
        if (filters.value.invoiceType && invoice.invoiceType !== filters.value.invoiceType) return false
        if (filters.value.status && invoice.status !== filters.value.status) return false
        if (filters.value.counterpartyId && String(invoice.counterpartyId || '') !== String(filters.value.counterpartyId)) return false

        if (!keyword) return true

        const blob = [
            invoice.invoiceNo,
            invoice.invoiceType,
            invoice.counterpartyName,
            invoice.description,
            invoice.status
        ].join(' ').toLowerCase()

        return blob.includes(keyword)
    })
})

const totalPages = computed(() => Math.max(1, Math.ceil(filteredInvoices.value.length / pagination.value.pageSize)))

const paginatedInvoices = computed(() => {
    const start = (pagination.value.page - 1) * pagination.value.pageSize
    const end = start + pagination.value.pageSize
    return filteredInvoices.value.slice(start, end)
})

watch(
    () => [filters.value.search, filters.value.invoiceType, filters.value.status, filters.value.counterpartyId, pagination.value.pageSize],
    () => {
        pagination.value.page = 1
    }
)

watch(
    () => newInvoice.value.lineItems,
    () => recomputeInvoiceTotals(),
    { deep: true }
)

function createInvoiceDraft() {
    const today = new Date().toISOString().split('T')[0]

    return {
        invoiceNo: '',
        invoiceType: 'Purchase Invoice',
        counterpartyId: '',
        invoiceDate: today,
        dueDate: '',
        status: 'Posted',
        totalAmount: 0,
        netAmount: 0,
        description: '',
        attachments: [],
        lineItems: [createLineItem()]
    }
}

function createPaymentDraft() {
    return {
        payDate: new Date().toISOString().split('T')[0],
        amount: 0,
        referenceNo: '',
        attachmentName: ''
    }
}

function createLineItem() {
    return {
        key: `${Date.now()}-${Math.random().toString(36).slice(2)}`,
        description: '',
        quantity: 1,
        unit: 'pcs',
        unitPrice: 0,
        lineTotal: 0,
        accountId: null,
        accountName: '',
        accountType: 'expense',
        accountSearch: ''
    }
}

function openAddInvoiceModal() {
    newInvoice.value = createInvoiceDraft()
    showInvoiceModal.value = true
}

function openInvoiceView(invoice) {
    selectedInvoice.value = invoice
    showInvoiceViewModal.value = true
}

function canRecordPayment(invoice) {
    return invoice?.status === 'Posted'
}

function canViewHistory(invoice) {
    return invoice?.status === 'Paid'
}

function latestPayDate(invoice) {
    const history = invoice?.paymentHistory || []
    if (!history.length) return ''
    return history[history.length - 1]?.payDate || ''
}

function openPaymentHistory(invoice) {
    const href = router.resolve({
        name: 'invoice_payment_history',
        params: {
            module: 'payables',
            invoiceNo: invoice.invoiceNo
        }
    }).href

    window.open(href, '_blank')
}

function openPaymentModal(invoice) {
    selectedInvoice.value = invoice
    paymentDraft.value = createPaymentDraft()
    showPaymentModal.value = true
}

function handlePaymentAttachment(event) {
    const file = event.target.files?.[0]
    paymentDraft.value.attachmentName = file ? file.name : ''
}

async function savePayment() {
    if (!selectedInvoice.value) return

    if (!paymentDraft.value.payDate || Number(paymentDraft.value.amount || 0) <= 0) {
        window.alert('Please complete pay date and amount.')
        return
    }

    try {
        const formData = new FormData()
        formData.append('pay_date', paymentDraft.value.payDate)
        formData.append('amount', String(Number(paymentDraft.value.amount || 0)))
        formData.append('reference_no', paymentDraft.value.referenceNo || '')

        const paymentInput = document.getElementById('payables-payment-attachment-input')
        const paymentFile = paymentInput?.files?.[0]
        if (paymentFile) {
            formData.append('payment_attachment', paymentFile)
        } else if (paymentDraft.value.attachmentName) {
            formData.append('attachment_name', paymentDraft.value.attachmentName)
        }

        const response = await axios.post(`/api/invoices/${selectedInvoice.value.id}/payments`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })

        const updatedInvoice = response.data?.data?.invoice
        if (updatedInvoice) {
            upsertInvoice(updatedInvoice)
            selectedInvoice.value = updatedInvoice
        }

        showPaymentModal.value = false
    } catch (error) {
        const message = error?.response?.data?.message || 'Failed to save payment.'
        window.alert(message)
    }
}

function viewPayment(payment) {
    selectedPayment.value = payment
    showPaymentViewModal.value = true
}

function addLineItem() {
    newInvoice.value.lineItems.push(createLineItem())
}

function removeLineItem(index) {
    if (newInvoice.value.lineItems.length === 1) return
    newInvoice.value.lineItems.splice(index, 1)
}

function recomputeLine(index) {
    const line = newInvoice.value.lineItems[index]
    const quantity = Number(line.quantity || 0)
    const unitPrice = Number(line.unitPrice || 0)
    line.lineTotal = Math.max(0, quantity * unitPrice)
    recomputeInvoiceTotals()
}

function recomputeInvoiceTotals() {
    const total = newInvoice.value.lineItems.reduce((sum, line) => sum + Number(line.lineTotal || 0), 0)
    newInvoice.value.totalAmount = total
    newInvoice.value.netAmount = total
}

function syncLineAccount(index) {
    const line = newInvoice.value.lineItems[index]
    const selected = chartAccounts.value.find((account) => Number(account.id) === Number(line.accountId))

    if (!selected) {
        line.accountName = ''
        return
    }

    line.accountName = selected.name
}

function filteredAccounts(type, search) {
    const needle = String(search || '').trim().toLowerCase()

    return chartAccounts.value.filter((account) => {
        if (String(account.type || '').toLowerCase() !== String(type || '').toLowerCase()) {
            return false
        }

        if (!needle) return true

        const blob = `${account.code || ''} ${account.name || ''}`.toLowerCase()
        return blob.includes(needle)
    })
}

function handleAttachments(event) {
    const files = Array.from(event.target.files || [])
    newInvoice.value.attachments = files
}

async function submitInvoice() {
    const parsedCounterpartyId = Number(newInvoice.value.counterpartyId)
    const selectedCounterparty = counterparties.value.find((party) => Number(party.id) === parsedCounterpartyId)

    if (!newInvoice.value.invoiceDate || !newInvoice.value.dueDate || !Number.isInteger(parsedCounterpartyId) || !selectedCounterparty) {
        window.alert('Please complete invoice date, due date, and vendor/client.')
        return
    }

    const validLines = newInvoice.value.lineItems.filter((line) => {
        return line.description && Number(line.quantity) > 0 && Number(line.unitPrice) >= 0 && line.accountId
    })

    if (!validLines.length) {
        window.alert('Add at least one valid line item with linked account.')
        return
    }

    recomputeInvoiceTotals()

    try {
        const lineItems = validLines.map((line) => ({
            description: line.description,
            quantity: Number(line.quantity || 0),
            unit: line.unit,
            unit_price: Number(line.unitPrice || 0),
            chart_account_id: Number(line.accountId)
        }))

        const formData = new FormData()
        formData.append('module', moduleKey)
        formData.append('invoice_type', newInvoice.value.invoiceType)
        formData.append('counterparty_id', String(parsedCounterpartyId))
        formData.append('invoice_date', newInvoice.value.invoiceDate)
        formData.append('due_date', newInvoice.value.dueDate)
        formData.append('status', newInvoice.value.status)
        formData.append('description', newInvoice.value.description || '')
        formData.append('line_items', JSON.stringify(lineItems))

        newInvoice.value.attachments.forEach((file) => {
            formData.append('attachments_files[]', file)
        })

        const response = await axios.post('/api/invoices', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
        const created = response.data?.data

        if (created) {
            invoices.value.unshift(created)
        }

        showInvoiceModal.value = false
    } catch (error) {
        const message = error?.response?.data?.message || 'Failed to create invoice.'
        window.alert(message)
    }
}

function formatDate(value) {
    if (!value) return '--'

    return new Date(value).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

function formatMoney(value) {
    return `₱${Number(value || 0).toLocaleString()}`
}

function upsertInvoice(invoice) {
    const index = invoices.value.findIndex((row) => Number(row.id) === Number(invoice.id))
    if (index === -1) {
        invoices.value.unshift(invoice)
        return
    }

    invoices.value[index] = invoice
}

async function fetchInvoices() {
    try {
        const response = await axios.get('/api/invoices', {
            params: {
                module: moduleKey
            }
        })

        invoices.value = response.data?.data || []
    } catch (error) {
        invoices.value = []
    }
}

async function fetchCounterparties() {
    try {
        const response = await axios.get('/api/invoice-clients', {
            params: {
                status: 'active'
            }
        })
        const clients = response.data?.data || []

        const baseNames = clients.map((client) => {
            return client.name || client.contact_person || client.email || 'Unnamed Party'
        })

        const nameCounts = baseNames.reduce((map, name) => {
            map[name] = (map[name] || 0) + 1
            return map
        }, {})

        counterparties.value = clients.map((client, index) => {
            const numericId = Number(client.id)
            if (!Number.isFinite(numericId)) {
                return null
            }

            const baseName = baseNames[index]
            const disambiguator = client.email || `ID:${numericId}`
            const name = nameCounts[baseName] > 1 ? `${baseName} (${disambiguator})` : baseName

            return {
                id: numericId,
                name,
                key: `party-${numericId}-${index}`
            }
        }).filter(Boolean)
    } catch (error) {
        counterparties.value = []
    }
}

async function fetchChartAccounts() {
    try {
        const response = await axios.get('/api/chart-accounts')
        const rows = response.data?.data || []
        chartAccounts.value = rows
            .filter((account) => String(account.status || '').toLowerCase() === 'active')
            .map((account, index) => {
                const numericId = Number(account.id)
                if (!Number.isFinite(numericId)) {
                    return null
                }

                return {
                    ...account,
                    id: numericId,
                    key: `account-${numericId}-${index}`
                }
            })
            .filter(Boolean)
    } catch (error) {
        chartAccounts.value = []
    }
}

onMounted(async () => {
    auth.pageTitle = 'Payables'
    loading.value = true
    await Promise.all([fetchInvoices(), fetchCounterparties(), fetchChartAccounts()])
    loading.value = false
})
</script>
