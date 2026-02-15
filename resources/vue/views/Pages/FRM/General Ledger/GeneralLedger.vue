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
                                        <template v-for="subAccount in accountGroup.subAccounts" :key="subAccount.code">
                                            <div
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
                                                        <template v-for="(trans, idx) in subAccount.transactions" :key="trans.id ?? idx">
                                                        <tr
                                                            class="hover:bg-white"
                                                            :class="canViewPayrollBreakdown(subAccount, trans) ? 'cursor-pointer' : ''"
                                                            @click="canViewPayrollBreakdown(subAccount, trans) ? togglePayrollBreakdown(subAccount, trans, idx) : null"
                                                        >
                                                            <td class="px-6 py-2 text-gray-700 whitespace-nowrap">{{ trans.date }}</td>
                                                            <td class="px-6 py-2 text-gray-700">
                                                                <div class="font-medium">{{ trans.desc }}</div>
                                                                <button
                                                                    v-if="canViewPayrollBreakdown(subAccount, trans)"
                                                                    type="button"
                                                                    class="text-xs text-[#0c8ce9] hover:underline mt-1"
                                                                    @click.stop="togglePayrollBreakdown(subAccount, trans, idx)"
                                                                >
                                                                    {{ isPayrollBreakdownExpanded(subAccount, trans, idx) ? 'Hide employee breakdown' : 'View employee breakdown' }}
                                                                </button>
                                                            </td>
                                                            <td class="px-6 py-2 text-gray-600">{{ trans.ref }}</td>
                                                            <td class="px-6 py-2 text-right text-gray-700">{{ trans.debit ? '₱' + trans.debit : '-' }}</td>
                                                            <td class="px-6 py-2 text-right text-gray-700">{{ trans.credit ? '₱' + trans.credit : '-' }}</td>
                                                            <td class="px-6 py-2 text-right font-semibold text-[#0c8ce9]">₱{{ trans.balance.toLocaleString() }}</td>
                                                        </tr>
                                                        <tr v-if="isPayrollBreakdownExpanded(subAccount, trans, idx)">
                                                            <td colspan="6" class="px-6 py-3 bg-white border-t border-dashed border-gray-200">
                                                                <div v-if="payrollBreakdownLoading" class="text-sm text-gray-500">Loading payroll breakdown...</div>
                                                                <div v-else-if="payrollBreakdownError" class="text-sm text-red-600">{{ payrollBreakdownError }}</div>
                                                                <div v-else>
                                                                    <div class="text-sm font-semibold text-gray-700 mb-2">
                                                                        {{ payrollBreakdownPayrollName || trans.payrollRunName || 'Payroll Run' }}
                                                                    </div>
                                                                    <table class="w-full text-xs border border-gray-200 rounded">
                                                                        <thead class="bg-gray-50">
                                                                            <tr>
                                                                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Employee</th>
                                                                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Role</th>
                                                                                <th class="px-3 py-2 text-right font-semibold text-gray-700">Debit</th>
                                                                                <th class="px-3 py-2 text-right font-semibold text-gray-700">Credit</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr
                                                                                v-for="(row, rowIndex) in payrollBreakdownRows"
                                                                                :key="`${row.employeeId}-${rowIndex}`"
                                                                                class="border-t border-gray-100"
                                                                            >
                                                                                <td class="px-3 py-2 text-gray-700">{{ row.employee }}</td>
                                                                                <td class="px-3 py-2 text-gray-600">{{ row.role || '-' }}</td>
                                                                                <td class="px-3 py-2 text-right text-gray-700">{{ row.debit > 0 ? '₱' + row.debit.toLocaleString() : '-' }}</td>
                                                                                <td class="px-3 py-2 text-right text-gray-700">{{ row.credit > 0 ? '₱' + row.credit.toLocaleString() : '-' }}</td>
                                                                            </tr>
                                                                            <tr v-if="!payrollBreakdownRows.length">
                                                                                <td colspan="4" class="px-3 py-3 text-center text-gray-500">No employee-level payroll details found.</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        </template>
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
import axios from 'axios'
import agnes from '@/assets/agnes.gif'
import close from '@/assets/close.png'
import open from '@/assets/open.png'

