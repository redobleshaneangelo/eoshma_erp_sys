<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">
            <!-- Back Button & Header -->
            <div class="mb-8">
                <router-link 
                    to="/inventory"
                    class="inline-flex items-center gap-2 text-[#0c8ce9] font-semibold hover:underline mb-4"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Inventory
                </router-link>

                <div v-if="item" class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ item.name }}</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ item.code }} • {{ item.supplier }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span :class="[
                            'inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold',
                            statusStyles[item.status]
                        ]">
                            {{ formatStatus(item.status) }}
                        </span>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 border border-blue-300">
                            {{ item.category }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div v-if="item" class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200 bg-gray-50">
                    <nav class="flex gap-0 px-6">
                        <button
                            v-for="tab in tabs"
                            :key="tab.key"
                            @click="activeTab = tab.key"
                            class="py-4 px-4 border-b-2 font-semibold text-sm transition-colors"
                            :class="activeTab === tab.key
                                ? 'border-[#0c8ce9] text-[#0c8ce9]'
                                : 'border-transparent text-gray-600 hover:text-gray-900'"
                        >
                            {{ tab.label }}
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-8">
                    <!-- Overview Tab -->
                    <div v-if="activeTab === 'overview'" class="space-y-6">
                        <!-- Item Information -->
                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Item Information</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Item Code</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ item.code }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Item Name</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ item.name }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Category</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ item.category }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Unit</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ item.unit }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Stock Information</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Quantity On Hand</span>
                                        <span class="text-base font-bold text-gray-900">{{ item.quantity }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Minimum Level</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ item.minimumLevel }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Unit Cost</span>
                                        <span class="text-sm font-semibold text-gray-900">₱{{ formatCurrency(item.unitCost) }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-200 pb-2">
                                        <span class="text-sm text-gray-600">Status</span>
                                        <span :class="[
                                            'text-sm font-semibold px-3 py-1 rounded-full',
                                            statusStyles[item.status]
                                        ]">
                                            {{ formatStatus(item.status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Brand & Pricing -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Brand Name</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between border-b border-gray-200 pb-2">
                                    <span class="text-sm text-gray-600">Brand</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ item.brand }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Warehouse & Location -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Warehouse & Location</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between border-b border-gray-200 pb-2">
                                    <span class="text-sm text-gray-600">Warehouse</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ item.warehouse }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 pb-2">
                                    <span class="text-sm text-gray-600">Location/Rack</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ item.location }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 pb-2">
                                    <span class="text-sm text-gray-600">Last Updated</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ formatDate(item.lastUpdated) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Available Sizes Tab -->
                    <div v-if="activeTab === 'sizes'" class="space-y-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-900">Available Sizes & Stock Levels</h3>
                            <button 
                                v-if="selectedSize"
                                @click="selectedSize = null"
                                class="text-sm text-blue-600 hover:text-blue-800 font-semibold px-3 py-2 rounded-lg hover:bg-blue-50 transition-colors"
                            >
                                Clear Filter
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <button 
                                v-for="sizeStock in item.sizeStocks" 
                                :key="sizeStock.size"
                                @click="selectedSize = selectedSize === sizeStock.size ? null : sizeStock.size"
                                :class="[
                                    'p-4 border-2 rounded-lg transition-all text-left',
                                    selectedSize === sizeStock.size
                                        ? 'border-blue-600 bg-blue-50 shadow-md'
                                        : 'border-gray-200 bg-white hover:border-blue-300'
                                ]"
                            >
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="text-lg font-bold text-gray-900">{{ sizeStock.size }}</h4>
                                    <span :class="[
                                        'text-xs font-semibold px-2 py-1 rounded-full',
                                        sizeStock.quantity > 0 
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800'
                                    ]">
                                        {{ sizeStock.quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                                    </span>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Current Stock</span>
                                        <span class="text-lg font-bold text-gray-900">{{ sizeStock.quantity }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Unit</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ item.unit }}</span>
                                    </div>
                                </div>
                            </button>
                        </div>

                        <!-- Selected Size Details -->
                        <div v-if="selectedSize" class="mt-8 p-6 bg-blue-50 border-2 border-blue-200 rounded-lg">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="text-xl font-bold text-gray-900">{{ selectedSize }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">Current filtered quantity</p>
                                </div>
                                <p class="text-3xl font-bold text-blue-600">{{ filteredQuantity }}</p>
                            </div>
                            <div class="text-sm text-gray-700">
                                <p>Showing available stock for size: <span class="font-bold text-gray-900">{{ selectedSize }}</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Stock History Tab -->
                    <div v-if="activeTab === 'history'" class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Stock Movement History</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Transaction Type</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Quantity</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Balance</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Reference</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="transaction in stockHistory" :key="transaction.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ formatDate(transaction.date) }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span :class="[
                                                'inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold',
                                                transaction.type === 'Inbound' ? 'bg-green-100 text-green-800' : 
                                                transaction.type === 'Outbound' ? 'bg-red-100 text-red-800' :
                                                'bg-gray-100 text-gray-800'
                                            ]">
                                                {{ transaction.type }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right font-semibold" :class="transaction.type === 'Inbound' ? 'text-green-600' : 'text-red-600'">
                                            {{ transaction.type === 'Inbound' ? '+' : '-' }}{{ transaction.quantity }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">{{ transaction.balance }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ transaction.reference }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ transaction.remarks }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Suppliers & Pricing Tab -->
                    <div v-if="activeTab === 'suppliers'" class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Suppliers & Pricing</h3>
                        <div class="space-y-3">
                            <div 
                                v-for="supplier in item.suppliers" 
                                :key="supplier.id"
                                class="w-full p-6 border border-gray-200 rounded-lg hover:shadow-lg transition-all hover:border-blue-300"
                            >
                                <div class="flex justify-between items-start gap-4">
                                    <div class="flex-1">
                                        <p class="text-lg font-bold text-gray-900">{{ supplier.name }}</p>
                                        <p class="text-sm text-gray-600 mt-2">
                                            <span class="font-semibold">Location:</span> {{ supplier.contact }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600 mb-1">Price per {{ item.unit }}</p>
                                        <p class="text-3xl font-bold text-blue-600">₱{{ formatCurrency(supplier.price) }}</p>
                                        <button class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
                                            Select Supplier
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Valuation Tab -->
                    <div v-if="activeTab === 'valuation'" class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Inventory Valuation</h3>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <p class="text-sm text-gray-600 font-semibold uppercase mb-2">Unit Cost (FIFO)</p>
                                    <p class="text-2xl font-bold text-gray-900">₱{{ formatCurrency(item.unitCost) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 font-semibold uppercase mb-2">Current Quantity</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ item.quantity }} {{ item.unit }}</p>
                                </div>
                                <div class="col-span-2 pt-4 border-t border-gray-300">
                                    <p class="text-sm text-gray-600 font-semibold uppercase mb-2">Total Inventory Value</p>
                                    <p class="text-3xl font-bold text-blue-600">₱{{ formatCurrency(item.totalValue) }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-base font-bold text-gray-900 mb-4">Cost Breakdown</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-3 px-4 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-600">Material Cost</span>
                                    <span class="text-sm font-bold text-gray-900">₱{{ formatCurrency(item.unitCost * 0.7) }}</span>
                                </div>
                                <div class="flex justify-between items-center py-3 px-4 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-600">Labor & Handling</span>
                                    <span class="text-sm font-bold text-gray-900">₱{{ formatCurrency(item.unitCost * 0.2) }}</span>
                                </div>
                                <div class="flex justify-between items-center py-3 px-4 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-600">Overhead</span>
                                    <span class="text-sm font-bold text-gray-900">₱{{ formatCurrency(item.unitCost * 0.1) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Tab -->
                    <div v-if="activeTab === 'location'" class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Storage Location Details</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                                <h4 class="text-sm font-bold text-gray-900 mb-4 uppercase">Primary Location</h4>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs text-gray-600 font-semibold mb-1">Warehouse</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ item.warehouse }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600 font-semibold mb-1">Rack/Location</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ item.location }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600 font-semibold mb-1">Quantity Stored</p>
                                        <p class="text-base font-bold text-gray-900">{{ item.quantity }} {{ item.unit }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                                <h4 class="text-sm font-bold text-blue-900 mb-4 uppercase">Storage Requirements</h4>
                                <div class="space-y-2 text-sm text-blue-900">
                                    <p>✓ Temperature controlled storage recommended</p>
                                    <p>✓ Keep away from moisture</p>
                                    <p>✓ Stack in designated areas only</p>
                                    <p>✓ Follow FIFO inventory method</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-lg border border-gray-200 shadow-sm p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10" />
                </svg>
                <p class="text-lg font-semibold text-gray-900 mb-2">Inventory Item Not Found</p>
                <p class="text-sm text-gray-600 mb-4">The item you're looking for doesn't exist.</p>
                <router-link
                    to="/scm/inventory"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#0c8ce9] text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Go Back
                </router-link>
            </div>

            <!-- Action Buttons -->
            <div v-if="item" class="mt-8 flex justify-end gap-3">
                <button 
                    @click="adjustInventory"
                    class="px-6 py-3 bg-white border-2 border-blue-600 text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition-all"
                >
                    Adjust Inventory
                </button>

                <button 
                    v-if="item.status === 'low-stock' || item.status === 'out-of-stock'"
                    @click="restockItem"
                    class="px-6 py-3 bg-orange-600 text-white rounded-lg font-semibold hover:bg-orange-700 transition-all"
                >
                    Create Restock Order
                </button>

                <button 
                    @click="transferItem"
                    class="px-6 py-3 bg-white border-2 border-purple-600 text-purple-600 rounded-lg font-semibold hover:bg-purple-50 transition-all"
                >
                    Transfer Item
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import Swal from 'sweetalert2'

const router = useRouter()
const route = useRoute()

const item = ref(null)
const activeTab = ref('overview')
const selectedSize = ref(null)

const tabs = [
    { key: 'overview', label: 'Overview' },
    { key: 'sizes', label: 'Available Sizes' },
    { key: 'suppliers', label: 'Suppliers & Pricing' },
    { key: 'history', label: 'Stock History' },
    { key: 'valuation', label: 'Valuation' },
    { key: 'location', label: 'Location' }
]

// Status Styles
const statusStyles = {
    'in-stock': 'bg-green-100 text-green-800 border border-green-300',
    'low-stock': 'bg-orange-100 text-orange-800 border border-orange-300',
    'out-of-stock': 'bg-red-100 text-red-800 border border-red-300'
}

// Computed property for filtered quantity
const filteredQuantity = computed(() => {
    if (!item.value || !selectedSize.value) return item.value?.quantity || 0
    const sizeData = item.value.sizeStocks?.find(s => s.size === selectedSize.value)
    return sizeData?.quantity || 0
})

// Mock Data - Inventory Items
const allInventoryItems = [
    {
        id: 1,
        code: 'STL-001',
        name: 'Steel Reinforcement Bar 12mm',
        brand: 'Philippine Steel',
        sizes: ['10mm', '12mm', '16mm', '20mm'],
        sizeStocks: [
            { size: '10mm', quantity: 30 },
            { size: '12mm', quantity: 45 },
            { size: '16mm', quantity: 35 },
            { size: '20mm', quantity: 15 }
        ],
        category: 'Steel',
        unit: 'Tons',
        quantity: 125,
        minimumLevel: 50,
        status: 'in-stock',
        warehouse: 'Main Warehouse',
        unitCost: 45000,
        totalValue: 5625000,
        lastUpdated: '2026-02-05',
        location: 'Rack A1',
        suppliers: [
            { id: 1, name: 'Steel Mills Inc', contact: 'Manila, Philippines', price: 45000 },
            { id: 2, name: 'Premium Steel Corp', contact: 'Quezon City', price: 46500 },
            { id: 3, name: 'Metro Steel Supplies', contact: 'Makati', price: 44000 }
        ]
    },
    {
        id: 2,
        code: 'CMT-001',
        name: 'Portland Cement 50kg',
        brand: 'Lafarge',
        sizes: ['25kg', '40kg'],
        sizeStocks: [
            { size: '25kg', quantity: 200 },
            { size: '40kg', quantity: 150 }
        ],
        category: 'Cement',
        unit: 'Bags',
        quantity: 350,
        minimumLevel: 200,
        status: 'in-stock',
        warehouse: 'Main Warehouse',
        unitCost: 300,
        totalValue: 105000,
        lastUpdated: '2026-02-05',
        location: 'Rack B1',
        suppliers: [
            { id: 1, name: 'Cement Suppliers Ltd', contact: 'Pasay City', price: 300 },
            { id: 2, name: 'Lafarge Distribution', contact: 'Manila', price: 295 },
            { id: 3, name: 'Solid Cement Corp', contact: 'Quezon City', price: 305 }
        ]
    },
    {
        id: 3,
        code: 'SAG-001',
        name: 'Fine Sand Aggregate',
        brand: 'Local Quarry',
        sizes: ['Fine', 'Medium', 'Coarse'],
        sizeStocks: [
            { size: 'Fine', quantity: 12 },
            { size: 'Medium', quantity: 15 },
            { size: 'Coarse', quantity: 8 }
        ],
        category: 'Aggregates',
        unit: 'm³',
        quantity: 35,
        minimumLevel: 20,
        status: 'low-stock',
        warehouse: 'Site A',
        unitCost: 2500,
        totalValue: 87500,
        lastUpdated: '2026-02-03',
        location: 'Outdoor Storage',
        suppliers: [
            { id: 1, name: 'Quarry A', contact: 'Antipolo, Rizal', price: 2500 },
            { id: 2, name: 'Aggregate Solutions Inc', contact: 'San Juan, Rizal', price: 2450 },
            { id: 3, name: 'Metro Quarry Supply', contact: 'Angono, Rizal', price: 2600 }
        ]
    },
    {
        id: 4,
        code: 'GRV-001',
        name: 'Gravel 10-20mm',
        brand: 'Local Quarry',
        sizes: ['10-20mm', '20-40mm'],
        sizeStocks: [
            { size: '10-20mm', quantity: 0 },
            { size: '20-40mm', quantity: 0 }
        ],
        category: 'Cement',
        unit: 'm³',
        quantity: 0,
        minimumLevel: 80,
        status: 'out-of-stock',
        warehouse: 'Site B',
        unitCost: 1500,
        totalValue: 0,
        lastUpdated: '2026-02-02',
        location: 'Outdoor Storage',
        suppliers: [
            { id: 1, name: 'Quarry B', contact: 'Cainta, Rizal', price: 1500 },
            { id: 2, name: 'Mountain Aggregates Ltd', contact: 'Morong, Rizal', price: 1480 },
            { id: 3, name: 'Stone Supply Company', contact: 'Tanay, Rizal', price: 1550 }
        ]
    },
    {
        id: 5,
        code: 'TBR-001',
        name: '2x4 Wood Planks',
        brand: 'Local Lumber',
        sizes: ['6ft', '8ft', '10ft', '12ft'],
        sizeStocks: [
            { size: '6ft', quantity: 85 },
            { size: '8ft', quantity: 95 },
            { size: '10ft', quantity: 60 },
            { size: '12ft', quantity: 40 }
        ],
        category: 'Timber',
        unit: 'pcs',
        quantity: 280,
        minimumLevel: 150,
        status: 'in-stock',
        warehouse: 'Equipment Yard',
        unitCost: 400,
        totalValue: 112000,
        lastUpdated: '2026-02-05',
        location: 'Shed 2',
        suppliers: [
            { id: 1, name: 'Timber Supplies Co', contact: 'Marikina, Metro Manila', price: 400 },
            { id: 2, name: 'Premium Lumber Traders', contact: 'Valenzuela', price: 420 },
            { id: 3, name: 'Quality Wood Supplies', contact: 'Makati', price: 385 }
        ]
    },
    {
        id: 6,
        code: 'PLY-001',
        name: 'Marine Grade Plywood 3/4"',
        brand: 'Benwood Philippines',
        sizes: ['3/4"', '1/2"', '1/4"'],
        sizeStocks: [
            { size: '3/4"', quantity: 28 },
            { size: '1/2"', quantity: 22 },
            { size: '1/4"', quantity: 15 }
        ],
        category: 'Timber',
        unit: 'Sheets',
        quantity: 65,
        minimumLevel: 80,
        status: 'low-stock',
        warehouse: 'Main Warehouse',
        unitCost: 1200,
        totalValue: 78000,
        lastUpdated: '2026-02-04',
        location: 'Rack C2',
        suppliers: [
            { id: 1, name: 'Benwood Supplies', contact: 'Pasay City', price: 1200 },
            { id: 2, name: 'Plywood Trading Center', contact: 'Manila', price: 1180 },
            { id: 3, name: 'Engineered Wood Solutions', contact: 'Quezon City', price: 1250 }
        ]
    },
    {
        id: 7,
        code: 'PNT-001',
        name: 'Acrylic House Paint',
        brand: 'Boysen',
        sizes: ['1L', '5L', '10L', '20L'],
        sizeStocks: [
            { size: '1L', quantity: 40 },
            { size: '5L', quantity: 50 },
            { size: '10L', quantity: 20 },
            { size: '20L', quantity: 10 }
        ],
        category: 'Paint & Coatings',
        unit: 'Cans',
        quantity: 120,
        minimumLevel: 60,
        status: 'in-stock',
        warehouse: 'Main Warehouse',
        unitCost: 850,
        totalValue: 102000,
        lastUpdated: '2026-02-05',
        location: 'Rack D1',
        suppliers: [
            { id: 1, name: 'Paint Distributors Inc', contact: 'Manila', price: 850 },
            { id: 2, name: 'Boysen Authorized Dealer', contact: 'Makati', price: 840 },
            { id: 3, name: 'Color Plus Trading', contact: 'Pasig', price: 880 }
        ]
    },
    {
        id: 8,
        code: 'NL-001',
        name: 'Common Nails Assorted',
        brand: 'Sabol',
        sizes: ['1.5"', '2"', '2.5"', '3"'],
        sizeStocks: [
            { size: '1.5"', quantity: 120 },
            { size: '2"', quantity: 140 },
            { size: '2.5"', quantity: 110 },
            { size: '3"', quantity: 80 }
        ],
        category: 'Hardware',
        unit: 'kg',
        quantity: 450,
        minimumLevel: 200,
        status: 'in-stock',
        warehouse: 'Main Warehouse',
        unitCost: 180,
        totalValue: 81000,
        lastUpdated: '2026-02-05',
        location: 'Rack B2',
        suppliers: [
            { id: 1, name: 'Hardware Wholesale', contact: 'Las Piñas', price: 180 },
            { id: 2, name: 'Sabol Fasteners Direct', contact: 'Kawit, Cavite', price: 175 },
            { id: 3, name: 'Metro Hardware Supply', contact: 'Navotas', price: 185 }
        ]
    },
    {
        id: 9,
        code: 'TNL-001',
        name: 'Professional Tool Set',
        brand: 'Bosch Professional',
        sizes: ['Compact', 'Standard', 'Complete'],
        sizeStocks: [
            { size: 'Compact', quantity: 8 },
            { size: 'Standard', quantity: 5 },
            { size: 'Complete', quantity: 2 }
        ],
        category: 'Tools',
        unit: 'Sets',
        quantity: 15,
        minimumLevel: 8,
        status: 'in-stock',
        warehouse: 'Equipment Yard',
        unitCost: 12500,
        totalValue: 187500,
        lastUpdated: '2026-02-05',
        location: 'Rack D1',
        suppliers: [
            { id: 1, name: 'Tools Hardware Store', contact: 'Divisoria, Manila', price: 12500 },
            { id: 2, name: 'Bosch Authorized Distributor', contact: 'Makati', price: 12800 },
            { id: 3, name: 'Power Tools Center', contact: 'Pasig', price: 12200 }
        ]
    },
    {
        id: 10,
        code: 'ELE-001',
        name: 'PVC Wire 2.5mm (100m)',
        brand: 'Philips Electric',
        sizes: ['1.5mm', '2.5mm', '4mm', '6mm'],
        sizeStocks: [
            { size: '1.5mm', quantity: 8 },
            { size: '2.5mm', quantity: 10 },
            { size: '4mm', quantity: 4 },
            { size: '6mm', quantity: 2 }
        ],
        category: 'Electrical',
        unit: 'Rolls',
        quantity: 24,
        minimumLevel: 15,
        status: 'in-stock',
        warehouse: 'Main Warehouse',
        unitCost: 3500,
        totalValue: 84000,
        lastUpdated: '2026-02-05',
        location: 'Rack E1',
        suppliers: [
            { id: 1, name: 'Electrical Supplies Inc', contact: 'Quezon City', price: 3500 },
            { id: 2, name: 'Philips Electric Distributor', contact: 'Manila', price: 3450 },
            { id: 3, name: 'Premium Electrical Wires', contact: 'Makati', price: 3600 }
        ]
    },
    {
        id: 11,
        code: 'PLB-001',
        name: 'PVC Pipes 3" Diameter',
        brand: 'Iplex',
        sizes: ['1"', '2"', '3"', '4"'],
        sizeStocks: [
            { size: '1"', quantity: 15 },
            { size: '2"', quantity: 12 },
            { size: '3"', quantity: 12 },
            { size: '4"', quantity: 6 }
        ],
        category: 'Plumbing',
        unit: 'pcs',
        quantity: 45,
        minimumLevel: 30,
        status: 'in-stock',
        warehouse: 'Site A',
        unitCost: 850,
        totalValue: 38250,
        lastUpdated: '2026-02-04',
        location: 'Rack F1',
        suppliers: [
            { id: 1, name: 'Plumbing Supplies Ltd', contact: 'Makati', price: 850 },
            { id: 2, name: 'Iplex Direct Sales', contact: 'Santa Rosa, Laguna', price: 820 },
            { id: 3, name: 'PVC Pipe Center', contact: 'Pasig', price: 880 }
        ]
    },
    {
        id: 12,
        code: 'CMT-002',
        name: 'Concrete Block 4" Standard',
        brand: 'Concrete Products Co',
        sizes: ['4"', '6"', '8"'],
        sizeStocks: [
            { size: '4"', quantity: 600 },
            { size: '6"', quantity: 400 },
            { size: '8"', quantity: 200 }
        ],
        category: 'Cement',
        unit: 'pcs',
        quantity: 1200,
        minimumLevel: 500,
        status: 'in-stock',
        warehouse: 'Main Warehouse',
        unitCost: 15,
        totalValue: 18000,
        lastUpdated: '2026-02-05',
        location: 'Outdoor Storage',
        suppliers: [
            { id: 1, name: 'Concrete Products Co', contact: 'Laguna', price: 15 },
            { id: 2, name: 'CMU Manufacturing Corp', contact: 'Cavite', price: 14.50 },
            { id: 3, name: 'Quality Concrete Blocks', contact: 'Batangas', price: 16 }
        ]
    }
]

// Mock Stock History
const stockHistory = ref([
    { id: 1, date: '2026-02-05', type: 'Inbound', quantity: 50, balance: 125, reference: 'PO-2026-087', remarks: 'Delivery from Steel Mills Inc' },
    { id: 2, date: '2026-02-04', type: 'Outbound', quantity: 30, balance: 75, reference: 'MR-2026-156', remarks: 'Material request for PRJ-2026-001' },
    { id: 3, date: '2026-02-02', type: 'Inbound', quantity: 100, balance: 105, reference: 'PO-2026-082', remarks: 'Stock replenishment' },
    { id: 4, date: '2026-02-01', type: 'Outbound', quantity: 20, balance: 5, reference: 'MR-2026-145', remarks: 'Emergency requirement' },
    { id: 5, date: '2026-01-30', type: 'Adjustment', quantity: 5, balance: 25, reference: 'ADJ-2026-012', remarks: 'Stock count adjustment' }
])

// Methods
const formatCurrency = (value) => {
    return parseFloat(value).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatStatus = (status) => {
    const map = {
        'in-stock': 'In Stock',
        'low-stock': 'Low Stock',
        'out-of-stock': 'Out of Stock'
    }
    return map[status] || status
}

const adjustInventory = () => {
    if (!item.value) return
    
    Swal.fire({
        title: 'Adjust Inventory',
        html: `
            <div class="text-left">
                <p class="text-sm text-gray-700 mb-3">Adjust quantity for ${item.value.name}</p>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Adjustment Type</label>
                        <select id="adjType" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="increase">Increase</option>
                            <option value="decrease">Decrease</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Quantity</label>
                        <input id="adjQty" type="number" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Reason</label>
                        <input id="adjReason" type="text" placeholder="e.g., Physical count, Damage, etc." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0c8ce9',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Apply Adjustment',
        cancelButtonText: 'Cancel',
        didOpen: () => {
            document.getElementById('adjQty')?.focus()
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Adjustment Applied!',
                text: 'Inventory adjustment has been recorded.',
                icon: 'success',
                confirmButtonColor: '#0c8ce9',
                confirmButtonText: 'OK'
            })
        }
    })
}

const restockItem = () => {
    if (!item.value) return

    Swal.fire({
        title: 'Create Restock Order',
        html: `
            <div class="text-left">
                <p class="text-sm text-gray-700 mb-3">Create restock order for ${item.value.name}</p>
                <div class="space-y-3">
                    <div class="bg-gray-50 rounded-lg p-3 space-y-2">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-600">Current Stock:</span>
                            <span class="font-semibold">${item.value.quantity} ${item.value.unit}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-600">Minimum Level:</span>
                            <span class="font-semibold">${item.value.minimumLevel} ${item.value.unit}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Restock Quantity</label>
                        <input id="restockQty" type="number" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0c8ce9',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Create Order',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Restock Order Created!',
                text: 'Restock order has been sent to procurement department.',
                icon: 'success',
                confirmButtonColor: '#0c8ce9',
                confirmButtonText: 'OK'
            })
        }
    })
}

const transferItem = () => {
    if (!item.value) return

    Swal.fire({
        title: 'Transfer Item',
        html: `
            <div class="text-left">
                <p class="text-sm text-gray-700 mb-3">Transfer ${item.value.name} to another location</p>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">From Location</label>
                        <input type="text" value="${item.value.warehouse}" disabled class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">To Location</label>
                        <select id="toLocation" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option>Select destination</option>
                            <option>Main Warehouse</option>
                            <option>Site A</option>
                            <option>Site B</option>
                            <option>Equipment Yard</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Quantity to Transfer</label>
                        <input id="transferQty" type="number" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0c8ce9',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Transfer',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Transfer Initiated!',
                text: 'Item transfer has been recorded in the system.',
                icon: 'success',
                confirmButtonColor: '#0c8ce9',
                confirmButtonText: 'OK'
            })
        }
    })
}

// Load item on mount
onMounted(() => {
    const itemId = parseInt(route.params.id)
    item.value = allInventoryItems.find(i => i.id === itemId)
})
</script>
