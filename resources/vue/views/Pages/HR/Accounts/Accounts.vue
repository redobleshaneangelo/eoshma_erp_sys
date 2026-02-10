<template>
    <auth-layout />
        <div class="ps-60 bg-[#F3F4F6] min-h-screen">
            <div class="px-10 py-10">
                <!-- Tabs -->
                <div class="bg-white border-b border-gray-200 rounded-t-lg mb-0">
                    <nav class="flex gap-0 px-6 py-0 overflow-x-auto">
                        <button
                            v-for="tab in tabs"
                            :key="tab.key"
                            @click="activeTab = tab.key"
                            class="flex items-center gap-2 px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap"
                            :class="activeTab === tab.key
                                ? 'border-[#0c8ce9] text-[#0c8ce9]'
                                : 'border-transparent text-gray-600 hover:text-gray-900'"
                        >
                            <span v-html="tab.icon"></span>
                            {{ tab.label }}
                        </button>
                    </nav>
                </div>

                <div class="bg-white rounded-b-lg">
                    <!-- Filters Section -->
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-10">
                            <div class="flex-1">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </span>
                                    <input 
                                        v-model="searchQuery"
                                        type="text"
                                        placeholder="Search by name or email..." 
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent transition-all"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-4 gap-4 mb-10">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">Department</label>
                                <select 
                                    v-model="filters.department"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                >
                                    <option value="">All Departments</option>
                                    <option value="Engineering">Engineering</option>
                                    <option value="Sales">Sales</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="HR">HR</option>
                                    <option value="Finance">Finance</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">Job Title</label>
                                <select 
                                    v-model="filters.jobTitle"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                >
                                    <option value="">All Job Titles</option>
                                    <option value="Senior Engineer">Senior Engineer</option>
                                    <option value="Engineer">Engineer</option>
                                    <option value="Manager">Manager</option>
                                    <option value="Director">Director</option>
                                    <option value="Analyst">Analyst</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">Employment Status</label>
                                <select 
                                    v-model="filters.status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                >
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="on_leave">On Leave</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">Branch</label>
                                <select 
                                    v-model="filters.location"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9] focus:border-transparent"
                                >
                                    <option value="">All Branch</option>
                                    <option value="Main Office">Main Office</option>
                                    <option value="Branch A">Branch A</option>
                                    <option value="Branch B">Branch B</option>
                                    <option value="Remote">Remote</option>
                                </select>
                            </div>
                        </div>
                        <!-- Table Section -->
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-linear-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">User ID</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Name</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Email</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Role</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Department</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Branch</th>
                                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Status</th>
                                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="employee in filteredEmployees" :key="employee.account_id" class="hover:bg-blue-50 transition-all duration-200">
                                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ employee.account_id }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ employee.name }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ employee.email }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ employee.role }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ employee.department }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ employee.location || 'N/A' }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                <span :class="[
                                                    'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                                                    employee.status === 1
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-red-100 text-red-800'
                                                ]">
                                                    {{ employee.status === 1 ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button 
                                                    @click="viewEmployee(employee.account_id)"
                                                    class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-semibold text-white bg-[#0c8ce9] hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Empty State -->
                            <div v-if="filteredEmployees.length === 0" class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                                <p class="text-gray-500 text-lg font-medium">No employees found</p>
                                <p class="text-gray-400 text-sm mt-1">Try adjusting your search or filter criteria</p>
                            </div>
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
    import { ref, computed, onMounted } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import axios from 'axios'
    import Swal from 'sweetalert2'
    import { useAuthStore } from '@/stores/auth'
    import agnes from '@/assets/agnes.gif'

    const auth = useAuthStore()
    const router = useRouter()
    const route = useRoute()

    const hasPermission = (permission) => {
        return auth.permissions?.includes(permission)
    }

    /* =====================================================
    State
    ===================================================== */
    const loading = ref(false)
    const activeTab = ref('p')
    const employees = ref([])
    const searchQuery = ref('')

    const filters = ref({
        department: '',
        jobTitle: '',
        status: '',
        location: ''
    })

    /* =====================================================
    Tabs Configuration
    ===================================================== */
    const tabs = [
        {
            key: 'p',
            label: 'Permanent',
            icon: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>'
        },
        {
            key: 'c',
            label: 'Coterminus',
            icon: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>'
        },
        {
            key: 'f',
            label: 'Fixed-Term',
            icon: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>'
        },
    ]

    /* =====================================================
    Fetch Employees
    ===================================================== */
    const fetchEmployees = async () => {
        loading.value = true
        try {
            const response = await axios.get('/api/users', {
                headers: { Authorization: `Bearer ${auth.token}` }
            })
            employees.value = response.data.users || []
        } catch (error) {
            console.error('Error fetching employees:', error)
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: 'Failed to load employees'
            })
        } finally {
            loading.value = false
        }
    }

    /* =====================================================
    Computed - Filtered Employees
    ===================================================== */
    const filteredEmployees = computed(() => {
        return employees.value.filter(emp => {
            const matchDept = !filters.value.department || emp.department === filters.value.department
            const matchTitle = !filters.value.jobTitle || emp.role === filters.value.jobTitle
            const matchStatus = !filters.value.status || (
                (filters.value.status === 'active' && emp.status === 1) ||
                (filters.value.status === 'inactive' && emp.status === 0)
            )
            const matchLocation = !filters.value.location || emp.location === filters.value.location
            const matchSearch = !searchQuery.value || 
                emp.name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                emp.email?.toLowerCase().includes(searchQuery.value.toLowerCase())

            return matchDept && matchTitle && matchStatus && matchLocation && matchSearch
        })
    })

    /* =====================================================
    Actions
    ===================================================== */
    const viewEmployee = (accountId) => {
        router.push(`/accounts/${accountId}`)
    }

    /* =====================================================
    Lifecycle
    ===================================================== */
    onMounted(() => {
        auth.pageTitle = 'Accounts'
        fetchEmployees()
    })
</script>
