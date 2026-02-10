<template>
    <nav class="bg-[#F3F4F6] w-full ps-60">
        <div class="mx-auto">
            <div class="flex justify-start h-auto items-center px-10">
                <div class="border-b-2 border-gray-300 flex w-full pt-5">
                    <div class="text-[40px] text-[#2B2B2B] font-bold">
                        <div>{{ auth.pageTitle }}</div>
                    </div>
                    <div class="ms-auto flex items-end pb-5 font-bold text-[#2B2B2B]">
                        Notifications
                        <svg class="w-5 h-5 ms-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <aside class="fixed top-0 left-0 h-screen w-60 bg-white flex flex-col border-r border-gray-200 z-40">
        <!-- Sidebar Header -->
        <div class="flex h-16 items-center justify-center border-b border-gray-200 px-4">
            <img :src="eoshma" alt="Logo" class="h-5 w-auto"/>
        </div>

        <!-- Sidebar Content -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            <router-link 
                to="/dashboard"
                class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
                :class="$route.path === '/dashboard' 
                    ? 'bg-[#0c8ce9] text-white' 
                    : 'text-gray-700 hover:bg-gray-100'"
            >
                <svg class="h-5 w-5 shrink-0" :class="$route.path === '/dashboard' ? 'text-white' : 'text-gray-700'" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M13 9V3h8v6h-8zM3 13V3h8v10H3zm10 8V11h8v10h-8zM3 21v-6h8v6H3z"/>
                </svg>
                <span>Dashboard</span>
            </router-link>

            <router-link
                to="/attendance"
                class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
                :class="$route.path.startsWith('/attendance')
                    ? 'bg-[#0c8ce9] text-white'
                    : 'text-gray-700 hover:bg-gray-100'"
            >
                <svg class="h-5 w-5 shrink-0" :class="$route.path.startsWith('/attendance') ? 'text-white' : 'text-gray-700'" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zm-7 5h5v5h-5z"/>
                </svg>
                <span>Attendance</span>
            </router-link>

            <router-link
                to="/communications"
                class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
                :class="$route.path.startsWith('/communications')
                    ? 'bg-[#0c8ce9] text-white'
                    : 'text-gray-700 hover:bg-gray-100'"
            >
                <svg class="h-5 w-5 shrink-0" :class="$route.path.startsWith('/communications') ? 'text-white' : 'text-gray-700'" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/>
                </svg>
                <span>Communications</span>
            </router-link>

            <router-link
                v-if="isManagerOrAdmin()"
                to="/pending-approvals"
                class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
                :class="$route.path.startsWith('/pending-approvals')
                    ? 'bg-[#0c8ce9] text-white'
                    : 'text-gray-700 hover:bg-gray-100'"
            >
                <svg class="h-5 w-5 shrink-0" :class="$route.path.startsWith('/pending-approvals') ? 'text-white' : 'text-gray-700'" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-2 14l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                </svg>
                <span>Pending Approvals</span>
            </router-link>

            <router-link
                to="/pending-requests"
                class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
                :class="$route.path.startsWith('/pending-requests')
                    ? 'bg-[#0c8ce9] text-white'
                    : 'text-gray-700 hover:bg-gray-100'"
            >
                <svg class="h-5 w-5 shrink-0" :class="$route.path.startsWith('/pending-requests') ? 'text-white' : 'text-gray-700'" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                </svg>
                <span>Pending Requests</span>
            </router-link>

            <router-link
                v-if="isManagerOrAdmin()"
                to="/reports"
                class="group relative px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-3"
                :class="$route.path.startsWith('/reports')
                    ? 'bg-[#0c8ce9] text-white'
                    : 'text-gray-700 hover:bg-gray-100'"
            >
                <svg class="h-5 w-5 shrink-0" :class="$route.path.startsWith('/reports') ? 'text-white' : 'text-gray-700'" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                </svg>
                <span>Reports</span>
            </router-link>

            <HRTabs/>
            <FinanceTabs/>
            <CRMTabs/>
            <OperationsTabs/>
            <SCMTabs/>
        </nav>

        <!-- Sidebar Footer -->
        <div class="border-t border-gray-200 p-3">
            <button
                @click="logout"
                class="w-full px-4 py-2 text-sm font-medium rounded-md transition-colors text-gray-700 hover:bg-red-50 hover:text-red-600 flex items-center gap-3"
            >
                <svg class="h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5-5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                </svg>
                <span>Log out</span>
            </button>
        </div>
    </aside>

    <div class="fixed bottom-0 right-0 p-2 z-50">
        <img :src="agnes" class="h-auto w-auto" v-if="loading" />
    </div>
    <slot></slot>
