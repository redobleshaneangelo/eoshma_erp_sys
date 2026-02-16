<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10 space-y-6">
            <div class="flex items-center justify-between">
                <button
                    @click="router.push({ name: 'leave_and_absence' })"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-300 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </button>

                <button
                    @click="openCreateModal"
                    class="px-5 py-2.5 bg-[#0c8ce9] text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition-colors"
                >
                    Add Leave Type
                </button>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-4">
                <div class="grid grid-cols-3 gap-4">
                    <input
                        v-model="filters.search"
                        type="text"
                        placeholder="Search leave type..."
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    />

                    <select
                        v-model="filters.status"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    >
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>

                    <select
                        v-model="pagination.perPage"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    >
                        <option :value="10">10 per page</option>
                        <option :value="25">25 per page</option>
                        <option :value="50">50 per page</option>
                    </select>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-100 border-b border-gray-200">
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Leave Type</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Days Per Year</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Updated</th>
                                <th class="px-6 py-3 text-center font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="item in rows" :key="item.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ item.name }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ item.daysPerYear }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="item.isActive ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700'"
                                        class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold"
                                    >
                                        {{ item.isActive ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-700">{{ formatDate(item.updatedAt) }}</td>
                                <td class="px-6 py-4 text-center space-x-2">
                                    <button
                                        @click="openEditModal(item)"
                                        class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        @click="removeType(item)"
                                        class="px-3 py-1 text-xs font-semibold text-white bg-red-600 rounded hover:bg-red-700"
                                    >
                                        Remove
                                    </button>
                                </td>
                            </tr>

                            <tr v-if="rows.length === 0">
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">No leave types found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        Showing {{ meta.total ? ((meta.current_page - 1) * meta.per_page) + 1 : 0 }} to
                        {{ Math.min(meta.current_page * meta.per_page, meta.total) }} of {{ meta.total }}
                    </p>

                    <div class="flex gap-2">
                        <button
                            @click="goToPage(meta.current_page - 1)"
                            :disabled="meta.current_page <= 1"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm disabled:opacity-50"
                        >
                            Previous
                        </button>
                        <button
                            @click="goToPage(meta.current_page + 1)"
                            :disabled="meta.current_page >= meta.last_page"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm disabled:opacity-50"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">{{ editingId ? 'Edit Leave Type' : 'Add Leave Type' }}</h2>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Leave Type Name</label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Days Per Year</label>
                    <input
                        v-model.number="form.daysPerYear"
                        type="number"
                        min="1"
                        max="365"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0c8ce9]"
                    />
                </div>
                <div class="flex items-center gap-2">
                    <input id="isActive" v-model="form.isActive" type="checkbox" class="rounded border-gray-300" />
                    <label for="isActive" class="text-sm font-medium text-gray-700">Active</label>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button
                    @click="closeModal"
                    class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold"
                >
                    Cancel
                </button>
                <button
                    @click="saveType"
                    class="px-5 py-2 bg-[#0c8ce9] text-white rounded-lg text-sm font-semibold hover:bg-blue-700"
                >
                    Save
                </button>
            </div>
        </div>
    </div>

    <div class="fixed bottom-0 right-0 p-2 z-50">
        <img :src="agnes" class="h-auto w-auto" v-if="loading" />
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'
import { useAuthStore } from '@/stores/auth'
import agnes from '@/assets/agnes.gif'

const auth = useAuthStore()
const router = useRouter()

const loading = ref(false)
const rows = ref([])
const meta = ref({ current_page: 1, last_page: 1, per_page: 10, total: 0 })

const filters = ref({ search: '', status: '' })
const pagination = ref({ page: 1, perPage: 10 })

const showModal = ref(false)
const editingId = ref(null)
const form = ref({ name: '', daysPerYear: 1, isActive: true })

const fetchLeaveTypes = async () => {
    try {
        loading.value = true
        const response = await axios.get('/api/leave-types', {
            params: {
                search: filters.value.search,
                status: filters.value.status,
                page: pagination.value.page,
                per_page: pagination.value.perPage,
            },
        })

        rows.value = response.data?.data || []
        meta.value = response.data?.meta || { current_page: 1, last_page: 1, per_page: pagination.value.perPage, total: 0 }
    } catch (error) {
        rows.value = []
        meta.value = { current_page: 1, last_page: 1, per_page: pagination.value.perPage, total: 0 }
        console.error('Failed to load leave types', error)
    } finally {
        loading.value = false
    }
}

const openCreateModal = () => {
    editingId.value = null
    form.value = { name: '', daysPerYear: 1, isActive: true }
    showModal.value = true
}

const openEditModal = (item) => {
    editingId.value = item.id
    form.value = {
        name: item.name,
        daysPerYear: item.daysPerYear,
        isActive: !!item.isActive,
    }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
}

const saveType = async () => {
    if (!String(form.value.name || '').trim()) {
        Swal.fire({ icon: 'error', title: 'Validation Error', text: 'Leave type name is required.', confirmButtonColor: '#ef4444' })
        return
    }

    if (!Number.isFinite(Number(form.value.daysPerYear)) || Number(form.value.daysPerYear) < 1) {
        Swal.fire({ icon: 'error', title: 'Validation Error', text: 'Days per year must be at least 1.', confirmButtonColor: '#ef4444' })
        return
    }

    try {
        if (editingId.value) {
            await axios.put(`/api/leave-types/${editingId.value}`, {
                name: String(form.value.name).trim(),
                days_per_year: Number(form.value.daysPerYear),
                is_active: !!form.value.isActive,
            })
        } else {
            await axios.post('/api/leave-types', {
                name: String(form.value.name).trim(),
                days_per_year: Number(form.value.daysPerYear),
                is_active: !!form.value.isActive,
            })
        }

        closeModal()
        fetchLeaveTypes()
    } catch (error) {
        const message = error.response?.data?.message || 'Unable to save leave type.'
        Swal.fire({ icon: 'error', title: 'Save Failed', text: message, confirmButtonColor: '#ef4444' })
    }
}

const removeType = async (item) => {
    const result = await Swal.fire({
        icon: 'warning',
        title: 'Remove leave type?',
        text: `This will remove ${item.name}.`,
        showCancelButton: true,
        confirmButtonText: 'Remove',
        confirmButtonColor: '#dc2626',
    })

    if (!result.isConfirmed) return

    try {
        await axios.delete(`/api/leave-types/${item.id}`)
        fetchLeaveTypes()
    } catch (error) {
        const message = error.response?.data?.message || 'Unable to remove leave type.'
        Swal.fire({ icon: 'error', title: 'Remove Failed', text: message, confirmButtonColor: '#ef4444' })
    }
}

const goToPage = (page) => {
    if (page < 1 || page > meta.value.last_page) return
    pagination.value.page = page
}

const formatDate = (value) => {
    if (!value) return '--'
    return new Date(value).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

watch(() => [filters.value.search, filters.value.status, pagination.value.perPage], () => {
    pagination.value.page = 1
    fetchLeaveTypes()
})

watch(() => pagination.value.page, () => {
    fetchLeaveTypes()
})

onMounted(() => {
    auth.pageTitle = 'Leave Configuration'
    fetchLeaveTypes()
})
</script>