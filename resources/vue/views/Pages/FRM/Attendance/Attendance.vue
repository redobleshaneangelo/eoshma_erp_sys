<template>
    <div class="bg-[#F3F4F6] min-h-screen">
        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Attendance</h1>
                <p class="text-sm text-gray-600 mt-1">Record and review daily attendance.</p>
            </div>
            <RecordAttendanceTab />
        </div>
    </div>
</template>

<script setup>
    import { onMounted } from 'vue'
    import { useAuthStore } from '@/stores/auth'
    import RecordAttendanceTab from '@/components/HR/Attendance/RecordAttendanceTab.vue'

    const auth = useAuthStore()

    onMounted(() => {
        auth.pageTitle = 'Attendance'
    })
</script>