</template>

<script setup>
    /* =====================================================
    Core Imports
    ===================================================== */
    import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import axios from 'axios'
    import Swal from 'sweetalert2'

    import HRTabs from '@/views/Layouts/Tabs/HRTabs.vue'
    import FinanceTabs from '@/views/Layouts/Tabs/FinanceTabs.vue'
    import CRMTabs from '@/views/Layouts/Tabs/CRMTabs.vue'
    import OperationsTabs from '@/views/Layouts/Tabs/OperationsTabs.vue'
    import SCMTabs from '@/views/Layouts/Tabs/SCMTabs.vue'

    /* =====================================================
    Stores
    ===================================================== */
    import { useAuthStore } from '@/stores/auth'

    const auth = useAuthStore()

    const hasPermission = (permission) => {
        return auth.permissions?.includes(permission)
    }

    const isManagerOrAdmin = () => {
        if (!auth.user) return false
        return auth.user.role.includes('_manager') || auth.user.role.includes('admin')
    }

    /* =====================================================
    Router
    ===================================================== */
    const route = useRoute()
    const router = useRouter()

    /* =====================================================
    Assets
    ===================================================== */
    import agnes from '@/assets/agnes.gif'
    import eoshma from '@/assets/EOSHMA.png'

    /* =====================================================
    UI State
    ===================================================== */
    const loading = ref(false)

    /* =====================================================
    Toast Helper
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
    Logout
    ===================================================== */
    const logout = async () => {
        loading.value = true

        try {
            const response = await auth.logout({
                headers: { Authorization: `Bearer ${auth.token}` }
            })

            if (response.status === 200) {
                showToast({ icon: 'success', html: 'User logged out successfully' })
                router.push('/login')
            } else {
                showToast({ icon: 'error', html: 'User logout failed' })
            }
        } catch (error) {
            showToast({ icon: 'error', html: 'User logout failed' })
            console.error(error.response?.data?.message || error.message)
        } finally {
            loading.value = false
        }
    }

    /* =====================================================
    Password Modal
    ===================================================== */
    const showPasswordModal = ref(false)
    const editAccountPassword = ref('')
    const confirmEditPassword = ref('')

    const closePasswordModal = () => {
        editAccountPassword.value = ''
        confirmEditPassword.value = ''
        showPasswordModal.value = false
    }

    const submitNewPassword = async () => {
        if (!editAccountPassword.value || editAccountPassword.value.length <= 8) {
            showToast({ icon: 'error', html: 'Please enter a valid password.' })
            return
        }

        if (editAccountPassword.value !== confirmEditPassword.value) {
            showToast({ icon: 'error', html: 'Password and confirm password<br>do not match.' })
            return
        }

        loading.value = true

        try {
            const res = await axios.put(
                `/users/${auth.user.id}/${editAccountPassword.value}/password`,
                null,
                { headers: { Authorization: `Bearer ${auth.token}` } }
            )

            showToast({ icon: 'success', title: res.data.message })
        } catch (err) {
            showToast({ icon: 'error', html: 'Edit failed<br>Check console for details' })
            console.error(err.response?.data?.message || err.message)
        } finally {
            loading.value = false
        }

        closePasswordModal()
    }

    /* =====================================================
    Lifecycle
    ===================================================== */
    onMounted(() => {
        // Init logic here if needed
    })
</script>

<style>
    /* optional: avoid flicker before Vue mounts */
    [v-cloak] { display: none !important; }
</style>