const auth = useAuthStore()
const loading = ref(false)
const filterType = ref('')
const payrollBreakdownKey = ref(null)
const payrollBreakdownLoading = ref(false)
const payrollBreakdownError = ref('')
const payrollBreakdownRows = ref([])
const payrollBreakdownPayrollName = ref('')
const payrollBreakdownCache = ref({})

const addChartAccountModal = ref(false)
const newChartAccount = ref({
    code: '',
    name: '',
    type: 'asset',
    subcategory: '',
    description: '',
    status: 'active'
})

const rangeGroups = [
    { id: 1, name: '1000-1999 – Assets', type: 'asset', min: 1000, max: 1999, expanded: true },
    { id: 2, name: '2000-2999 – Liabilities', type: 'liability', min: 2000, max: 2999, expanded: false },
    { id: 3, name: '3000-3999 – Equity', type: 'equity', min: 3000, max: 3999, expanded: false },
    { id: 4, name: '4000-4999 – Revenue', type: 'revenue', min: 4000, max: 4999, expanded: false },
    { id: 5, name: '5000-5999 – Direct Costs (Project Costs)', type: 'expense', min: 5000, max: 5999, expanded: false },
    { id: 6, name: '6000-6999 – Operating Expenses', type: 'expense', min: 6000, max: 6999, expanded: false }
]

const accounts = ref(rangeGroups.map(group => ({
    ...group,
    totalBalance: 0,
    subAccounts: []
})))

const formatTransactionDate = (value) => {
    if (!value) return '--'
    const date = new Date(value)
    if (Number.isNaN(date.getTime())) {
        return value
    }

    return date.toLocaleString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    })
}

const mapSubAccount = (row) => ({
    code: row.code,
    name: row.name,
    subcategory: row.subcategory,
    description: row.description,
    status: row.status,
    totalDebit: Number(row.total_debit || 0),
    totalCredit: Number(row.total_credit || 0),
    balance: Number(row.balance || 0),
    expanded: false,
    transactions: (row.transactions || []).map((transaction) => ({
        id: transaction.id,
        date: formatTransactionDate(transaction.transaction_date),
        desc: transaction.description,
        ref: transaction.reference,
        payrollRunId: transaction.payroll_run_id,
        payrollRunName: transaction.payroll_run?.name || null,
        debit: Number(transaction.debit || 0),
        credit: Number(transaction.credit || 0),
        balance: Number(transaction.balance || 0)
    }))
})

const makePayrollBreakdownKey = (subAccountCode, trans, idx) => `${subAccountCode}-${trans.id ?? idx}`

const isPayrollBreakdownAccount = (subAccount) => {
    const code = String(subAccount?.code || '')
    return code === '2200' || code === '2210'
}

const canViewPayrollBreakdown = (subAccount, trans) => isPayrollBreakdownAccount(subAccount) && !!trans?.payrollRunId

const isPayrollBreakdownExpanded = (subAccount, trans, idx) => payrollBreakdownKey.value === makePayrollBreakdownKey(subAccount.code, trans, idx)

const toMoney = (value) => Number(Number(value || 0).toFixed(2))

const getBreakdownAmount = (row, accountCode) => {
    if (String(accountCode) === '2200') {
        return toMoney(row.netPay)
    }

    if (String(accountCode) === '2210') {
        return toMoney(row.tax)
    }

    return 0
}

const mapPayrollRowsToBreakdown = (rows, accountCode, trans) => {
    const hasDebit = Number(trans?.debit || 0) > 0

    return (rows || []).map((row) => {
        const amount = getBreakdownAmount(row, accountCode)

        return {
        employeeId: row.id,
        employee: row.employee || '--',
        role: row.role || '--',
        debit: hasDebit ? amount : 0,
        credit: hasDebit ? 0 : amount
        }
    }).filter((row) => row.debit > 0 || row.credit > 0)
}

