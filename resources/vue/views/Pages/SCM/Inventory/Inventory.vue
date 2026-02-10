<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10">

            <!-- Stats Dashboard -->
            <div class="grid grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <div class="text-gray-600 text-xs font-semibold uppercase">Total Items</div>
                    <div class="text-3xl font-bold text-gray-900 mt-2">{{ totalItems }}</div>
                    <p class="text-xs text-gray-500 mt-2">{{ activeItemsCount }} active</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <div class="text-gray-600 text-xs font-semibold uppercase">Total Value</div>
                    <div class="text-3xl font-bold text-blue-600 mt-2">₱{{ formatCurrency(totalInventoryValue) }}</div>
                    <p class="text-xs text-gray-500 mt-2">Current stock value</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <div class="text-gray-600 text-xs font-semibold uppercase">Low Stock</div>
                    <div class="text-3xl font-bold text-orange-600 mt-2">{{ lowStockCount }}</div>
                    <p class="text-xs text-gray-500 mt-2">Below minimum level</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <div class="text-gray-600 text-xs font-semibold uppercase">Out of Stock</div>
                    <div class="text-3xl font-bold text-red-600 mt-2">{{ outOfStockCount }}</div>
                    <p class="text-xs text-gray-500 mt-2">Requires immediate order</p>
                </div>
            </div>

            <!-- Filters & Search -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 mb-8">
                <div class="grid grid-cols-5 gap-4 mb-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Search</label>
                        <input 
                            v-model="searchQuery"
                            type="text" 
                            placeholder="Item name, code..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Category</label>
                        <select 
                            v-model="selectedCategory"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="">All Categories</option>
                            <option value="Steel">Steel & Metal</option>
                            <option value="Cement">Cement & Concrete</option>
                            <option value="Timber">Timber & Wood</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Tools">Tools & Hardware</option>
                            <option value="Electrical">Electrical Supplies</option>
                            <option value="Plumbing">Plumbing Supplies</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Status</label>
                        <select 
                            v-model="selectedStatus"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="">All Status</option>
                            <option value="in-stock">In Stock</option>
                            <option value="low-stock">Low Stock</option>
                            <option value="out-of-stock">Out of Stock</option>
                        </select>
                    </div>

                    <!-- Warehouse Filter -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Warehouse</label>
                        <select 
                            v-model="selectedWarehouse"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="">All Warehouses</option>
                            <option value="Main Warehouse">Main Warehouse</option>
                            <option value="Site A">Site A</option>
                            <option value="Site B">Site B</option>
                            <option value="Equipment Yard">Equipment Yard</option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Sort By</label>
                        <select 
                            v-model="sortBy"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                        >
                            <option value="name">Item Name</option>
                            <option value="quantity">Quantity</option>
                            <option value="value">Total Value</option>
                            <option value="updated">Last Updated</option>
                        </select>
                    </div>
                </div>

                <!-- Filter Actions -->
                <div class="flex gap-2">
                    <button 
                        @click="clearFilters"
                        class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-semibold transition-all"
                    >
                        Clear Filters
                    </button>
                    <button 
                        @click="exportInventory"
                        class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-semibold transition-all flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Export
                    </button>
                </div>
            </div>

            <!-- Inventory Table -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Item Code</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Item Name</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Category</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase">Qty On Hand</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase">Unit Cost</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="item in filteredItems" :key="item.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900 font-semibold">{{ item.code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-semibold">{{ item.name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ item.category }}</td>
                            <td class="px-6 py-4 text-sm text-right font-semibold text-gray-900">{{ item.quantity }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span :class="[
                                    'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                                    statusStyles[item.status]
                                ]">
                                    {{ formatStatus(item.status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-right text-gray-900 font-semibold">₱{{ formatCurrency(item.unitCost) }}</td>
                            <td class="px-6 py-4 text-sm">
                                <button 
                                    @click="viewItem(item.id)"
                                    class="px-3 py-2 bg-blue-100 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-200 transition-all"
                                >
                                    View Details
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Empty State -->
                <div v-if="filteredItems.length === 0" class="p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10" />
                    </svg>
                    <p class="text-lg font-semibold text-gray-900 mb-2">No inventory items found</p>
                    <p class="text-sm text-gray-600">Try adjusting your filters or search criteria</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Swal from 'sweetalert2'

const router = useRouter()
const auth = useAuthStore()

// Filter States
const searchQuery = ref('')
const selectedCategory = ref('')
const selectedStatus = ref('')
const selectedWarehouse = ref('')
const sortBy = ref('name')

// Status Styles
const statusStyles = {
    'in-stock': 'bg-green-100 text-green-800 border border-green-300',
    'low-stock': 'bg-orange-100 text-orange-800 border border-orange-300',
    'out-of-stock': 'bg-red-100 text-red-800 border border-red-300'
}

// Mock Data - Construction Company Inventory
const allInventoryItems = ref([
    {
        id: 1,
        code: 'STL-001',
        name: 'Steel Reinforcement Bar 12mm',
        category: 'Steel',
        unit: 'Tons',
        quantity: 125,
        minimumLevel: 50,
        status: 'in-stock',
        warehouse: 'Main Warehouse',
        unitCost: 45000,
        totalValue: 5625000,
        lastUpdated: '2026-02-05',
        supplier: 'Steel Mills Inc',
        location: 'Rack A1-A5',
        brand: 'Philippine Steel',
        sizes: ['10mm', '12mm', '16mm', '20mm'],
        brandPrice: 45000
    },
    {
        id: 2,
        code: 'CMT-001',
        name: 'Portland Cement Type 1',
        category: 'Cement',
        unit: 'Bags',
        quantity: 450,
        minimumLevel: 200,
        status: 'in-stock',
        warehouse: 'Main Warehouse',
        unitCost: 300,
        totalValue: 135000,
        lastUpdated: '2026-02-04',
        supplier: 'Lafarge Philippines',
        location: 'Rack B1-B3',
        brand: 'Lafarge',
        sizes: ['25kg', '40kg'],
        brandPrice: 300
    },
    {
        id: 3,
        code: 'SND-001',
        name: 'River Sand',
        category: 'Cement',
        unit: 'm³',
        quantity: 35,
        minimumLevel: 100,
        status: 'low-stock',
        warehouse: 'Site A',
        unitCost: 2500,
        totalValue: 87500,
        lastUpdated: '2026-02-03',
        supplier: 'Quarry A',
        location: 'Outdoor Storage',
        brand: 'Quarry Premium',
        sizes: ['Bulk'],
        brandPrice: 2500
    },
    {
        id: 4,
        code: 'GRV-001',
        name: 'Gravel 10-20mm',
        category: 'Cement',
        unit: 'm³',
        quantity: 0,
        minimumLevel: 80,
        status: 'out-of-stock',
        warehouse: 'Site B',
        unitCost: 1500,
        totalValue: 0,
        lastUpdated: '2026-02-02',
        supplier: 'Quarry B',
        location: 'Outdoor Storage',
        brand: 'Quarry Standard',
        sizes: ['10-20mm'],
        brandPrice: 1500
    },
    {
        id: 5,
        code: 'TBR-001',
        name: '2x4 Wood Planks',
        category: 'Timber',
        unit: 'pcs',
        quantity: 280,
        minimumLevel: 150,
        status: 'in-stock',
        warehouse: 'Equipment Yard',
        unitCost: 400,
        totalValue: 112000,
        lastUpdated: '2026-02-05',
        supplier: 'Timber Supplies Co',
        location: 'Shed 2',
        brand: 'Premium Timber',
        sizes: ['2x4 Inches'],
        brandPrice: 400
    },
    {
        id: 6,
        code: 'PLY-001',
        name: 'Marine Grade Plywood 3/4"',
        category: 'Timber',
        unit: 'Sheets',
        quantity: 65,
        minimumLevel: 80,
        status: 'low-stock',
        warehouse: 'Main Warehouse',
        unitCost: 1200,
        totalValue: 78000,
        lastUpdated: '2026-02-04',
        supplier: 'Timber Supplies Co',
        location: 'Rack C1',
        brand: 'Marine Grade Pro',
        sizes: ['1/2 inch', '3/4 inch', '1 inch'],
        brandPrice: 1200
    },
    {
        id: 7,
        code: 'EQP-001',
        name: 'Concrete Mixer 9 cu. ft.',
        category: 'Equipment',
        unit: 'pcs',
        quantity: 5,
        minimumLevel: 3,
        status: 'in-stock',
        warehouse: 'Equipment Yard',
        unitCost: 45000,
        totalValue: 225000,
        lastUpdated: '2026-02-05',
        supplier: 'Equipment Rentals Inc',
        location: 'Shed 1',
        brand: 'Makita Professional',
        sizes: ['9 cu.ft.'],
        brandPrice: 45000
    },
    {
        id: 8,
        code: 'EQP-002',
        name: 'Hand Scaffolding Sets',
        category: 'Equipment',
        unit: 'Sets',
        quantity: 8,
        minimumLevel: 5,
        status: 'in-stock',
        warehouse: 'Equipment Yard',
        unitCost: 25000,
        totalValue: 200000,
        lastUpdated: '2026-02-04',
        supplier: 'Scaffolding Solutions Ltd',
        location: 'Shed 3',
        brand: 'SafeScaff',
        sizes: ['Standard Set'],
        brandPrice: 25000
    },
    {
        id: 9,
        code: 'TLS-001',
        name: 'Power Drill Set',
        category: 'Tools',
        unit: 'pcs',
        quantity: 12,
        minimumLevel: 8,
        status: 'in-stock',
        warehouse: 'Main Warehouse',
        unitCost: 8500,
        totalValue: 102000,
        lastUpdated: '2026-02-05',
        supplier: 'Tools Hardware Store',
        location: 'Rack D1',
        brand: 'Bosch Professional',
        sizes: ['Standard'],
        brandPrice: 8500
    },
    {
        id: 10,
        code: 'ELE-001',
        name: 'PVC Wire 2.5mm (100m)',
        category: 'Electrical',
        unit: 'Rolls',
        quantity: 24,
        minimumLevel: 15,
        status: 'in-stock',
        warehouse: 'Main Warehouse',
        unitCost: 3500,
        totalValue: 84000,
        lastUpdated: '2026-02-05',
        supplier: 'Electrical Supplies Inc',
        location: 'Rack E1',
        brand: 'Philips Electric',
        sizes: ['1.5mm', '2.5mm', '4mm', '6mm'],
        brandPrice: 3500
    },
    {
        id: 11,
        code: 'PLB-001',
        name: 'PVC Pipes 3" Diameter',
        category: 'Plumbing',
        unit: 'pcs',
        quantity: 45,
        minimumLevel: 30,
        status: 'in-stock',
        warehouse: 'Site A',
        unitCost: 850,
        totalValue: 38250,
        lastUpdated: '2026-02-04',
        supplier: 'Plumbing Supplies Ltd',
        location: 'Rack F1',
        brand: 'Iplex Plumbing',
        sizes: ['1.5 inch', '2 inch', '3 inch', '4 inch'],
        brandPrice: 850
    },
    {
        id: 12,
        code: 'CMT-002',
        name: 'Concrete Block 4" Standard',
        category: 'Cement',
        unit: 'pcs',
        quantity: 1200,
        minimumLevel: 500,
        status: 'in-stock',
        warehouse: 'Main Warehouse',
        unitCost: 15,
        totalValue: 18000,
        lastUpdated: '2026-02-05',
        supplier: 'Concrete Products Co',
        location: 'Outdoor Storage',
        brand: 'Standard Blocks',
        sizes: ['4 inch', '6 inch', '8 inch'],
        brandPrice: 15
    }
])

// Computed Properties
const filteredItems = computed(() => {
    let items = allInventoryItems.value.filter(item => {
        const matchesSearch = !searchQuery.value || 
            item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.code.toLowerCase().includes(searchQuery.value.toLowerCase())
        
        const matchesCategory = !selectedCategory.value || item.category === selectedCategory.value
        const matchesStatus = !selectedStatus.value || item.status === selectedStatus.value
        const matchesWarehouse = !selectedWarehouse.value || item.warehouse === selectedWarehouse.value
        
        return matchesSearch && matchesCategory && matchesStatus && matchesWarehouse
    })

    // Sort
    if (sortBy.value === 'name') {
        items.sort((a, b) => a.name.localeCompare(b.name))
    } else if (sortBy.value === 'quantity') {
        items.sort((a, b) => b.quantity - a.quantity)
    } else if (sortBy.value === 'value') {
        items.sort((a, b) => b.totalValue - a.totalValue)
    } else if (sortBy.value === 'updated') {
        items.sort((a, b) => new Date(b.lastUpdated) - new Date(a.lastUpdated))
    }

    return items
})

const totalItems = computed(() => allInventoryItems.value.length)

const activeItemsCount = computed(() => allInventoryItems.value.filter(i => i.quantity > 0).length)

const lowStockCount = computed(() => allInventoryItems.value.filter(i => i.status === 'low-stock').length)

const outOfStockCount = computed(() => allInventoryItems.value.filter(i => i.status === 'out-of-stock').length)

const totalInventoryValue = computed(() => {
    return allInventoryItems.value.reduce((sum, item) => sum + item.totalValue, 0)
})

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

const clearFilters = () => {
    searchQuery.value = ''
    selectedCategory.value = ''
    selectedStatus.value = ''
    selectedWarehouse.value = ''
    sortBy.value = 'name'
}

const viewItem = (itemId) => {
    router.push(`/inventory/${itemId}`)
}

const restockItem = (itemId) => {
    const item = allInventoryItems.value.find(i => i.id === itemId)
    if (!item) return

    Swal.fire({
        title: 'Restock Item?',
        html: `
            <div class="text-left">
                <p class="text-sm text-gray-700 mb-3">Initiate restock order for this item?</p>
                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Item:</span>
                        <span class="font-semibold text-gray-900">${item.name}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Current Stock:</span>
                        <span class="font-semibold text-gray-900">${item.quantity} ${item.unit}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Minimum Level:</span>
                        <span class="font-semibold text-gray-900">${item.minimumLevel} ${item.unit}</span>
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0c8ce9',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Create Restock Order',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'rounded-lg',
            confirmButton: 'px-6 py-2 rounded-lg font-semibold',
            cancelButton: 'px-6 py-2 rounded-lg font-semibold'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Restock Order Created!',
                text: `Restock order for ${item.name} has been created and sent to procurement.`,
                icon: 'success',
                confirmButtonColor: '#0c8ce9',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'rounded-lg',
                    confirmButton: 'px-6 py-2 rounded-lg font-semibold'
                }
            })
        }
    })
}

const exportInventory = () => {
    // TODO: API call to export inventory as CSV/Excel
    Swal.fire({
        title: 'Export Inventory',
        text: 'Inventory list has been exported to Excel',
        icon: 'success',
        confirmButtonColor: '#0c8ce9',
        confirmButtonText: 'OK',
        customClass: {
            popup: 'rounded-lg',
            confirmButton: 'px-6 py-2 rounded-lg font-semibold'
        }
    })
}

onMounted(() => {
    auth.pageTitle = 'Inventory'
})

</script>

