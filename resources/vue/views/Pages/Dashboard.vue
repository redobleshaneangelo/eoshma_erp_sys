<template>
    <auth-layout />
    <div class="ps-60 bg-[#F3F4F6] min-h-screen">
        <div class="w-full px-10 py-10">
            <HRDashboard v-if="auth.department === 'HR'" />
            <FinanceDashboard v-else-if="auth.department === 'Finance'" />
        </div>
    </div>

    <div class="fixed bottom-0 right-0 p-2 z-50">
        <img :src="agnes" class="h-auto w-auto" v-if="loading" />
    </div>
</template>

<script setup>
    /* =====================================================
    Core Imports
    ===================================================== */
    import { ref, onMounted, onBeforeUnmount, nextTick, watch, computed } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import axios from 'axios'
    import Swal from 'sweetalert2'

    import HRDashboard from '@/views/Pages/HR/Dashboard/Dashboard.vue'
    import FinanceDashboard from '@/views/Pages/FRM/Dashboard/FinanceDashboard.vue'

    /* =====================================================
    Stores & Layouts
    ===================================================== */
    import { useAuthStore } from '@/stores/auth'

    const auth = useAuthStore()

    /* =====================================================
    Router
    ===================================================== */
    const route = useRoute()
    const router = useRouter()

    /* =====================================================
    Assets
    ===================================================== */
    import agnes from '@/assets/agnes.gif'

    /* =====================================================
    UI State
    ===================================================== */
    const loading = ref(false)

    /* =====================================================
    Helpers
    ===================================================== */
    const showToast = ({
        icon = 'info',
        title = '',
        html = '',
        position = 'top-end',
        timer = 3000
    }) => {
        Swal.fire({
            toast: true,
            position,
            icon,
            title,
            html,
            showConfirmButton: false,
            timer,
            customClass: { popup: 'no-wrap-toast' }
        })
    }

    /* =====================================================
    Lifecycle
    ===================================================== */
    onMounted(() => {
        // Initialization logic
        auth.pageTitle = 'Dashboard'
    })
</script>