const togglePayrollBreakdown = async (subAccount, trans, idx) => {
    const key = makePayrollBreakdownKey(subAccount.code, trans, idx)

    if (payrollBreakdownKey.value === key) {
        payrollBreakdownKey.value = null
        return
    }

    payrollBreakdownKey.value = key
    payrollBreakdownError.value = ''
    payrollBreakdownRows.value = []
    payrollBreakdownPayrollName.value = trans.payrollRunName || ''

    const runId = trans.payrollRunId
    if (!runId) {
        payrollBreakdownError.value = 'No payroll run is linked to this transaction.'
        return
    }

    if (payrollBreakdownCache.value[runId]) {
        payrollBreakdownRows.value = payrollBreakdownCache.value[runId].rows
        payrollBreakdownPayrollName.value = payrollBreakdownCache.value[runId].name || payrollBreakdownPayrollName.value
        return
    }

    payrollBreakdownLoading.value = true
    try {
        const response = await axios.get(`/api/payroll-runs/${runId}/computed-payroll`)
        const rows = response.data?.data || []
        const mappedRows = mapPayrollRowsToBreakdown(rows, subAccount.code, trans)

        payrollBreakdownRows.value = mappedRows
        payrollBreakdownCache.value[runId] = {
            name: trans.payrollRunName || '',
            rows: mappedRows
        }
    } catch (error) {
        console.error('Failed to load payroll breakdown', error)
        payrollBreakdownError.value = error.response?.data?.message || 'Failed to load payroll breakdown.'
    } finally {
        payrollBreakdownLoading.value = false
    }
}

const toCodeNumber = (code) => {
    const parsed = Number.parseInt(String(code), 10)
    return Number.isNaN(parsed) ? -1 : parsed
}

const buildGroupedAccounts = (rows) => {
    const grouped = rangeGroups.map(group => ({
        ...group,
        totalBalance: 0,
        subAccounts: []
    }))

    rows.forEach((row) => {
        const code = toCodeNumber(row.code)
        let target = grouped.find(group => code >= group.min && code <= group.max)

        if (!target) {
            target = grouped.find(group => group.type === row.type)
        }

        if (!target) {
            return
        }

        target.subAccounts.push(mapSubAccount(row))
    })

    grouped.forEach(group => {
        group.subAccounts.sort((a, b) => toCodeNumber(a.code) - toCodeNumber(b.code))
        group.totalBalance = group.subAccounts.reduce((sum, item) => sum + Number(item.balance || 0), 0)
    })

    return grouped
}

const fetchChartAccounts = async () => {
    loading.value = true
    try {
        const response = await axios.get('/api/chart-accounts')
        const rows = response.data?.data || []
        accounts.value = buildGroupedAccounts(rows)
    } catch (error) {
        console.error('Failed to load chart accounts', error)
    } finally {
        loading.value = false
    }
}

const submitChartAccount = async () => {
    const code = newChartAccount.value.code.trim()
    const name = newChartAccount.value.name.trim()

    if (!code || !name) {
        alert('Please fill in required fields (Code and Name)')
        return
    }

    loading.value = true
    try {
        await axios.post('/api/chart-accounts', {
            code,
            name,
            type: newChartAccount.value.type,
            subcategory: newChartAccount.value.subcategory || null,
            description: newChartAccount.value.description || null,
            status: newChartAccount.value.status || 'active'
        })

        newChartAccount.value = {
            code: '',
            name: '',
            type: 'asset',
            subcategory: '',
            description: '',
            status: 'active'
        }

        addChartAccountModal.value = false
        await fetchChartAccounts()
    } catch (error) {
        console.error('Failed to add chart account', error)
        const message = error.response?.data?.message || 'Failed to add chart account.'
        alert(message)
    } finally {
        loading.value = false
    }
}

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

onMounted(async () => {
    auth.pageTitle = 'General Ledger'
    await fetchChartAccounts()
})
</script>
