<template>
        <div
            class="min-h-screen flex items-start justify-center bg-cover bg-center bg-no-repeat py-15"
            :style="{ backgroundImage: `url(${loginPageBg})` }"
        >   
            <div class="w-100 max-w-sm mt-20">
                <div class="flex items-center justify-center mb-10">
                    <img :src="eoshma" class="h-auto w-50"/>
                </div>
                <div>
                    <div class="p-6">
                        <form class="space-y-4" @submit.prevent="handleLogin" novalidate>
                            <div>
                                <label for="email" class="text-white font-medium">Email or Employee ID</label>
                                <input
                                    id="email"
                                    v-model="userData.email"
                                    type="email"
                                    class="w-full mt-2 px-3 py-1 border border-gray-200 rounded bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300"
                                    autocomplete="username"
                                />
                            </div>
                            <div>
                                <label for="password" class="text-white font-medium">Password</label>
                                <input
                                    id="password"
                                    v-model="userData.password"
                                    type="password"
                                    class="w-full mt-2 px-3 py-1 border border-gray-200 rounded bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300"
                                    autocomplete="current-password"
                                />
                            </div>
                            <div class="mt-5">
                                <button
                                    type="submit"
                                    :disabled="loading"
                                    class="w-full py-2 rounded bg-[#0C8CE9] text-white font-medium text-sm shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300 disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                                >
                                    <span>{{ loading ? 'Logging in...' : 'Login' }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</template>

<script setup>
    /* =====================================================
    Core & Stores
    ===================================================== */
    import { ref } from 'vue'
    import { useRouter } from 'vue-router'
    import Swal from 'sweetalert2'
    import { useAuthStore } from '@/stores/auth'

    const router = useRouter()
    const auth = useAuthStore()

    /* =====================================================
    Assets
    ===================================================== */
    import eoshma from '@/assets/EOSHMAlogin.png'
    import loginPageBg from '@/assets/loginPageBG.jpg'

    /* =====================================================
    UI State
    ===================================================== */
    const userData = ref({
        email: 'superadmin@gmail.com',
        password: 'eoshma123'
    })

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
            customClass: {
                popup: 'no-wrap-toast'
            }
        })
    }

    /* =====================================================
    Actions
    ===================================================== */
    const handleLogin = async () => {
        if (!userData.value.email || !userData.value.password) {
            showToast({
                icon: 'warning',
                html: "Looks like something's missing",
                position: 'top-end'
            })
            return
        }

        loading.value = true
        try {
            const response = await auth.login(userData.value.email, userData.value.password)
            if (response && response.status === 200) {
                showToast({
                    icon: 'success',
                    html: `Welcome back, ${response.data.user.last_name}!`,
                    position: 'top'
                })
                router.push('/dashboard')
                return
            }
            Swal.fire(response.data?.message || 'Login response received')
        } catch (error) {
            const msg = error?.response?.data?.message?.text || error?.message || 'Login failed'
            showToast({
                icon: 'error',
                title: msg,
                position: 'top'
            })
        } finally {
            loading.value = false
        }
    }
</script>

<style>
    /* optional: avoid flicker before Vue mounts */
    [v-cloak] { display: none !important; }
</style>
