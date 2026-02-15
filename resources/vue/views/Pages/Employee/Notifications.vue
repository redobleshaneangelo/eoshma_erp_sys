<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="px-10 py-10 space-y-6">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Notifications</h2>
                    <span class="text-sm text-gray-600">Unread: {{ unreadCount }}</span>
                </div>

                <div class="divide-y divide-gray-200">
                    <div
                        v-for="item in notifications"
                        :key="item.id"
                        class="px-6 py-4 hover:bg-gray-50 transition"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold" :class="item.readAt ? 'text-gray-800' : 'text-[#0c8ce9]'">{{ item.title }}</p>
                                <p class="text-sm text-gray-600 mt-1">{{ item.message }}</p>
                                <p class="text-xs text-gray-500 mt-2">{{ formatDateTime(item.createdAt) }}</p>
                            </div>

                            <div class="flex flex-col items-end gap-2">
                                <span v-if="!item.readAt" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                                    New
                                </span>

                                <button
                                    @click="openNotification(item)"
                                    class="px-3 py-1 text-xs font-semibold text-white bg-[#0c8ce9] rounded hover:bg-blue-700"
                                >
                                    {{ item.kind === 'payslip_released' ? 'Open Payslip' : 'View' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="!notifications.length" class="px-6 py-10 text-center text-gray-500 text-sm">
                        No notifications yet.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

const auth = useAuthStore()
const router = useRouter()

const notifications = ref([])
const unreadCount = ref(0)

const fetchNotifications = async () => {
    try {
        const response = await axios.get('/api/employee/notifications')
        notifications.value = response.data?.data || []
        unreadCount.value = Number(response.data?.meta?.unread || 0)
    } catch (error) {
        console.error('Failed to load notifications', error)
    }
}

const openNotification = async (item) => {
    try {
        await axios.post(`/api/employee/notifications/${item.id}/read`)
    } catch (error) {
        console.error('Failed to mark notification as read', error)
    }

    if (item.kind === 'payslip_released' && item.payslipId) {
        router.push({ name: 'my_payslip', params: { id: item.payslipId } })
        return
    }

    await fetchNotifications()
}

const formatDateTime = (dateTime) => {
    if (!dateTime) return '--'
    return new Date(dateTime).toLocaleString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    })
}

onMounted(() => {
    auth.pageTitle = 'Notifications'
    fetchNotifications()
})
</script>
