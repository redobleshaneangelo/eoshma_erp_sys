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
                                        @click="addChartAccountModal = true"
                                        class="flex items-center ms-auto gap-2 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Chart Account
                                    </button>
                                </div>
                            </div>

                            <!-- Filter Section -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
                                <div class="grid grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Type</label>
                                        <select v-model="filterType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c8ce9]">
                                            <option value="">All Types</option>
                                            <option value="asset">Assets</option>
                                            <option value="liability">Liabilities</option>
                                            <option value="equity">Equity</option>
                                            <option value="revenue">Revenue</option>
                                            <option value="expense">Expenses</option>
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
                                        <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c8ce9]" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c8ce9]" />
                                    </div>
                                </div>
                            </div>

                            <!-- Summary Cards -->
                            <div class="grid grid-cols-4 gap-4 mb-6">
                                <div class="bg-linear-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Assets</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">₱2,450,000</p>
                                    <p class="text-xs text-gray-600 mt-1">↑ 5.2% from last month</p>
                                </div>
                                <div class="bg-linear-to-br from-red-50 to-red-100 rounded-lg p-4 border border-red-200">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Liabilities</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">₱850,000</p>
                                    <p class="text-xs text-gray-600 mt-1">↓ 2.3% from last month</p>
                                </div>
                                <div class="bg-linear-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Net Equity</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">₱1,600,000</p>
                                    <p class="text-xs text-gray-600 mt-1">↑ 8.1% from last month</p>
                                </div>
                                <div class="bg-linear-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Transactions</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">584</p>
                                    <p class="text-xs text-gray-600 mt-1">This month</p>
                                </div>
                            </div>

                            <!-- Accounts by Type -->
                            <div class="space-y-4">
                                <div v-for="accountGroup in filteredAccounts" :key="accountGroup.id" class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                                    <!-- Account Group Header -->
                                    <div
                                        @click="toggleGroup(accountGroup.id)"
                                        class="px-6 py-4 bg-linear-to-r from-[#0c8ce9]/10 to-transparent border-b border-gray-100 cursor-pointer hover:bg-gray-50 flex items-center justify-between"
                                    >
                                        <div class="flex items-center gap-3">
                                            <img :src="accountGroup.expanded ? open : close" class="w-5 h-5" alt="toggle" />
                                            <div>
                                                <h3 class="font-semibold text-[#1f2937]">{{ accountGroup.name }}</h3>
                                                <p class="text-xs text-gray-500">{{ accountGroup.subAccounts.length }} accounts</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-[#0c8ce9]">₱{{ accountGroup.totalBalance.toLocaleString() }}</p>
                                            <p class="text-xs text-gray-500">Total Balance</p>
                                        </div>
                                    </div>

                                    <!-- Sub-accounts -->
                                    <div v-if="accountGroup.expanded" class="divide-y divide-gray-100">
                                        <div
                                            v-for="subAccount in accountGroup.subAccounts"
                                            :key="subAccount.code"
                                            @click="toggleSubAccount(accountGroup.id, subAccount.code)"
                                            class="px-6 py-3 cursor-pointer hover:bg-gray-50 flex items-center justify-between border-l-4 border-l-[#0c8ce9]"
                                        >
                                            <div class="flex items-center gap-2 flex-1">
                                                <img :src="subAccount.expanded ? open : close" class="w-4 h-4" alt="toggle" />
                                                <div>
                                                    <p class="font-medium text-[#333333]">{{ subAccount.code }} - {{ subAccount.name }}</p>
                                                    <p class="text-xs text-gray-500">{{ subAccount.transactions.length }} transactions</p>
                                                </div>
                                            </div>
                                            <div class="text-right ml-4">
                                                <p class="font-semibold text-[#1f2937]">₱{{ subAccount.balance.toLocaleString() }}</p>
                                                <div class="flex gap-4 text-xs text-gray-500 mt-1">
                                                    <span>D: ₱{{ subAccount.totalDebit.toLocaleString() }}</span>
                                                    <span>C: ₱{{ subAccount.totalCredit.toLocaleString() }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Transactions -->
                                        <template v-for="subAccount in accountGroup.subAccounts" :key="`trans-${subAccount.code}`">
                                            <div v-if="subAccount.expanded" class="bg-gray-50">
                                                <table class="w-full text-sm">
                                                    <thead class="bg-gray-100 border-t border-gray-200">
                                                        <tr>
                                                            <th class="px-6 py-2 text-left font-semibold text-gray-700">Date</th>
                                                            <th class="px-6 py-2 text-left font-semibold text-gray-700">Description</th>
                                                            <th class="px-6 py-2 text-left font-semibold text-gray-700">Reference</th>
                                                            <th class="px-6 py-2 text-right font-semibold text-gray-700">Debit</th>
                                                            <th class="px-6 py-2 text-right font-semibold text-gray-700">Credit</th>
                                                            <th class="px-6 py-2 text-right font-semibold text-gray-700">Running Bal.</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-200">
                                                        <tr v-for="(trans, idx) in subAccount.transactions" :key="idx" class="hover:bg-white">
                                                            <td class="px-6 py-2 text-gray-700 whitespace-nowrap">{{ trans.date }}</td>
                                                            <td class="px-6 py-2 text-gray-700">{{ trans.desc }}</td>
                                                            <td class="px-6 py-2 text-gray-600">{{ trans.ref }}</td>
                                                            <td class="px-6 py-2 text-right text-gray-700">{{ trans.debit ? '₱' + trans.debit : '-' }}</td>
                                                            <td class="px-6 py-2 text-right text-gray-700">{{ trans.credit ? '₱' + trans.credit : '-' }}</td>
                                                            <td class="px-6 py-2 text-right font-semibold text-[#0c8ce9]">₱{{ trans.balance.toLocaleString() }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Chart Account Modal -->
        <div v-if="addChartAccountModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-96 overflow-y-auto">
                <div class="sticky top-0 bg-linear-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white">Add New Chart Account</h3>
                    <button @click="addChartAccountModal = false" class="text-white hover:bg-blue-800 p-1 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Account Code</label>
                            <input v-model="newChartAccount.code" type="text" placeholder="e.g., 1010" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Account Name</label>
                            <input v-model="newChartAccount.name" type="text" placeholder="e.g., Cash on Hand" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Account Type</label>
                            <select v-model="newChartAccount.type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="asset">Assets</option>
                                <option value="liability">Liabilities</option>
                                <option value="equity">Equity</option>
                                <option value="revenue">Revenue</option>
                                <option value="expense">Expenses</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subcategory</label>
                            <input v-model="newChartAccount.subcategory" type="text" placeholder="e.g., Current Assets" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea v-model="newChartAccount.description" placeholder="Enter account description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select v-model="newChartAccount.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                    <button @click="addChartAccountModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Cancel</button>
                    <button @click="submitChartAccount" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">Add Account</button>
                </div>
            </div>
        </div>

        <div class="fixed bottom-0 right-0 p-2 z-50">
            <img :src="agnes" class="h-auto w-auto" v-if="loading" />
        </div>
    
</template>

<script setup>
    import { ref, computed, onMounted } from 'vue'
    import { useAuthStore } from '@/stores/auth'
    import agnes from '@/assets/agnes.gif'
    import close from '@/assets/close.png'
    import open from '@/assets/open.png'

    const auth = useAuthStore()
    const loading = ref(false)
    const filterType = ref('')

    /* =====================================================
    Modal State
    ===================================================== */
    const addChartAccountModal = ref(false)
    const newChartAccount = ref({
        code: '',
        name: '',
        type: 'asset',
        subcategory: '',
        description: '',
        status: 'active'
    })

    const submitChartAccount = () => {
        if (!newChartAccount.value.code || !newChartAccount.value.name) {
            alert('Please fill in required fields (Code and Name)')
            return
        }

        // Add new account to the appropriate group
        const accountType = newChartAccount.value.type
        const accountGroup = accounts.value.find(acc => acc.type === accountType)
        
        if (accountGroup) {
            accountGroup.subAccounts.unshift({
                code: newChartAccount.value.code,
                name: newChartAccount.value.name,
                subcategory: newChartAccount.value.subcategory,
                description: newChartAccount.value.description,
                status: newChartAccount.value.status,
                totalDebit: 0,
                totalCredit: 0,
                balance: 0,
                expanded: false,
                transactions: []
            })
            
            // Expand the group
            accountGroup.expanded = true
        }

        // Reset form
        newChartAccount.value = {
            code: '',
            name: '',
            type: 'asset',
            subcategory: '',
            description: '',
            status: 'active'
        }
        addChartAccountModal.value = false
    }

    const accounts = ref([
        {
            id: 1,
            name: 'Assets',
            type: 'asset',
            totalBalance: 2450000,
            expanded: true,
            subAccounts: [
                {
                    code: '1010',
                    name: 'Cash on Hand',
                    totalDebit: 500000,
                    totalCredit: 200000,
                    balance: 300000,
                    expanded: false,
                    transactions: [
                        { date: 'Jan 15, 2026', desc: 'Client Payment - Project A', ref: 'OR-001', debit: 150000, credit: null, balance: 150000 },
                        { date: 'Jan 20, 2026', desc: 'Subcontractor Payment', ref: 'CV-032', debit: null, credit: 50000, balance: 100000 }
                    ]
                },
                {
                    code: '1020',
                    name: 'Materials Inventory',
                    totalDebit: 800000,
                    totalCredit: 300000,
                    balance: 500000,
                    expanded: false,
                    transactions: [
                        { date: 'Jan 10, 2026', desc: 'Material Purchase - Steel', ref: 'PO-156', debit: 350000, credit: null, balance: 350000 },
                        { date: 'Jan 18, 2026', desc: 'Material Issue - Project B', ref: 'MI-045', debit: null, credit: 150000, balance: 200000 }
                    ]
                }
            ]
        },
        {
            id: 2,
            name: 'Liabilities',
            type: 'liability',
            totalBalance: 850000,
            expanded: false,
            subAccounts: [
                {
                    code: '2010',
                    name: 'Accounts Payable',
                    totalDebit: 200000,
                    totalCredit: 600000,
                    balance: 400000,
                    expanded: false,
                    transactions: [
                        { date: 'Jan 5, 2026', desc: 'Contractor Invoice - Labor', ref: 'INV-5001', debit: null, credit: 300000, balance: 300000 },
                        { date: 'Jan 25, 2026', desc: 'Payment to Contractor', ref: 'CV-050', debit: 150000, credit: null, balance: 150000 }
                    ]
                }
            ]
        },
        {
            id: 3,
            name: 'Equity',
            type: 'equity',
            totalBalance: 1600000,
            expanded: false,
            subAccounts: [
                {
                    code: '3010',
                    name: 'Capital Stock',
                    totalDebit: 0,
                    totalCredit: 1600000,
                    balance: 1600000,
                    expanded: false,
                    transactions: []
                }
            ]
        }
    ])

    const filteredAccounts = computed(() => {
        if (!filterType.value) return accounts.value
        return accounts.value.filter(a => a.type === filterType.value)
    })

    const toggleGroup = (id) => {
        const account = accounts.value.find(a => a.id === id)
        if (account) account.expanded = !account.expanded
    }

    const toggleSubAccount = (accountId, subAccountCode) => {
        const account = accounts.value.find(a => a.id === accountId)
        if (account) {
            const subAccount = account.subAccounts.find(sa => sa.code === subAccountCode)
            if (subAccount) subAccount.expanded = !subAccount.expanded
        }
    }

    onMounted(() => {
        auth.pageTitle = 'General Ledger'    
    })
</script>
